<?php
session_start();
if (!isset($_SESSION['UserId'])) {
	echo '<script>window.location="'.LINK_PATH.'"</script>';
}
include('inc/db_connect.php');

$sqlData = "where user_id > 0 ";
$PostPatientCategory = $postStartDate = $postEndDate = $PostRateFor =  $PostUserId =  $postCountdata ='';

$postStartDate = date('Y-m-d');
if(isset($_GET["action"]) && $_GET["action"] =="search"){
	$PostUserId = $_GET["user_id"];
    $postStartDate = !empty($_GET["start_date"])?$_GET["start_date"]:date('Y-m-d');
    $postEndDate = $_GET["end_date"];
	if(!empty($PostUserId)){
		$sqlData .= " and user_id = ".$PostUserId;
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
$urlPrmas = "&action=". $action."&component_id=".$PostUserId."&start_date=".$postStartDate."&end_date=".$postEndDate;
//echo '<pre />';
//print_r($rows);die;

function cleanData(&$str) {
	$str = preg_replace("/\t/", "\\t", $str); 
	$str = preg_replace("/\r?\n/", "\\n", $str);
}

$filename = "user-type-report-" . date('Ymd') . ".xls";
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/vnd.ms-excel");

$flag = false;
if(count($rows) > 0){
    if(!$flag){
		echo "Sr. No. \t Bill No. \t GST No. \t Name \t   Address \t  Mobile No. \t Amount(Rs.) \t CGST(Rs.) \t SGST(Rs.) \t IGST(Rs.) \t  Total Amount \t Invoice Date \n";
		$flag = true;
	}
    $j = 1;
    foreach($rows as $row){
        array_walk($row, 'cleanData'); 
		
		$UserName =  "No Available";
		#get User Data
		if(isset($row['user_id']) && $row['user_id'] > 0){
			$userDataSql = "SELECT * from users where id = ".$row['user_id']." limit 1";
			$statement = $conn->prepare($userDataSql);
			$statement->execute();
			$statement->setFetchMode(PDO::FETCH_ASSOC);
			$userDataData =$statement->fetch();
			$UserName = isset($userDataData['name'])?$userDataData['name']:'Not Available';
		}
		
		$totalAmount = 0;
		$historySql = "SELECT * from registration_informations where reg_id = ".$row['id']." order by created_at ASC";
		$historystatement = $conn->prepare($historySql);
		if(!$historystatement->execute()){//execute returns false if failed
			$returned_data['response_code'] = "-2";
			$returned_data['response_message'] = "Server error code -2(failed query)";
		}
		if ($historystatement->rowCount() > 0){
			$historystatement->setFetchMode(PDO::FETCH_ASSOC);
			$historyrows =$historystatement->fetchAll();
			foreach($historyrows as $key => $historyRow){
				$totalAmount += $historyRow['amount']*$historyRow['component_quantity'];
			}
		}
		
		$totalAmount = str_replace(',','',$totalAmount);
		
		$CGSTAmt = $SGSTAmt = $IGSTAmt = 0;						
		if($row['cgst'] > 0 && $totalAmount > 0){
			$CGSTAmt = floatval($totalAmount/100)*$row['cgst'];
		}
		if($row['sgst'] > 0 && $totalAmount > 0){
			$SGSTAmt = floatval($totalAmount/100)*$row['sgst'];
		}
		if($row['igst'] > 0 && $totalAmount > 0){
			$IGSTAmt = floatval($totalAmount/100)*$row['igst'];
		}
		
		$GST = $CGSTAmt+$SGSTAmt+$IGSTAmt;		
		$Amount = $row['total_amount']-$GST;
		
		if($row['cgst'] > 0 && $totalAmount > 0){
			$CGSTAmt = '('.$row['cgst'].'%) '. number_format(floatval($totalAmount/100)*$row['cgst'],2);
		}
		if($row['sgst'] > 0 && $totalAmount > 0){
			$SGSTAmt = '('.$row['sgst'].'%) '. number_format(floatval($totalAmount/100)*$row['sgst'],2);
		}
		if($row['igst'] > 0 && $totalAmount > 0){
			$IGSTAmt = '('.$row['igst'].'%) '. number_format(floatval($totalAmount/100)*$row['igst'],2);
		}
		
		$inDate = date('M d, Y',strtotime($row['created_at']));
		echo $j." \t ".trim($row['billing_no'])." \t ".trim($row['party_gst_no'])." \t ".trim($row['name'])." \t ".trim($row['address'])." \t ".trim($row['mobile'])." \t ".number_format($Amount,2)." \t ".$CGSTAmt." \t ".$SGSTAmt." \t ".$IGSTAmt." \t ".number_format($row['total_amount'],2)." \t ".$inDate."\n";
		$j++;
	}
}

?>
