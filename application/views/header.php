<!DOCTYPE html>
<html>
<head>
<title><?= $args['title'] ?></title>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link href="/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/css/layout.css" type="text/css">
<link rel="stylesheet" href="/css/font-awesome.min.css" type="text/css">
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script src="/js/jquery.form.min.js"></script>
</head>
<body>

<? if(!empty($args['show_previewPanel']) && $args['show_previewPanel']) :?>
<div class="previewPanel">
  <div class="previewPanelInner">
    <button class="btn btn-success toReconciliation">Опубликовать пост</button>&nbsp;&nbsp;&nbsp;
    <button class="btn btn-default toEditor">Вернуться к редактированию</button>
  </div>
</div>
<script type="text/javascript">
  $(function(){

    var articleId = window.location.href.split('/')[4];
    var url = '/user/sendToReconciliationFromPreview/' + articleId;
    
    $(".previewPanel .toReconciliation").on("click", function(){
      <? if( !empty($_SESSION['admin'])): ?>
        window.location = "/admin/verify/" + articleId;
      <? else: ?>
        window.location = url;
      <? endif; ?>
    });


    

    $(".previewPanel .toEditor").on("click", function(){
      <? if( !empty($_SESSION['admin'])): ?>
        window.location = "/admin/editPost/" + articleId;
      <? else: ?>
        window.location = "/user/editPost/" + articleId;
      <? endif; ?>
    })
  })
</script>
<? endif ?>

<div class="wrapper row1">
  <header id="header" class="clear">
    <div id="hgroup">
      <h1><a href="/"><?= SITENAME ?></a></h1>
      <h2>the best site of WEB</h2>
    </div>
    <form action="/search/" id="searchForm">
      <fieldset>
        <legend>Search:</legend>
        <input type="text" value="" name="q" id="searchField" placeholder="Search Our Website&hellip;">
        <input type="submit" id="sf_submit" value="submit">
      </fieldset>
    </form>
    <script type="text/javascript">
      $(function(){
        $('#sf_submit').on('click', function(e){
          e.preventDefault();
          var $form = $('#searchForm');
          var q = $form.find("#searchField").val();
          var action = $form.attr('action');
          window.location = action + q;
          return;
        })
      })
    </script>
    <nav>
      <ul>
        <?php

        foreach ($args['categories'] as $k => $v) {
          echo "<li><a href='/category/".$v->id."'>".$v->category."</a></li>";
        }

        ?>
      </ul>
    </nav>
    <? if(!empty($_SESSION['user_login'])): ?>
      <div class="userPanel" style="margin-top: 15px;">
        Доброго времени суток, <a href="/user"><?= $_SESSION['user_login']; ?></a>
        <a class="pull-right" href="/logout">Выйти</a>
      </div>
    <? endif; ?>
  </header>
</div>