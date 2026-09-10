<?php
$directoryName = str_replace('\\Registration', '', __DIR__);
$directoryName = str_replace('/Registration', '', $directoryName);
require_once $directoryName.'/inc/db_connect.php';
require_once $directoryName.'/inc/quotation.php';
require_once $directoryName.'/vendor/autoload.php';
require_once $directoryName.'/inc/business_details.php';
require_once $directoryName.'/inc/amount_to_words.php';

function quotationValue($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['rowID'])) {
    $record = quotationFetch($conn, (int) base64_decode($_GET['rowID']));
    if (!$record) {
        http_response_code(404);
        exit('Quotation not found.');
    }
    $_POST = $record;
    $_POST['description'] = array_column($record['items'], 'description');
    $_POST['size'] = array_column($record['items'], 'size');
    $_POST['quantity'] = array_column($record['items'], 'quantity');
    $_POST['unit'] = array_column($record['items'], 'unit');
    $_POST['price'] = array_column($record['items'], 'price');
    $_POST['quotation_id'] = $record['id'];
} elseif ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'generate') {
    header('Location: '.LINK_PATH.'quotation.html');
    exit;
}

try {
    $quotation = quotationBuildData($_POST);
    $quotationId = (int) ($_POST['quotation_id'] ?? 0);
    if ($quotation['quotation_no'] === '' || $quotation['place_of_supply'] === '' || $quotation['customer_name'] === '' || $quotation['customer_address'] === '' || $quotation['customer_contact'] === '') {
        throw new InvalidArgumentException('Please complete all required quotation details.');
    }
    $quotationId = quotationSave($conn, $quotation, $quotationId ?: null);
} catch (InvalidArgumentException $exception) {
    http_response_code(422);
    echo $exception->getMessage();
    exit;
} catch (PDOException $exception) {
    http_response_code(500);
    echo 'Unable to save quotation. Please check that the quotation tables have been created.';
    exit;
}

$items = $quotation['items'];
$totalQuantity = $quotation['total_quantity'];
$totalGst = $quotation['total_gst'];
$totalAmount = $quotation['total_amount'];
$business = getAdexBusinessDetails();
$quotationNumber = $quotation['quotation_no'];
$quotationDate = $quotation['quotation_date'];
$quotationDateDisplay = date('d-m-Y', strtotime($quotationDate));
$amountWords = adexAmountInWords($totalAmount);
$featureText = trim((string) ($quotation['feature_text'] ?? ''));

$html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
body { font-family: freeserif, serif; font-size: 10px; color: #222; }
table { width: 100%; border-collapse: collapse; }
td, th { border: 1px solid #888; padding: 5px; vertical-align: top; }
.title { border: 0; text-align: center; font-size: 16px; font-weight: bold; padding: 0 0 6px; }
.no-border, .no-border td { border: 0; }
.company { font-size: 10px; line-height: 1.5; }
.company strong { font-size: 14px; }
.header-meta { text-align: right; font-size: 11px; line-height: 1.5; }
.header-meta img { display: block; margin: 0 0 10px auto; }
.meta td { height: 28px; }
.section-label { font-weight: bold; }
.heading th { background: #f1f1f1; font-weight: bold; text-align: center; }
.item td { height: auto; padding: 3px 5px; }
.item-description strong { display: block; }
.right { text-align: right; }
.center { text-align: center; }
.total td { font-weight: bold; border-top: 2px solid #777; }
.words { font-weight: bold; min-height: 35px; }
.page-two { page-break-before: always; }
.features { margin-top: 12px; }
.features td { border: 0; padding: 0; }
.features h3 { margin: 0 0 8px; font-size: 14px; }
.features ol { margin: 0; padding-left: 18px; }
.features li { padding-bottom: 5px; }
.terms-section { margin-top: 12px; }
.terms-section td { border: 0; padding: 0; }
.terms-section h3 { margin: 0 0 8px; font-size: 14px; }
.terms-section ol { margin: 0; padding-left: 18px; }
.terms-section li { padding-bottom: 5px; }
.bank-section { margin-top: 15px; }
.bank-section td { border: 0; padding: 0; vertical-align: top; }
.bank-details { line-height: 1.3; }
.signature { text-align: right; vertical-align: bottom !important; }
</style></head><body>';
$logoPath = $directoryName.'/assets/images/logo.png';
$logoHtml = is_file($logoPath) ? '<img src="'.quotationValue($logoPath).'" width="160" style="margin-bottom:7px;"><br>' : '';
$address = str_replace(', Jhotwara,', ',<br>Jhotwara,', quotationValue($business['address']));
$html .= '<table class="no-border"><tr><td width="60%" class="company"><strong style="font-size: 16px;">'.quotationValue($business['name']).'</strong><br>'.$address.'<br>Phone: '.quotationValue($business['phone']).'<br>Email: '.quotationValue($business['email']).'<br>WEB: '.quotationValue($business['website']).'</td><td width="40%" class="header-meta">'.$logoHtml.'PAN No. '.quotationValue($business['pan']).'<br> UDYAM : '.quotationValue($business['udyam']).'<br>GST No. '.quotationValue($business['gstin']).'</td></tr></table>';
$html .= '<table class="no-border"><tr><td class="title">QUOTATION</td></tr></table>';
$html .= '<table><tr><td width="52%"><span class="section-label">To</span><br><br><strong>'.quotationValue($_POST['customer_name'] ?? '').'</strong><br>'.nl2br(quotationValue($_POST['customer_address'] ?? '')).'<br>Contact No. : '.quotationValue($_POST['customer_contact'] ?? '').'<br>GSTIN : '.quotationValue($_POST['customer_gstin'] ?? '').'<br>State: '.quotationValue($_POST['customer_state'] ?? '').'</td><td width="48%" style="padding:0"><table class="no-border meta"><tr><td width="50%">Estimate No.<br><strong>'.quotationValue($quotationNumber).'</strong></td><td>Date<br><strong>'.quotationValue($quotationDateDisplay).'</strong></td></tr><tr><td>Place of supply<br><strong>'.quotationValue($_POST['place_of_supply'] ?? '').'</strong></td><td></td></tr></table></td></tr></table>';
$html .= '<table><thead><tr class="heading"><th width="4%">#</th><th width="27%">Description of Goods</th><th width="11%">Size</th><th width="9%">Total Sqf</th><th width="8%">Quantity</th><th width="7%">Unit</th><th width="12%">Price/ Unit</th><th width="10%">GST(18%)</th><th width="12%">Amount</th></tr></thead><tbody>';
foreach ($items as $index => $item) {
    $totalSqf = $item['total_sqf'] === null ? '' : number_format($item['total_sqf'], 2, '.', '');
    $html .= '<tr class="item"><td class="center">'.($index + 1).'</td><td class="item-description"><strong>'.quotationValue($item['description']).'</strong></td><td>'.quotationValue($item['size']).'</td><td class="right">'.$totalSqf.'</td><td class="right">'.number_format($item['quantity'], 2, '.', '').'</td><td class="center">'.quotationValue($item['unit']).'</td><td class="right">₹ '.number_format($item['price'], 2).'</td><td class="right">₹ '.number_format($item['gst'], 2).'</td><td class="right">₹ '.number_format($item['amount'], 2).'</td></tr>';
}
$html .= '</tbody><tfoot><tr class="total"><td colspan="4" class="right">Total</td><td class="right">'.number_format($totalQuantity, 2, '.', '').'</td><td></td><td></td><td class="right">₹ '.number_format($totalGst, 2).'</td><td class="right">₹ '.number_format($totalAmount, 2).'</td></tr></tfoot></table>';
$html .= '<table><tr><td width="60%" class="words">Estimate Amount in Words<br><br>'.quotationValue($amountWords).'</td><td width="40%"><strong>Amounts</strong><br><br>Sub Total <span style="float:right">₹ '.number_format($totalAmount, 2).'</span><hr>Total <span style="float:right"><strong>₹ '.number_format($totalAmount, 2).'</strong></span><br><br>Total GST <span style="float:right">₹ '.number_format($totalGst, 2).'</span></td></tr></table>';
$html .= $featureText !== '' ? '<table class="features"><tr><td><h3>Keys &amp; Feature</h3><div>'.nl2br(quotationValue($featureText)).'</div></td></tr></table>' : '';
$html .= '<table class="terms-section"><tr><td><h3>Terms and conditions</h3><ol><li>All prices will be changed if the quantity and quality are changed.</li><li>Payment terms: 50% with the work order and the remaining amount as per invoice.</li><li>The price payable will be as stated in ADEX written quotation and/or the order as accepted.</li></ol></td></tr></table>';
$html .= '<table class="bank-section"><tr><td width="65%" class="bank-details"><strong>Bank Details</strong><br>Account Holder\'s Name : '.quotationValue($business['account_name']).'<br>Account No. : '.quotationValue($business['account_number']).'<br>IFSC code : '.quotationValue($business['ifsc']).'<br>Name : '.quotationValue($business['bank_name']).'</td><td width="35%" class="signature">&nbsp;<br /><br /><br />For : '.quotationValue($business['name']).'<br><br><strong>Authorized Signatory</strong></td></tr></table>';
$html .= '</body></html>';

try {
    $mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
    $mpdf->SetTitle('Quotation '.($quotationNumber ?: ''));
    $mpdf->WriteHTML($html);
    $mpdf->Output('quotation-'.($quotationNumber ?: date('YmdHis')).'.pdf', 'I');
} catch (\Mpdf\MpdfException $exception) {
    http_response_code(500);
    echo $exception->getMessage();
}
