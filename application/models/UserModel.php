<?php

namespace application\models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use application\models\ArticleModel;

class UserModel extends MainModel{

	private function getUserArticles($status=null) {
		$db = Db::getInstance();
		$user_login = $db->quote($_SESSION['user_login']);
		$q = "
			SELECT *, articles.id, articles.status FROM articles 
			JOIN users ON articles.user_id = users.id
			AND users.login = $user_login
		";
		if(!empty($status)) $q .= " AND articles.status = $status";
		$q .= " ORDER BY articles.id DESC";
		return $db->query($q)->fetchAll();
	}

	public function getAllArticlesOfUser() {
		return $this->getUserArticles();
	}

	public function getPublishedArticles(){
		return $this->getUserArticles(1);
	}

	public function getRejectedArticles(){
		return $this->getUserArticles(2);
	}

	public function getSavedArticles(){
		return $this->getUserArticles(3);
	}

	public function getReconciliationArticles(){
		return $this->getUserArticles(4);
	}

	public function getPublishedArticlesOfAllUsers(){
		$db = Db::getInstance();

		$q = "
			SELECT *, articles.id, articles.status FROM articles 
			JOIN users ON articles.user_id = users.id
			AND articles.status = 1
			ORDER BY date_create DESC
		";
		return $db->query($q)->fetchAll();
	}

	public function getBids(){
		$db = Db::getInstance();

		$q = "
			SELECT *, articles.id, articles.status FROM articles 
			JOIN users ON articles.user_id = users.id
			AND articles.status = 4
			ORDER BY articles.id DESC
		";
		return $db->query($q)->fetchAll();
	}

	public function checkOldPassword($oldPwd){
		$db = Db::getInstance();

		$id = $_SESSION['user_id'];
		$q = "
			SELECT password FROM users 
			where id = $id
			limit 1
		";

		$hash = $db->query($q)->fetch()->password;

		if (!password_verify($oldPwd, $hash)) return 1;
	}

	public function checkNewPasswords($oldPwd, $newPwd1, $newPwd2) {
		if( $newPwd1 !== $newPwd2) return 1;
		if( strlen($newPwd1) < 4 || strlen($newPwd2) < 4) return 2;
		if( $oldPwd === $newPwd1) return 3;
	}

	public function changePassword($newPass){
		$db = Db::getInstance();
		$hash = $db->quote(password_hash($newPass, PASSWORD_BCRYPT, $this->passwordHashCost));
		$id = $_SESSION['user_id'];
		$q = "UPDATE users SET password = $hash where id = $id LIMIT 1";
		$db->exec($q);
	}

	public function remindPass($email) {

		if(strlen($email) < 5) return false;
		if(strlen($email) > 254) return false;
		if (!preg_match('/^.+@.+\..+$/', $email)) return false;

		$db = Db::getInstance();
		$emailQuoted = $db->quote($email);
		$q = "SELECT email from users where email = $emailQuoted";
		$res = $db->query($q)->fetch();
		if(empty($res)) return false;

		$symbols = str_split("abcdefghijklmnopqrstuxyvwzABCDEFGHIJKLMNOPQRSTUXYVWZ1234567890");
		shuffle($symbols);
		$rand = '';
		foreach (array_rand($symbols, 4) as $k) $rand .= $symbols[$k];


		$hash = $db->quote(password_hash($rand, PASSWORD_BCRYPT, $this->passwordHashCost));
		$q = "UPDATE users SET password = $hash where email = $emailQuoted LIMIT 1";
		$db->exec($q);

		return ['email' => $email, 'pass' => $rand];
	}

	public function sendRemindPassMail($email, $pass) {

		$mailer = new PHPMailer(true);
		$email = htmlspecialchars($email);

		$mailer->addAddress($email);
		$mailer->From = "admin@esy24.esy.es";
		$mailer->FromName = "esy24.esy.es";
		$mailer->Subject = 'Новый пароль для esy24.esy.es';
		$mailer->CharSet = 'UTF-8';
		$mailer->Body = 'Новый пароль: ' . $pass;
		$mailer->send();

	}

	public function getPostsByUserLogin($login){
		$db = Db::getInstance();
		$loginQuoted = $db->quote($login);
		$q = "SELECT a.*, c.category, u.login as user_login FROM articles a
			JOIN categories c on c.id = a.category
			JOIN users u on u.id = a.user_id
			WHERE u.login = $loginQuoted
			AND a.status = 1
		";

		$dbRes = $db->query($q)->fetchAll();

		if(empty($dbRes)) return $dbRes;

		$articleModel = new ArticleModel();
		foreach ($dbRes as &$article) {
			$articleRating = $articleModel->getRatingForArticle($article->id);
			$article->rating = $articleRating->rating;
			$article->voicesCount = $articleRating->voicesCount;
		}

		return $dbRes;
	}

	public function isUserExist($login){
		$db = Db::getInstance();
		$loginQuoted = $db->quote($login);
		$q = "SELECT login FROM users WHERE login = $loginQuoted";
		$res = $db->query($q)->fetch();
		return empty($res) ? false : true;
	}

	public function getCountArticlesByStatusForUser(){

		$db = Db::getInstance();
		$id = $_SESSION['user_id'];
		$q = "SELECT COUNT(*) as count FROM `articles` WHERE user_id = $id and status = ";
		$publishedCount = $db->query($q . '1')->fetch()->count;
		$rejectedCount = $db->query($q . '2')->fetch()->count;
		$savedCount = $db->query($q . '3')->fetch()->count;
		$reconciliationCount = $db->query($q . '4')->fetch()->count;
		
		$res = [ 
			'publishedCount' 		=> $publishedCount,
			'rejectedCount' 		=> $rejectedCount,
			'savedCount' 			=> $savedCount,
			'reconciliationCount' 	=> $reconciliationCount,
		];
		$res['all'] = array_sum($res);

		return $res;
	}

	public function getCountArticlesByStatusForAdmin(){
		$db = Db::getInstance();
		$q = "SELECT COUNT(*) as count FROM `articles` WHERE status = ";
		$publishedCount = $db->query($q . '1')->fetch()->count;
		$bidsCount = $db->query($q . '4')->fetch()->count;

		return [ 
			'publishedCount' 		=> $publishedCount,
			'bidsCount' 					=> $bidsCount,
		];
	}
}