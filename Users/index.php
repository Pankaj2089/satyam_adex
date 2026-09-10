<?php
if ($_SESSION['Type'] != 'Admin') {
    echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>';die;
}
$sqlData = "where type = 'User' ";
$postName = $postEmail = $Name = $Email = $Mobile ='';
if(isset($_POST["action"]) && $_POST["action"] =="add"){
  
    $Name = ucwords($_POST["name"]);
	$Email = trim(strtolower($_POST["email"]));
	$mobile = $_POST["mobile"];
	$error = '';
	if(empty($Name)){ $error = 'Please enter name.'; }
	if(empty($Email)){ $error = 'Please enter father name.'; }
	
	if(empty($error)){
		$dataDumpsql = "SELECT * from users where email = '".$Email."' limit 1";
		$statement = $conn->prepare($dataDumpsql);
		$statement->execute();
		if($statement->rowCount() <= 0){
			$pass = rand(00000000,99999999);
			$Password = md5($pass);
			$createdAt = date('Y-m-d H:i:s');
			
			$UserPermissions = $_POST["user_permissions"];
	
			$userRoles = '';
			if(count($UserPermissions) > 0){
				$userRolesArr = array();
				foreach($UserPermissions as $key => $UserPermission){
					$userRolesArr[] = $UserPermission;
				}
				if(count($userRolesArr) > 0){
					$userRoles = json_encode($userRolesArr);
				}
			}
						
			$sql = "INSERT INTO users (type, name, email, password, user_permissions, mobile, pass_string, created_at) VALUES (?,?,?,?,?,?,?,?)";
			$stmt= $conn->prepare($sql);
			$stmt->execute(['User', $Name, $Email, $Password, $userRoles, $mobile, $pass, $createdAt]);    
			$Name = "";
			$Email = "";
			$successmsg = "User information has been added successfully.";
			$_SESSION['success'] = $successmsg;
			echo '<script>window.location="'.LINK_PATH.'users.html"</script>';die;
		}else{
			$errormsg = "User Account already exists";
			$_SESSION['error'] = $errormsg;
		} 
   }else{
    $_SESSION['error'] = $error;
   }
}
if(isset($_POST["action"]) && $_POST["action"] =="edit"){
   	$Name = ucwords($_POST["name"]);
	$Email = trim(strtolower($_POST["email"]));
	$mobile = $_POST["mobile"];
	$error = '';
	if(empty($Name)){ $error = 'Please enter name.'; }
	if(empty($Email)){ $error = 'Please enter  father name.'; }
	
	if(empty($error)){
		$rowID =  $_POST['rowID'];
		$dataDumpsql = "SELECT * from users where email = '".$Email."' AND id != ".$rowID." limit 1";
		$statement = $conn->prepare($dataDumpsql);
		$statement->execute();
		if($statement->rowCount() <= 0){
			
			$UserPermissions = $_POST["user_permissions"];
			$userRoles = '';
			if(count($UserPermissions) > 0){
				$userRolesArr = array();
				foreach($UserPermissions as $key => $UserPermission){
					$userRolesArr[] = $UserPermission;
				}
				if(count($userRolesArr) > 0){
					$userRoles = json_encode($userRolesArr);
				}
			}
						
			$createdAt = date('Y-m-d H:i:s');
			$sql = "UPDATE users SET name=?, email=?,  mobile=?, user_permissions=? WHERE id=?";
			$stmt= $conn->prepare($sql);
			$stmt->execute([$Name, $Email, $mobile, $userRoles, $rowID]);  
			$Title = "";
			$Email = "";
			$successmsg = "User information has been updated successfully.";
			$_SESSION['success'] = $successmsg;
			echo '<script>window.location="'.LINK_PATH.'users.html"</script>';die;
		}else{
			$errormsg = "User Account already exists";
			$_SESSION['error'] = $errormsg;
			echo '<script>window.location="'.LINK_PATH.'users.html"</script>';die;
		} 
   }else{
    $_SESSION['error'] = $error;
   }
}
if(isset($_GET["action"]) && $_GET["action"] =="search"){
    $postName = $_GET["name"];
	$postEmail = $_GET["email"];
    if(!empty($postName)){
        $sqlData .= " and name LIKE '%".$postName."%'";
    }
	if(!empty($postEmail)){
        $sqlData .= " and email LIKE '%".$postEmail."%'";
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

$sql = "SELECT * from users $sqlData order by created_at desc limit $starter, $perpage ";
$sql2 = "SELECT * from users $sqlData order by created_at desc";

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

$urlPrmas = "&action=". $action."&name=".$postName."&email=".$postEmail;
?>
<!-- partial -->

<div class="main-panel">
<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title"> <span class="page-title-icon bg-gradient-primary text-white mr-2"> <i class="mdi mdi-format-list-bulleted menu-icon"></i> </span> Users Management </h3>
  </div>
  <div class="row">
    <div class="col-md-4 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add New User</h4>
          <form class="forms-sample" method="post" action="">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
              <label for="exampleInputUsername1">Name</label>
              <input type="text" class="form-control" id="name" name="name" required placeholder="Enter Name" value="<?php echo $Name; ?>">
            </div>
            <div class="form-group">
              <label for="exampleInputUsername1">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" required placeholder="Enter Email Address" value="<?php echo $Email; ?>">
            </div>
            <div class="form-group">
              <label for="exampleInputUsername1">Mobile Number</label>
              <input type="number" class="form-control" id="mobile" name="mobile" placeholder="Enter Mobile Number" value="<?php echo $Mobile; ?>">
            </div>
            <div class="form-group">
              <h5>User Permissions</h5>
              <div class="form-group row" style="margin-top:30px">
             <?php
				$UserPormissions = ['Blood Component Type', 'Petient Categories', 'Rates', 'Financial Year', 'Registration'];
				foreach($UserPormissions as $key=>$UserPormission){
				echo '
				
					<label for="exampleInputUsername'.$key.'" class="col-sm-5">'.$UserPormission.'</label>
					<div class="col-sm-7" style="margin-bottom:10px">
					  <input type="checkbox" class="form-control checkboxes" name="user_permissions[]" id="exampleInputUsername'.$key.'" value="'.$UserPormission.'">
					</div>
				  ';
				}
				?>
                </div>
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
                <label>User Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="<?php echo $postName; ?>">
              </div>
              <div class="input-group mb-2 mr-sm-2">
                <label>Email Address</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email Address" value="<?php echo $postEmail; ?>">
              </div>
              <button type="submit" class="btn btn-gradient-primary mb-2" style="margin-top: 21px;">Search</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">User List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th> # </th>
                    <th> Name </th>
                    <th> Email Address </th>
                    <th> Mobile Number </th>
                    <th> Password </th>
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
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['mobile']; ?></td>
                    <td><?php echo $row['pass_string']; ?></td>
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
                      <a class="pageLink" href="<?php echo "users.html?page=1".$urlPrmas;?>">First Page</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page>1){ ?>
                      <a class="pageLink" href="<?php echo "users.html?page=".($page-1).$urlPrmas;?>" >Previous</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                      <a class="pageLink" href="<?php echo "users.html?&page=".$i.$urlPrmas;?>" ><?php echo $i; ?></a>&nbsp;&nbsp;
                      <?php 
                            }
                            }
                            if($totalPages > $page){ ?>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="pageLink" href="<?php echo "users.html?page=".($page+1).$urlPrmas;?>" >Next</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page <> $totalPages){ ?>
                      <a class="pageLink" href="<?php echo "users.html?page=".($totalPages).$urlPrmas; ?>" >Last Page</a>&nbsp;&nbsp;&nbsp;&nbsp;<strong>(</strong><?php echo $page .'&nbsp;&nbsp;<strong>of</strong>&nbsp;&nbsp;'.$totalPages ?><strong>)</strong>
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
          <h5 class="modal-title">Update User Information</h5>
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
		url: "<?php echo LINK_PATH.'edit-user.html' ?>",
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
