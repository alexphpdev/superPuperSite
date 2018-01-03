<!DOCTYPE html>
<html>
<head>
	<title><?= $args['title'] ?></title>
	<link rel="stylesheet" type="text/css" href="/css/auth.css">
	<link rel="icon" href="/favicon.png">
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<style type="text/css">
		.main{
		    margin-top: -40px;
		    margin-left: -200px;
		}
		.inner { 
			width: 400px;
			text-align: center;
		}
		.mainWrapper {
			height: 100%;
			background-color: rgba(32,32,32,0.7);
		    opacity: 0.9;
		}
	</style>
</head>
<body>
	<div class="mainWrapper">
		<div class="main">
			<div class="inner">
				Регистрация успешно завершена!<br>
				Теперь, Вы можете <a href="/enter">авторизироваться</a> на сайте.
			</div>		
		</div>
	</div>
</body>
</html>