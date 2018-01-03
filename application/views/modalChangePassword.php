<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Изменение пароля</h4>
      </div>
      <div class="modal-body">
        <form id="changePasswordForm">
          <div class="form-group">
            <label for="oldPwd" class="control-label">Старый пароль:</label>
            <input type="password" class="form-control OldPassword" id="oldPwd" name="oldPwd">
          </div>
          <div class="form-group">
            <label for="newPwd1" class="control-label">Новый пароль:</label>
            <input type="password" class="form-control password" id="newPwd1" name="newPwd1">
          </div>
          <div class="form-group">
            <label for="newPwd2" class="control-label">Новый пароль еще раз:</label>
            <input type="password" class="form-control password" id="newPwd2" name="newPwd2">
          </div>
      	</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
        <button type="button" class="btn btn-primary changePassword" id="submitRegistrationForm">Сохранить</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function(){

	$("#submitRegistrationForm").on('click', function(){
		$('#changePasswordForm').submit();
	})

	var data = $('#changePasswordForm').serialize();

	$('#changePasswordForm').ajaxForm({
		type: 'post',
		url:'/changePassword',
		data: data,
		beforeSend: function(xhr) {
			if(!formValid()) xhr.abort();
		},
		success: function(res){
			$("input.form-control").css('border-color', '#ccc');
			$("span.err").remove();
			
			res = JSON.parse(res);
			for(var el in res){
				if(res[el]) {
					$("#changePasswordForm ." + el)
						.css('border-color', 'red')
						.after("<span class='"+el+" err' style='color:red'>"+res[el]+"</span>");
					document.getElementById("changePasswordForm").reset();
				}
			}
			if(res == 0){
				var email = $("#email").val();
				$('.modal-body')
					.empty()
					.append("Пароль успешно изменён!")
					.css({
						'text-align': 'center',
						'font-size': '20px',
						'color': 'green'
					});
				$(".modal-footer, .modal-header").remove();
			}
		}
	});

	function formValid(){
		$(".password, #oldPwd").trigger('input');
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

	$(".password").on('input', function(){
		var $this = $(this),
			pass1 = $("#newPwd1").val(),
			pass2 = $("#newPwd2").val(),
			mess = "";

		if(pass1 !== pass2) mess = "Пароли не совпадают";
		if(pass1.length < 4 && pass2.length < 4) mess = "Минимальная длина пароля 4 символа";


		errorInput($(".password"), 'password', mess);
	})

	$("#oldPwd").on('input', function(){
		var $this = $(this);
		var pass = $this.val();
		var mess = '';

		if(!pass) mess = "Введите новый пароль";
		if(pass.length < 4) mess = "Минимальная длина пароля 4 символа";

		errorInput($this, 'OldPassword', mess);
	})
});
</script>