<? include('header.php'); ?>
<div id="posts">
	<?php
		if(!empty($args['articles'])) {
			foreach($args['articles'] as $article){
				
				echo "<div class='post'>";
					echo "<div class='col-md-1'><span>#$article->id</span></div>";
					if($article->status == 1)
						echo "<div class='col-md-6'><a href='/article/".$article->id."'>".$article->header."</a></div>";
					else
						echo "<div class='col-md-6'>$article->header</div>";
					switch ($article->status) {
						case 1: echo "<div class='col-md-3'>Опубликована</div>"; break;
						case 2: echo "<div class='col-md-3'>Отклонена</div>"; break;
						case 3: echo "<div class='col-md-3'>Сохранена</div>"; break;
						case 4: echo "<div class='col-md-3'>На согласовании</div>"; break;
					}

					if($article->status != 1) {
						echo "<div class='col-md-1 editPost'><a class='' href='/user/editPost/$article->id'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></div>";
						echo "<div class='col-md-1 removePost'><a class='' href='/user/removePost/$article->id' data-posttitle='$article->header'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></div>";
					}
					echo "<div style='clear:both'></div>";
				echo "</div>";
			}
		} else echo 'У вас, пока что, нет статей';
	?>
</div>
<? include('footer.php'); ?>