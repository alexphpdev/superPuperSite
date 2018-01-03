<?php $template = "<div class='article'>
  <h2><a href='/article/". $article->id ."'>". $article->header ."</a></h2>
  <div class='article_top'>
    <div class='article_top_inner'>
      <span class='published'>". date("d.m.y", $article->date_create) ."</span>,&nbsp;&nbsp;<a href='". HOST . 'userPosts/' .$article->user_login ."' class='author'>". $article->user_login ."</a>
    </div>
  </div>
  <div class='articleShortText'>
    ".html_entity_decode($article->short_description)."
  </div>
  <div class='article_bottom'>
    <div class='article_bottom_inner'>
      <a href='/article/".$article->id."#comments' class='comments_count'>Комментариев: ".$article->commentsCount."</a>
      &nbsp;&nbsp;
      <span>".$article->rating."/5 (Проголосовало: ".$article->voicesCount.")</span>
    </div>
  </div>
</div>";