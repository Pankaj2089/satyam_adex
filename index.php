<?php
include('inc/db_connect.php');
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode("/", $url);
$pageName = str_replace('.html','',end($parts));
if($pageName == '' || $pageName == 'login'){
    include('login.php');
}else if($pageName == 'logout'){
    include('logout.php');
}else if($pageName == 'edit-blood-component-type'){
    include('ComponentType/edit_type.php');
}else if($pageName == 'edit-patient-category'){
    include('PatientCategories/edit.php');
}else if($pageName == 'edit-rates'){
    include('Rates/edit.php');
}else if($pageName == 'edit-financial-year'){
    include('FinancialYear/edit.php');
}else if($pageName == 'edit-user'){
    include('Users/edit.php');
}else if($pageName == 'print-invoice'){
    include('Registration/print_invoice.php');
}else if($pageName == 'print-quotation'){
    include('Registration/print_quotation.php');
}else if($pageName == 'delete-quotation'){
    include('Registration/quotation_delete.php');
}else{
    include('layout.php');
}
?>