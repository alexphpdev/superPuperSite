<? include('header.php'); ?>
<div id="posts">
	<?php
		if(!empty($args['articles'])) {
			foreach($args['articles'] as $article){
				
				echo "<div class='post'>";
					echo "<div class='col-md-1'><span>#$article->id</span></div>";
					echo "<div class='col-md-6'>$article->header</div>";
					echo "<div class='col-md-3'>На согласовании</div>";
					echo "<div class='col-md-1 editPost'><a class='' href='/user/editPost/$article->id'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></div>";
					echo "<div class='col-md-1 removePost'><a class='' href='/user/removePost/$article->id' data-posttitle='$article->header'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></div>";
					echo "<div style='clear:both'></div>";
				echo "</div>";
			}
		} else echo 'У вас, пока что, нет статей отправленных на согласование администратору сайта';
	?>
</div>
<? include('footer.php'); ?>