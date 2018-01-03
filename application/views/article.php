<? include 'application'.DIRSEP.'views'.DIRSEP.'header.php' ?>

<!-- content -->
<style type="text/css">
  .article img{
    max-width: 100%;
  }  
</style>

<div class="wrapper row2">
  <div id="container" class="clear">
    <?php
	  $article = $args['article'];
    if(!empty($article)) {
      echo "<div class='article'>
        <h2><a href='/article/". $article->id ."'>". $article->header ."</a></h2>
        <div class='article_top'>
          <div class='article_top_inner'>
            <span class='published'>". date("d.m.y", $article->date_create) ."</span>,&nbsp;&nbsp;<a href='". HOST. 'userPosts/' . $article->user_login ."' class='author'>". $article->user_login ."</a>
          </div>
        </div>
        <div class='articleShortText'>
          ".html_entity_decode($article->description)."
        </div>
        <div class='article_bottom'>
          <div class='article_bottom_inner'>
            <span class='comments_count'>Комментариев: ".$article->commentsCount."</span>&nbsp;&nbsp;<span>".$article->rating."/5 (Проголосовало: " . $article->voicesCount.")</span>
          </div>
        </div>";
if(!empty($_SESSION['user_login'])):
echo "<div class='rating'>
          <span>Твоя оценка статьи:</span><br>";
          for ($i=1; $i <= 5; $i++) { 
            $checked = '';
            if($args['userRating'] == $i) $checked = 'checked';
            echo "<input name='rating' type='radio' ".$checked." value='".$i."'>". $i . "&nbsp&nbsp&nbsp";
          }
echo "</div>";
endif;
        echo "</div>";
    } else {
      echo 'Такой статьи не существует!';
    }
    ?>

    <script type="text/javascript">
      $(function(){
        $("input[name='rating']").on('click', function(){

          var data = {
            rating: $(this).val(),
            articleId: window.location.href.split('/')[4]
          }
          $.ajax({
            type: 'post',
            url: '/setArticleRating',
            data: data,
            success: function(res){
              $('span.thank').remove();
              if(res == 1) {

                $(".rating").append("<span class='thank'><br><br>Чтобы проголосовать, зайдите в учётную запись</span>");

              } else if(res == 2) {

                $(".rating").append("<span class='thank'><br><br>Администратор не может ставить оценки</span>");

              } else if(res == 3) {

                $(".rating").append("<span class='thank'><br><br>Спасибо, мы учтём вашу оценку</span>");

              }
              
            }
          })
        })
      })
    </script>


<style type="text/css">
  .comment{
    border-radius: 8px;
    padding: 7px 20px;
    color: #6d6d6d;
    margin-bottom: 10px;
    box-shadow: 1px 1px 3px 0px #aaa;
    position: relative;
  }

  .comment:first-child{
    margin-top: 10px;
  }
  .comment:nth-child(odd){
    background-color: #e7f5ff;
  }

  .comment:nth-child(even){
    background-color: #bddcf3;
  }

  .comment .name{
    font-size: 20px;
    font-weight: bold;
  }

  .comment .date{
    float: right;
  }

  .comment .removeComment{
    position:  absolute;
    right: 20px;
    top: 30px;
    color: red;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
  }
</style>

<a name="comments"></a>
<div class="comments">
  <? if(empty($article)): ?>

  <? elseif(empty($args['comments'])): ?>
    <div class="comment nocomments">
      Комментариев нет
    </div>
  <? else:?>
    <? foreach ($args['comments'] as $comment): ?>

      <div class="comment">

        <? if(!empty($_SESSION['admin'])): ?>
          <span class="removeComment" data-comment_id="<?= $comment->id?>">удалить x</span>
        <? endif; ?>

        <span class="name"><?= $comment->login ?></span><span class="date"><?= date('d.m.y', $comment->date) ?></span>
        <p class="commentText"><?= $comment->commentText ?></p>
      </div>

    <? endforeach; ?>
  <? endif; ?>
</div>

<script type="text/javascript">
  $(function(){
    $(".removeComment").on('click', function(){
      var $span = $(this);
      var comment_id = $span.data('comment_id');
      $.ajax({
        type: 'post',
        data: {'comment_id' : comment_id},
        url: '/removeComment',
        success: function(res){
          if(!res) return;
          $span.siblings('p.commentText').text(res);
        }
      })
    })
  })
</script>

<? if(empty($article)): ?>

<? elseif(!empty($_SESSION['user_login'])): ?>

<style type="text/css">
  .addCommentForm{
    margin-top: 50px;
  }

  .addCommentForm button{
    width: 220px;
    margin-top: 10px;
    margin-bottom: 30px;
    height: 40px;
  }

  .addCommentForm textarea{
    resize: vertical;
    min-height: 114px;
  }
</style>
<div class="addCommentForm">
  <form id="sendCommentForm">
    <p>Добавить комментарий:</p>
    <textarea class="form-control" placeholder="Текст комментария" rows="5"></textarea>
    <button class="btn btn-primary sendComment">Отправить</button>
  </form>
</div>

<script type="text/javascript">
  $(function(){

    var data = {};

    $(".sendComment").on('click', function(e){
      e.preventDefault();

      data.commentText = $(".addCommentForm textarea").val();
      data.articleId = window.location.href.split('/')[4];

      $('#sendCommentForm').submit();
    })

    $('#sendCommentForm').ajaxForm({
      type: 'post',
      url:'/addComment',
      data: data,
      beforeSend: function(xhr) {
        $("span.errorComment").remove();
        if($(".addCommentForm textarea").val() == '') {
          xhr.abort();
          var errorHtml = '<span style="color:red" class="errorComment">Введи текст комментария!</span>';
          $( "#sendCommentForm p" ).after( errorHtml );
        }
      },
      success: function(res){
        if(!res) {
          var errorHtml = '<span style="color:red" class="errorComment">Ошибка добавления комментария!</span>';
          $( "#sendCommentForm p" ).after( errorHtml );
        } else {
          res = JSON.parse(res);
          document.getElementById("sendCommentForm").reset();
          var comment = '\
          <div class="comment">\
            <span class="name">'+res.login+'</span><span class="date">'+res.date+'</span>\
            <p>'+res.commentText+'</p>\
          </div>';
          $(".nocomments").remove();
          $(".comments").append(comment);
          $('span.comments_count').text("Комментариев: " + res.countComments)
        }
        
      }
    });

  })
</script>

<? else: ?>
  <style type="text/css">
    .messageNeedRegistration{
      margin: 50px;
    }
  </style>
  <p class="messageNeedRegistration"><a href="/registration">Зарегистрируйся</a> или <a href="/enter">Войди</a>, чтобы оставить комментарий.</p>
<? endif; ?>

<? include 'application/views/footer.php' ?>