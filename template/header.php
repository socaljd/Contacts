<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/login/functions.php';
?>

<nav class="navbar navbar-default" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/index.php">Home</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <?php if(login_check($mysqli) == true): ?>
          <!-- <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Browse <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="/browse/browse_ci.php">Current Inventory</a></li>
              <li><a href="#">Received Inventory</a></li>
              <li><a href="#">Customer Sales</a></li>
            </ul>
          </li> -->
          <li><a href="/contacts/contact.php">Add</a></li>
          <li><a href="/contacts/search_contacts.php">Browse</a></li>
          <li><a href="/contacts/contact_groups.php">Groups</a></li>
        <?php else: ?>
          <li><a href="/login/login.php">Log In</a></li>
          <li><a href="/login/register.php">Register</a></li>
        <?php endif ?>
      </ul>
      <!-- <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form> -->
      <?php if(login_check($mysqli) == true): ?>
        <ul class="nav navbar-nav navbar-right">
          <!-- <li><a href="#">Link</a></li> -->
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Account <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="/user/settings.php">Settings</a></li>
              <li class="divider"></li>
              <li><a href="/login/logout.php">Log Out</a></li>
            </ul>
          </li>
        </ul>
      <?php endif ?>

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>



