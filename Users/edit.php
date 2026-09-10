<?php
if (isset($_POST['rowID']) && $_POST['rowID'] > 0 ) {
	$sql = "SELECT * from users where id = ".$_POST['rowID']." limit 1";
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
              <label for="exampleInputUsername1">Name</label>
              <input type="text" class="form-control" id="name" name="name" required placeholder="Enter Name" value="<?php echo isset($recData['name'])?$recData['name']:''; ?>" style="width:100%">
            </div>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12 mb-4">
            <div class="form-group">
              <label for="exampleInputUsername1">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" required placeholder="Enter Email Address" value="<?php echo isset($recData['email'])?$recData['email']:''; ?>" style="width:100%">
            </div>
            </div>
             <div class="col-lg-12 col-md-12 col-xs-12 mb-4">
            <div class="form-group">
              <label for="exampleInputUsername1">Mobile Number</label>
              <input type="number" class="form-control" id="mobile" name="mobile" placeholder="Enter Mobile Number" value="<?php echo isset($recData['mobile'])?$recData['mobile']:''; ?>">
            </div>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12 mb-4">
            <div class="form-group" >
              <h5>User Permissions</h5>
              <div class="form-group row" style="margin-top:30px">
             <?php
			 	$myPermissions = array();
				if(isset($recData['user_permissions']) && !empty($recData['user_permissions'])){
					$myPermissions = json_decode($recData['user_permissions']);
				}
				$UserPormissions = ['Blood Component Type', 'Petient Categories', 'Rates', 'Financial Year', 'Registration'];
				foreach($UserPormissions as $key => $UserPormission){
				echo '
				
					<label for="exampleInputUsername'.$key.'" class="col-sm-3">'.$UserPormission.'</label>
					<div class="col-sm-7" style="margin-bottom:10px">
					  <input type="checkbox" class="form-control checkboxes" name="user_permissions[]" id="exampleInputUsername'.$key.'" value="'.$UserPormission.'" '.(in_array($UserPormission,$myPermissions)?"checked":"").'>
					</div>
				  ';
				}
				?>
                </div>
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
