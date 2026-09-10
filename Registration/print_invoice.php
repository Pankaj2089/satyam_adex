<?php
$directoryName = str_replace('\Registration','',__DIR__);
$directoryName = str_replace('/Registration','',$directoryName);
require_once $directoryName . '/vendor/autoload.php';
require_once $directoryName . '/inc/business_details.php';
$businessDetails = getAdexBusinessDetails();
$invoiceAddress = str_replace(', Jhotwara,', ',<br />Jhotwara,', $businessDetails['address']);

$validUser = 'No';
if(isset($_GET["rowID"])){
	$rowID = base64_decode($_GET["rowID"]);
	if(!empty($rowID)){
	  $sql = "SELECT * from registrations where id = '".$rowID."' limit 1";
	  $statement = $conn->prepare($sql);
	  $statement->execute();
	  $statement->setFetchMode(PDO::FETCH_ASSOC);
	  $rowData =$statement->fetch();
	  if(isset($rowData['id'])){
		  $validUser = 'Yes';
		$html = '
		<!DOCTYPE html>
			<html>
				<head>
					<meta charset="utf-8" />
					<title>ADEX</title>
			
					<style>
					body{
						font-size:13px;
					}
					.total{
							border-top: 2px solid #eee;
							font-weight: bold;
							font-size:12px;
						}
						.invoice-box {
							max-width: 100%;
							margin: auto;
							padding: 0px;
							font-size: 13px;
							line-height: 24px;
							color: #555;
						}
			
						.invoice-box table {
							width: 100%;
							line-height: inherit;
							text-align: left;
						}
			
						.invoice-box table td {
							padding: 5px;
							vertical-align: top;
						}
			
						.invoice-box table tr td:nth-child(2) {
							text-align: right;
						}
			
						.invoice-box table tr.top table td {
							padding-bottom: 20px;
						}
			
						.invoice-box table tr.top table td.title {
							font-size: 30px;
							line-height: 45px;
							color: #333;
						}
			
						.invoice-box table tr.information table td {
							padding-bottom: 5px;
						}
			
						.invoice-box table tr.heading td {
							background: #eee;
							border: 1px solid #ddd;
							font-weight: bold;
						}
			
						.invoice-box table tr.details td {
							padding-bottom: 20px;
						}
			
						.invoice-box table tr.item td {
							border: 1px solid #eee;
						}
			
						.invoice-box table tr.item.last td {
							border-bottom: none;
						}
						.heading{
							float:left;
							width:100%;
							padding-bottom: 15px;
							font-size:18px; 
							padding-bottom:10px; 
							font-weight:bold
						}
						.invoice-header-content{
							width:180px;
							float:right;
							margin-left:auto;
							text-align:right;
						}
						.invoice-header-content img{
							display:block;
							width:180px;
							margin:0 0 10px;
						}
						.invoice-header-content p{
							margin:0;
							text-align:right;
						}
			
									
					</style>
				</head>
			
				<body>
					<div class="invoice-box">
					<table cellpadding="0" cellspacing="0">
							<tr class="information">
								<td colspan="2">
									<table>
										<tr>
										<td width="60%" style="text-align:left; font-family: freeserif; line-height:19px;"  >
														<span class="heading">'.$businessDetails['name'].'</span><br/>
																'.$invoiceAddress.'<br />
														Phone: '.$businessDetails['phone'].'<br />
														Email: '.$businessDetails['email'].'<br />
														WEB: '.$businessDetails['website'].'
																														</td>
																														<td style="text-align:right; font-family: freeserif;" width="40%" >
															<div class="invoice-header-content">
    <img src="assets/images/logo.png" width="180" style="margin-bottom:10px;" />

    <p>
        PAN No. '.$businessDetails['pan'].'<br />
        UDYAM : '.$businessDetails['udyam'].'<br />
        GST No. '.$businessDetails['gstin'].'<br />
    </p>
</div>
																													</td>
																														</tr>
									</table>
								</td>
							</tr>
							</table>
							<table cellpadding="0" cellspacing="0">
							<tr class="information">
								<td style="font-weight:bold; text-align:center; font-size:14px">
								Tax Invoice
								</td>
									
								</tr>
							
								</table>
								<hr />
						<table cellpadding="0" cellspacing="0">
							<tr class="information">
								<td colspan="2">
									<table>
										<tr>
											<td>
												<strong>M/S '.strtoupper($rowData['name']).'</strong><br />';
												if(!empty($rowData['address'])){ $html .=  $rowData['address'].'<br />'; }
												if(!empty($rowData['mobile'])){ $html .= $rowData['mobile'].'<br />'; }
												$html .= 'GSTIN: '.strtoupper($rowData['party_gst_no']).'
											</td>
			
											<td>
												Invoice #: '.$rowData['billing_no'].'<br />
												Date: '.date('d M, Y',strtotime($rowData['created_at'])).'<br />';
												
												if(!empty($rowData['order_no'])){ $html .= 'Purchase Order No #: '.strtoupper($rowData['order_no']).'<br />'; }
												if(!empty($rowData['order_no'])){ $html .= 'Purchase Order Date: '.date('d, M Y',strtotime($rowData['order_date'])).'<br />'; }
												$html .= 'Payment Mode: '.$rowData['payment_mode'].'<br />
												Sale Type: '.$rowData['sale_type'].'
											</td>
										</tr>
									</table>
								</td>
							</tr>
							</table>
							<table cellpadding="0" cellspacing="0">
							<tr class="heading">
								<td  align="left">S.No.</td>
								<td width="30%" align="left">Particular</td>
								<td width="15%" align="left">HSN Code</td>
								<td width="30%" align="left">Description of Goods</td>
								<td width="15%" align="center">Qty</td>
								<td width="20%" align="left">Rate(₹)</td>
								<td width="20%">Total Amount(₹)</td>
							</tr>';
							
							$infosql = "SELECT * from registration_informations where reg_id = ".$rowData['id'];
							$statement = $conn->prepare($infosql);
							$statement->execute();
							$statement->setFetchMode(PDO::FETCH_ASSOC);
							$inforows =$statement->fetchAll();
							if(count($inforows) > 0){
								$grandTotal = '0';
								foreach($inforows as $key => $inforow){
									$bloodComponent =  "No Available";
									
									
									$amount = number_format($inforow['amount'],2);
									$totalAmount = number_format($inforow['total_amount'],2);
									$totalAmount = str_replace(',','',$totalAmount);
									$grandTotal += $totalAmount;
									
									$compsql = "SELECT * from blood_component_types where id = ".$inforow['component_id']." limit 1";
									$statement = $conn->prepare($compsql);
									$statement->execute();
									$statement->setFetchMode(PDO::FETCH_ASSOC);
									$recCompData =$statement->fetch();
									$bloodComponent = isset($recCompData['title'])?$recCompData['title']:'Not Available';
									$HSNCode = isset($recCompData['hsn_code'])?$recCompData['hsn_code']:'Not Available';
									$Note = !empty($inforow['note'])?$inforow['note']:'N/A';
									
									$totalRows = $key+1;
									$html .= '<tr class="item">
										<td align="left">'.$totalRows.'.</td>
										<td width="30%"  align="left">'.$bloodComponent.'</td>
										<td width="15%"  align="left">'.$HSNCode.'</td>
										<td width="30%" align="left">'.$Note.'</td>
										<td width="15%" align="center">'.$inforow['component_quantity'].'</td>
										<td width="20%" align="left">'.$amount.'</td>
										<td width="20%">'.$totalAmount.'</td>
									</tr>';									
									
								}
								
							}
							$html .= '
							</table>
					</div>
					<br />';
					if($rowData['note'] != ""){
					    $html .= '<p style="font-size:11px; margin:0px; padding:0px;"><strong>Note:</strong> '.nl2br($rowData['note']).'</p><br />';
					}
					$html .= '
					
					<div class="invoice-box">
					<table cellpadding="0" cellspacing="0">
							<tr class="information">
							<td>
							<p style="font-size:12px">
								Company Bank Details:-<br /> 
														'.$businessDetails['account_name'].' <br /> 
A/C. No. <strong>'.$businessDetails['account_number'].'</strong><br />
IFSC Code:- <strong>'.$businessDetails['ifsc'].'</strong>   <br /> Bank Name & Branch:- '.$businessDetails['bank_name'].' <br /> 
														<span style="font-size:10px"><strong>Declaration:-</strong> '.$businessDetails['declaration'].'</span>
							</p>
							</td>
								<td>
									<table>
							<tr>
							<td width="100%" style="text-align:right; font-family: freeserif; padding:0" >
							';
							$infosql = "SELECT * from registration_informations where reg_id = ".$rowData['id'];
							$statement = $conn->prepare($infosql);
							$statement->execute();
							$statement->setFetchMode(PDO::FETCH_ASSOC);
							$inforows =$statement->fetchAll();
							if(count($inforows) > 0){
								$grandTotal = '0';
								foreach($inforows as $key => $inforow){
									$bloodComponent =  "No Available";
									$amount = number_format($inforow['amount'],2);
									$totalAmount = number_format($inforow['total_amount'],2);
									$totalAmount = str_replace(',','',$totalAmount);
									$grandTotal += $totalAmount;
									
									$compsql = "SELECT * from blood_component_types where id = ".$inforow['component_id']." limit 1";
									$statement = $conn->prepare($compsql);
									$statement->execute();
									$statement->setFetchMode(PDO::FETCH_ASSOC);
									$recCompData =$statement->fetch();
									$bloodComponent = isset($recCompData['title'])?$recCompData['title']:'Not Available';
									$HSNCode = isset($recCompData['hsn_code'])?$recCompData['hsn_code']:'Not Available';
									$totalRows = $key+1;
								}
								$CGSTAmt = 0;
								if($rowData['cgst'] > 0){
									$CGSTAmt = floatval(floatval($grandTotal/100)*floatval($rowData['cgst']));
								}
								$SGSTAmt = 0;
								if($rowData['sgst'] > 0){
									$SGSTAmt = floatval(floatval($grandTotal/100)*floatval($rowData['sgst']));
								}
								$IGSTAmt = 0;
								if($rowData['igst'] > 0){
									$IGSTAmt = floatval(floatval($grandTotal/100)*floatval($rowData['igst']));
								}
		
						$html .= '<table style="float:right; width:300px" cellpadding="0" cellspacing="0">
							<tr class="heading">
								<td width="20%" align="right">SUMMARY</td>
								<td width="20%">Amount(₹)</td>
							</tr>
							<tr class="item">
								<td width="20%" align="right">Gross Amount</td>
								<td width="20%">'.number_format($grandTotal, 2).'</td>
							</tr>';
							
							if($CGSTAmt > 0){
								$html .= '<tr class="item">
									<td width="20%" align="right">CGST('.$rowData['cgst'].'%)</td>
									<td width="20%">'.number_format($CGSTAmt, 2).'</td>
								</tr>
								';
							}
							
							if($SGSTAmt > 0){
								$html .= '<tr class="item">
									<td width="20%" align="right">SGST('.$rowData['sgst'].'%)</td>
									<td width="20%">'.number_format($SGSTAmt, 2).'</td>
								</tr>
								';
							}
							if($IGSTAmt > 0){
								$html .= '<tr class="item">
									<td width="20%" align="right">IGST('.$rowData['igst'].'%)</td>
									<td width="20%">'.number_format($IGSTAmt, 2).'</td>
								</tr>
								';
							}
							
							$html .= '</table>';
									
							}	
							$amtInWord = convertNumber($rowData['total_amount']);
							$amtInWord = str_replace(' Point Zero Zero','',$amtInWord). ' Only';
							$html .= '
							</table>
											</td>
										</tr>
									</table>
									<table cellpadding="0" cellspacing="0">
								<tr class="information">
									<td>
									<table cellpadding="0" cellspacing="0"><tr class="total">
										<td width="60%" >'.$amtInWord.'</td>
										<td width="40%" colspan="2" style="font-weight:bold; font-size:12px;">Net Amount Payable: ₹'.number_format($rowData['total_amount'], 2).'</td>
									</tr>
									</td>
								</tr>
							</table>
							
								</td>
							</tr>
							</table>
							<br />
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td>
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td width="70%" style="font-weight:bold; font-size:12px;">Customer Seal and Signature</td>
											<td width="30%" style="font-weight:bold; font-size:12px;">
																	For '.$businessDetails['name'].'
											</td>
									</tr>
									</td>
								</tr>
							</table>
							
								</td>
							</tr>
							</table>
							
							
					</div>
					<br />
					
				</body>
		</html>
		';
		try {
		$mpdf = new \Mpdf\Mpdf();
		$mpdf->defaultfooterfontsize=9;
		$mpdf->defaultfooterfontstyle='N';
		$mpdf->SetFooter('This is computer generated Invoice. No seal Required.');
		$mpdf->WriteHTML($html);
		$mpdf->Output();
		} catch (\Mpdf\MpdfException $e) { // Note: safer fully qualified exception name used for catch
			// Process the exception, log, print etc.
			echo $e->getMessage();
		}
	  }
	}
}
function convertNumber($number)
{
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        100000             => 'lakh',
        10000000          => 'crore'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convertNumber(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convertNumber($remainder);
            }
            break;
        case $number < 100000:
            $thousands   = ((int) ($number / 1000));
            $remainder = $number % 1000;

            $thousands = convertNumber($thousands);

            $string .= $thousands . ' ' . $dictionary[1000];
            if ($remainder) {
                $string .= $separator . convertNumber($remainder);
            }
            break;
        case $number < 10000000:
            $lakhs   = ((int) ($number / 100000));
            $remainder = $number % 100000;

            $lakhs = convertNumber($lakhs);

            $string = $lakhs . ' ' . $dictionary[100000];
            if ($remainder) {
                $string .= $separator . convertNumber($remainder);
            }
            break;
        case $number < 1000000000:
            $crores   = ((int) ($number / 10000000));
            $remainder = $number % 10000000;

            $crores = convertNumber($crores);

            $string = $crores . ' ' . $dictionary[10000000];
            if ($remainder) {
                $string .= $separator . convertNumber($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convertNumber($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convertNumber($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    return ucwords($string);
}
if($validUser != 'Yes'){
	echo '<script>window.location="'.LINK_PATH.'registration.html"</script>';die;
}
?>