<?php
if (isset($_POST['rowID']) && $_POST['rowID'] > 0 ) {
	$sql = "SELECT * from rates where id = ".$_POST['rowID']." limit 1";
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
              <label for="exampleInputUsername1">Rate For</label>
              <select class="form-control" id="rate_for" name="rate_for" style="width:100%">
                <option value="Govt." <?php echo $recData['rate_for'] == 'Govt.' ? "selected":"";?>> Govt. </option>
                <option value="Private" <?php echo $recData['rate_for'] == 'Private' ? "selected":"";?>> Private </option>
              </select>
            </div>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12 mb-4">
            <div class="form-group">
              <label>Patient Category</label>
              <select class="form-control" id="patient_category" name="patient_category" style="width:100%">
                <option value="0"> Select Patient Category </option>
                <?php
                    $rtosql = "SELECT * from patient_categories where status = 1";
                    $statement = $conn->prepare($rtosql);
                    $statement->execute();
                    $statement->setFetchMode(PDO::FETCH_ASSOC);
                    $catRows =$statement->fetchAll();
                    if(count($catRows) > 0){
                        foreach($catRows as $catRow){
                            echo '<option value ="'.$catRow['id'].'" '.($recData['patient_category'] == $catRow['id'] ? "selected":"").'> '.$catRow['title'].' </option>';
                        }
                    }
                    ?>
              </select>
            </div>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12 mb-4">
            <div class="form-group">
               <div class="col-lg-12 col-md-12 col-xs-12 mb-4"><h5>Blood Components</h5></div>
                <div class="col-lg-12 col-md-12 col-xs-12 mb-4">
             <?php
			 #get blood_components Data
			 $bloodComponents = array();
			if(isset($recData['blood_components']) && !empty($recData['blood_components'])){
				$bloodComponentArr = json_decode($recData['blood_components']);
				if(count($bloodComponentArr) > 0 ){
					foreach($bloodComponentArr as $bloodComponentData){
						$bloodComponentDataFields = explode('::', $bloodComponentData);
						if(isset($bloodComponentDataFields[0])){
							$bloodComponents[$bloodComponentDataFields[0]] = number_format($bloodComponentDataFields[1],2);
						}
					}
				}
			}
					
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
					  <input type="text" maxlength="6" class="form-control rateData" id="'.$row['id'].'" name="bloodComponentPrice[]" id="exampleInputUsername2" placeholder="Enter Price" value="'.(isset($bloodComponents[$row['id']])? $bloodComponents[$row['id']]:'').'">
					</div>
				  </div>';
				}
			}
			?>
            </div>
            </div>
        </div>
     </div>
    </div>
    </div>
   <script type="text/javascript" src="assets/js/custom.js"></script>
<script>
$(document).ready(function(){
	$('.rateData').filter_input({regex:'[0-9.]'});
});
</script> 
<?php
}else{
	echo 'Error'; die;
}
?>
