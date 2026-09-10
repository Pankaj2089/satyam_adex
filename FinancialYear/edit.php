<?php
if (isset($_POST['rowID']) && $_POST['rowID'] > 0 ) {
	$sql = "SELECT * from financial_years where id = ".$_POST['rowID']." limit 1";
	$statement = $conn->prepare($sql);
	$statement->execute();
	$statement->setFetchMode(PDO::FETCH_ASSOC);
	$recData =$statement->fetch();
?>
<!-- partial -->

<div class="row">
  <div class="col-md-12 ">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-xs-12 mb-4">
            <div class="form-group">
              <label for="exampleInputUsername1">Title</label>
              <input type="text" class="form-control" id="title" name="title" style="width:100%"  placeholder="Enter Title" value="<?php echo isset($recData['title'])?$recData['title']:''; ?>" required>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-xs-6 mb-4">
          <div class="form-group">
            <label for="exampleInputUsername1">Start Date</label>
            <input type="date" class="form-control" id="start_date" name="start_date" required value="<?php echo isset($recData['start_date'])?$recData['start_date']:''; ?>" style="width:100%">
          </div>
          </div>
           <div class="col-lg-6 col-md-6 col-xs-6 mb-4">
          <div class="form-group">
            <label for="exampleInputUsername1">End Date</label>
            <input type="date" class="form-control" id="end_date" name="end_date" required value="<?php echo isset($recData['end_date'])?$recData['end_date']:''; ?>" style="width:100%">
          </div>
          </div>
           <div class="col-lg-6 col-md-6 col-xs-6 mb-4">
          <div class="form-group">
            <label for="exampleInputUsername1">Start Billing Number</label>
            <input type="number" class="form-control" id="start_billing_no" name="start_billing_no" required placeholder="Enter Start Billing Number" value="<?php echo isset($recData['start_billing_no'])?$recData['start_billing_no']:''; ?>" style="width:100%">
          </div>
          </div>
           <div class="col-lg-6 col-md-6 col-xs-6 mb-4">
          <div class="form-group">
            <label for="exampleInputUsername1">Current Billing Number</label>
            <input type="number" class="form-control" id="current_billing_no" name="current_billing_no" required placeholder="Enter Current Billing Number" value="<?php echo isset($recData['current_billing_no'])?$recData['current_billing_no']:''; ?>" style="width:100%">
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
}else{
	echo 'Error'; die;
}
?>
