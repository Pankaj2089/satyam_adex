<?php
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
$urlPrmas = "&action=". $action."&user_id=".$PostUserId."&start_date=".$postStartDate."&end_date=".$postEndDate;
?>
<!-- partial -->
<div class="main-panel">
<div class="content-wrapper">
<div class="page-header">
    <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
    </span> User Type Report
    </h3>
    <a href="<?php echo LINK_PATH.'export-user-type-data.html?'.$urlPrmas ?>" class="btn btn-sm btn-gradient-info mb-2 pull-right" style="margin-top: 21px;">Export Data</a>
</div>
    
<div class="row">

    <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
        <form class="form-inline searchForm" method="get" action="">
            <input type="hidden" name="action" value="search">
            
            <div class="input-group mb-2 mr-sm-2" style="width:200px;">
            <label>Users</label>
            <select class="form-control" id="user_id" name="user_id">
                <option value =""> All </option>
                <?php
                $usersql = "SELECT * from users where type = 'User'";
                $statement = $conn->prepare($usersql);
                $statement->execute();
                $statement->setFetchMode(PDO::FETCH_ASSOC);
                $userRows =$statement->fetchAll();
                if(count($userRows) > 0){
                    foreach($userRows as $userRow){
                        echo '<option value ="'.$userRow['id'].'" '.($PostUserId == $userRow['id'] ? "selected":"").'> '.$userRow['name'].' </option>';
                    }
                }
                ?>
            </select>
            </div>
            
            <div class="input-group mb-2 mr-sm-2">
            <label>Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Select Start Date" value="<?php echo $postStartDate; ?>">
            </div>
            <div class="input-group mb-2 mr-sm-2">
            <label>End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" placeholder="Select End Date" value="<?php echo $postEndDate; ?>">
            </div>
            
           
            <button type="submit" class="btn btn-gradient-primary mb-2" style="margin-top: 21px;">Search</button>
        </form>
        </div>
    </div>
    </div>
    <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
        <h4 class="card-title">Transaction Records</h4>
        <div class="table-responsive">
            <table class="table">
            <thead>
                <tr>
                    <th> # </th>
                    <th> Bill No./ Date </th>
                    <th> Name </th>
                    <th> Address </th>
                    <th> Mobile No. </th>
                    <th> Total Amount(₹) </th>
                    <th> Action </th>
                </tr>
            </thead>
            <tbody>
                <?php
                //echo $sql;
                $statement = $conn->prepare($sql);
                if(!$statement->execute()){//execute returns false if failed
                    $returned_data['response_code'] = "-2";
                    $returned_data['response_message'] = "Server error code -2(failed query)";
                }
                if ($statement->rowCount() > 0){
                    $statement->setFetchMode(PDO::FETCH_ASSOC);
                    $rows =$statement->fetchAll();

                foreach($rows as $key => $row){
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
                ?>
                <tr>
                <td><?php echo $key+1; ?> </td>
                <td> <?php echo $row['billing_no']; ?>/ <?php echo date('M d, Y',strtotime($row['created_at'])); ?> </td>
                <td> <?php echo $row['name']; ?> </td>
                <td> <?php echo $row['address']; ?> </td>
                <td> <?php echo $row['mobile']; ?> </td>
                <td> <?php echo $row['total_amount'];?> </td>
                <td><a href="javascript:void(0);" class="btn btn-sm btn-info" onclick="ViewProfile('<?= $row['id'] ?>')"  id="view_btn_<?= $row['id'] ?>" title="View Details">View Details</a>
                </tr>
                <tr class="profilelist" style="display:none;" id="profiledtl_<?= $row['id'] ?>">
                  <td id="extra_result_detail_<?= $row['id'] ?>" colspan="10" class=""><div class="col-lg-12 col-sm-12 col-xs-12 nopadding">
                      <div>
                        <h4>VIEW COMPONENT DETAILS</h4>
                      </div>
                      <?php 
					  $historySql = "SELECT * from registration_informations where reg_id = ".$row['id']." order by created_at ASC";
					   $historystatement = $conn->prepare($historySql);
						if(!$historystatement->execute()){//execute returns false if failed
							$returned_data['response_code'] = "-2";
							$returned_data['response_message'] = "Server error code -2(failed query)";
						}
						if ($historystatement->rowCount() > 0){
							$historystatement->setFetchMode(PDO::FETCH_ASSOC);
							$historyrows =$historystatement->fetchAll();
		
						
					  ?>
                      <div class="col-lg-12 col-sm-12 col-xs-12 nopadding">
                        <div class="row listData">
                          <table class="table table-striped historyTable">
                            <thead>
                              <tr style="background: #787878;color: #FFF;">
                                <th>S.No.</th>
                                <th>Blood Component</th>
                                <th>Qty.</th>
                                <th>Rate(₹)</th>
                                <th>Total Amount(₹)</th>
                                <th>Date and Time</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
							  $grandTotal = 0;
							foreach($historyrows as $key => $historyRow){
								$bloodComponent =  "No Available";
									$compsql = "SELECT * from blood_component_types where id = ".$historyRow['component_id']." limit 1";
									$statement = $conn->prepare($compsql);
									$statement->execute();
									$statement->setFetchMode(PDO::FETCH_ASSOC);
									$recCompData =$statement->fetch();
									$bloodComponent = isset($recCompData['title'])?$recCompData['title']:'Not Available';
									
									$amount = number_format($historyRow['amount'],2);
									$totalAmount = number_format($historyRow['total_amount'],2);
									$grandTotal += $totalAmount;
							?>
                              <tr>
                                <td><?php echo $key+1 ?>.</td>
                                <td><?php echo $bloodComponent ?></td>
                                <td width="20%" align="left"><?php echo $historyRow['component_quantity']?></td>
                                <td width="20%" align="left"><?php echo $amount; ?></td>
                                <td width="20%"><?php echo  $totalAmount; ?></td>
                                <td><?php echo isset($historyRow['created_at'])?date('d F, Y h:i A',strtotime($historyRow['created_at'])):'Not Available'?></td>
                              </tr>
                              <?php } 
							  $CGSTAmt = 0;
								if($row['cgst'] > 0){
									$CGSTAmt = floatval($grandTotal/100)*$row['cgst'];
								}
								$SGSTAmt = 0;
								if($row['sgst'] > 0){
									$SGSTAmt = floatval($grandTotal/100)*$row['sgst'];
								}
							?>
                            <tr style="background: #ddd;color: #000;">
                                <th colspan="5" style="background: #FFF;color: #FFF;">&nbsp;</th>
                                <th>Gross Amount : ₹<?php echo number_format($grandTotal,2); ?></th>
                              </tr>
                            <?php
                            if($CGSTAmt > 0){
							?>
                              <tr style="background: #ddd;color: #000;">
                                <th colspan="5" style="background: #FFF;color: #FFF;">&nbsp;</th>
                                <th>CGST : ₹<?php echo number_format($CGSTAmt,2); ?></th>
                              </tr>
                              <?php }
							  if($SGSTAmt > 0){
							   ?>
                              <tr style="background: #ddd;color: #000;">
                                <th colspan="5" style="background: #FFF;color: #FFF;">&nbsp;</th>
                                <th>SGST : ₹<?php echo number_format($SGSTAmt,2); ?></th>
                              </tr>
                              <?php } ?>
                              <tr style="background: #787878;color: #FFF;">
                                <th colspan="5" style="background: #FFF;color: #FFF;">&nbsp;</th>
                                <th>Net Amount Payable : ₹<?php echo number_format($row['total_amount'],2); ?></th>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <?php 
                      }
                      ?>
                    </div></td>
                </tr>
                <?php 
           		 }
                ?>

                <tr style="padding-bottom:20px">
                <td colspan="9" class="admlsttxt">Result pages:
                    <?php 
                    if($page<>1){ ?>
                    <a class="pageLink" href="<?php echo "user-report.html?page=1".$urlPrmas;?>">First Page</a>
                    <?php } ?>
                    &nbsp;&nbsp;
                    <?php if ($page>1){ ?>
                    <a class="pageLink" href="<?php echo "user-report.html?page=".($page-1).$urlPrmas;?>" >Previous</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php }
                    $start = $page-5;
                    if ($start <1)$start =1;
                    $ends = $page+5;
                    if ($ends > $totalPages)$ends =  $totalPages;
                    for ($i=$start;$i<=$ends;$i++){
                    if ($i== $page){
                        ?>
                    <?php echo $i;?>&nbsp;&nbsp;
                    <?php }else{ ?>
                    <a class="pageLink" href="<?php echo "user-report.html?&page=".$i.$urlPrmas;?>" ><?php echo $i; ?></a>&nbsp;&nbsp;
                    <?php 
                    }
                    }
                    if($totalPages > $page){ ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="pageLink" href="<?php echo "user-report.html?page=".($page+1).$urlPrmas;?>" >Next</a>
                    <?php } ?>
                    &nbsp;&nbsp;
                    <?php if ($page <> $totalPages){ ?>
                    <a class="pageLink" href="<?php echo "user-report.html?page=".($totalPages).$urlPrmas; ?>" >Last Page</a>&nbsp;&nbsp;&nbsp;&nbsp;<strong>(</strong><?php echo $page .'&nbsp;&nbsp;<strong>of</strong>&nbsp;&nbsp;'.$totalPages ?><strong>)</strong>
                    <?php } ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <?php
                }
                
                ?>
            </tbody>
            </table>
        </div>
        </div>
    </div>
    </div>
</div>
</div>
<script>
function ViewProfile(uid){
	var divCondition = $('#profiledtl_'+uid).is(":visible"); 
	if(divCondition == false){
		$('#profiledtl_'+uid).fadeIn();
	}else{
		$('#profiledtl_'+uid).hide();
	}
}
</script>
<?php
$conn = null;
?>
<style>
.searchForm label{
    display: block;
    width: 100%;
    font-size: 14px;
    margin-bottom: 5px;
}
</style>