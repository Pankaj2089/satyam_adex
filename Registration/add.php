<?php
if(checkPermissions('Registration') != true){
	echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>';die;
}
$PostOrderNo = $PostOrderDate = $PostPartGSTNo = $PostName = $PostFName = $PostAddress = $PostMobile = $PostAadharNo = $rateFor = $patientCategory = $validCertificateNo = $SaleType = $CGST = $SGST = $IGST = $PaymentMode = $Note = '';
if(isset($_POST["action"]) && $_POST["action"] =="add"){
	
    $PostPartGSTNo = strtoupper($_POST["party_gst_no"]);
	$PostName = ucwords($_POST["name"]);
	$PostFName = ucwords($_POST["fname"]);
	$PostAddress = $_POST["address"];
	$PostMobile = $_POST["mobile"];
	$PostAadharNo = $_POST["aadhar_no"];
	$rateFor = "";
	$validCertificateNo = $_POST['valid_certificate_no'];
	$PostOrderNo = strtoupper($_POST['order_no']);
	$PostOrderDate = $_POST['order_date'];
	$PaymentMode = $_POST['payment_mode'];
	$Note = $_POST['note'];
	
	#set gst data
	$SaleType = $_POST['sale_type'];
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
	if($_POST['sale_type'] == 'State Sale'){ $SGST = $_POST['sgst']; }
	
	$error = '';
	
	if(empty($PostName)){ $error = 'Please enter name.'; }
	if(empty($PostMobile)){ $error = 'Please enter mobile number.'; }
	if(!empty($patientCategory) && empty($validCertificateNo)){ $error = 'Please enter valid certificate number.'; }
	
	if(empty($error)){
		
		$createdAt = date('Y-m-d H:i:s');
		$sql = "SELECT id, start_billing_no from financial_years where status = 1 limit 1";
		$statement = $conn->prepare($sql);
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$recData =$statement->fetch();
		
		$financialYearID = $recData['id'];
		$billingNO = $recData['start_billing_no'];
		
		$componentIDs = '';
		
		$sql = "SELECT id from registrations ORDER BY id DESC limit 1";
		$statement = $conn->prepare($sql);
		$statement->execute();
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$recData =$statement->fetch();
		if(isset($recData['id'])){
			$billingNO = $recData['id']+100;
		}
		
		$startYear = (date('y')-1);
		$nextYear = date('y');
		
		if(date('m') >= 4){
		    $startYear = date('y');
		    $nextYear = (date('y')+1);
		}
		
		$billingNO =  'AD/'.$startYear.'-'.$nextYear.'/'.$num_str = sprintf("%04d", $billingNO);

		$sql = "INSERT INTO registrations (user_id, financial_year_id, billing_no, party_gst_no, order_no, order_date , name, fname, address, mobile, aadhar_no, valid_certificate_no, sale_type, cgst, sgst, igst, payment_mode, note, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt= $conn->prepare($sql);
		$stmt->execute([$_SESSION['UserId'], $financialYearID, $billingNO, $PostPartGSTNo, $PostOrderNo, $PostOrderDate, $PostName, $PostFName, $PostAddress, $PostMobile, $PostAadharNo, $validCertificateNo, $SaleType, $CGST, $SGST, $IGST, $PaymentMode, $Note, $createdAt]); 
		$reg_id = $conn->lastInsertId();
		
		$bloodComponents = $_POST["component"];
		$bloodComponentQty = $_POST["bloodComponentQty"];
		$componentPrice = $_POST["componentPrice"];
		$componentNote = $_POST["componentNote"];
		$finalAmount = 0;
		$bloodComponentData = '';
		if(count($bloodComponents) > 0){
			$ComponentQty = 1;
			$ComponentPrice = 0;
			$componentIDs = implode(',',$bloodComponents);
			foreach($bloodComponents as $key => $bloodComponent){
				#set quantity
				if(isset($bloodComponentQty[$key]) && $bloodComponentQty[$key] > 0){ $ComponentQty= intval($bloodComponentQty[$key]);}

				#set price
				if(isset($componentPrice[$key]) && $componentPrice[$key] > 0){ $ComponentPrice= floatval($componentPrice[$key]);}
				$price = $ComponentPrice;
				$amount = number_format($price,2);
				$amount = str_replace(',','',$amount);
				$price = str_replace(',','',$price);
				$totalAmount = number_format(floatval($price)*floatval($ComponentQty),2);
				$totalAmount = str_replace(',','',$totalAmount);
				$finalAmount += $totalAmount;	
				$ComponentNotes = $componentNote[$key];
				
				$sql = "INSERT INTO registration_informations(reg_id, component_id, component_quantity, amount, total_amount, note, created_at) VALUES (?,?,?,?,?,?,?)";
				$stmt= $conn->prepare($sql);
				$stmt->execute([$reg_id, $bloodComponent, $ComponentQty, $amount, $totalAmount, $ComponentNotes, $createdAt]);
			}
		}
		
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
		#update final invoice
		$sql = "UPDATE registrations SET total_amount=?, component_ids=? WHERE id=?";
		$stmt= $conn->prepare($sql);
		$stmt->execute([$finalAmount, $componentIDs, $reg_id]);
		
		//$successmsg = "Registration record has been added successfully.";
		//$_SESSION['success'] = $successmsg;
		$RowID = base64_encode($reg_id);
		echo '<script>window.location="'.LINK_PATH.'print-invoice.html?rowID='.$RowID.'"</script>';die;
	}else{
		$_SESSION['error'] = $error;
	}
}
?>
<!-- partial -->

<div class="main-panel">
<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title"> <span class="page-title-icon bg-gradient-primary text-white mr-2"> <i class="mdi mdi-format-list-bulleted menu-icon"></i> </span> Add New Invoice </h3>
    <a href="<?php echo LINK_PATH.'registration.html'; ?>" class="btn btn-gradient-secondary btn-sm mb-2 pull-right" style="margin-top: 21px;">Back To List</a>
  </div>
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <form class="forms-sample" method="post" action="">
            <input type="hidden" name="action" value="add">
            <div class="row">
              <div class="col-lg-3 col-md-3 col-xs-4 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Party GST No</label>
                  <input type="text" class="form-control" id="party_gst_no" value="<?php echo $PostPartGSTNo; ?>" name="party_gst_no" placeholder="Party GST No" required/>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-xs-8 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Name</label>
                  <input type="text" class="form-control" id="name" name="name" style="width:100%"  placeholder="Enter Name" value="<?php echo $PostName; ?>" required>
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-xs-12 mb-2 d-none">
                <div class="form-group">
                  <label for="exampleInputUsername1">Spouse Name</label>
                  <input type="text" class="form-control" id="fname" name="fname" style="width:100%"  placeholder="Enter Spouse Name" value="<?php echo $PostFName; ?>">
                </div>
              </div>
              <div class="col-lg-3 col-md-3 col-xs-8 mb-2">
                <div class="form-group">
                  <label for="exampleInputUsername1">Purchase Order No.</label>
                  <input type="text" class="form-control" id="order_no" name="order_no" style="width:100%"  placeholder="Enter Purchase Order No." value="<?php echo $PostOrderNo; ?>" >
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
              
              
              <div class="col-lg-2 col-md-2 col-xs-8 mb-2">
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
              <div class="col-lg-3 col-md-3 col-xs-12 mb-2 d-none">
                <div class="form-group">
                  <label for="exampleInputUsername1">Aadhar Number</label>
                  <input type="text" class="form-control" id="aadhar_no" name="aadhar_no" style="width:100%"  placeholder="Enter Aadhar Number" value="<?php echo $PostAadharNo; ?>" maxlength="16">
                </div>
              </div>
            </div>
            
             
            <div class="col-lg-2 col-md-4 col-xs-8 mb-2" id="CertificateNo" style=" <?php echo ($patientCategory > 0?'':'display:none'); ?> ">
            <div class="form-group">
              <label>Issue No</label>
              <input type="text" class="form-control" id="valid_certificate_no" name="valid_certificate_no" <?php echo ($patientCategory>0?'required':''); ?> style="width:100%"  placeholder="Enter Certificate Number" value="<?php echo $validCertificateNo; ?>" maxlength="24">
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
              <div class="col-lg-2 col-md-2 col-xs-4 mb-2 " id="igst_box" >
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
              <div class="col-lg-2 col-md-2 col-xs-4 mb-2 d-none" id="cgst_box">
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
               <div class="col-lg-2 col-md-2 col-xs-4 mb-2 d-none" id="sgst_box">
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
              <div class="form-group row">
                  <div class="col-sm-3">
                  <select class="form-control" name="component[]" required onchange="setComponentValues();" id="component_id0">
                  	<option value="0">Select Particular</option>
                     <?php
                    $csql = "SELECT * from blood_component_types where status = 1";
                    $statement = $conn->prepare($csql);
                    $statement->execute();
                    $statement->setFetchMode(PDO::FETCH_ASSOC);
                    $rows =$statement->fetchAll();
                    if(count($rows) > 0){
                        foreach($rows as $row){
                            echo '<option value="'.$row['id'].'" >'.$row['title'].' ('.$row['hsn_code'].')</option>';
                        }
                    }
                    ?>
                    </select>
                </div>
               <div class="col-sm-2">
                  <input type="text" minlength="1" maxlength="6" class="form-control priceData" name="componentPrice[]" id="component_price0" placeholder="Enter Price" onblur="getPrice(0);" value="" required>
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
var counter = 1;
var counterData = 1;
var setComponent = [];

function addMore(){
	if($('#totalComponents').val() > counterData ){
		
		var html = '<div class="form-group row" id="row_'+counter+'" style="padding-top:20px;">\
					  <div class="col-sm-3">\
					  <select class="form-control" name="component[]" required onchange="setComponentValues(); getPrice('+counter+');" id="component_id'+counter+'">\
						<option value="" >Select Particula</option>';
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
									html +='<?php echo '<option value="'.$row['id'].'" >'.addslashes($row['title']).' ('.$row['hsn_code'].')</option>';?>';
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
function removeComponent(rowID){
	$('#row_'+rowID).remove();
	counterData--;
	setComponentValues();
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
