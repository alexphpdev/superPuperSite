<? include 'application'.DIRSEP.'views'.DIRSEP.'header.php' ?>

<!-- content -->
<div class="wrapper row2">
  <div id="container" class="clear">
    <div class="articlesWrapper">
    <?php
    if($args['title'] == 'Такой категории не существует!') echo $args['title'];
    elseif(!empty($args['articles'])) { 
      foreach ($args['articles'] as $k => $article) {
        include ('application'.DIRSEP.'views'.DIRSEP.'articleTemplate.php');
        echo $template;
      }
    } else echo 'Категория пустая!';

    ?>
    </div>
    <div class="pagination_wrapper">
      <script type="text/javascript">
        $(function(){

          var offset = 0;
          var currentPage = 1;
          var categoryId = window.location.href.split('/')[4];

          if(window.location.href.split('/')[6]) {
            currentPage = +window.location.href.split('/')[6];
          }
          offset = currentPage * <?= $args['countPerPage'] ?>;
          
          $(".show_more").on('click', function(){
            $.ajax({
              type:'post',
              data: {'offset' : offset, 'currentPage' : currentPage , 'categoryId': categoryId},
              success: function(res){
                res = JSON.parse(res);
                console.log(res)
                if(!res.show_more) $('.show_more').remove();
                $('.articlesWrapper').append(res.articles);
                for(var num in res.current_pages){
                  $('.pagination li').eq(num).addClass('active');
                }
                offset += <?= $args['countPerPage'] ?>;
                currentPage += 1;
              }
            })
          })
        })
      </script>
      <?php if($args['show_more']) :?>
        <button class="btn btn-primary show_more">Показать еще</button><br>
      <?php endif; ?>

      <? if($args['show_pagination']) : ?>
      <ul class="pagination">
        <?php 
        $pageCount = $args['pageCount'];
        $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $currentPage = $args['currentPage'];
        for ($i=1; $i <= $pageCount ; $i++) {
          $active = '';
          if($currentPage == $i) $active = 'active';
          echo "<li class=" . $active . "><a href='".HOST."category/".$uri[1]."/page/".$i."/'>".$i."</a></li>";
        }
        ?>
      </ul>
      <? endif; ?>
    </div>
    
<? include 'application/views/footer.php' ?>