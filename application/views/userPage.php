<? include 'application'.DIRSEP.'views'.DIRSEP.'header.php' ?>

<div class="wrapper row2">
  <div id="container" class="clear">
    <div class='articlesWrapper'>
    <?php
    if(!empty($args['emptyArticles'])) {
    	echo 'К сожалению, данный пользователь не опубликовал ни одного поста... будем ждать.';
    } elseif(!empty($args['emptyUser'])) {
    	echo 'Такого пользователя не существует';
    } elseif(!empty($args['articles'])) {
    
	    foreach ($args['articles'] as $k => $article) {
        include ('application'.DIRSEP.'views'.DIRSEP.'articleTemplate.php');
        echo $template;
	    }
	}
    ?>
    </div>
<? include 'application/views/footer.php' ?>
