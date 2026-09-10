<?php
session_start();
if (!isset($_SESSION['UserId'])) {
	echo '<script>window.location="'.LINK_PATH.'"</script>';
}
include('inc/functions.php');
include('inc/header.php');
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode("/", $url);
$pageName = str_replace('.html','',end($parts));
if($pageName == 'dashboard'){
    include('dashboard.php');
}else if($pageName == 'blood-component-type'){
    include('ComponentType/blood_component_type.php');
}else if($pageName == 'patient-categories'){
    include('PatientCategories/index.php');
}else if($pageName == 'rates'){
    include('Rates/index.php');
}else if($pageName == 'financial-year'){
    include('FinancialYear/index.php');
}else if($pageName == 'registration'){
    include('Registration/index.php');
}else if($pageName == 'add-registration'){
    include('Registration/add.php');
}else if($pageName == 'quotation'){
    include('Registration/quotation_list.php');
}else if($pageName == 'add-quotation'){
    include('Registration/quotation.php');
}else if($pageName == 'quotation-list'){
    include('Registration/quotation_list.php');
}else if($pageName == 'edit-registration'){
    include('Registration/edit.php');
}else if($pageName == 'users'){
    include('Users/index.php');
}else if($pageName == 'component-type-report'){
    include('Reports/component_type_report.php');
}else if($pageName == 'user-report'){
    include('Reports/user_report.php');
}

include('inc/footer.php');
?>