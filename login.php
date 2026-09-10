<?php
session_start();

if (!isset($_SESSION['UserId'])) {
	if (isset($_POST['adminloginok'])) {	
	   if(!$_POST['usern'] || !$_POST['pass']) {
			$errormsg = "Please enter your username and password";
            echo $errormsg;die;
            $_SESSION['error'] = $errormsg;
	   }
	   
      $md5pass = md5($_POST['pass']);
      $username = trim(strtolower($_POST['usern']));
      $userssql = "SELECT * from users where email = '".$username."' limit 1";
      $statement = $conn->prepare($userssql);
      $statement->execute();
      $statement->setFetchMode(PDO::FETCH_ASSOC);
      $dbarray =$statement->fetch();
	  $result = false;
      if(isset($dbarray['password']) && $md5pass == $dbarray['password']){
        $result = true; 
        $_SESSION['UserId'] = $dbarray['id'];
        $_SESSION['Type'] =  $dbarray['type'];
        $_SESSION['Name'] = $dbarray['name'];
        $_SESSION['AccountPermissions'] = $dbarray['user_permissions'];
      }
	   if($result != true) {
			$errormsg = "Please enter valid login details";
      		$_SESSION['error'] = $errormsg;
	   } else {
		   echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>';die;
	   }
	}
}

if (isset($_SESSION['UserId']) && !empty($_SESSION['UserId'])) {
	echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>';
} else {
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Satyam ADEX Billing</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png">
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
                <?php 
                if(isset($_SESSION['error']) && !empty($_SESSION['error'])){
                    echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                }
                ?>
              <div class="auth-form-light text-left p-5">
                <div class="brand-logo text-center">
                  <img src="assets/images/logo.png">
                </div>
                <h4>Hello! let's get started</h4>
                <h6 class="font-weight-light">Sign in to continue.</h6>
                <form method="post" name="login" class="pt-3" id="login" action="">
            <input type="hidden" name="adminloginok" value="adminloginok">
                  <div class="form-group">
                    <input type="email" class="form-control form-control-lg" id="usern" name="usern" value="" placeholder="Username" required>
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" id="pass" name="pass" value="" placeholder="Password" required>
                  </div>
                  <div class="mt-3">
                    <input type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" value="SIGN IN">
                  </div> 
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <!-- endinject -->
  </body>
</html>
<?php
}

$_SESSION['error'] ="";
unset ($_SESSION['error']);
?>