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
			<div class="pull-right" style="height: 65px">
				<a href="/admin/categories/">Категории</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
						['url' => '/admin/', 'txt' => 'Опубликованные', 'count' => $args['countArticles']['publishedCount']],
						['url' => '/admin/bids/', 'txt' => 'Заявки', 'count' => $args['countArticles']['bidsCount']],
					];

					foreach($menu as $info) {
						$class = $_SERVER["REQUEST_URI"] == $info['url'] ? "class='active'" : "";
						echo "<a {$class} href='{$info['url']}'>{$info['txt']} <span class='count'>({$info['count']})</span></a>";
					}
				?>
				
			</div>
		</div>
		<div class="row">	
			<div id="posts">
				<?php
					foreach($args['articles'] as $article){
						
						echo "<div class='post'>";
							echo "<div class='col-md-1'><span>#$article->id</span></div>";
							if($article->status == 1)
								echo "<div class='col-md-7'><a href='/article/".$article->id."'>".$article->header."</a></div>";
							else
								echo "<div class='col-md-7'>$article->header</div>";
							if(!empty($args['bids'])) {
								echo "<div class='col-md-1 verifyPost'><a class='' href='/admin/verify/$article->id'><span class='glyphicon glyphicon-plus' aria-hidden='true'></span></a></div>";
								echo "<div class='col-md-1 rejectPost'><a class='' href='/admin/reject/$article->id'><span class='glyphicon glyphicon-minus' aria-hidden='true'></span></a></div>";
							} else echo "<div class='col-md-1'></div>";
							echo "<div class='col-md-1 editPost'><a class='' href='/admin/editPost/$article->id'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></div>";
							if(empty($args['bids'])) {
								echo "<div class='col-md-1 removePost'><a class='' href='/admin/removePost/$article->id' data-posttitle='$article->header'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></div>";
							}
							echo "<div style='clear:both'></div>";
						echo "</div>";
					}
				?>
			</div>
			<? include('application/views/modalChangePassword.php'); ?>
			<? include 'application/views/user/footer.php'; ?>