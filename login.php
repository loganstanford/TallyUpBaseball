<?php
session_start();
// Require files
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';

// Page variables
$pageTitle = "Login";

// Database calls
if ($_SERVER['REQUEST_METHOD'] == "Post") {
  
}

// Database call variables


// HTTPS Redirector
redirectHTTPS();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <?php include 'head.php';?>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="dist/css/site.css">
</head>
<body class="hold-transition login-page dark-mode">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="index.php" class="h1"><b>Tally</b>Fantasy</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in</p>

      <form action="includes/login.inc.php" method="post">
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" name="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <!-- <div class="social-auth-links text-center mt-2 mb-3">
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
        </a>
      </div> -->
      <!-- /.social-auth-links -->

      <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p>
      <p class="mb-0">
        <a href="register.php" class="text-center">Register</a>
      </p>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->
<?php
if (isset($_GET['register'])) {
  echo '<div class="alert alert-success alert-dismissible">';
  echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
  if ($_GET['register'] == 'true') {
    echo '<h5>Registration successful</h5>';
    echo "Login using the email and password you created";
  }
  echo '</div>';
}
if (isset($_GET['error'])) {
  echo '<div class="alert alert-danger alert-dismissible">';
  echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
  if ($_GET['error'] == 'formEmpty') {
    echo '<h5><i class="icon fas fa-ban"></i> Form incomplete</h5>';
    echo "Form fields empty. Please make sure all fields are filled in before clicking Submit";
  }
  else if ($_GET['error'] == 'badEmail') {
    echo '<h5><i class="icon fas fa-ban"></i> No account found</h5>';
    echo "A user does not exist with this email. Click the Register link to sign up.";
  }
  else if ($_GET['error'] == 'badPass') {
    echo '<h5><i class="icon fas fa-ban"></i> Invalid login</h5>';
    echo "Login credentials incorrect. Please try again.";
  }
  echo '</div>';
}
?>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
