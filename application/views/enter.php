<!DOCTYPE html>
<html>
<head>
	<title><?= $args['title'] ?></title>
	<link rel="stylesheet" type="text/css" href="/css/auth.css">
	<link rel="icon" href="/favicon.png">
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script src="/js/jquery.form.min.js"></script>
</head>
<body>
	<form action="" method="post" id="enterForm">
		<div class="main">
			<div class="inner">
				<div class="form-group">
					<input type="text" class="form-control login" id="login" name="login" placeholder="Логин" autofocus 
					value="<? if(!empty($_POST)) echo $_POST['login'] ?>">
				</div>
				<div class="form-group">
					<input type="password" class="form-control password" id="pass1" name="password" placeholder="Пароль">
				</div>
				<? if(!empty($args['error'])): ?>
					<div class="form-group" style="color: red;">
						Неверно указан логин или пароль
					</div>
				<? endif; ?>
				<label class="form-check-label">
				    <input class="form-check-input" type="checkbox" value="1" name="rememberMe" <? if(!empty($_POST['rememberMe']) && $_POST['rememberMe']) echo 'checked' ?>>
				    Запомнить меня
				</label><br><br>
				<input type="submit" name="enter" value="Вход" class="btn btn-primary" />
				<button name="forgetPass" value="" class="btn forgetPass">Я забыл пароль</button>
				<button name="registration" class="btn btn-danger pull-right">Регистрация</button>
			</div>		
		</div>
	</form>
<script type="text/javascript">
	$(function(){
		$('button[name="registration"').on('click', function(e){
			e.preventDefault();
			window.location.href = 'registration';
		})

		function forgetPassForm(){

			$('.main').css({
				'margin-top': '-82.5px',
    			'margin-left': '-190px'
			});

			var html = '<div class="form-group">\
					<input type="text" class="form-control emailForRemind" id="emailForRemind" name="emailForRemind" placeholder="E-mail" autofocus="" value="">\
				</div>\
				<button name="remind" class="btn btn-success remind">Отправить</button>';

			$(".inner")
				.css('overflow', 'hidden')
				.css('width', '380px')
				.empty()
				.append("<span>Введи почту, на неё будет отправлен новый пароль:</span><br><br>" + html);
		}

		$('.forgetPass').on('click', function(e){
			e.preventDefault();

			forgetPassForm();


			$('html').on('click', '.remind', function(e){

				var data = {'email' : $('#emailForRemind').val()};

				$('#enterForm').ajaxForm({
					type: 'post',
					data: data,
					url:'/remindPass',
					success: function(res){
						var html = "";
						if(res) {
							var email = $("#emailForRemind").val();
							html = "<span>Новый пароль отправлен на указанный адрес: "+ email +"</span><br><br><a href='/enter'>Используй его</a>";
						} else {
							html = "<span>К превеликому сожалению, пользователь с такой почтой не зарегистрирован...<br>\
								<a href='#' class='another_attempt'>попробовать снова</a> или <a href='/'>перейти на сайт?</a>\
							</span>";
							$(".inner").css('width', '555px');
							$('.main').css({
				    			'margin-left': '-277.5px'
							});
						}

						$(".inner").empty().append(html);
						
					}
				});

				$('html').on('click', '.another_attempt', forgetPassForm);
			})
		})
	})
</script>
</body>
</html>