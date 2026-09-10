<?php
if(checkPermissions('Registration') != true){
	echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>';die;
}
$sqlData = "where id > 0";
$PostName = $PostAadharNo = $PostMobileNo = '';

if(isset($_GET["action"]) && $_GET["action"] =="search"){
    $PostName = $_GET["name"];
	$PostAadharNo = $_GET["aadhar_no"];
	$PostMobileNo = $_GET["mobile_no"];
    if(!empty($PostName)){
        $sqlData .= " and name LIKE '%".$PostName."%'";
    }
	if(!empty($PostAadharNo)){
        $sqlData .= " and aadhar_no = '".$PostAadharNo."'";
    }
	if(!empty($PostMobileNo)){
        $sqlData .= " and mobile = '".$PostMobileNo."'";
    }
}
//get records
$perpage = 10;
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
$urlPrmas = "&action=". $action."&name=".$PostName."&aadhar_no=".$PostAadharNo."&mobile_no=".$PostMobileNo;
?>
<!-- partial -->

<div class="main-panel">
<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title"> <span class="page-title-icon bg-gradient-primary text-white mr-2"> <i class="mdi mdi-format-list-bulleted menu-icon"></i> </span> Invoices Management </h3>
    <a href="<?php echo LINK_PATH.'add-registration.html'; ?>" class="btn btn-sm btn-gradient-success mb-2 pull-right" style="margin-top: 21px;">Add New Invoice</a>
  </div>
  <div class="row">
    <div class="col-md-12 ">
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
                <label>Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="<?php echo $PostName; ?>">
              </div>
              <div class="input-group mb-2 mr-sm-2">
                <label>Mobile No.</label>
                <input type="text" class="form-control" id="mobile_no" name="mobile_no" placeholder="Enter Mobile No." value="<?php echo $PostMobileNo; ?>">
              </div>
              <div class="input-group mb-2 mr-sm-2 d-none">
                <label>Addhar No.</label>
                <input type="text" class="form-control" id="aadhar_no" name="aadhar_no" placeholder="Enter Addhar No." value="<?php echo $PostAadharNo; ?>">
              </div>
              <button type="submit" class="btn btn-gradient-primary mb-2" style="margin-top: 21px;">Search</button>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Invoices List</h4>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th> # </th>
                    <th> Bill No. </th>
                    <th> Name </th>
                    <th> Address </th>
                    <th> Mobile </th>
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
                    ?>
                  <tr>
                    <td><?php echo $key+1; ?></td>
                    <td><?php echo $row['billing_no']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['mobile']; ?></td>
                    <td>₹<?php echo number_format($row['total_amount'],2); ?></td>
                    <td><a href="<?php echo LINK_PATH ?>edit-registration.html?rowID=<?php echo base64_encode($row['id']) ?>" class="btn btn-xs btn-primary"> <i class="mdi mdi-pencil-box"></i></a> <a href="<?php echo LINK_PATH ?>print-invoice.html?rowID=<?php echo base64_encode($row['id']) ?>" class="btn btn-xs btn-success" target="_blank"> <i class="mdi mdi-printer"></i></a></td>
                  </tr>
                  <?php 
                }
                ?>
                  <tr style="padding-bottom:20px">
                    <td colspan="9" class="admlsttxt">Result pages
                    <?php 
                            if($page<>1){ ?>
                      <a class="pageLink" href="<?php echo "registration.html?page=1".$urlPrmas;?>">First Page</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page>1){ ?>
                      <a class="pageLink" href="<?php echo "registration.html?page=".($page-1).$urlPrmas;?>" >Previous</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                      <a class="pageLink" href="<?php echo "registration.html?&page=".$i.$urlPrmas;?>" ><?php echo $i; ?></a>&nbsp;&nbsp;
                      <?php 
                            }
                            }
                            if($totalPages > $page){ ?>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a class="pageLink" href="<?php echo "registration.html?page=".($page+1).$urlPrmas;?>" >Next</a>
                      <?php } ?>
                      &nbsp;&nbsp;
                      <?php if ($page <> $totalPages){ ?>
                      <a class="pageLink" href="<?php echo "registration.html?page=".($totalPages).$urlPrmas; ?>" >Last Page</a>&nbsp;&nbsp;&nbsp;&nbsp;<strong>(</strong><?php echo $page .'&nbsp;&nbsp;<strong>of</strong>&nbsp;&nbsp;'.$totalPages ?><strong>)</strong>
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
