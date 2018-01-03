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
					echo "<div class='col-md-3'>Опубликована</div>";
					echo "<div class='col-md-1 removePost'></div>";
					echo "<div style='clear:both'></div>";
				echo "</div>";
			}
		} else echo 'У вас, пока что, нет опубликованных статей';
	?>
</div>
<? include('footer.php'); ?>