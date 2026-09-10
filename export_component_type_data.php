<?php
session_start();
if (!isset($_SESSION['UserId'])) {
	echo '<script>window.location="'.LINK_PATH.'"</script>';
}
include('inc/db_connect.php');

$sqlData = "where user_id > 0 ";
$PostPatientCategory = $postStartDate = $postEndDate = $PostRateFor =  $PostComponentId =  $postCountdata ='';

$postStartDate = date('Y-m-d');
if(isset($_GET["action"]) && $_GET["action"] =="search"){
	$PostComponentId = $_GET["component_id"];
    $postStartDate = !empty($_GET["start_date"])?$_GET["start_date"]:date('Y-m-d');
    $postEndDate = $_GET["end_date"];

	if(!empty($PostComponentId)){
		$sqlData .= ' and FIND_IN_SET(\''.$PostComponentId.'\', component_ids )';
    }
    if(!empty($postEndDate)){
        $sqlData .=" and created_at BETWEEN  '".$postStartDate." 00:00:00' AND '".$postEndDate." 23:59:00'";
    }else{
		$sqlData .= " and created_at BETWEEN '".$postStartDate." 00:00:00' AND '".$postStartDate." 23:59:00'";
	}
}

//get records
$perpage=20;
if(!isset($_GET["page"]) || $_GET["page"] ==""){
    $page=1;
}else{
    $page=$_GET["page"];
}
if($page<1){
    $page=1;
}
$starter = (($page -1)*$perpage);

$sql = "SELECT * from registrations $sqlData order by created_at desc limit $starter, $perpage ";
$sql2 = "SELECT * from registrations $sqlData order by created_at desc";

$statement2 = $conn->prepare($sql2);
if(!$statement2->execute()){//execute returns false if failed
    $returned_data['response_code'] = "-2";
    $returned_data['response_message'] = "Server error code -2(failed query)";
}
if ($statement2->rowCount() > 0){
    $statement2->setFetchMode(PDO::FETCH_ASSOC);
    $rows =$statement2->fetchAll();
    $countRows = $statement2->rowCount();
    $totalPages= ceil($countRows/$perpage);
    if($totalPages==0){$totalPages=1;}
}

$action = isset($_GET["action"])?$_GET["action"]:'';
$urlPrmas = "&action=". $action."&component_id=".$PostComponentId."&start_date=".$postStartDate."&end_date=".$postEndDate;
//echo '<pre />';
//print_r($rows);die;

function cleanData(&$str) {
	$str = preg_replace("/\t/", "\\t", $str); 
	$str = preg_replace("/\r?\n/", "\\n", $str);
}

$filename = "component-type-report-" . date('Ymd') . ".xls";
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/vnd.ms-excel");

$flag = false;
if(count($rows) > 0){
    if(!$flag){
		echo "Sr. No. \t Bill No. \t GST No. \t Name \t  Address \t  Mobile No. \t Product \t Qty. \t Rate(Rs.) \t CGST(Rs.) \t SGST(Rs.) \t IGST(Rs.) \t Total Amount(Rs.) \t Invoice Date \n";
		$flag = true;
	}
    $j = 1;
    foreach($rows as $row){
        array_walk($row, 'cleanData'); 
		if($PostComponentId > 0){
			$historySql = "SELECT * from registration_informations where reg_id = ".$row['id']." AND component_id = ".$PostComponentId." order by created_at ASC";
		}else{
			$historySql = "SELECT * from registration_informations where reg_id = ".$row['id']." order by created_at ASC";
		}
	   $historystatement = $conn->prepare($historySql);
		if(!$historystatement->execute()){//execute returns false if failed
			$returned_data['response_code'] = "-2";
			$returned_data['response_message'] = "Server error code -2(failed query)";
		}
		if ($historystatement->rowCount() > 0){
			$historystatement->setFetchMode(PDO::FETCH_ASSOC);
			$historyrows =$historystatement->fetchAll();
			foreach($historyrows as $key => $historyRow){
				$totalAmount = str_replace(',','',$historyRow['amount']);
				$totalAmount = $totalAmount*$historyRow['component_quantity'];
				$bloodComponent =  "No Available";
						$compsql = "SELECT * from blood_component_types where id = ".$historyRow['component_id']." limit 1";
						$statement = $conn->prepare($compsql);
						$statement->execute();
						$statement->setFetchMode(PDO::FETCH_ASSOC);
						$recCompData =$statement->fetch();
						$bloodComponent = isset($recCompData['title'])?$recCompData['title']:'Not Available';
						
						$CGSTAmt = $SGSTAmt = $IGSTAmt = 0;						
						if($row['cgst'] > 0 && $row['total_amount'] > 0){
							$CGSTAmt = floatval($totalAmount/100)*$row['cgst'];
						}
						if($row['sgst'] > 0 && $row['total_amount'] > 0){
							$SGSTAmt = floatval($totalAmount/100)*$row['sgst'];
						}
						if($row['igst'] > 0 && $row['total_amount'] > 0){
							$IGSTAmt = floatval($totalAmount/100)*$row['igst'];
						}
						
						$finalAmount = $historyRow['total_amount']+$CGSTAmt+$SGSTAmt+$IGSTAmt;
						
						if($row['cgst'] > 0 && $row['total_amount'] > 0){
							$CGSTAmt = '('.$row['cgst'].'%) '. number_format(floatval($totalAmount/100)*$row['cgst'],2);
						}
						if($row['sgst'] > 0 && $row['total_amount'] > 0){
							$SGSTAmt = '('.$row['sgst'].'%) '. number_format(floatval($totalAmount/100)*$row['sgst'],2);
						}
						if($row['igst'] > 0 && $row['total_amount'] > 0){
							$IGSTAmt = '('.$row['igst'].'%) '. number_format(floatval($totalAmount/100)*$row['igst'],2);
						}
						
						
		$inDate = date('M d, Y',strtotime($row['created_at']));
		echo $j." \t ".trim($row['billing_no'])." \t ".trim($row['party_gst_no'])." \t ".trim($row['name'])." \t ".trim($row['address'])." \t ".trim($row['mobile'])." \t ".$bloodComponent." \t ".$historyRow['component_quantity']." \t ".$totalAmount." \t ".$CGSTAmt." \t ".$SGSTAmt." \t ".$IGSTAmt." \t ".number_format($finalAmount,2)." \t ".$inDate."\n";
		$j++;
			}
		}
	}
}

?>
