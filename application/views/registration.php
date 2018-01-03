<!DOCTYPE html>
<html>
<head>
	<title><?= $args['title'] ?></title>
	<link rel="stylesheet" type="text/css" href="/css/auth.css">
	<link rel="icon" href="/favicon.png">
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js"></script>
	<style type="text/css">
		.main{
		    margin-top: -86px;
		}
	</style>
</head>
<body>
	<form action="/registration" method="post" id="registrationForm">
		<div class="main">
			<div class="inner">
				<div class="form-group">
					<input type="text" class="form-control login" id="login" name="login" placeholder="Логин" autofocus>
				</div>
				<div class="form-group">
					<input type="email" class="form-control email" id="email" name="email" placeholder="Почта">
				</div>
				<div class="form-group">
					<input type="password" class="form-control password" id="pass1" name="pass1" placeholder="Пароль">
				</div>
				<div class="form-group">
					<input type="password" class="form-control password" id="pass2" name="pass2" placeholder="Подтверждение пароля">
				</div>
				<input type="submit" id="submitRegistrationForm" name="registration" value="Зарегистрироваться" class="btn btn-primary" />
			</div>		
		</div>
	</form>

	<script type="text/javascript">
		$(function(){

			var data = $('#registrationForm').serialize();

			$('#registrationForm').ajaxForm({
				type: 'post',
				data: data,
				beforeSend: function(xhr) {
					if(!formValid()) xhr.abort();
				},
				success: function(res){
					$("input.form-control").css('border-color', '#ccc');
					$("span.err").remove();
					
					res = JSON.parse(res);
					for(var el in res){
						if(!res[el]) {
							$("#registrationForm ." + el)
								.css('border-color', 'green');
						} else {
							$("#registrationForm ." + el)
								.css('border-color', 'red')
								.after("<span class='"+el+" err' style='color:red'>"+res[el]+"</span>");
							$("#submitRegistrationForm").attr("disabled", "disabled");
						}
					}
					if(res == 0){
						var email = $("#email").val();
						$('.inner')
							.empty()
							.append("Письмо с подтверждением регистрации отправленно по адресу " + email);
					}
				}
			});

			function formValid(){
				$("#login, #email, .password").trigger('input');
				if($("#submitRegistrationForm").attr("disabled") == 'disabled') return false;
				return true;
			}

			function errorInput($field, fieldClass, mess){
				
				$("."+fieldClass+".err").remove();

				if(mess) {
					$("#submitRegistrationForm").attr("disabled", "disabled");

					$field
						.css('border-color', 'red')
						.after("<span class='"+fieldClass+" err' style='color:red'>"+mess+"</span>");
				} else {
					$field.css('border-color', '#ccc');

					if($('span.err').size() == 0) $("#submitRegistrationForm").removeAttr("disabled");
				}
			}

			$("#login").on('input', function(){
				var $this = $(this),
					login = $this.val(),
					mess = "";

				if(login.length < 3 ) mess = "Длина логина должна быть более 2ух символов";
				else if(login.length > 50 ) mess = "Длина логина должна быть не более 50 символов";
				
				if(!login.match( /^[a-zA-Z0-9_.-]+$/ )) mess = 'Логин может содержать только буквы латинского алфавита, _, ., -';
				
				errorInput($this, 'login', mess);
			});

			$("#email").on('input', function(){
				var $this = $(this),
					email = $this.val(),
					mess = "";
				
				if(email.length < 5) mess = "Email слишком короткий";
				else if(email.length > 254 ) mess = "Email слишком длинный";

				if(!email.match( /^.+@.+\..+$/ )) mess = 'Некорректный email';

				errorInput($this, 'email', mess);
			});

			$(".password").on('input', function(){
				var $this = $(this),
					pass1 = $("#pass1").val(),
					pass2 = $("#pass2").val(),
					mess = "";

				if(pass1 !== pass2) mess = "Пароли не совпадают";
				if(pass1.length < 4 && pass2.length < 4) mess = "Минимальная длина пароля 4 символа";


				errorInput($(".password"), 'password', mess);
			})
		});
	</script>
</body>
</html>