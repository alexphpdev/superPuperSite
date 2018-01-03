<?php
namespace application\controllers;

use application\models\IndexModel;
use application\models\ArticleModel;

class IndexController {
	private $model;
	protected $countPerPage = 4;

	public function __construct(){
		$this->model = new IndexModel();
	}

	public function index() {
		$args = ['title' => 'Главная'];
		$currentPage = 1;
		$show_pagination = true;
		$show_more = true;
		
		$offset = !empty($_POST['offset']) ? $_POST['offset'] : 0;
		if($offset != 0) $currentPage = $offset / $this->countPerPage;
		$articles = $this->model->getPublishedArticlesCount($offset, $this->countPerPage);
		
		$categories = $this->model->getAllCategories();
		$pageCount = $this->model->getPagination($this->countPerPage);
		if($pageCount == 1) $show_pagination = false;
		if ($currentPage >= $pageCount) $show_more = false;

		$args['articles'] = $articles;
		$args['categories'] = $categories;
		$args['pageCount'] = $pageCount;
		$args['currentPage'] = $currentPage;
		$args['countPerPage'] = $this->countPerPage;
		$args['show_pagination'] = $show_pagination;
		$args['show_more'] = $show_more;

		$this->model->render('index.php', $args);
	}

	public function showCertainPage($currentPage) {
		$args = ['title' => 'Главная'];
		$show_more = true;
		$show_pagination = true;
		
		$offset = !empty($_POST['offset']) ? $_POST['offset'] : 0;
		$offset = ($currentPage - 1)  * $this->countPerPage + $offset;

		$articles = $this->model->getPublishedArticlesCount($offset, $this->countPerPage);
		$categories = $this->model->getAllCategories();
		$pageCount = $this->model->getPagination($this->countPerPage);
		if($currentPage < 0 || $currentPage > $pageCount) $currentPage = 1;
		if ($currentPage >= $pageCount) $show_more = false;
		if($pageCount == 1) $show_pagination = false;

		$args['show_more'] = $show_more;
		$args['articles'] = $articles;
		$args['categories'] = $categories;
		$args['pageCount'] = $pageCount;
		$args['currentPage'] = $currentPage;
		$args['countPerPage'] = $this->countPerPage;
		$args['show_pagination'] = $show_pagination;		

		$this->model->render('index.php', $args);
	}

	public function addArticles(){
		$articleModel = new ArticleModel();
		$offset = intval($_POST['offset']);
		$articles = $this->model->getPublishedArticlesCount($offset, $this->countPerPage);
		$articleModel->getCommentsCount($articles);
		$countArticles = $this->model->getCountPublicArticles();
		$html = '';
		$show_more = true;
		if ($countArticles <= ($offset + $this->countPerPage)) $show_more = false;

		$howManyPagesShowed = ($offset + $this->countPerPage) / $this->countPerPage;
		$current_pages = array();

		$currentPage = 1;
		if(isset(explode('/', trim($_SERVER['REQUEST_URI'], '/'))[1])){
			$currentPage = intval(explode('/', trim($_SERVER['REQUEST_URI'], '/'))[1]);
		}
		for ($i=$currentPage; $i < $howManyPagesShowed; $i++) { 
			$current_pages[$i] = $i;
		}

		$response = [
			'show_more' => $show_more,
			'current_pages' => $current_pages,
		];

		
      
		foreach ($articles as $k => $article) {
			include ('application'.DIRSEP.'views'.DIRSEP.'articleTemplate.php');
			$html .= $template;			
		}

		$response['articles'] = $html;

		echo json_encode($response);
	}

	public function showCertainCategory($categoryId, $currentPage = 1){
		$articleModel = new ArticleModel();
		$offset = $currentPage * $this->countPerPage - $this->countPerPage;
		$articles = $articleModel->getArticlesOfCategoryById($categoryId, $this->countPerPage, $offset);
		$articleModel->getCommentsCount($articles);
		$categoryTitle = $articleModel->getCategoryTitleById($categoryId);
		$title = $categoryTitle ? $categoryTitle : 'Такой категории не существует!';
		$articlesCount = $articleModel->getCountArticlesInCategoryById($categoryId);
		$pageCount = ceil($articlesCount / $this->countPerPage);
		$categories = $this->model->getAllCategories();
		$show_more = true;
		if ($currentPage >= $pageCount) $show_more = false;
		if ($articlesCount <= ($offset + $this->countPerPage)) $show_more = false;
		$show_pagination = true;
		if($pageCount == 1) $show_pagination = false;
		

		$args = [
			'articles' => $articles,
			'categories' => $categories,
			'show_more' => $show_more,
			'show_pagination' => $show_pagination,
			'title' => $title,
			'pageCount' => $pageCount,
			'currentPage' => $currentPage,
			'countPerPage' => $this->countPerPage
		];
		

		$this->model->render('category.php', $args);
	}

	public function showCertainCategoryAJAX() {
		$args = [];
		$articleModel = new ArticleModel();
		$offset = intval($_POST['offset']);
		$categoryId = intval($_POST['categoryId']);
		$currentPage = intval($_POST['currentPage']);
		$articles = $articleModel->getArticlesOfCategoryById($categoryId, $this->countPerPage, $offset);
		$articleModel->getCommentsCount($articles);
		$articlesCount = $articleModel->getCountArticlesInCategoryById($categoryId);
		$show_more = true;
		if ($articlesCount <= ($offset + $this->countPerPage)) $show_more = false;
		$howManyPagesShowed = ($offset + $this->countPerPage) / $this->countPerPage;
		$current_pages = array();
		for ($i=$currentPage; $i < $howManyPagesShowed; $i++) { 
			$current_pages[$i] = $i;
		}
		$args['current_pages'] = $current_pages;
		$args['show_more'] = $show_more;
	
		if(!empty($_POST['currentPage'])) {

			$html = '';
			foreach ($articles as $k => $article) {
				include ('application'.DIRSEP.'views'.DIRSEP.'articleTemplate.php');
				$html .= $template;
			}

			$args['articles'] = $html;

			echo json_encode($args);
			exit;
		}
	}

	public function search($searchQuery, $currentPage = 1) {
		$args = [];
		$html = '';
		$offset = $currentPage * $this->countPerPage - $this->countPerPage;
		if(!empty($_POST['offset'])) $offset = intval($_POST['offset']);
		$searchQuery = htmlspecialchars($searchQuery);
		$searchResult = $this->model->search($searchQuery, $this->countPerPage, $offset);
		$articleModel = new ArticleModel();
		$articleModel->getCommentsCount($searchResult);
		$args['searchQuery'] = $searchQuery;
		$args['title'] = 'Поиск | ' . $searchQuery;
		$categories = $this->model->getAllCategories();
		$args['categories'] = $categories;
		$args['articles'] = $searchResult;
		$searchCount = $this->model->searchCountResult($searchQuery, $this->countPerPage);
		$args['pageCount'] = ceil($searchCount / $this->countPerPage);
		$args['countPerPage'] = $this->countPerPage;
		$args['currentPage'] = intval($currentPage);
		$howManyPagesShowed = ($offset + $this->countPerPage) / $this->countPerPage;
		$current_pages = array();
		for ($i=$currentPage; $i < $howManyPagesShowed; $i++) { 
			$current_pages[$i] = $i;
		}
		$args['current_pages'] = $current_pages;

		$show_more = true;
		if ($currentPage >= $args['pageCount']) $show_more = false;
		if ($searchCount <= ($offset + $this->countPerPage)) $show_more = false;
		$args['show_more'] = $show_more;
		$args['show_pagination'] = true;
		if($args['pageCount'] == 1) $args['show_pagination'] = false;

		if(!empty($_POST['currentPage'])) {

			foreach ($args['articles'] as $k => $article) {
				include ('application'.DIRSEP.'views'.DIRSEP.'articleTemplate.php');
				$html .= $template;
			}

			$args['articles'] = $html;

			echo json_encode($args);
			exit;
		}
		$this->model->render('search.php', $args);
	}

	public function about(){
		$args = [
			'title' => 'О нас',
			'categories' => $this->model->getAllCategories()
		];

		$this->model->render('about.php', $args);
	}

	public function _404(){
		$args = [
			'title' => 'Такой страницы не существует',
			'categories' => $this->model->getAllCategories()
		];

		$this->model->render('404.php', $args);
	}
}