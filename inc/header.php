<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Satyam Group ADEX Billing</title>
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
    <link rel="shortcut icon" href="assets/images/favicon.png" />
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
          <a class="navbar-brand brand-logo" href="<?php echo LINK_PATH ?>"><img src="assets/images/logo.png" alt="logo" /></a>
          <a class="navbar-brand brand-logo-mini" href="<?php echo LINK_PATH ?>"><img src="assets/images/logo.png" alt="logo" /></a>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
          </button>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>

          <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
              <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                <div class="nav-profile-img">
                  <img src="assets/images/favicon.png" alt="image">
                  <span class="availability-status online"></span>
                </div>
                <div class="nav-profile-text">
                  <p class="mb-1 text-black"><?php echo(isset($_SESSION['Name']) && !empty($_SESSION['Name'])?$_SESSION['Name']:''); ?></p>
                </div>
              </a>
              <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                
                <a class="dropdown-item" href="<?php echo LINK_PATH ?>logout.html">
                  <i class="mdi mdi-logout mr-2 text-primary"></i> Signout </a>
              </div>
            </li>
            <li class="nav-item nav-logout d-none d-lg-block">
              <a class="nav-link" href="<?php echo LINK_PATH ?>logout.html">
                <i class="mdi mdi-power"></i>
              </a>
            </li>
          </ul>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">

           <li class="nav-item">
              <a class="nav-link" href="<?php echo LINK_PATH ?>dashboard.html">
                <span class="menu-title">Home</span>
                <i class="mdi mdi-home menu-icon"></i>
              </a>
            </li>
            <?php 
			if(checkPermissions('Components') == true){
			?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo LINK_PATH ?>blood-component-type.html">
                <span class="menu-title">Products</span>
                <i class="mdi mdi-database-plus menu-icon"></i>
              </a>
            </li>
            <?php } 
			if(checkPermissions('Petient Categories') == true){
			?>
            <li class="nav-item" style="display:none">
              <a class="nav-link" href="<?php echo LINK_PATH ?>patient-categories.html">
                <span class="menu-title">Petient Categories</span>
                <i class="mdi mdi-hotel menu-icon"></i>
              </a>
            </li>
            <?php } 
			if(checkPermissions('Rates') == true){
			?>
            <li class="nav-item" style="display:none">
              <a class="nav-link" href="<?php echo LINK_PATH ?>rates.html">
                <span class="menu-title">Rates</span>
                <i class="mdi mdi-currency-inr menu-icon"></i>
              </a>
            </li>
            <li class="nav-item" style="display:none">
              <a class="nav-link" href="<?php echo LINK_PATH ?>financial-year.html">
                <span class="menu-title">Financial Year</span>
                <i class="mdi mdi-folder-account menu-icon"></i>
              </a>
            </li>
            <?php } 
			if(checkPermissions('Financial Year') == true){
			?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo LINK_PATH ?>registration.html">
                <span class="menu-title">Invoices</span>
                <i class="mdi mdi-account-card-details menu-icon"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo LINK_PATH ?>quotation.html">
                <span class="menu-title">Quotations</span>
                <i class="mdi mdi-file-document-outline menu-icon"></i>
              </a>
            </li>
            <?php } 
			if(checkPermissions('Users') == true){
			?>
            <li class="nav-item" style="display:none">
              <a class="nav-link" href="<?php echo LINK_PATH ?>users.html">
                <span class="menu-title">Users Management</span>
                <i class="mdi mdi-account-multiple-plus menu-icon"></i>
              </a>
            </li>
            <?php } 
			?>
            <li class="nav-item">
              <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <span class="menu-title">Reports</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-chart-bar menu-icon"></i>
              </a>
              <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"> <a class="nav-link" href="<?php echo LINK_PATH.'component-type-report.html' ?>">Product Wise</a></li>
                  <li class="nav-item"> <a class="nav-link" href="<?php echo LINK_PATH.'user-report.html' ?>">Invoice</a></li>
                </ul>
              </div>
            </li>
          </ul>
        </nav>