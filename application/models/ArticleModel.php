<?php

namespace application\models;

class ArticleModel extends MainModel
{
	public function getArticleById($id){
		$db = Db::getInstance();
		$q = "SELECT articles.*, users.login as user_login FROM articles 
			JOIN users ON articles.user_id = users.id 
			AND articles.id = $id 
			LIMIT 1";
		$article = $db->query($q)->fetch();
		if(empty($article)) return false;
		$articleRating = $this->getRatingForArticle($id);
		$article->rating = $articleRating->rating;
		$article->voicesCount = $articleRating->voicesCount;

		$q = "SELECT count(*) as count from comments where article_id = $id";
		$article->commentsCount = $db->query($q)->fetch()->count;

		return $article;
	}

	public function getArticlesOfCategoryById($id, $countPerPage, $offset = 0){
		$db = Db::getInstance();
		$q = "SELECT articles.*, users.login as user_login FROM articles
		JOIN users ON articles.user_id = users.id 
		AND category = $id
		AND articles.status = 1
		LIMIT $offset, $countPerPage
		";

		$dbRes = $db->query($q)->fetchAll();

		if(empty($dbRes)) return $dbRes;

		foreach ($dbRes as &$article) {
			$articleRating = $this->getRatingForArticle($article->id);
			$article->rating = $articleRating->rating;
			$article->voicesCount = $articleRating->voicesCount;
		}

		return $dbRes;
	}

	public function getCategoryTitleById($categoryId) {
		$db = Db::getInstance();
		$q = "SELECT category FROM categories WHERE id = $categoryId";
		$res = $db->query($q)->fetch();
		return $res ? $res->category : false;
	}

	public function getCountArticlesInCategoryById($categoryId) {
		$db = Db::getInstance();
		$q = "SELECT count(*) as count FROM articles WHERE category = $categoryId";
		return $db->query($q)->fetch()->count;
	}

	public function savePost($id = null) {
		$db = Db::getInstance();
		$header = $db->quote(strip_tags(trim($_POST['header'])));
        $description = $db->quote(htmlentities(trim($_POST['postText']), ENT_NOQUOTES));
        $short_description = $db->quote(htmlentities(trim($_POST['previewText']), ENT_NOQUOTES));
        $category = (int) $_POST['category'];
        $user_id = (int) $_SESSION['user_id'];

        if (isset($id)) {
            $sql = "UPDATE articles 
                SET
                category = $category, header = $header, short_description = $short_description, description = $description
                WHERE id = $id
            ";
            $db->exec($sql);

            return $id;
        } else {
            $sql = "INSERT INTO articles
                (user_id, status, category, header, short_description, description) 
                VALUES 
                ($user_id, 3, $category, $header, $short_description, $description)
            ";
            $db->exec($sql);

            return $db->lastInsertId();
		}
	}

	public function sendToReconciliation($id = null){
		$id = $this->savePost($id);
		$db = Db::getInstance();
		$sql = "UPDATE articles 
                SET
                status = 4
                WHERE id = $id
        ";
        $db->exec($sql);
	}

	public function sendToReconciliationFromPreview($id) {
		$id = (int) $id;
		$db = Db::getInstance();
		$sql = "UPDATE articles 
                SET
                status = 4
                WHERE id = $id
        ";
        $db->exec($sql);
	}

	public function showPreview($id, $countPerPage) {
		$id = (int) $id;
		if(empty($_SESSION['user_id'])) return false;
		$user_id = $_SESSION['user_id'];
		$admin = !empty($_SESSION['admin']);

		

		$db = Db::getInstance();
		$q = "SELECT articles.*, users.login as user_login, users.rating FROM articles
			JOIN users ON articles.user_id = users.id
			WHERE articles.id = $id
		";

		if(!$admin && $user_id) $q .= "AND (articles.status = 1 OR articles.user_id = $user_id)";

		$previewPost = $db->query($q)->fetch();

		if(!$previewPost) return false;

		$q = "SELECT articles.*, users.login as user_login, users.rating FROM articles 
			JOIN users ON articles.user_id = users.id
			WHERE articles.id <> $id
			AND articles.status = 1 
			ORDER BY articles.date_create DESC
			LIMIT $countPerPage
		";

		$posts = $db->query($q)->fetchAll();

		if(empty($posts)) return $posts;

		array_unshift($posts, $previewPost);

		foreach ($posts as &$article) {
			$articleRating = $this->getRatingForArticle($article->id);
			$article->rating = $articleRating->rating;
			$article->voicesCount = $articleRating->voicesCount;
		}

		return $posts;
	}

	public function removePost($id){
		$id = (int) $id;
		$db = Db::getInstance();
		$q = "DELETE FROM articles where id = $id LIMIT 1";
		$db->exec($q);

		$q = "DELETE FROM comments where article_id = $id";
		$db->exec($q);
	}

	public function verifyPost($id) {
		$id = (int) $id;
		$db = Db::getInstance();
		$time = time();
		$q = "UPDATE articles set status = 1, date_create = $time where id = $id LIMIT 1";
		$db->exec($q);
	}

	public function rejectPost($id) {
		$id = (int) $id;
		$db = Db::getInstance();
		$q = "UPDATE articles set status = 2 where id = $id LIMIT 1";
		$db->exec($q);
	}

	public function publicPostAdmin($id) {
		$id = (int) $id;
		$db = Db::getInstance();
		$time = time();
		$header = $db->quote(strip_tags(trim($_POST['header'])));
        $description = $db->quote(htmlentities(trim($_POST['postText']), ENT_NOQUOTES));
        $short_description = $db->quote(htmlentities(trim($_POST['previewText']), ENT_NOQUOTES));
        $category = (int) $_POST['category'];
        $status = 1;

		$sql = "UPDATE articles 
                SET
                status = $status,
                category = $category, 
                header = $header, 
                short_description = $short_description, 
                description = $description,
                date_create = $time
                WHERE id = $id
            ";
        $db->exec($sql);
	}

	public function setArticleRating($newRating, $articleId) {
		$db = Db::getInstance();
		$ratingDb = $this->getUserRatings($articleId);
		$ratingDb->$articleId = $newRating;
		$ratingJSON = json_encode($ratingDb);
		$ratingQuoted = $db->quote($ratingJSON);
		$id = $_SESSION['user_id'];

		$q = "UPDATE users SET rating = $ratingQuoted WHERE id = $id LIMIT 1";
		$db->exec($q);		
	}

	private function getUserRatings($articleId) {
		$db = Db::getInstance();
		$id = $_SESSION['user_id'];
		$q = "SELECT rating FROM users where id = $id LIMIT 1";
		$userRating = $db->query($q)->fetch();
		$ratingDb = new \stdClass;

		if(!empty($userRating)){
			$ratingDb = json_decode($userRating->rating);
		}

		return $ratingDb;
	}

	public function getUserRatingForArticle($articleId) {
		
		if(empty($_SESSION['user_id'])) return false;
		$ratingDb = $this->getUserRatings($articleId);
		if(!empty($ratingDb->$articleId)) return $ratingDb->$articleId;
		return false;

	}

	public function getRatingForArticle($articleId) {
		$db = Db::getInstance();
		$q = "SELECT rating FROM users WHERE rating IS NOT NULL";
		$res = $db->query($q)->fetchAll();
		$count = 0;
		$sum = 0;
		foreach ($res as $userRating) {
			$userRating = json_decode($userRating->rating);
			if(!empty($userRating->$articleId)) {
				$count++;
				$sum+=$userRating->$articleId;
			}
		}
		if($sum == 0) $rating = 0;
		else $rating = round($sum / $count, 1);

		return (object)[
			'voicesCount' => $count,
			'rating' => $rating
		];
	}

	public function addComment($user_id, $articleId, $commentText){
		$db = Db::getInstance();
		$commentText = $db->quote($commentText);
		$date = time();
		$q = "INSERT INTO comments (user_id, article_id, text, date) 
		VALUES ($user_id, $articleId, $commentText, $date)";

		return $db->exec($q);
	}

	public function getCommentsByArticleId($articleId){
		$db = Db::getInstance();
		$q = "SELECT c.*, c.text as commentText, u.login FROM comments c
		JOIN users u ON c.user_id = u.id
		AND c.article_id = $articleId
		ORDER BY c.date";

		return $db->query($q)->fetchAll();
	}

	public function getCommentsCount(&$articles){

		$db = Db::getInstance();
		$q = "SELECT article_id, count(*) as count from comments group by article_id";
		$res = $db->query($q)->fetchAll();

		$article_ids = array_map(function($e) {
			return $e->article_id;
		}, $res);

		if(is_array($articles)) {
			
			foreach($articles as $article) {
				$key = array_search($article->id, $article_ids);
				$count = 0;
				if($key !== false) $count = $res[$key]->count;
				$article->commentsCount = $count;
			}
			
		} else {
			
			$key = array_search($articles->id, $article_ids);
			$articles->commentsCount = $res[$key]->count;
		}		

		return $articles;
	}

	function countCommentsByArticleId($id) {
		$db = Db::getInstance();
		$q = "SELECT count(*) as count from comments where article_id = $id";

		return $db->query($q)->fetch()->count;
	}

	public function removeComment($comment_id, $txt) {
		$db = Db::getInstance();
		$txt = $db->quote($txt);
		$q = "UPDATE comments set text = $txt where id = $comment_id LIMIT 1";
		return $db->exec($q);
	}

	public function removeCategory($id) {
		$db = Db::getInstance();
		$q = "DELETE FROM categories where id = $id LIMIT 1";
		return $db->exec($q);
	}

	public function createCategory($name) {
		$db = Db::getInstance();
		$name = $db->quote($name);
		$q = "INSERT into  categories (category) VALUES ($name)";
		return $db->exec($q);
	}

	public function getCategoryNameById($id) {
		$db = Db::getInstance();
		$q = "SELECT category from categories where id = $id LIMIT 1";
		return $db->query($q)->fetch()->category;
	}

	public function updateCategory($id, $name) {
		$db = Db::getInstance();
		$name = $db->quote($name);
		$q = "UPDATE categories set category = $name where id = $id";
		return $db->exec($q);
	}
}