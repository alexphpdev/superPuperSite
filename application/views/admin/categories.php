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
			height: 50px;
			line-height: 50px;
		}
			.post:hover {
			    background-color: #5CB85C;
			}

			.editCategory, .removeCategory{
				font-size: 22px;
			}
			.glyphicon-remove:before{
				color: #C12020;
			}

	.addCategoryWrapper {
		margin: auto;
    	width: 180px;
	}

	
	.addCategoryWrapper span{
		color: green;
		font-size: 21px;
	}
	</style>
	<script type="text/javascript">
		$(function(){
			$('.removeCategory').on('click', function(e){
				e.preventDefault();
				var $this = $(this).find('a').eq(0);
				var categoryTitle = $this.data('categorytitle');
				if(window.confirm('Вы уверены что хотите удалить "' + categoryTitle + '" ?')){
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
				<a href="/admin/">Статьи</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#" data-toggle="modal" data-target="#myModal">Изменить пароль</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="/">Перейти на сайт</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="/logout">Выйти из профиля</a>
			</div>
			<div id='filter'>
				<div class="addCategoryWrapper">
					<a href="#" data-toggle="modal" data-target="#newCategoryModal"><span class='glyphicon glyphicon-plus' aria-hidden='true'></span> Добавить категорию</a>
				</div>
			</div>
		</div>
		<div class="row">	
			<div id="posts">
				<?php
					foreach($args['categories'] as $category){
						
						echo "<div class='post'>";
							echo "<div class='col-md-1'><span>#$category->id</span></div>";
							echo "<div class='col-md-9'>$category->category</div>";
							echo "<div class='col-md-1 editCategory' data-category-id='$category->id' href='#' data-toggle='modal' data-target='#updateCategoryModal'><a class='' href='#'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></div>";
							echo "<div class='col-md-1 removeCategory'><a class='' href='/admin/removeCategory/$category->id' data-categorytitle='$category->category'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></div>";
							echo "<div style='clear:both'></div>";
						echo "</div>";
					}
				?>
			</div>

			<? include('modalCategory.php'); ?>
			<? include('modalCategoryUpdate.php'); ?>
			<? include('application/views/modalChangePassword.php'); ?>
			<? include 'application/views/user/footer.php'; ?>