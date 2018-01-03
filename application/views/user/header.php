<!DOCTYPE html>
<html>
<head>
	<title><?= $args['title'] ?></title>
	<meta charset="utf-8">
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap/bootstrap.min.js"></script>
	<script src="/js/jquery.form.min.js"></script>

	<link href="/css/bootstrap.min.css" rel="stylesheet">

	<style type="text/css">
	#workflow{
		width: 80%;
		margin: 0 auto;
	}
		#header{
			margin: 10px 0 10px 10px;
		}
			#filter{
				clear:both;
				margin: 20px 0;
				text-align: right;
			}
			#filter a{
				margin-right: 20px;
			}

	.glyphicon{
		top: 0;
		vertical-align: middle;
	}
	.row{
		margin: 0;
	}
	#posts{ margin-bottom: 100px; }
		.post{
			background-color: bisque;
			min-height: 50px;
			line-height: 50px;
		}
			.post:hover {
			    background-color: #5CB85C;
			}

			.editPost, .removePost{
				font-size: 22px;
			}
			.glyphicon-remove:before{
				color: #C12020;
			}
	</style>
	<script type="text/javascript">
		$(function(){
			$('.removePost').on('click', function(e){
				e.preventDefault();
				var $this = $(this).find('a').eq(0);
				var postTitle = $this.data('posttitle');
				if(window.confirm('Вы уверены что хотите удалить "' + postTitle + '" ?')){
					window.location.href = $this.attr('href');
				}
				return false;
			});
		});
	</script>
</head>
<body>
	<div id='workflow'>
		<div id='header'>
			<a class="btn btn-success btn-lg" href="/user/addPost/"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>&nbsp;&nbsp;Добавить пост</a>
			<div class="pull-right">
				<a href="#" data-toggle="modal" data-target="#myModal">Изменить пароль</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="/">Перейти на сайт</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="/logout">Выйти из профиля</a>
			</div>
			<div id='filter'>
				<style type="text/css">
					.active{
						background: green;
					    color: white;
					    border-radius: 5px;
					    padding: 5px 15px;
					}
				</style>
				<? 

					$menu = [
						['url' => '/user/', 'txt' => 'Все посты', 'count' => $args['countArticles']['all']],
						['url' => '/user/published/', 'txt' => 'Опубликованные', 'count' => $args['countArticles']['publishedCount']],
						['url' => '/user/rejected/', 'txt' => 'Отклоненные', 'count' => $args['countArticles']['rejectedCount']],
						['url' => '/user/saved/', 'txt' => 'Сохраненные', 'count' => $args['countArticles']['savedCount']],
						['url' => '/user/reconciliation/','txt' =>'На согласовании', 'count' => $args['countArticles']['reconciliationCount']],
					];

					foreach($menu as $info) {
						$class = $_SERVER["REQUEST_URI"] == $info['url'] ? "class='active'" : "";
						echo "<a {$class} href='{$info['url']}'>{$info['txt']} <span class='count'>({$info['count']})</span></a>";
					}  
				?>
			</div>
		</div>
		<div class="row">	



<? include('application/views/modalChangePassword.php'); ?>