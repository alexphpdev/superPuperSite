<?php
namespace application\controllers;

use application\models\UserModel;
use application\models\IndexModel;

class UserController {
	private $model;
	private $countArticles;

	public function __construct(){
		$this->model = new UserModel();
		if($_SESSION['user_id']) {
			$this->countArticles = $this->model->getCountArticlesByStatusForUser();
			$this->countArticlesForAdmin = $this->model->getCountArticlesByStatusForAdmin();
		}
	}

	public function index(){
		$args = ['title' => 'Личный кабинет'];
		$args['articles'] = $this->model->getAllArticlesOfUser();
		$args['countArticles'] = $this->countArticles;

		$this->model->render("user".DIRSEP."index.php", $args);
	}

	public function showPublishedPosts() {
		$args = ['title' => 'Личный кабинет'];
		$args['articles'] = $this->model->getPublishedArticles();
		$args['countArticles'] = $this->countArticles;
		$this->model->render("user".DIRSEP."published_posts.php", $args);
	}

	public function showRejectedPosts() {
		$args = ['title' => 'Личный кабинет'];
		$args['articles'] = $this->model->getRejectedArticles();
		$args['countArticles'] = $this->countArticles;
		$this->model->render("user".DIRSEP."rejected_posts.php", $args);
	}

	public function showSavedPosts() {
		$args = ['title' => 'Личный кабинет'];
		$args['articles'] = $this->model->getSavedArticles();
		$args['countArticles'] = $this->countArticles;
		$this->model->render("user".DIRSEP."saved_posts.php", $args);
	}

	public function showReconciliationPosts() {
		$args = ['title' => 'Личный кабинет'];
		$args['articles'] = $this->model->getReconciliationArticles();
		$args['countArticles'] = $this->countArticles;
		$this->model->render("user".DIRSEP."reconciliation_posts.php", $args);
	}

	public function logout(){

		setcookie('data', null, -1, '/');
		session_destroy();

		header("Location:" . HOST);
	}

	public function adminIndex(){
		$args = ['title' => 'Личный кабинет администратора'];
		$args['articles'] = $this->model->getPublishedArticlesOfAllUsers();
		$args['bids'] = false;
		$args['countArticles'] = $this->countArticlesForAdmin;
		$this->model->render("admin".DIRSEP."index.php", $args);
	}

	public function showBids() {
		$args = ['title' => 'Личный кабинет администратора'];
		$args['articles'] = $this->model->getBids();
		$args['bids'] = true;
		$args['countArticles'] = $this->countArticlesForAdmin;
		$this->model->render("admin".DIRSEP."index.php", $args);
	}

	public function changePassword(){
		$oldPwd = trim($_POST['oldPwd']);
		$newPwd1 = trim($_POST['newPwd1']);
		$newPwd2 = trim($_POST['newPwd2']);

		$errors = [];
		$errors['OldPassword'] = $this->model->checkOldPassword($oldPwd);
		$errors['password'] = $this->model->checkNewPasswords($oldPwd, $newPwd1, $newPwd2);

		switch ($errors['OldPassword']) {
			case 1: $errors['OldPassword'] = 'Неправильный пароль'; break;
			default: unset($errors['OldPassword']);
		}

		if(empty($errors['OldPassword'])) {
			switch ($errors['password']) {
				case 1: $errors['password'] = 'Пароли не совпадают'; break;
				case 2: $errors['password'] = 'Минимальная длина пароля 4 символа'; break;
				case 3: $errors['password'] = 'Новый пароль не может быть таким же как и старый'; break;
				default: unset($errors['password']);
			}
		} else unset($errors['password']);

		if(count($errors) == 0) {
			$this->model->changePassword($newPwd1);
		}

		echo json_encode($errors ? $errors : 0);
	}

	public function remindPass(){
		$email = trim($_POST['email']);
		if(!$email) return false;

		$res = $this->model->remindPass($email);
		if($res) {
			$this->model->sendRemindPassMail($res['email'], $res['pass']);
		}

		echo $res ? true : false;
	}

	public function remindPassForm()
	{
		$this->model->render("remindPass.php", ['title' => 'Восстановление пароля']);
	}

	public function showPostsOfUser($login){
		$args = ['title' => 'Статьи от ' . $login];
		$userExist = $this->model->isUserExist($login);
		if($userExist) {
			$articles = $this->model->getPostsByUserLogin($login);
			$args['articles'] = $articles;
			if(empty($articles)) $args['emptyArticles'] = true;
		} else $args['emptyUser'] = true;
		
		$indexModel = new IndexModel();
		$args['categories'] = $indexModel->getAllCategories();

		$this->model->render("userPage.php", $args);	

	}
}