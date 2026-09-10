<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__.'/../inc/functions.php';
if (checkPermissions('Financial Year') != true) {
    header('Location: '.LINK_PATH.'dashboard.html');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['quotation_id'])) {
    $statement = $conn->prepare('DELETE FROM quotations WHERE id=?');
    $statement->execute([(int) $_POST['quotation_id']]);
    $_SESSION['success'] = 'Quotation deleted successfully.';
}
header('Location: '.LINK_PATH.'quotation.html');
exit;