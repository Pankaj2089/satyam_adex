<?php
include('inc/db_connect.php');
$totalAmount = 0;
if(isset($_POST['componentType']) && $_POST['componentType'] > 0 && isset($_POST['componentQty']) && $_POST['componentQty'] > 0 && isset($_POST['rateFor']) && !empty($_POST['rateFor']) && isset($_POST['patientCategory'])){
#get price
$price = 0;
$ratesql = "SELECT blood_components from rates where rate_for= '".$_POST['rateFor']."' AND patient_category = ".$_POST['patientCategory']." ORDER BY id DESC limit 1";
$statement = $conn->prepare($ratesql);
$statement->execute();
$statement->setFetchMode(PDO::FETCH_ASSOC);
$reateData =$statement->fetch();
if(isset($reateData['blood_components']) && !empty($reateData['blood_components'])){
	$bloodComponentArr = json_decode($reateData['blood_components']);
	if(count($bloodComponentArr) > 0 ){
		$bloodComponents = '';
		foreach($bloodComponentArr as $bloodComponentData){
			$bloodComponentDataFields = explode('::', $bloodComponentData);
			if(isset($bloodComponentDataFields[0]) && $bloodComponentDataFields[0] == $_POST['componentType']){
				$price = $bloodComponentDataFields[1];
			}
		}
	}
}
$amount = number_format($price,2);
$totalAmount = number_format(floatval($price)*floatval($_POST['componentQty']),2);
}
echo number_format(str_replace(',','',$totalAmount),2); die;
?>
