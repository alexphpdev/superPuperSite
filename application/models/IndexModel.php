<?php

namespace application\models;

use application\models\ArticleModel;

class IndexModel extends MainModel{
	public function getAllPublishedArticles(){

		$db = Db::getInstance();

		$q = "SELECT a.*, c.category FROM `articles` as a
		JOIN `categories` as c ON a.category = c.id
		AND a.status = 1
		ORDER BY date_create DESC";

		return $db->query($q)->fetchAll();
	}

	public function getAllCategories(){
		$db = Db::getInstance();

		$q = "SELECT * FROM `categories` ORDER BY id";

		return $db->query($q)->fetchAll();
	}

	public function getPublishedArticlesCount($offset, $countPerPage){
		$db = Db::getInstance();

		$q = "SELECT a.*, c.category, users.login as user_login FROM `articles` as a
		JOIN `categories` as c ON a.category = c.id
		JOIN `users` ON users.id = a.user_id
		WHERE a.status = 1
		ORDER BY date_create DESC
		LIMIT $offset, $countPerPage";
		
		$dbRes = $db->query($q)->fetchAll();

		if(empty($dbRes)) return $dbRes;

		$articleModel = new ArticleModel();
		foreach ($dbRes as &$article) {
			$articleRating = $articleModel->getRatingForArticle($article->id);
			$article->rating = $articleRating->rating;
			$article->voicesCount = $articleRating->voicesCount;
		}

		$articleModel->getCommentsCount($dbRes);

		return $dbRes;
	}

	public function getPagination($countPerPage) {

		$db = Db::getInstance();
		$q = "SELECT count(id) as count FROM articles WHERE status = 1";
		$totalRows = $db->query($q)->fetch()->count;
		$pageCount=ceil($totalRows/$countPerPage);
		return $pageCount;
	}

	public function getCountPublicArticles(){
		$db = Db::getInstance();
		$q = "SELECT count(id) as count FROM articles WHERE status = 1";
		return $db->query($q)->fetch()->count;
	}

	public function search($searchQuery, $countPerPage, $offset = 0) {

		$db = Db::getInstance();
		$searchQuery = $db->quote('%'.$searchQuery.'%');
		$q = "SELECT articles.*, users.login as user_login FROM articles
		JOIN `users` ON users.id = articles.user_id
		WHERE (header LIKE $searchQuery OR
		short_description LIKE $searchQuery OR
		description LIKE $searchQuery)
		AND articles.status = 1
		ORDER BY date_create DESC
		LIMIT $offset, $countPerPage";

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

	public function searchCountResult($searchQuery, $countPerPage) {
		$db = Db::getInstance();
		$searchQuery = $db->quote('%'.$searchQuery.'%');
		$q = "SELECT count(*) as count FROM articles
		WHERE (header LIKE $searchQuery OR
		short_description LIKE $searchQuery OR
		description LIKE $searchQuery) AND status = 1
		ORDER BY date_create DESC
		";

		return $db->query($q)->fetch()->count;
	}
} 