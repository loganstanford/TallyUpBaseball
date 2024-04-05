<?php
require_once 'includes/dbconfig.inc.php';
require_once 'includes/functions.inc.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TallyFantasy | Register</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page dark-mode">
<div class="register-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="index.php" class="h1"><b>Tally</b>Fantasy</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Register</p>

      <form action="includes/register.inc.php" method="post">
        <div class="input-group mb-3">
          <input type="text" name="name" class="form-control" placeholder="Full name">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
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
        <div class="input-group mb-3">
          <input type="password" name="passwordRepeat" class="form-control" placeholder="Retype password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="agreeTerms" name="terms" value="agree">
              <label for="agreeTerms">
               I agree to the <a href="#">terms</a>
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" name="submit" class="btn btn-primary btn-block">Register</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

<!--       <div class="social-auth-links text-center">
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i>
          Sign up using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i>
          Sign up using Google+
        </a>
      </div> -->

      <a href="login.php" class="text-center">Go to login page</a>
    </div>
    <!-- /.form-box -->
  </div><!-- /.card -->
</div>
<!-- /.register-box -->
<?php 
if (isset($_GET['error'])) {
  echo '<div class="alert alert-danger alert-dismissible">';
  echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
  if ($_GET['error'] == 'formEmpty') {
    echo '<h5><i class="icon fas fa-ban"></i> Form incomplete</h5>';
    echo "Form fields empty. Please make sure all fields are filled in before clicking Submit";
  }
  if ($_GET['error'] == 'invalidEmail') {
    echo '<h5><i class="icon fas fa-ban"></i> Email invalid!</h5>';
    echo "Invalid email. Please check that you filled in your email correctly.";
  }
  if ($_GET['error'] == 'pwdMatch') {
    echo '<h5><i class="icon fas fa-ban"></i> Passwords don\'t match!</h5>';
    echo "Passwords do not match. Make sure that you enter the same password in both password fields.";
  }
  if ($_GET['error'] == 'emailExists') {
    echo '<h5><i class="icon fas fa-ban"></i> Email already exists!</h5>';
    echo "You may have already registered. Click the above link to go to the login page.";
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
