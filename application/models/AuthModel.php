<?php

namespace application\models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthModel extends MainModel{ 

	public function checkLogin($login){

		if(strlen($login) < 3) return 2;

		if(strlen($login) > 50) return 3;

		if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $login)) return 4; // Логин может содержать только буквы латинского алфавита, _, ., -

		$db = Db::getInstance();
		$login = $db->quote($login);

		$q = "
		SELECT login FROM users
		WHERE users.login = $login
		UNION
		SELECT login FROM pending_users
		WHERE pending_users.login = $login
		";

		$res = $db->query($q)->fetch();
		
		if (!empty($res)) return 1; // Пользователь с таким логином существует
		
		return 0;
	}

	public function checkEmail($email) {
		if(strlen($email) < 5) return 1;
		if(strlen($email) > 254) return 2;
		if (!preg_match('/^.+@.+\..+$/', $email)) return 3;

		
		$db = Db::getInstance();
		$email = $db->quote($email);

		$q = "
		SELECT email FROM users
		WHERE users.email = $email
		UNION
		SELECT email FROM pending_users
		WHERE pending_users.email = $email
		";

		$res = $db->query($q)->fetch();
		
		if (!empty($res)) return 4; // такой email уже используется
		return 0;
	}

	public function checkPass($pass1, $pass2) {
		if (empty($pass1)) return 1;
		if (empty($pass2)) return 1;
		if ($pass1 !== $pass2) return 1;
		if(strlen($pass1) < 4) return 2;
		return 0;
	}

	public function removeOverdueProfiles(){
		$db = Db::getInstance();

		$delta = 86400; //60 * 60 * 24;
		$curTime = time();
		$q = "
		DELETE FROM pending_users
		WHERE $curTime - date_registration > $delta
		";

		$db->exec($q);
	}

	public function createPendingUser($login, $email, $pass){
		$db = Db::getInstance();
		$login = $db->quote($login);
		$email = $db->quote($email);
		$pass_hash = $db->quote(password_hash($pass, PASSWORD_BCRYPT, $this->passwordHashCost));
		$date_registration = time();
		$token = md5(uniqid($login, true));
		$tokenQuoted = $db->quote($token);

		$q = "INSERT INTO pending_users 
            (login, email, password, date_registration, token) 
            VALUES 
            ($login, $email, $pass_hash, $date_registration, $tokenQuoted)
        ";
        
        $db->exec($q);

        return $token;
	}

	public function sendConfirmMail($email, $token) {

		$mailer = new PHPMailer(true);
		$email = htmlspecialchars($email);

		$mailer->addAddress($email);
		$mailer->From = "admin@esy24.esy.es";
		$mailer->FromName = "esy24.esy.es";
		$mailer->Subject = 'Завершение регистрации на сайте esy24.esy.es';
		$mailer->Body = "<a href='".HOST."registration/token/". $token. "'>Заверши регистрацию перейдя по этой ссылке</a>";
		$mailer->isHTML(true);
		$mailer->CharSet = 'UTF-8';
		$mailer->send();

	}

	public function confirmRegistration($token) {
		$db = Db::getInstance();
		$tokenQuoted = $db->quote($token);

		$q = "INSERT INTO users (login, email, password, date_registration)
			SELECT login, email, password, date_registration FROM pending_users
			WHERE token = $tokenQuoted
        ";
        
        if($db->exec($q) != false) {
        	// если есть пользователь, который подтвердил регистрацию, то удаляем его из ожидающих
        	$q = "DELETE FROM pending_users WHERE token = $tokenQuoted LIMIT 1";
        	return $db->exec($q);
        }

        return false;
	}

	public function checkEnterForm($login, $pass) {
		$db = Db::getInstance();
		$login = $db->quote($login);
		$q = "SELECT * FROM users WHERE login = $login";
		$user = $db->query($q)->fetch();
		if(empty($user)) return false;

		$hash = $user->password;
		if (password_verify($pass, $hash)) {
		    if (password_needs_rehash($hash, PASSWORD_BCRYPT, $this->passwordHashCost)) {
		        $newHash = password_hash($pass, PASSWORD_BCRYPT, $this->passwordHashCost);
		        $newHash = $db->quote($newHash);
		        $q = "UPDATE users SET password = $newHash WHERE login = $login";
	        	$db->exec($q);
		    }
		    $_SESSION['user_login'] = $user->login;
		    $_SESSION['user_id'] = $user->id;
		    $_SESSION['admin'] = $user->status ? true : false;

		    if(!empty($_POST['rememberMe']) && $_POST['rememberMe']) {
		    	$cookieData = [
		    		'user_login' => $user->login,
		    		'user_id' => $user->id,
		    	];

		    	if($_SESSION['admin']) $cookieData['admin'] = true;

		    	setcookie("data", json_encode($cookieData), time()+60*60*24*30, '/');
		    }
		} else return false;

		return true;
	}
}