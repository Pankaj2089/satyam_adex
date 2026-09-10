<?php
if(checkPermissions('Petient Categories') != true){
	echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>';die;
}
$sqlData = "where status != 3 ";
$postName = $Title = $Status ='';
if(isset($_POST["action"]) && $_POST["action"] =="add"){
   if(isset($_POST["title"]) && !empty($_POST["title"])){
    $Title = ucwords($_POST["title"]);
	$Status = isset($_POST["status"]) && $_POST["status"] = 1?1:2;
	$dataDumpsql = "SELECT * from patient_categories where title = '".$postName."' limit 1";
	$statement = $conn->prepare($dataDumpsql);
	$statement->execute();
	if($statement->rowCount() <= 0){
		$createdAt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO patient_categories (title, status, created_at) VALUES (?,?,?)";
		$stmt= $conn->prepare($sql);
		$stmt->execute([$Title, $Status, $createdAt]);    
		$Title = "";
		$rtoEmail = "";
		$successmsg = "Patient category has been added successfully.";
		$_SESSION['success'] = $successmsg;
		echo '<script>window.location="'.LINK_PATH.'patient-categories.html"</script>';die;
	}else{
		$errormsg = "Patient category already exists";
		$_SESSION['error'] = $errormsg;
	} 
   }else{
    $errormsg = "Please enter category Title";
    $_SESSION['error'] = $errormsg;
   }
}
if(isset($_POST["action"]) && $_POST["action"] =="edit"){
   if(isset($_POST["title"]) && !empty($_POST["title"])){
		$Title = ucwords($_POST["title"]);
		$Status = isset($_POST["status"]) && $_POST["status"] = 1?1:2;
		$createdAt = date('Y-m-d H:i:s');
		$rowID =  $_POST['rowID'];
		$sql = "UPDATE patient_categories SET title=?, status=? WHERE id=?";
		$stmt= $conn->prepare($sql);
		$stmt->execute([$Title, $Status, $rowID]);  
		$Title = "";
		$successmsg = "Patient category has been updated successfully.";
		$_SESSION['success'] = $successmsg;
		echo '<script>window.location="'.LINK_PATH.'patient-categories.html"</script>';die;
   }else{
    $errormsg = "Please enter Component Title";
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

$sql = "SELECT * from patient_categories $sqlData order by created_at desc limit $starter, $perpage ";
$sql2 = "SELECT * from patient_categories $sqlData order by created_at desc";

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
    <h3 class="page-title"> <span class="page-title-icon bg-gradient-primary text-white mr-2"> <i class="mdi mdi-format-list-bulleted menu-icon"></i> </span> Patient Categories Management </h3>
  </div>
  <div class="row">
    <div class="col-md-4 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add New Patient Category</h4>
          <form class="forms-sample" method="post" action="">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
              <label for="exampleInputUsername1">Title</label>
              <input type="text" class="form-control" id="title" name="title" required placeholder="Enter Title" value="<?php echo $Title; ?>">
            </div>
            <div class="form-group">
              <label for="exampleInputUsername1">Status</label>
              <input type="checkbox" class="form-control checkboxes" id="status" value="1" name="status" checked>
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
            <h4 class="card-title">Patient Categories List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th> # </th>
                    <th> Title </th>
                    <th> Status </th>
                    <th> Created Date </th>
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
                    <td><label class="badge badge-<?php echo $row['status'] == 1 ?'success':'danger'; ?>"><?php echo $row['status']  ==1 ?'Active':'Inactive'; ?></label></td>
                    <td><?php echo isset($row['created_at'])?date('d F, Y h:i A',strtotime($row['created_at'])):'Not Available'?></td>
                    <td><a href="javascript:void(0);" onclick="editRecord('<?php echo $row['id']; ?>');" class="btn btn-xs btn-primary"> <i class="mdi mdi-pencil-box"></i></a></td>
                  </tr>
                  <?php 
                }
                ?>
                  <tr style="padding-bottom:20px">
                    <td colspan="9" class="admlsttxt">Result pages
                    <?php 
                            if($page<>1){ ?>
                      <a class="pageLink" href="<?php echo "patient-categories.html?page=1".$urlPrmas;?>">First Page</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page>1){ ?>
                      <a class="pageLink" href="<?php echo "patient-categories.html?page=".($page-1).$urlPrmas;?>" >Previous</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                      <a class="pageLink" href="<?php echo "patient-categories.html?&page=".$i.$urlPrmas;?>" ><?php echo $i; ?></a>&nbsp;&nbsp;
                      <?php 
                            }
                            }
                            if($totalPages > $page){ ?>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="pageLink" href="<?php echo "patient-categories.html?page=".($page+1).$urlPrmas;?>" >Next</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page <> $totalPages){ ?>
                      <a class="pageLink" href="<?php echo "patient-categories.html?page=".($totalPages).$urlPrmas; ?>" >Last Page</a>&nbsp;&nbsp;&nbsp;&nbsp;<strong>(</strong><?php echo $page .'&nbsp;&nbsp;<strong>of</strong>&nbsp;&nbsp;'.$totalPages ?><strong>)</strong>
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
          <h5 class="modal-title">Update Category</h5>
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
		url: "<?php echo LINK_PATH.'edit-patient-category.html' ?>",
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
