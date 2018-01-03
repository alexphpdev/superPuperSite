<?php

namespace application\controllers;

use application\models\AuthModel;

class AuthController
{
	private $model;

	public function __construct(){
		$this->model = new AuthModel();
	}

	public function showRegistrationForm()
	{
		$args = [
			'title' => 'Регистрация'
		];
		$this->model->render("registration.php", $args);
	}

	public function checkNewUser(){

		$this->model->removeOverdueProfiles();

		$errors = [];
		$errors['login'] = $this->model->checkLogin($_POST['login']);
		switch ($errors['login']) {
			case 1: $errors['login'] = 'Пользователь с таким именем уже существует'; break;
			case 2: $errors['login'] = 'Длина логина должна быть более 2ух символов'; break;
			case 3: $errors['login'] = 'Длина логина должна быть не более 50 символов'; break;
			case 4: $errors['login'] = 'Логин может содержать только буквы латинского алфавита, _, ., -'; break;
			default: unset($errors['login']);
		}
		

		$errors['email'] = $this->model->checkEmail($_POST['email']);
		switch ($errors['email']) {
			case 1: $errors['email'] = 'Email слишком короткий'; break;
			case 2: $errors['email'] = 'Email слишком длинный'; break;
			case 3: $errors['email'] = 'Некорректный email'; break;
			case 4: $errors['email'] = 'Такой email уже используется'; break;
			default: unset($errors['email']);
		}

		$errors['password'] = $this->model->checkPass($_POST['pass1'], $_POST['pass2']);
		switch ($errors['password']) {
			case 1: $errors['password'] = 'Пароли не совпадают'; break;
			case 2: $errors['password'] = 'Минимальная длина пароля 4 символа'; break;
			default: unset($errors['password']);
		}

		if(count($errors) == 0) {
			$token = $this->model->createPendingUser($_POST['login'], $_POST['email'], $_POST['pass1']);
			$this->model->sendConfirmMail($_POST['email'], $token);
		}

		echo json_encode($errors ? $errors : 0);
	}

	public function checkToken($token){
		$args = [
			'title' => 'Регистрация завершена успешно'
		];
		// если всё хорошо , то поздравляем иначе, отправляем на страницу регистрации
		$res = $this->model->confirmRegistration($token);
		if(!$res) {
			header('Location:' . HOST . 'registration/');
			exit;
		}
		$this->model->render("registration_done.php", $args);
	}

	public function showEnterForm(){

		$args = [
			'title' => 'Вход'
		];

		if(!empty($_COOKIE['data'])){
			$cookieData = json_decode($_COOKIE['data']);

			$user_login = $cookieData->user_login ? $cookieData->user_login : false;
			$user_id 	= $cookieData->user_id	? $cookieData->user_id : false;
			$admin 		= $cookieData->admin 	 	? $cookieData->admin : false;

			if($user_login && $user_id) {

				$_SESSION['user_login'] = $user_login;
				$_SESSION['user_id'] 	= $user_id;

				if($admin) {
					$_SESSION['admin'] = $admin;
					header('Location:' . HOST . 'admin/');
					exit;
				}

				header('Location:' . HOST . 'user/');
				exit;
			}
		}

		$this->model->render("enter.php", $args);
	}

	public function checkEnterForm(){
		$res = $this->model->checkEnterForm(htmlspecialchars($_POST['login']), htmlspecialchars($_POST['password']));

		if(!$res) {
			$args = [
				'title' => 'Вход',
				'error' => true,
			];
			$this->model->render("enter.php", $args);
			exit;
		}
		
		if(!empty($_SESSION['admin']) && $_SESSION['admin']) {
			header("Location: " . HOST . 'admin/');
		} else {
			header("Location: " . HOST . 'user/');
		}
		
	}
}