<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


header('Content-Type: text/html; charset=utf-8');
session_start();


use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;

define('DIRSEP', DIRECTORY_SEPARATOR);
define('HOST', 'http://'.$_SERVER['HTTP_HOST'] . '/');
define('CONTROLLERS' , 'application\\controllers\\');
define('MODELS' , 'application\\models\\');
define('VIEWS' , 'application\\views\\');
define('SITENAME' , 'SuperPuperSite');

$loader = require 'vendor/autoload.php';
$loader->add('application', __DIR__);

$router = new RouteCollector();

$router->get('/registration', [CONTROLLERS.'AuthController','showRegistrationForm']);
$router->post('/registration', [CONTROLLERS.'AuthController','checkNewUser']);
$router->get('/registration/token/{token:a}', [CONTROLLERS.'AuthController','checkToken']);

$router->get('/enter', [CONTROLLERS.'AuthController','showEnterForm']);
$router->post('/enter', [CONTROLLERS.'AuthController','checkEnterForm']);

$router->get('/', [CONTROLLERS.'IndexController','index']);
$router->post('/', [CONTROLLERS.'IndexController','addArticles']);

$router->get('/page/{page:i}', [CONTROLLERS.'IndexController','showCertainPage']);
$router->post('/page/{page:i}', [CONTROLLERS.'IndexController','addArticles']);

$router->get('/category/{categoryId:i}/page/{page:i}', [CONTROLLERS.'IndexController','showCertainCategory']);
$router->post('/category/{categoryId:i}/page/{page:i}', [CONTROLLERS.'IndexController','showCertainCategoryAJAX']);

$router->get('/category/{categoryId:i}', [CONTROLLERS.'IndexController','showCertainCategory']);
$router->post('/category/{categoryId:i}', [CONTROLLERS.'IndexController','showCertainCategoryAJAX']);

$router->get('/article/{articleId:i}', [CONTROLLERS.'ArticleController','showCertainArticle']);

$router->get('/search/{searchQuery}/', [CONTROLLERS.'IndexController','search']);
$router->post('/search/{searchQuery}/', [CONTROLLERS.'IndexController','search']);
$router->get('/search/{searchQuery}/page/{page:i}', [CONTROLLERS.'IndexController','search']);
$router->post('/search/{searchQuery}/page/{page:i}', [CONTROLLERS.'IndexController','search']);

$router->get('/logout', [CONTROLLERS.'UserController','logout']);

$router->get('/userPosts/{userName}', [CONTROLLERS.'UserController','showPostsOfUser']);
$router->get('/about', [CONTROLLERS.'IndexController','about']);
$router->get('/404', [CONTROLLERS.'IndexController','_404']);

$router->filter('user', function(){    
    if(!isset($_SESSION['user_login'])) 
    {
        header('Location: ' . HOST . 'enter/');

        return false;
    }

    if(!empty($_SESSION['admin']) && $_SESSION['admin']) {
        header('Location: ' . HOST . 'admin/');

        return false;
    }
});

$router->filter('admin', function(){    
    if(!isset($_SESSION['admin']) && !$_SESSION['admin']) 
    {
        header('Location: ' . HOST . 'user/');

        return false;
    }
});


$router->group(['before' => 'user'], function($router){
    $router->get('/user', [CONTROLLERS.'UserController','index']);

    $router->get('/user/published', [CONTROLLERS.'UserController','showPublishedPosts']);
    $router->get('/user/rejected', [CONTROLLERS.'UserController','showRejectedPosts']);
    $router->get('/user/saved', [CONTROLLERS.'UserController','showSavedPosts']);
    $router->get('/user/reconciliation', [CONTROLLERS.'UserController','showReconciliationPosts']);

    $router->get('/user/addPost', [CONTROLLERS.'ArticleController','addPostForm']);
    $router->post('/user/savePost', [CONTROLLERS.'ArticleController','savePost']);
    $router->post('/user/savePost/{id}', [CONTROLLERS.'ArticleController','savePost']);

    $router->get('/user/editPost/{id}', [CONTROLLERS.'ArticleController','editPostForm']);

    $router->post('/user/sendToReconciliation/{id}', [CONTROLLERS.'ArticleController','sendToReconciliation']);
    $router->get('/user/sendToReconciliationFromPreview/{id}', [CONTROLLERS.'ArticleController','sendToReconciliationFromPreview']);
    $router->post('/user/sendToReconciliation/', [CONTROLLERS.'ArticleController','sendToReconciliation']);

    $router->post('/user/updatePost/{id}', [CONTROLLERS.'ArticleController','updatePost']);

    $router->get('/user/removePost/{id}', [CONTROLLERS.'ArticleController','removePost']);


});

$router->group(['before' => 'admin'], function($router){
    $router->get('/admin', [CONTROLLERS.'UserController','adminIndex']);
    $router->get('/admin/verify/{id}', [CONTROLLERS.'ArticleController','verifyPost']);
    $router->get('/admin/reject/{id}', [CONTROLLERS.'ArticleController','rejectPost']);
    $router->get('/admin/removePost/{id}', [CONTROLLERS.'ArticleController','removePost']);
    $router->get('/admin/editPost/{id}', [CONTROLLERS.'ArticleController','editPostFormAdmin']);
    $router->post('/admin/updatePost/{id}', [CONTROLLERS.'ArticleController','updatePostAdmin']);
    $router->post('/admin/publicPost/{id}', [CONTROLLERS.'ArticleController','publicPostAdmin']);

    $router->get('/admin/bids', [CONTROLLERS.'UserController','showBids']);
    $router->get('/admin/categories', [CONTROLLERS.'ArticleController','showCategories']);
    $router->post('/removeComment', [CONTROLLERS.'ArticleController','removeComment']);
    $router->get('/admin/removeCategory/{categoryId}', [CONTROLLERS.'ArticleController','removeCategory']);
    $router->post('/admin/createCategory', [CONTROLLERS.'ArticleController','createCategory']);
    $router->post('/admin/getCategoryName', [CONTROLLERS.'ArticleController','getCategoryName']);
    $router->post('/admin/updateCategory', [CONTROLLERS.'ArticleController','updateCategory']);
    

});

$router->post('/prevPost/', [CONTROLLERS.'ArticleController','prevPost']);
$router->post('/prevPost/{id}', [CONTROLLERS.'ArticleController','prevPost']);
$router->get('/preview/{id}', [CONTROLLERS.'ArticleController','showPreview']);

$router->post('/changePassword', [CONTROLLERS.'UserController','changePassword']);
$router->post('/remindPass', [CONTROLLERS.'UserController','remindPass']);
// $router->get('/remindPass', [CONTROLLERS.'UserController','remindPassForm']);


$router->post('/setArticleRating', [CONTROLLERS.'ArticleController','setArticleRating']);
$router->post('/addComment', [CONTROLLERS.'ArticleController','addComment']);
    


$dispatcher = new Dispatcher($router->getData());
try {
    $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
} catch (Exception $e) {
    header("Location: " . HOST . '404');
}