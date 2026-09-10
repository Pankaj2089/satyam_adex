<?php
if(checkPermissions('Registration') != true){
	echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>';die;
}
if (isset($_GET['rowID']) && !empty($_GET['rowID']) ) {
	$rowID = base64_decode($_GET['rowID']);
	$sql = "SELECT * from registrations where id = ".$rowID." limit 1";
	$statement = $conn->prepare($sql);
	$statement->execute();
	$statement->setFetchMode(PDO::FETCH_ASSOC);
	$recData =$statement->fetch();
	if(!isset($recData['id'])){
		echo '<script>window.location="'.LINK_PATH.'registration.html"</script>';die;
	}
}else{
	echo '<script>window.location="'.LINK_PATH.'registration.html"</script>';die;
}
$PostPartGSTNo = $recData['party_gst_no'];
$PostName = $recData['name'];
$PostFName = "";
$PostAddress = $recData['address'];
$PostMobile = $recData['mobile'];
$PostAadharNo = $recData['aadhar_no'];
$validCertificateNo = $recData['valid_certificate_no'];
$PostOrderNo = $recData['order_no'];
$PostOrderDate = $recData['order_date'];
$SaleType = $recData['sale_type'];
$CGST = $recData['cgst'];
$SGST = $recData['sgst'];
$IGST = $recData['igst'];
$PaymentMode = $recData['payment_mode'];

$Note = $recData['note'];

if(isset($_POST["action"]) && $_POST["action"] =="edit"){
	
    $PostPartGSTNo = strtoupper($_POST["party_gst_no"]);
	$PostName = ucwords($_POST["name"]);
	$PostFName = "";
	$PostAddress = $_POST["address"];
	$PostMobile = $_POST["mobile"];
	$PostAadharNo = $_POST["aadhar_no"];
	$validCertificateNo = $_POST['valid_certificate_no'];
	$PostOrderNo = strtoupper($_POST['order_no']);
	$PostOrderDate = $_POST['order_date'];
	$SaleType = $_POST['sale_type'];
	$PaymentMode = $_POST['payment_mode'];
	$Note = $_POST['note'];
	
	$SGST = 0;
	$CGST = 0;
	$IGST = 0;
	if($_POST['sale_type'] == 'State Sale'){ 
		$SGST = $_POST['sgst'];
		$CGST = $_POST['cgst'];
	}
	if($_POST['sale_type'] == 'Inter State Sale'){ 
		$IGST = $_POST['igst'];
	}
		
	$error = '';
	if(empty($PostName)){ $error = 'Please enter name.'; }
	if(empty($PostMobile)){ $error = 'Please enter mobile number.'; }
	
	if(empty($error)){
		$rowID = base64_decode($_GET['rowID']);
		$createdAt = date('Y-m-d H:i:s');
		$sql = "UPDATE registrations SET party_gst_no=?, order_no=?, order_date=?, name=?, fname=?, address=? ,mobile=?, aadhar_no=?, valid_certificate_no=?, sale_type=?, cgst=?, sgst=?, igst=?, payment_mode=?, note=?  WHERE id=?";
		$stmt= $conn->prepare($sql);
		$stmt->execute([$PostPartGSTNo, $PostOrderNo, $PostOrderDate, $PostName, $PostFName, $PostAddress, $PostMobile, $PostAadharNo,  $validCertificateNo, $SaleType, $CGST, $SGST, $IGST, $PaymentMode, $Note,  $rowID]); 
		
		$bloodComponents = $_POST["component"];
		$bloodComponentQty = $_POST["bloodComponentQty"];
		$componentPrice = $_POST["componentPrice"];
		$componentNote = $_POST["componentNote"];
		
		$bloodComponentData = '';
		$finalAmount = 0;
		
		$componentIDs = '';
		$updateInfoIDS = array(0);
		if(count($bloodComponents) > 0){
			$ComponentQty = 1;
			$ComponentPrice = 0;
			$componentIDs = implode(',',$bloodComponents);
			foreach($bloodComponents as $key => $bloodComponent){
				$updateInfoIDS[] = $bloodComponent;
				if(isset($bloodComponentQty[$key]) && $bloodComponentQty[$key] > 0){$ComponentQty= intval($bloodComponentQty[$key]);}
				$infoDataSql = "SELECT * from registration_informations where component_id = ".$bloodComponent." and reg_id = ".$rowID." limit 1";
				$statement = $conn->prepare($infoDataSql);
				$statement->execute();
				$statement->setFetchMode(PDO::FETCH_ASSOC);
				$infoDataRow =$statement->fetch();
				
				#get price
				if(isset($componentPrice[$key]) && $componentPrice[$key] > 0){ $ComponentPrice= floatval($componentPrice[$key]);}
				$price = $ComponentPrice;
				$amount = number_format($price,2);
				$amount = str_replace(',','',$amount);
				$price = str_replace(',','',$price);
				$totalAmount = number_format(floatval($price)*floatval($ComponentQty),2);
				$totalAmount = str_replace(',','',$totalAmount);
				$finalAmount += $totalAmount;
				$ComponentNotes = $componentNote[$key];
				
				if(isset($infoDataRow['id'])){
					$upsql = "UPDATE registration_informations SET component_id=?, component_quantity=?, amount=?, total_amount=?, note=? WHERE id=?";
					$stmt= $conn->prepare($upsql);
					$stmt->execute([$bloodComponent, $ComponentQty, $amount, $totalAmount, $ComponentNotes, $infoDataRow['id']]); 
				}else{
					$sql = "INSERT INTO registration_informations (reg_id, component_id, component_quantity, amount, total_amount, note,  created_at) VALUES (?,?,?,?,?,?,?)";
					$stmt= $conn->prepare($sql);
					$stmt->execute([$rowID, $bloodComponent, $ComponentQty, $amount, $totalAmount, $ComponentNotes,  $createdAt]); 
				}
			}
		}  
		
		#update final invoice
		$CGSTAmt = 0;
		if($CGST > 0){
			$CGSTAmt = floatval($finalAmount/100)*$CGST;
		}
		$SGSTAmt = 0;
		if($SGST > 0){
			$SGSTAmt = floatval($finalAmount/100)*$SGST;
		}
		$IGSTAmt = 0;
		if($IGST > 0){
			$IGSTAmt = floatval($finalAmount/100)*$IGST;
		}
		$finalAmount = floatval($finalAmount) + floatval($CGSTAmt) + floatval($SGSTAmt) + floatval($IGSTAmt);
		
		$sql = "UPDATE registrations SET total_amount=?, component_ids=? WHERE id=?";
		$stmt= $conn->prepare($sql);
		$stmt->execute([$finalAmount, $componentIDs,  $rowID]);
		
		$in  = str_repeat('?,', count($updateInfoIDS) - 1) . '?';
		$sqlData = 'DELETE FROM registration_informations WHERE reg_id=? AND component_id NOT IN ('.$in.')'; 
		$statement = $conn->prepare($sqlData);
		$params = array_merge([$rowID], $updateInfoIDS);
		$statement->execute($params);
		
		$successmsg = "Registration information has been updated successfully.";
		$_SESSION['success'] = $successmsg;
		echo '<script>window.location="'.LINK_PATH.'registration.html"</script>';die;
	}else{
		$_SESSION['error'] = $error;
	}
}
?>
<!-- partial -->

<div class="main-panel">
<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title"> <span class="page-title-icon bg-gradient-primary text-white mr-2"> <i class="mdi mdi-format-list-bulleted menu-icon"></i> </span> Edit Invoice </h3>
    <a href="<?php echo LINK_PATH.'registration.html'; ?>" class="btn btn-gradient-secondary btn-sm mb-2 pull-right" style="margin-top: 21px;">Back To List</a>
  </div>
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <form class="forms-sample" method="post" action="">
            <input type="hidden" name="action" value="edit">
            <div class="row">
              <div class="col-lg-3 col-md-3 col-xs-4 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Party GST No</label>
                  <input type="text" class="form-control" id="party_gst_no" name="party_gst_no" value="<?php echo $PostPartGSTNo; ?>"  required/>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-xs-8 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Name</label>
                  <input type="text" class="form-control" id="name" name="name" style="width:100%"  placeholder="Enter Name" value="<?php echo $PostName; ?>" required>
                </div>
              </div>
              
              <div class="col-lg-3 col-md-3 col-xs-8 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Purchase Order No.</label>
                  <input type="text" class="form-control" id="order_no" name="order_no" style="width:100%"  placeholder="Enter Purchase Order No." value="<?php echo $PostOrderNo; ?>">
                </div>
              </div>
               <div class="col-lg-3 col-md-3 col-xs-8 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Order Date</label>
                  <input type="date" class="form-control" id="order_date" name="order_date" style="width:100%"  placeholder="Enter Order Date" value="<?php echo $PostOrderDate; ?>">
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-xs-12 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Address</label>
                  <input type="text" class="form-control" id="address" name="address" style="width:100%"  placeholder="Enter Address" value="<?php echo $PostAddress; ?>">
                </div>
              </div>
              
              <div class="col-lg-4 col-md-4 col-xs-8 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Mobile Number</label>
                  <input type="number" class="form-control" id="mobile" name="mobile" style="width:100%"  placeholder="Enter Mobile Number" value="<?php echo $PostMobile; ?>" maxlength="16" required>
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-xs-4 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Payment Mode</label>
                  <select class="form-control" id="payment_mode" name="payment_mode">
                    <option value="Credit" <?php echo $PaymentMode == 'Credit' ? "selected":"";?>> Credit </option>
                    <option value="Cash" <?php echo $PaymentMode == 'Cash' ? "selected":"";?>> Cash </option>
                    <option value="Account" <?php echo $PaymentMode == 'Account' ? "selected":"";?>> Account  </option>
                    <option value="PhonePay" <?php echo $PaymentMode == 'PhonePay' ? "selected":"";?>> PhonePay </option>
                    <option value="GooglePay" <?php echo $PaymentMode == 'GooglePay' ? "selected":"";?>> GooglePay </option>
                    <option value="Paytm" <?php echo $PaymentMode == 'Paytm' ? "selected":"";?>> Paytm </option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-xs-12 mb-2 d-none">
                <div class="form-group">
                  <label for="exampleInputUsername1">Aadhar Number</label>
                  <input type="text" class="form-control" id="aadhar_no" name="aadhar_no" style="width:100%"  placeholder="Enter Aadhar Number" value="<?php echo $PostAadharNo; ?>" maxlength="16">
                </div>
              </div>
            </div>
            
            <div class="row">
            <div class="col-lg-2 col-md-4 col-xs-8 mb-2" id="CertificateNo" style="display:none">
            <div class="form-group">
              <label>Issue No</label>
              <input type="text" class="form-control" id="valid_certificate_no" name="valid_certificate_no" style="width:100%"  placeholder="Enter Certificate Number" value="<?php echo $validCertificateNo; ?>" <?php echo ($PostPatientCategory>0?'required':''); ?> maxlength="24">
            </div>
            </div>
            </div>
            
            <div class="form-group">
              <h5>Sale Data</h5>
              <div class="form-group row" style="padding-top:20px;">
              <div class="col-lg-2 col-md-2 col-xs-4 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Sale Type</label>
                  <select class="form-control" id="sale_type" name="sale_type" onchange="updateSaleType();">
                    <option value="Inter State Sale" <?php echo $SaleType == 'Inter State Sale' ? "selected":"";?>> Inter State Sale </option>
                    <option value="State Sale" <?php echo $SaleType == 'State Sale' ? "selected":"";?>> State Sale </option>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-xs-4 mb-2 <?php echo $SaleType == 'Inter State Sale' ? "":"d-none";?>" id="igst_box" >
                <div class="form-group">
                  <label for="exampleInputUsername1">IGST</label>
                  <select class="form-control" id="igst" name="igst">
                    <option value="0" <?php echo $IGST == '0'  || $IGST == '' ? "selected":"";?>> Select IGST </option>
                    <option value="5" <?php echo $IGST == '5' ? "selected":"";?>> 5% </option>
                    <option value="12" <?php echo $IGST == '12' ? "selected":"";?>> 12% </option>
                    <option value="18" <?php echo $IGST == '18' ? "selected":"";?>> 18% </option>
                  </select>
                </div>
              </div>
              <div class="col-lg-2 col-md-2 col-xs-4 mb-2 <?php echo $SaleType == 'State Sale' ? "":"d-none";?>" id="cgst_box" >
                <div class="form-group">
                  <label for="exampleInputUsername1">CGST</label>
                  <select class="form-control" id="cgst" name="cgst">
                    <option value="0" <?php echo $CGST == '0'  || $CGST == '' ? "selected":"";?>> Select CGST </option>
                    <option value="2.5" <?php echo $CGST == '2.5' ? "selected":"";?>> 2.5% </option>
                    <option value="6" <?php echo $CGST == '6' ? "selected":"";?>> 6% </option>
                    <option value="9" <?php echo $CGST == '9' ? "selected":"";?>> 9% </option>
                  </select>
                </div>
              </div>
               <div class="col-lg-2 col-md-2 col-xs-4 mb-2 <?php echo $SaleType == 'State Sale' ? "":"d-none";?>" id="sgst_box">
                <div class="form-group">
                  <label for="exampleInputUsername1">SGST</label>
                  <select class="form-control" id="sgst" name="sgst">
                    <option value="0" <?php echo $SGST == '0' || $SGST == '' ? "selected":"";?>> Select SGST </option>
                    <option value="2.5" <?php echo $SGST == '2.5' ? "selected":"";?>> 2.5% </option>
                    <option value="6" <?php echo $SGST == '6' ? "selected":"";?>> 6% </option>
                    <option value="9" <?php echo $SGST == '9' ? "selected":"";?>> 9% </option>
                  </select>
                </div>
              </div>
              </div>
              </div>
            
            <div class="form-group">
              <h5>Particulars</h5>
              
               <div class="form-group row" style="padding-top:20px;">
                  <div class="col-sm-3">
                  <label>Particular</label>
                </div>
               <div class="col-sm-2">
                  <label>Price</label>
                </div>
                
                <div class="col-sm-2">
                  <label>Description of Goods</label>
                </div>
                <div class="col-sm-2">
                	<label>Quantity</label>
                </div>
                <div class="col-sm-1"><label>Amount</label></div>
            </div>
              
              <?php
			  $totalRows = 0;
				$infosql = "SELECT * from registration_informations where reg_id = ".$recData['id'];
				$statement = $conn->prepare($infosql);
				$statement->execute();
				$statement->setFetchMode(PDO::FETCH_ASSOC);
				$inforows = $statement->fetchAll();
				if(count($inforows) > 0){
					foreach($inforows as $key => $inforow){
						$totalRows = $key+1;
					
				?>
              
              <div class="form-group row" style="padding-top:20px;" id="row_<?php echo $key; ?>">
                  <div class="col-sm-3">
                  <select class="form-control" name="component[]" required onchange="setComponentValues(); getPrice(<?php echo $key; ?>);" id="component_id<?php echo $key; ?>">
                  	<option value="" >Select Component</option>
                     <?php
                    $csql = "SELECT * from blood_component_types where status = 1";
                    $statement = $conn->prepare($csql);
                    $statement->execute();
                    $statement->setFetchMode(PDO::FETCH_ASSOC);
                    $rows =$statement->fetchAll();
                    if(count($rows) > 0){
                        foreach($rows as $row){
                            echo '<option value="'.$row['id'].'" '.($inforow['component_id'] == $row['id']?'selected':'').'>'.$row['title'].'</option>';
                        }
                    }
                    ?>
                    </select>
                </div>
                 <div class="col-sm-2">
                  <input type="text" minlength="1" maxlength="6" class="form-control priceData" name="componentPrice[]" id="component_price0" placeholder="Enter Price" onblur="getPrice(0);" value="<?php echo $inforow['amount']; ?>" required>
                </div>
                <div class="col-sm-2">
                  <input type="text" class="form-control noteData" name="componentNote[]" id="component_note0" placeholder="Enter Description of Goods" value="<?php echo $inforow['note']; ?>">
                </div>
                <div class="col-sm-2">
                  <input type="hidden" id="totalComponents" value="<?php echo count($rows)-count($inforows);?>">
                  <input type="text" minlength="1" maxlength="6" class="form-control qtyData" name="bloodComponentQty[]" id="component_qty<?php echo $key; ?>" placeholder="Enter Quantity" value="<?php echo $inforow['component_quantity']; ?>" onblur="getPrice(<?php echo $key; ?>);" required>
                </div>
                <div class="col-sm-1 priceBox" id="priceBox_<?php echo $key; ?>">₹<?php echo number_format($inforow['total_amount'] , 2);?></div>
                <div class="col-sm-2">
                	<?php 
					if($key == 0){
					?>
                	<a class="btn btn-success" href="javascript:void(0);" onclick="addMore()">Add More</a>
                    <?php 
					}else{
					?>
                    <a class="btn btn-danger" href="javascript:void(0);" onclick="removeComponent(<?php echo $key; ?>)">Remove</a>
					<?php
					}?>
                    
                </div>
            </div>
            <?php 
			}
                    }else{
						$totalRows = 0;
			?>
            <div class="form-group row" style="padding-top:20px;">
                  <div class="col-sm-4">
                  <select class="form-control" name="component[]" required onchange="setComponentValues(); getPrice(0);" id="component_id0">
                  	<option value="0">Select Component</option>
                     <?php
                    $csql = "SELECT * from blood_component_types where status = 1";
                    $statement = $conn->prepare($csql);
                    $statement->execute();
                    $statement->setFetchMode(PDO::FETCH_ASSOC);
                    $rows =$statement->fetchAll();
                    if(count($rows) > 0){
                        foreach($rows as $row){
                            echo '<option value="'.$row['id'].'" >'.$row['title'].'</option>';
                        }
                    }
                    ?>
                    </select>
                </div>
                
                <div class="col-sm-2">
                  <input type="text" class="form-control noteData" name="componentNote[]" id="component_note0" placeholder="Enter Description of Goods" value="">
                </div>
               
                <div class="col-sm-2">
                	<input type="hidden" id="totalComponents" value="<?php echo count($rows);?>">
                  <input type="text" minlength="1" maxlength="6" class="form-control qtyData" name="bloodComponentQty[]" id="component_qty0" placeholder="Enter Quantity" onblur="getPrice(0);" value="" required>
                </div>
                
                <div class="col-sm-1 priceBox" id="priceBox_0">₹0.00</div>
                <div class="col-sm-2">
                	<a class="btn btn-success" href="javascript:void(0);" onclick="addMore()">Add More</a>
                </div>
            </div>
            <?php } ?>
            
            <input type="hidden" id="totalRec" value="<?php echo $totalRows;?>">
            <div id="AddMore">
            </div>
            
            </div>
            
             <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12 mb-2">
            <div class="form-group">
              <label>Note</label>
              <textarea type="text" class="form-control" id="note" name="note" style="width:100%" rows="5"><?php echo $Note; ?></textarea>
            </div>
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
  </div>
</div>

<script type="text/javascript" src="assets/js/custom.js"></script>
<script>
$(document).ready(function(){
	$('.qtyData').filter_input({regex:'[0-9]'});
	$('.priceData').filter_input({regex:'[0-9.]'});
	setComponentValues();
});
function updateSaleType(){
	var saleType = $('#sale_type').val();
	$('#sgst_box, #cgst_box, #igst_box').addClass('d-none');
	if(saleType == "State Sale"){
		$('#cgst_box, #sgst_box').removeClass('d-none');
	}
	if(saleType == "Inter State Sale"){
		$('#igst_box').removeClass('d-none');
	}
}
var counter = $('#totalRec').val();
var counterData = 0;
var setComponent = [];
function addMore(){
	if($('#totalComponents').val() > counterData ){
		var html = '<div class="form-group row" id="row_'+counter+'" style="padding-top:20px;">\
					  <div class="col-sm-3">\
					  <select class="form-control" name="component[]" id="component_id'+counter+'" required onchange="setComponentValues(); getPrice('+counter+');">\
						<option value="" >Select Component</option>';
						 <?php
						$csql = "SELECT * from blood_component_types where status = 1";
						$statement = $conn->prepare($csql);
						$statement->execute();
						$statement->setFetchMode(PDO::FETCH_ASSOC);
						$rows =$statement->fetchAll();
						if(count($rows) > 0){
							foreach($rows as $row){
								?>
								if (setComponent.indexOf('<?php echo $row['id']; ?>') > -1) {
								}else{
									html +='<?php echo '<option value="'.$row['id'].'" >'.addslashes($row['title']).'</option>';?>';
								}
								<?php
							}
						}
						?>
						html +='</select>\
					</div>\
					<div class="col-sm-2">\
                  <input type="text" minlength="1" maxlength="6" class="form-control priceData" name="componentPrice[]" id="component_price'+counter+'" placeholder="Enter Price" onblur="getPrice('+counter+');" value="" required>\
                </div>\
                <div class="col-sm-2">\
                  <input type="text" class="form-control noteData" name="componentNote[]" id="component_note'+counter+'" placeholder="Enter Description of Goods" value="">\
                </div>\
					<div class="col-sm-2">\
					  <input type="text" minlength="1" maxlength="6" class="form-control qtyData" name="bloodComponentQty[]" id="component_qty'+counter+'" placeholder="Enter Quantity" value="" onblur="getPrice('+counter+');" required>\
					</div>\
					<div class="col-sm-1 priceBox"  id="priceBox_'+counter+'">₹0.00</div>\
					<div class="col-sm-2">\
						<a class="btn btn-danger" href="javascript:void(0);" onclick="removeComponent('+counter+')">Remove</a>\
					</div>\
				</div>';
				
		$('#AddMore').append(html);
		$('.qtyData').filter_input({regex:'[0-9]'});
		counter++;
		counterData++;
	}
}
function removeComponent(rowID){
$('#row_'+rowID).remove();
counterData--;
	setComponentValues();
}
function getPrice(rowID){
	$('#priceBox_'+rowID).html('₹0.00');
	var componentType = $('#component_id'+rowID).val();
	var componentQty = $('#component_qty'+rowID).val();
	var componentPrice = $('#component_price'+rowID).val();
	if(parseInt(componentType) > 0 && parseFloat(componentPrice) > 0 && parseInt(componentQty)){
		var price = parseFloat(parseFloat(componentPrice) * parseFloat(componentQty));
		price = parseFloat(price).toFixed(2);
		$('#priceBox_'+rowID).html('₹'+price);
	}
}
function setComponentValues(){
	setComponent = [];
	$('select[name="component[]"]').each(function(){
		if($(this).val() > 0){
			setComponent.push($(this).val());
		}
	})
}
</script>
<?php
$conn = null;

$_SESSION['error']= $_SESSION['success'] ="";
unset ($_SESSION['error']);
unset ($_SESSION['success']);
?>
<style>
.priceBox{padding:10px 0;
font-weight:bold;}
    .searchForm label{
        display: block;
    width: 100%;
    font-size: 14px;
    margin-bottom: 5px;
    }
    </style>
