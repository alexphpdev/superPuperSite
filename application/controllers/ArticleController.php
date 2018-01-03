<?php

namespace application\controllers;

use application\models\ArticleModel;
use application\models\IndexModel;

class ArticleController extends IndexController{

	private $model;

	public function __construct(){
		$this->model = new ArticleModel();
	}

	public function showCertainArticle($articleId){

		$article = $this->model->getArticleById($articleId);
		$args = [];
		// если пост не опубликован
		if(!empty($article) && $article->status != 1 ) {
			
			// если пост имеет другой статус, то проверяем, является ли текущий пользователь администратором
			if(!empty($_SESSION['admin'])) {
				$args['show_previewPanel'] = true;

			// если пользователь не авторизован или не является автором, то пост скрываем
			} elseif(empty($_SESSION['user_login']) || $article->user_id != $_SESSION['user_id']) {
				$article = null;

			// если пост пользователя, то покажем панель в шапке с кнопкой "опубликовать пост"
			} elseif(!empty($_SESSION['user_login']) && $article->user_id == $_SESSION['user_id']) { 
				$args['show_previewPanel'] = true;
			}
			
		}

		$indexModel = new IndexModel();
		$categories = $indexModel->getAllCategories();

		if($article && !$article->date_create) $article->date_create = time();
		$args['article'] = $article;
		$args['categories'] = $categories;
		$args['userRating'] = $this->model->getUserRatingForArticle($articleId);
		$args['title'] = !empty($article->header) ? $article->header : 'Такой статьи не существует!';
		$args['comments'] = $this->model->getCommentsByArticleId($articleId);

		$this->model->render('article.php', $args);
	}

	public function addPostForm() {
		$indexModel = new IndexModel();
		$args = ['title' => 'Новый пост'];
		$args['categories'] = $indexModel->getAllCategories();
		$this->model->render('user'. DIRSEP . 'addPostForm.php', $args);
	}

	public function savePost($id = null) {
		$post_id = $this->model->savePost($id);
		header("Location: " . HOST . "user/editPost/$post_id");
	}

	public function editPostForm($id) {
		$indexModel = new IndexModel();
		$article = $this->model->getArticleById($id);
		$args = [
			'title' => 'Изменение записи',
			'article' => $article,
			'categories' => $indexModel->getAllCategories(),
		];
		$args['categories'] = $indexModel->getAllCategories();
		$this->model->render('user'. DIRSEP . 'editPostForm.php', $args);
	}

	public function sendToReconciliation($id = null){
		$this->model->sendToReconciliation($id);
		header("Location: " . HOST . "user/reconciliation/");
	}

	public function sendToReconciliationFromPreview($id){
		$this->model->sendToReconciliationFromPreview($id);
		header("Location: " . HOST . "user/reconciliation/");
	}

	public function prevPost($id = null) {
		$id = $this->model->savePost($id);
		header('Location: ' . HOST . "preview/$id");
	}

	public function showPreview($id) {
		$args = [];
		$indexModel = new IndexModel();
		$articles = $this->model->showPreview($id, $this->countPerPage);
		
		if($articles === false) {
			$args['title'] = 'Такого поста не существует';
		} else {
			$articles[0]->date_create = time();
			$articleModel = new ArticleModel();
			$articleModel->getCommentsCount($articles);
			$args['title'] = 'Предосмотр';
			$args['articles'] = $articles;
			$args['show_previewPanel'] = true;	
		}

		$args['countPerPage'] = $this->countPerPage;
		$args['show_pagination'] = false;
		$args['show_more'] = false;
		$args['categories'] = $indexModel->getAllCategories();

		$this->model->render('index.php', $args);
	}

	public function removePost($id) {
		
		if(!empty($_SESSION['admin']) && $_SESSION['admin']) {	// админ может удалить любой пост

			$this->model->removePost($id);
			if(!empty($_SERVER['HTTP_REFERER'])) {
				header("Location: " . $_SERVER['HTTP_REFERER']);
				exit;
			}
			header("Location: " . HOST . 'user');

		} elseif(!empty($_SESSION) && $_SESSION['user_id']) {	// пользователь может удалять только свои НЕ опубликованные посты

			$article = $this->model->getArticleById($id);
			if(!empty($article) && ($article->user_id == $_SESSION['user_id'] && $article->status != 1)) {

				$this->model->removePost($id);
				if(!empty($_SERVER['HTTP_REFERER'])) {
					header("Location: " . $_SERVER['HTTP_REFERER']);
					exit;
				}
				header("Location: " . HOST . 'user');

			} else {

				if(!empty($_SERVER['HTTP_REFERER'])) {
					header("Location: " . $_SERVER['HTTP_REFERER']);
					exit;
				}
				header("Location: " . HOST . 'user');

			}

		} else {

			if(!empty($_SERVER['HTTP_REFERER'])) {
				header("Location: " . $_SERVER['HTTP_REFERER']);
				exit;
			}
			header("Location: " . HOST . 'user');

		}
	}

	public function verifyPost($id) {
		$this->model->verifyPost($id);
		header("Location: " . HOST . 'admin/bids');
	}

	public function rejectPost($id) {
		$this->model->rejectPost($id);
		header("Location: " . HOST . 'admin/bids');
	}

	public function editPostFormAdmin($id) {
		$indexModel = new IndexModel();
		$article = $this->model->getArticleById($id);
		$args = [
			'title' => 'Изменение записи',
			'article' => $article,
			'categories' => $indexModel->getAllCategories(),
		];
		$args['categories'] = $indexModel->getAllCategories();
		$this->model->render('admin'. DIRSEP . 'editPostForm.php', $args);
	}

	public function updatePostAdmin($id){
		$this->model->savePost($id);
		header("Location: " . HOST . "admin/editPost/$id");
	}

	public function publicPostAdmin($id) {
		$this->model->publicPostAdmin($id);
		header("Location: " . HOST . 'admin');
	}

	public function setArticleRating() {
		if (empty($_SESSION['user_login'])) {
			echo 1;
			exit;
		} else if(!empty($_SESSION['admin'])) {
			echo 2;
			exit;
		}

		$rating = (int) $_POST['rating'];
		$articleId = (int) $_POST['articleId'];
		$this->model->setArticleRating($rating, $articleId);
		echo 3;
		exit;
	}

	public function addComment() {
		$commentText = htmlspecialchars($_POST['commentText']);
		$articleId = (int) $_POST['articleId'];
		$user_id = $_SESSION['user_id'];

		if(empty($commentText) || empty($articleId) || empty($user_id)) return false;

		$added = $this->model->addComment($user_id, $articleId, $commentText);
		if(!$added) {
			echo false;
			exit;
		}

		$res = [];
		$res['commentText'] = $commentText;
		$res['date'] = date("d.m.y");
		$res['login'] = $_SESSION['user_login'];
		$res['countComments'] = $this->model->countCommentsByArticleId($articleId);
		echo json_encode($res);
	}

	public function removeComment(){
		$txt = 'Комментарий удалён администратором!';
		$comment_id = (int) $_POST['comment_id'];
		$updated = $this->model->removeComment($comment_id, $txt);
		if($updated) {
			echo $txt;
		} else echo false;
	}

	public function showCategories() {
		$args = [];
		$indexModel = new IndexModel();
		$categories = $indexModel->getAllCategories();
		
		$args['categories'] = $categories;
		$args['title'] = 'Категории';

		$this->model->render('admin'. DIRSEP . 'categories.php', $args);
	}

	public function removeCategory($categoryId){
		$categoryId = (int) $categoryId;
		$this->model->removeCategory($categoryId);
		header("Location: " . HOST . 'admin/categories');
	}

	public function createCategory() {
		$categoryName = $_POST['categoryName'];
		$this->model->createCategory($categoryName);
		header("Location: " . HOST . 'admin/categories');
	}

	public function getCategoryName() {
		$categoryId = (int) $_POST['categoryId'];
		$categoryName = $this->model->getCategoryNameById($categoryId);
		echo $categoryName;
	}

	public function updateCategory() {
		$categoryName = $_POST['categoryName'];
		$id = $_POST['categoryId'];
		$this->model->updateCategory($id, $categoryName);
		header("Location: " . HOST . 'admin/categories');
	}

}