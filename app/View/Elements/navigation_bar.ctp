<? if(!AuthComponent::user('id')): ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $("a.login-link").click(function(e) {
        e.preventDefault();

        showLogin();
      });
    });

    function showLogin() {
      $.ajax({
           type: "GET",
           url: "/users/login/",
           success: function(html){
              $("#navpopup #modalContent").html(html);
              
              $("#navpopup").modal('show');
           }
        });    
    }
  </script>
<? endif; ?>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <a class="navbar-brand" href="/">Caunce Types</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="<?= (strstr($this->here, 'players') || $this->here == '/' || $this->here == '/cauncetypes/' ? 'active' : '') ?>"><a href="/">Players</a></li>
        <li class="<?= (strstr($this->here, 'seasons') ? 'active' : '') ?>"><a href="/seasons/home">Seasons</a></li>
        <li class="<?= (strstr($this->here, 'character_types') ? 'active' : '') ?>"><a href="/character_types/home">Character Types</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <? if(AuthComponent::user('id')): ?>
          <li><div style="padding-top: 15px; padding-bottom: 15px;">Welcome <?= AuthComponent::user('name') ?></div></li>
          <li><a href="/users/logout">Logout</a></li>
        <? else: ?>
          <li><a class="login-link" href="#">Login</a></li>
        <? endif; ?>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div id="navpopup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content" id="modalContent"></div>
  </div>
</div> 