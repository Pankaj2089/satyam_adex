<?php
if(checkPermissions('Rates') != true){
	echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>';die;
}
$sqlData = "where status != 3 ";
$PostRateFor = $PostPatientCategory = $rateFor = $patientCategory ='';
if(isset($_POST["action"]) && $_POST["action"] =="add"){
	
    $rateFor = ucwords($_POST["rate_for"]);
	$patientCategory = $_POST["patient_category"];
	$bloodComponents = $_POST["bloodComponent"];
	$bloodComponentPrice = $_POST["bloodComponentPrice"];
	
	$bloodComponentData = '';
	$bloodComponentIDs ='';
	if(count($bloodComponents) > 0){
		$bloodComponentPriceArr = array();
		$bloodComponentIDsArray = array();
		foreach($bloodComponents as $key => $bloodComponent){
			if(isset($bloodComponentPrice[$key]) && !empty($bloodComponentPrice[$key])){
				$bloodComponentPriceArr[] = $bloodComponent.'::'.floatval($bloodComponentPrice[$key]);
				$bloodComponentIDsArray[] = $bloodComponent;
			}
		}
		if(count($bloodComponentPriceArr) > 0){
			$bloodComponentData = json_encode($bloodComponentPriceArr);
			$bloodComponentIDs = implode(',', $bloodComponentIDsArray );
		}
	}
	
	$createdAt = date('Y-m-d H:i:s');
	$sql = "INSERT INTO rates (rate_for, patient_category, blood_components, blood_component_ids, created_at) VALUES (?,?,?,?,?)";
	$stmt= $conn->prepare($sql);
	$stmt->execute([$rateFor, $patientCategory, $bloodComponentData, $bloodComponentIDs, $createdAt]);    
	$Title = "";
	$rtoEmail = "";
	$successmsg = "Rates has been added successfully.";
	$_SESSION['success'] = $successmsg;
	echo '<script>window.location="'.LINK_PATH.'rates.html"</script>';die;
}
if(isset($_POST["action"]) && $_POST["action"] =="edit"){
   if(isset($_POST["rate_for"]) && !empty($_POST["rate_for"])){
		
		$rateFor = ucwords($_POST["rate_for"]);
		$patientCategory = $_POST["patient_category"];
		$bloodComponents = $_POST["bloodComponent"];
		$bloodComponentPrice = $_POST["bloodComponentPrice"];
		
		$bloodComponentData = '';
		$bloodComponentIDs ='';
		if(count($bloodComponents) > 0){
			$bloodComponentPriceArr = array();
			$bloodComponentIDsArray = array();
			foreach($bloodComponents as $key => $bloodComponent){
				if(isset($bloodComponentPrice[$key]) && !empty($bloodComponentPrice[$key])){
					$bloodComponentPriceArr[] = $bloodComponent.'::'.floatval($bloodComponentPrice[$key]);
					$bloodComponentIDsArray[] = $bloodComponent;
				}
			}
			if(count($bloodComponentPriceArr) > 0){
				$bloodComponentData = json_encode($bloodComponentPriceArr);
				$bloodComponentIDs = implode(',', $bloodComponentIDsArray );
			}
		}
		
		$createdAt = date('Y-m-d H:i:s');
		$rowID =  $_POST['rowID'];
		$sql = "UPDATE rates SET rate_for=?, patient_category=?, blood_components=?, blood_component_ids=? WHERE id=?";
		$stmt= $conn->prepare($sql);
		$stmt->execute([$rateFor, $patientCategory, $bloodComponentData, $bloodComponentIDs, $rowID]);  
		$Title = "";
		$successmsg = "Rates has been updated successfully.";
		$_SESSION['success'] = $successmsg;
		echo '<script>window.location="'.LINK_PATH.'rates.html"</script>';die;
   }else{
    $errormsg = "Please enter Component Title";
    $_SESSION['error'] = $errormsg;
   }
}
if(isset($_GET["action"]) && $_GET["action"] =="search"){
    $PostRateFor = $_GET["rate_for"];
	$PostPatientCategory = $_GET["patient_category"];
    if(!empty($postName)){
        $sqlData .= " and rate_for LIKE '%".$PostRateFor."%'";
    }
	if(!empty($PostPatientCategory)){
        $sqlData .= " and patient_category = '".$PostPatientCategory."'";
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

$sql = "SELECT * from rates $sqlData order by created_at desc limit $starter, $perpage ";
$sql2 = "SELECT * from rates $sqlData order by created_at desc";

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

$urlPrmas = "&action=". $action."&rate_for=".$PostRateFor."&patient_category=".$PostPatientCategory;
?>
<!-- partial -->

<div class="main-panel">
<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title"> <span class="page-title-icon bg-gradient-primary text-white mr-2"> <i class="mdi mdi-format-list-bulleted menu-icon"></i> </span> Rates Management </h3>
  </div>
  <div class="row">
    <div class="col-md-4 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add New Rates</h4>
          <form class="forms-sample" method="post" action="">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
              <label for="exampleInputUsername1">Rate For</label>
              <select class="form-control" id="rate_for" name="rate_for">
                <option value="Govt." <?php echo $rateFor == 'Govt.' ? "selected":"";?>> Govt. </option>
                <option value="Private" <?php echo $rateFor == 'Private' ? "selected":"";?>> Private </option>
              </select>
            </div>
            <div class="form-group">
              <label>Patient Category</label>
              <select class="form-control" id="patient_category" name="patient_category">
                <option value="0"> Select Patient Category </option>
                <?php
                    $rtosql = "SELECT * from patient_categories where status = 1";
                    $statement = $conn->prepare($rtosql);
                    $statement->execute();
                    $statement->setFetchMode(PDO::FETCH_ASSOC);
                    $catRows =$statement->fetchAll();
                    if(count($catRows) > 0){
                        foreach($catRows as $catRow){
                            echo '<option value ="'.$catRow['id'].'" '.($patientCategory == $catRow['id'] ? "selected":"").'> '.$catRow['title'].' </option>';
                        }
                    }
                    ?>
              </select>
            </div>
            <div class="form-group">
              <h5>Blood Components</h5>
             <?php
			$csql = "SELECT * from blood_component_types where status = 1";
			$statement = $conn->prepare($csql);
			$statement->execute();
			$statement->setFetchMode(PDO::FETCH_ASSOC);
			$rows =$statement->fetchAll();
			if(count($rows) > 0){
				foreach($rows as $row){
				echo '
				<input type="hidden" value="'.$row['id'].'" name="bloodComponent[]">
				<div class="form-group row">
					<label for="exampleInputUsername2" class="col-sm-5 col-form-label">'.$row['title'].'</label>
					<div class="col-sm-7">
					  <input type="text" maxlength="6" class="form-control rateData" id="'.$row['id'].'" name="bloodComponentPrice[]" id="exampleInputUsername2" placeholder="Enter Price">
					</div>
				  </div>';
				}
			}
			?>
            </div>
            <?php 
                if(isset($_SESSION['error']) && !empty($_SESSION['error'])){
                    echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
                }
                ?>
            <button type="submit" class="btn btn-gradient-primary mr-2">Submit</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-8 ">
      <?php 
        if(isset($_SESSION['success']) && !empty($_SESSION['success'])){
            echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
        }
        ?>
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <form class="form-inline searchForm" method="get" action="">
              <input type="hidden" name="action" value="search">
              <div class="input-group mb-2 mr-sm-2">
                <label for="exampleInputUsername1">Rate For</label>
              <select class="form-control" id="rate_for" name="rate_for">
              <option value=""> Select</option>
                <option value="Govt." <?php echo $PostRateFor == 'Govt.' ? "selected":"";?>> Govt. </option>
                <option value="Private" <?php echo $PostRateFor == 'Private' ? "selected":"";?>> Private </option>
              </select>
              </div>
              
              <div class="input-group mb-2 mr-sm-2">
              <label>Patient Category</label>
              <select class="form-control" id="patient_category" name="patient_category">
                <option value=""> Select Patient Category </option>
                <?php
                    $rtosql = "SELECT * from patient_categories where status = 1";
                    $statement = $conn->prepare($rtosql);
                    $statement->execute();
                    $statement->setFetchMode(PDO::FETCH_ASSOC);
                    $catRows =$statement->fetchAll();
                    if(count($catRows) > 0){
                        foreach($catRows as $catRow){
                            echo '<option value ="'.$catRow['id'].'" '.($PostPatientCategory == $catRow['id'] ? "selected":"").'> '.$catRow['title'].' </option>';
                        }
                    }
                    ?>
              </select>
            </div>
              <button type="submit" class="btn btn-gradient-primary mb-2" style="margin-top: 21px;">Search</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Rates List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th> # </th>
                    <th> Rate For </th>
                    <th> Patient Type </th>
                    <th> Blood Components </th>
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
					$patientCategory =  "No Available";
                    #get RTO Data
                    if(isset($row['patient_category']) && $row['patient_category'] > 0){
                        $categorysql = "SELECT * from patient_categories where id = ".$row['patient_category']." limit 1";
                        $statement = $conn->prepare($categorysql);
                        $statement->execute();
                        $statement->setFetchMode(PDO::FETCH_ASSOC);
                        $categoryData =$statement->fetch();
                        $patientCategory = isset($categoryData['title'])?$categoryData['title']:'Not Available';
                    }
					$bloodComponents =  "No Available";
                    #get blood_components Data
                    if(isset($row['blood_components']) && !empty($row['blood_components'])){
						
						$bloodComponentArr = json_decode($row['blood_components']);
						if(count($bloodComponentArr) > 0 ){
							$bloodComponents = '';
							foreach($bloodComponentArr as $bloodComponentData){
								$bloodComponentDataFields = explode('::', $bloodComponentData);
								if(isset($bloodComponentDataFields[0])){
									$componenetsql = "SELECT * from blood_component_types where id = ".$bloodComponentDataFields[0]." limit 1";
									$statement = $conn->prepare($componenetsql);
									$statement->execute();
									$statement->setFetchMode(PDO::FETCH_ASSOC);
									$categoryData =$statement->fetch();
									$bloodComponentTitle = isset($categoryData['title'])?$categoryData['title']:'Not Available';
									$bloodComponents .= '<p style="margin-bottom:3px; font-size:13px">'.$bloodComponentTitle.' = ₹'.number_format($bloodComponentDataFields[1],2).'</p>';
								}
							}
						}
                    }
                    ?>
                  <tr>
                    <td><?php echo $key+1; ?></td>
                    <td><?php echo $row['rate_for']; ?></td>
                    <td><?php echo $patientCategory; ?></td>
                    <td><?php echo $bloodComponents; ?></td>
                    <td><a href="javascript:void(0);" onclick="editRecord('<?php echo $row['id']; ?>');" class="btn btn-xs btn-primary"> <i class="mdi mdi-pencil-box"></i></a></td>
                  </tr>
                  <?php 
                }
                ?>
                  <tr style="padding-bottom:20px">
                    <td colspan="9" class="admlsttxt">Result pages
                    <?php 
                            if($page<>1){ ?>
                      <a class="pageLink" href="<?php echo "rates.html?page=1".$urlPrmas;?>">First Page</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page>1){ ?>
                      <a class="pageLink" href="<?php echo "rates.html?page=".($page-1).$urlPrmas;?>" >Previous</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                      <a class="pageLink" href="<?php echo "rates.html?&page=".$i.$urlPrmas;?>" ><?php echo $i; ?></a>&nbsp;&nbsp;
                      <?php 
                            }
                            }
                            if($totalPages > $page){ ?>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="pageLink" href="<?php echo "rates.html?page=".($page+1).$urlPrmas;?>" >Next</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page <> $totalPages){ ?>
                      <a class="pageLink" href="<?php echo "rates.html?page=".($totalPages).$urlPrmas; ?>" >Last Page</a>&nbsp;&nbsp;&nbsp;&nbsp;<strong>(</strong><?php echo $page .'&nbsp;&nbsp;<strong>of</strong>&nbsp;&nbsp;'.$totalPages ?><strong>)</strong>
                      <?php } ?></td>
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
</div>

<div id="myLogoModal" class="modal fade" data-keyboard="false" data-backdrop="static" role="dialog">
  <div class="modal-dialog" style="min-width: 700px;">
    <form class="form-inline searchForm" method="post" action="">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="rowID" id="rowID" value="">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title">Update Rates</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body" id="modalBody">
          <p>Loading...</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="SaveBTN">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript" src="assets/js/custom.js"></script>
<script>
$(document).ready(function(){
	$('.rateData').filter_input({regex:'[0-9.]'});
});
function editRecord(rowID){
	$('#rowID').val(rowID);
	$('#myLogoModal').modal('show');
	$.ajax({
		type: "POST",
		url: "<?php echo LINK_PATH.'edit-rates.html' ?>",
		data: {rowID: rowID},
		success: function(e) {
			if(e != 'Error'){
				$('#modalBody').html(e);
				$('#SaveBTN').show();	
			}else{
				$('#modalBody').html('<div class="alert alert-danger">Something want to wrong, please try after sometime.</div>');
				$('#SaveBTN').hide();	
			}
		}
	});
}
</script>
<?php
$conn = null;

$_SESSION['error']= $_SESSION['success'] ="";
unset ($_SESSION['error']);
unset ($_SESSION['success']);
?>
<style>
    .searchForm label{
        display: block;
    width: 100%;
    font-size: 14px;
    margin-bottom: 5px;
    }
    </style>
