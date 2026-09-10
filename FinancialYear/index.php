<?php
if(checkPermissions('Financial Year') != true){
	echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>';die;
}
$sqlData = "where status != 3 ";
$postName = $Title = $Status = $StartDate = $EndDate = $StartBillingNo = $CurrentBillingNo ='';
if(isset($_POST["action"]) && $_POST["action"] =="add"){
   if(isset($_POST["title"]) && !empty($_POST["title"])){
	   
	   #inactive other financial records
	    $fasql = "SELECT id from financial_years where status = 1";
		$statement = $conn->prepare($fasql);
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$faRows =$statement->fetchAll();
		if(count($faRows) > 0){
			foreach($faRows as $faRow){
				$endDate = date('Y-m-d');
				$FAUpdateSql = "UPDATE financial_years SET status=?, end_date=? WHERE id=?";
				$stmt= $conn->prepare($FAUpdateSql);
				$stmt->execute([2, $endDate, $faRow['id']]);  
			}
		}
	   
		$Title = ucwords($_POST["title"]);
		$StartDate = $_POST["start_date"];
		$EndDate = $_POST["end_date"];
		$StartBillingNo = $_POST["start_billing_no"];
		$CurrentBillingNo = $_POST["current_billing_no"];
		$Status = 1;

		$createdAt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO financial_years (title, start_date, end_date, start_billing_no, current_billing_no, status, created_at) VALUES (?,?,?,?,?,?,?)";
		$stmt= $conn->prepare($sql);
		$stmt->execute([$Title, $StartDate, $EndDate, $StartBillingNo, $CurrentBillingNo, $Status, $createdAt]);    
		$Title = "";
		$rtoEmail = "";
		$successmsg = "Financial Year details has been added successfully.";
		$_SESSION['success'] = $successmsg;
		echo '<script>window.location="'.LINK_PATH.'financial-year.html"</script>';die;
   }else{
    $errormsg = "Please enter Financial Year Title";
    $_SESSION['error'] = $errormsg;
   }
}
if(isset($_POST["action"]) && $_POST["action"] =="edit"){
   if(isset($_POST["title"]) && !empty($_POST["title"])){
		$Title = ucwords($_POST["title"]);
		$StartDate = $_POST["start_date"];
		$EndDate = $_POST["end_date"];
		$StartBillingNo = $_POST["start_billing_no"];
		$CurrentBillingNo = $_POST["current_billing_no"];
		$createdAt = date('Y-m-d H:i:s');
		$rowID =  $_POST['rowID'];
		
		$sql = "UPDATE financial_years SET title=?, start_date=?, end_date=?, start_billing_no=?, current_billing_no=? WHERE id=?";
		$stmt= $conn->prepare($sql);
		$stmt->execute([$Title, $StartDate, $EndDate, $StartBillingNo, $CurrentBillingNo, $rowID]);  
		$Title = "";
		$successmsg = "Financial Year details  has been updated successfully.";
		$_SESSION['success'] = $successmsg;
		echo '<script>window.location="'.LINK_PATH.'financial-year.html"</script>';die;
   }else{
		$errormsg = "Please enter Financial Year Title";
		$_SESSION['error'] = $errormsg;
   }
}
if(isset($_GET["action"]) && $_GET["action"] =="search"){
    $postName = $_GET["name"];
    if(!empty($postName)){
        $sqlData .= " and title LIKE '%".$postName."%'";
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

$sql = "SELECT * from financial_years $sqlData order by created_at desc limit $starter, $perpage ";
$sql2 = "SELECT * from financial_years $sqlData order by created_at desc";

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

$urlPrmas = "&action=". $action."&name=".$postName;
?>
<!-- partial -->

<div class="main-panel">
<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title"> <span class="page-title-icon bg-gradient-primary text-white mr-2"> <i class="mdi mdi-format-list-bulleted menu-icon"></i> </span> Financial Year Management </h3>
  </div>
  <div class="row">
    <div class="col-md-4 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add New Financial Year</h4>
          <form class="forms-sample" method="post" action="">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
              <label for="exampleInputUsername1">Title</label>
              <input type="text" class="form-control" id="title" name="title" required placeholder="Enter Title" value="<?php echo $Title; ?>">
            </div>
           
           <div class="form-group">
              <label for="exampleInputUsername1">Start Date</label>
              <input type="date" class="form-control" id="start_date" name="start_date" required value="<?php echo $StartDate; ?>">
            </div>
            <div class="form-group">
              <label for="exampleInputUsername1">End Date</label>
              <input type="date" class="form-control" id="end_date" name="end_date" required value="<?php echo $EndDate; ?>">
            </div>
            <div class="form-group">
              <label for="exampleInputUsername1">Start Billing Number</label>
              <input type="number" class="form-control" id="start_billing_no" name="start_billing_no" required placeholder="Enter Start Billing Number" value="<?php echo $StartBillingNo; ?>">
            </div>
            <div class="form-group">
              <label for="exampleInputUsername1">Current Billing Number</label>
              <input type="number" class="form-control" id="current_billing_no" name="current_billing_no" required placeholder="Enter Current Billing Number" value="<?php echo $CurrentBillingNo; ?>">
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
                <label>Title</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Title" value="<?php echo $postName; ?>">
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
                    <th> Title </th>
                    <th> Start Date </th>
                    <th> End Date </th>
                    <th> Start Billing No. </th>
                    <th> Current Billing No. </th>
                    <th> Status </th>
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
                    ?>
                  <tr>
                    <td><?php echo $key+1; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo isset($row['start_date'])?date('d F, Y',strtotime($row['start_date'])):'Not Available'?></td>
                    <td><?php echo isset($row['end_date'])?date('d F, Y',strtotime($row['end_date'])):'Not Available'?></td>
                    <td><?php echo $row['start_billing_no']; ?></td>
                    <td><?php echo $row['current_billing_no']; ?></td>
                    <td><label class="badge badge-<?php echo $row['status'] == 1 ?'success':'danger'; ?>"><?php echo $row['status']  ==1 ?'Active':'Inactive'; ?></label></td>
                    <td>
                    <?php
					if($row['status'] == 1){
					?>
                    <a href="javascript:void(0);" onclick="editRecord('<?php echo $row['id']; ?>');" class="btn btn-xs btn-primary"> <i class="mdi mdi-pencil-box"></i></a>
                    <?php }else{
						?>
                        <a href="javascript:void(0);" class="btn btn-xs btn-secondary"> <i class="mdi mdi-pencil-box"></i></a>
                        <?php }  ?>
                    </td>
                  </tr>
                  <?php 
                }
                ?>
                  <tr style="padding-bottom:20px">
                    <td colspan="9" class="admlsttxt">Result pages
                    <?php 
                            if($page<>1){ ?>
                      <a class="pageLink" href="<?php echo "financial-year.html?page=1".$urlPrmas;?>">First Page</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page>1){ ?>
                      <a class="pageLink" href="<?php echo "financial-year.html?page=".($page-1).$urlPrmas;?>" >Previous</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                      <a class="pageLink" href="<?php echo "financial-year.html?&page=".$i.$urlPrmas;?>" ><?php echo $i; ?></a>&nbsp;&nbsp;
                      <?php 
                            }
                            }
                            if($totalPages > $page){ ?>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="pageLink" href="<?php echo "financial-year.html?page=".($page+1).$urlPrmas;?>" >Next</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page <> $totalPages){ ?>
                      <a class="pageLink" href="<?php echo "financial-year.html?page=".($totalPages).$urlPrmas; ?>" >Last Page</a>&nbsp;&nbsp;&nbsp;&nbsp;<strong>(</strong><?php echo $page .'&nbsp;&nbsp;<strong>of</strong>&nbsp;&nbsp;'.$totalPages ?><strong>)</strong>
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
<script>
function editRecord(rowID){
	$('#rowID').val(rowID);
	$('#myLogoModal').modal('show');
	$.ajax({
		type: "POST",
		url: "<?php echo LINK_PATH.'edit-financial-year.html' ?>",
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
