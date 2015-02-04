 <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Events <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="?action=viewEvents">
              <i class="fa fa-calendar"></i>
              All Events
            </a></li>
            <li><a href="?action=myEvents">
              <i class="fa fa-flag"></i>
              Events I'm Working
            </a></li>
            <?php if($user->isAdmin()) { ?>
            <li class="divider"></li>
            <li><a href="?action=addEvent">
              <i class="fa fa-plus"></i>
              Add Event
            </a></li>
            <?php } ?>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Teams <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="?action=viewTeams">
              <i class="fa fa-users"></i>
              All Teams
            </a></li>
            <li><a href="?action=myTeams">
              <i class="fa fa-bullhorn"></i>
              My Teams
            </a></li>
            <?php if($user->isAdmin()) { ?>
            <li class="divider"></li>
            <li><a href="?action=addTeam">
              <i class="fa fa-plus"></i>
              Add Team
            </a></li>
            <?php } ?>
          </ul>
        </li>

      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="?action=myProfile">Logged in as <?php echo $_SESSION['username']." (".$_SESSION['publicname'].")"; ?></a></li>
        <li><a href="?action=logout">Logout</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
