    <div id="homepage" class="last clear">
      <img class="imgl" src="/img/spots.png" width="130" height="130" alt="">
      <div>
        <a href="/about">О нас</a>&nbsp;&nbsp;
        <? if(empty($_SESSION['user_login'])): ?>
          <a href="/enter">Вход</a>&nbsp;&nbsp;
          <a href="/registration">Регистрация</a>&nbsp;&nbsp;
        <? endif; ?>
      </div>
      <div>
        <div class="sometext">
          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </div>
        <div class="sn">
          <a href="https://twitter.com/" rel="external follow" target="_blank" class="external a-not-img"><i class="fa fa-twitter" aria-hidden="true"></i></a>
          <a href="https://facebook.com/" rel="external follow" target="_blank" class="external a-not-img"><i class="fa fa-facebook" aria-hidden="true"></i></a>
          <a href="https://plus.google.com/" rel="external follow" target="_blank" class="external a-not-img"><i class="fa fa-google-plus" aria-hidden="true"></i></a>
          <a href="https://youtube.com" rel="external follow" target="_blank" class="a-not-img"><i class="fa fa-youtube" aria-hidden="true"></i></a>
        </div>
      </div>
    </div>
    <!-- / content body -->
  </div>
</div>
<!-- Footer -->
<div class="wrapper row3">
  <footer id="footer" class="clear">
    <p class="fl_left">Copyright &copy; 2012 - All Rights Reserved - <a href="#">Domain Name</a></p>
    <p class="fl_right">Template by <a href="http://www.os-templates.com/" title="Free Website Templates">OS Templates</a></p>
  </footer>
</div>
</body>
</html>