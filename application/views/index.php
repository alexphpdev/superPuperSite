<? include 'application'.DIRSEP.'views'.DIRSEP.'header.php' ?>

<!-- content -->
<div class="wrapper row2">
  <div id="container" class="clear">
    <div class='articlesWrapper'>
    <?php
    if(!empty($args['articles']))
    foreach ($args['articles'] as $k => $article) {
      include ('application'.DIRSEP.'views'.DIRSEP.'articleTemplate.php');
      echo $template;
    } else echo "Такого поста не существует";

    ?>
    </div>
    <div class="pagination_wrapper">
      <script type="text/javascript">
        $(function(){

          var offset = 0;
          if(window.location.href.split('/')[4]) {
            offset = (window.location.href.split('/')[4] - 1)* <?= $args['countPerPage'] ?>;
          }
          
          $(".show_more").on('click', function(){
            offset += <?= $args['countPerPage'] ?>;
            $.ajax({
              type:'post',
              data: {'offset' : offset},
              success: function(res){
                res = JSON.parse(res);
                console.log(res.current_pages)
                if(!res.show_more) $('.show_more').remove();
                $('.articlesWrapper').append(res.articles);
                for(var num in res.current_pages){
                  $('.pagination li').eq(num).addClass('active');
                }
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
        $currentPage = $args['currentPage'];
        for ($i=1; $i <= $pageCount ; $i++) {
          $active = '';
          if($currentPage == $i) $active = 'active';
          echo "<li class=" . $active . "><a href='".HOST."page/".$i."/'>".$i."</a></li>";
        }
        ?>
      </ul>
      <? endif; ?>
    </div>
<? include 'application/views/footer.php' ?>