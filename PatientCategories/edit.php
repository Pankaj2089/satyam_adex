<?php
if (isset($_POST['rowID']) && $_POST['rowID'] > 0 ) {
	$sql = "SELECT * from patient_categories where id = ".$_POST['rowID']." limit 1";
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
              <div class="col-lg-12 col-md-12 col-xs-12">
                <div class="form-group">
                  <label for="exampleInputUsername1" class="statusLabel">Status</label>
                  <input type="checkbox" class="form-control checkboxes" id="status" value="1" name="status" <?php echo isset($recData['status']) && $recData['status'] == 1?'checked':''; ?>>
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
