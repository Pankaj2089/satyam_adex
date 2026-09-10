<?php
if(checkPermissions('Financial Year') != true){
    echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>'; die;
}
require_once __DIR__.'/../inc/quotation.php';
$quotationId = isset($_GET['rowID']) ? (int) base64_decode($_GET['rowID']) : 0;
$existingQuotation = $quotationId ? quotationFetch($conn, $quotationId) : null;
if ($quotationId && !$existingQuotation) {
  echo '<script>window.location="'.LINK_PATH.'quotation.html"</script>'; die;
}
$quotationDate = $existingQuotation['quotation_date'] ?? date('Y-m-d');
$quotationNumber = $existingQuotation['quotation_no'] ?? quotationNextNumber($conn, 'ADEX', $quotationDate);
$quotationItems = $existingQuotation['items'] ?? [];
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title"><span class="page-title-icon bg-gradient-primary text-white mr-2"><i class="mdi mdi-file-document-outline menu-icon"></i></span><?php echo $existingQuotation ? 'Edit Quotation' : 'Create Quotation'; ?></h3>
      <a href="<?php echo LINK_PATH.'quotation.html'; ?>" class="btn btn-gradient-secondary btn-sm mb-2 pull-right" style="margin-top: 21px;">Quotation List</a>
    </div>
    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <form id="quotationForm" class="forms-sample" method="post" action="<?php echo LINK_PATH.'print-quotation.html'; ?>" target="_blank">
              <input type="hidden" name="action" value="generate">
              <input type="hidden" name="quotation_id" value="<?php echo (int) $quotationId; ?>">
              <div class="row">
                <div class="col-lg-3 col-md-4 mb-2"><div class="form-group"><label for="quotation_no">Estimate/Quotation No.</label><input type="text" class="form-control" id="quotation_no" name="quotation_no" value="<?php echo htmlspecialchars($quotationNumber, ENT_QUOTES, 'UTF-8'); ?>" required></div></div>
                <div class="col-lg-3 col-md-4 mb-2"><div class="form-group"><label for="quotation_date">Date</label><input type="date" class="form-control" id="quotation_date" name="quotation_date" value="<?php echo htmlspecialchars($quotationDate, ENT_QUOTES, 'UTF-8'); ?>" required></div></div>
                <div class="col-lg-3 col-md-4 mb-2"><div class="form-group"><label for="place_of_supply">Place of Supply</label><input type="text" class="form-control" id="place_of_supply" name="place_of_supply" value="<?php echo htmlspecialchars($existingQuotation['place_of_supply'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></div></div>
              </div>
              <h5 class="mt-3">Estimate For</h5>
              <div class="row">
                <div class="col-lg-4 col-md-6 mb-2"><div class="form-group"><label for="customer_name">Customer/Company Name</label><input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($existingQuotation['customer_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></div></div>
                <div class="col-lg-5 col-md-6 mb-2"><div class="form-group"><label for="customer_address">Address</label><input type="text" class="form-control" id="customer_address" name="customer_address" value="<?php echo htmlspecialchars($existingQuotation['customer_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></div></div>
                <div class="col-lg-3 col-md-4 mb-2"><div class="form-group"><label for="customer_contact">Contact Number</label><input type="text" class="form-control" id="customer_contact" name="customer_contact" value="<?php echo htmlspecialchars($existingQuotation['customer_contact'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></div></div>
                <div class="col-lg-3 col-md-4 mb-2"><div class="form-group"><label for="customer_gstin">GSTIN</label><input type="text" class="form-control" id="customer_gstin" name="customer_gstin" value="<?php echo htmlspecialchars($existingQuotation['customer_gstin'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div></div>
                <div class="col-lg-3 col-md-4 mb-2"><div class="form-group"><label for="customer_state">State</label><input type="text" class="form-control" id="customer_state" name="customer_state" value="<?php echo htmlspecialchars($existingQuotation['customer_state'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div></div>
              </div>
              <div class="form-group mt-3"><label for="feature_text">Keys &amp; Feature</label><textarea class="form-control" id="feature_text" name="feature_text" rows="5" placeholder="Enter one feature per line"><?php echo htmlspecialchars($existingQuotation['feature_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></div>
              <div class="d-flex flex-wrap align-items-center mt-3 mb-2">
                <h5 class="mr-3 mb-2">Quotation Items</h5>
                <button type="button" class="btn btn-success btn-sm mr-2 mb-2" id="addItem"><i class="mdi mdi-plus"></i> Add More</button>
                <button type="button" class="btn btn-info btn-sm mr-2 mb-2" id="importExcel"><i class="mdi mdi-file-import"></i> Import from Excel</button>
                <button type="button" class="btn btn-outline-secondary btn-sm mb-2" id="downloadSample"><i class="mdi mdi-file-excel"></i> Download Sample Excel</button>
                <input type="file" id="excelFile" accept=".xls,.xlsx,.csv,.txt" class="d-none">
              </div>
              <div id="quotationMessage" class="alert d-none" role="alert"></div>
              <div class="table-responsive">
                <table class="table table-bordered" id="quotationItems">
                  <thead><tr><th>#</th><th>Description of Goods</th><th>Size</th><th>Total Sqf</th><th>Quantity</th><th>Unit</th><th>Price/Unit</th><th>GST (18%)</th><th>Amount</th><th>Remove</th></tr></thead>
                  <tbody></tbody>
                  <tfoot>
                    <tr><th colspan="4" class="text-right">Total</th><th id="totalQuantity">0.00</th><th></th><th></th><th id="totalGst">₹ 0.00</th><th id="totalAmount">₹ 0.00</th><th></th></tr>
                  </tfoot>
                </table>
              </div>
              <button type="submit" class="btn btn-gradient-primary mt-3"><i class="mdi mdi-file-pdf"></i> Generate Quotation PDF</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
<style>
#quotationItems th, #quotationItems td { vertical-align: middle; white-space: nowrap; padding:10px 6px; }
#quotationItems input { min-width: 90px; }
#quotationItems input.descriptionData { min-width: 220px; }
#quotationItems input.sizeData { min-width: 120px; }
#quotationItems input.totalSqfData { min-width: 105px; }
#quotationItems select.unitData { min-width: 120px; }
.calculatedData { background-color: #f5f5f5; text-align: right; }
</style>
<script>
(function($) {
    var rowNumber = 0;
    var gstRate = 0.18;
  var existingItems = <?php echo json_encode($quotationItems, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

    function money(value) { return '₹ ' + Number(value || 0).toFixed(2); }
    function showMessage(message, type) {
        $('#quotationMessage').removeClass('d-none alert-danger alert-success').addClass('alert-' + type).text(message);
    }
    function clearMessage() { $('#quotationMessage').addClass('d-none').text(''); }
    function isAreaUnit(unit) { return unit === 'Sqf' || unit === 'Sqi'; }
    function recalculate() {
        var totalQuantity = 0, totalGst = 0, totalAmount = 0;
        $('#quotationItems tbody tr').each(function(index) {
            var $row = $(this), quantity = parseFloat($row.find('.quantityData').val()) || 0, price = parseFloat($row.find('.priceData').val()) || 0, unit = $row.find('.unitData').val(), totalSqf = parseSize($row.find('.sizeData').val());
        $row.find('.totalSqfData').val(isAreaUnit(unit) && totalSqf ? totalSqf.toFixed(2) : '').prop('disabled', !isAreaUnit(unit));
        var taxable = (isAreaUnit(unit) && totalSqf ? totalSqf * quantity : quantity) * price, gst = taxable * gstRate, amount = taxable + gst;
            $row.find('.rowNumber').text(index + 1);
            $row.find('.gstData').val(money(gst) + ' (18%)');
            $row.find('.amountData').val(money(amount));
            totalQuantity += quantity; totalGst += gst; totalAmount += amount;
        });
        $('#totalQuantity').text(totalQuantity.toFixed(2));
        $('#totalGst').text(money(totalGst));
        $('#totalAmount').text(money(totalAmount));
    }
    function parseSize(value) {
      var parts = String(value || '').trim().split(/\s*[xX*×]\s*/);
      if (parts.length !== 2 || !isFinite(Number(parts[0])) || !isFinite(Number(parts[1])) || Number(parts[0]) <= 0 || Number(parts[1]) <= 0) return 0;
      return Number(parts[0]) * Number(parts[1]);
    }
    function updateUnitState($row) { $row.find('.totalSqfData').prop('disabled', !isAreaUnit($row.find('.unitData').val())); recalculate(); }
    function addRow(item) {
        item = item || {};
        var id = rowNumber++;
        var html = '<tr data-row="' + id + '">' +
            '<td class="rowNumber"></td>' +
            '<td><input type="text" class="form-control descriptionData" name="description[]" value="' + escapeHtml(item.description || '') + '" required></td>' +
            '<td><input type="text" class="form-control sizeData" name="size[]" value="' + escapeHtml(item.size || '') + '" required></td>' +
            '<td><input type="text" class="form-control calculatedData totalSqfData" name="total_sqf[]" value="" readonly></td>' +
            '<td><input type="number" step="any" min="0.0001" class="form-control quantityData" name="quantity[]" value="' + escapeHtml(item.quantity || '') + '" required></td>' +
            '<td><select class="form-control unitData" name="unit[]" required><option value="Sqf"' + (item.unit === 'Sqf' ? ' selected' : '') + '>Sqf</option><option value="Sqi"' + (item.unit === 'Sqi' ? ' selected' : '') + '>Square Inch (Sqi)</option><option value="Pcs"' + (item.unit === 'Pcs' ? ' selected' : '') + '>Pcs</option></select></td>' +
            '<td><input type="number" step="0.01" min="0" class="form-control priceData" name="price[]" value="' + escapeHtml(item.price || '') + '" required></td>' +
            '<td><input type="text" class="form-control calculatedData gstData" name="gst_display[]" value="₹ 0.00 (18%)" readonly></td>' +
            '<td><input type="text" class="form-control calculatedData amountData" name="amount_display[]" value="₹ 0.00" readonly></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm removeItem" title="Remove row"><i class="mdi mdi-delete"></i></button></td>' +
            '</tr>';
        $('#quotationItems tbody').append(html);
        updateUnitState($('#quotationItems tbody tr').last());
        recalculate();
    }
    function escapeHtml(value) { return $('<div>').text(value).html(); }
    function parseDelimited(text) {
        var lines = text.replace(/^\ufeff/, '').split(/\r?\n/).filter(function(line) { return line.trim() !== ''; });
        if (!lines.length) { throw new Error('The selected file is empty.'); }
        var separator = lines[0].indexOf('\t') >= 0 ? '\t' : ',';
        return lines.map(function(line) {
            var values = [], current = '', quoted = false;
            for (var i = 0; i < line.length; i++) {
                var char = line[i];
                if (char === '"') { quoted = !quoted; }
                else if (char === separator && !quoted) { values.push(current.trim()); current = ''; }
                else { current += char; }
            }
            values.push(current.trim());
            return values;
        });
    }
    function importRows(text) {
        var records = parseDelimited(text), header = records.shift().map(function(value) { return value.toLowerCase().replace(/[^a-z0-9]/g, ''); });
        var expected = ['descriptionofgoods', 'size', 'quantity', 'unit', 'priceunit'];
        if (expected.some(function(value, index) { return header[index] !== value; })) { throw new Error('Invalid format. Use columns: Description of Goods, Size, Quantity, Unit, Price/Unit.'); }
        var imported = records.map(function(record) {
            if (record.length < 5 || !record[0] || !record[1] || !record[2] || !record[3] || record[4] === '' || isNaN(Number(record[2])) || Number(record[2]) <= 0 || isNaN(Number(record[4])) || Number(record[4]) < 0) { throw new Error('Every imported row must have a description, size, quantity greater than 0, unit, and valid price.'); }
            return { description: record[0], size: record[1], quantity: record[2], unit: record[3], price: record[4] };
        });
        if (!imported.length) { throw new Error('The selected file has no item rows.'); }
        imported.forEach(addRow); clearMessage();
    }
    $('#addItem').on('click', function() { addRow(); });
    $('#quotationItems').on('input', '.quantityData, .priceData, .sizeData', recalculate);
    $('#quotationItems').on('change', '.unitData', function() { updateUnitState($(this).closest('tr')); });
    $('#quotationItems').on('click', '.removeItem', function() { $(this).closest('tr').remove(); recalculate(); });
    $('#importExcel').on('click', function() { $('#excelFile').val('').trigger('click'); });
    $('#excelFile').on('change', function() {
        if (!this.files.length) return;
        var reader = new FileReader();
        reader.onload = function(event) {
            try { importRows(event.target.result); showMessage('Items imported successfully.', 'success'); }
            catch (error) { showMessage(error.message, 'danger'); }
        };
        reader.readAsText(this.files[0]);
    });
    $('#downloadSample').on('click', function() {
        var content = 'Description of Goods\tSize\tQuantity\tUnit\tPrice/Unit\nFLEX BOARD WITH IRON FRAME\t16X7\t1\tSqf\t60\nFLEX BOARD WITH IRON FRAME\t5X9.5\t1\tSqf\t60\nSTAIR VINYL FLOORING PRINT\t6"X48"\t1\tPcs\t116\n';
        var link = document.createElement('a'); link.href = URL.createObjectURL(new Blob([content], {type: 'application/vnd.ms-excel'})); link.download = 'quotation-items-sample.xls'; link.click(); URL.revokeObjectURL(link.href);
    });
    $('#quotationForm').on('submit', function(event) {
        clearMessage();
        if (!$('#quotationItems tbody tr').length) { event.preventDefault(); showMessage('Add at least one quotation item before generating the PDF.', 'danger'); return; }
        var valid = true;
        $('#quotationItems tbody tr').each(function() {
            var $row = $(this), quantity = Number($row.find('.quantityData').val()), price = Number($row.find('.priceData').val()), unit = $row.find('.unitData').val();
            if (!$row.find('.descriptionData').val() || !$row.find('.sizeData').val() || !unit || !isFinite(quantity) || quantity <= 0 || !isFinite(price) || price < 0 || (isAreaUnit(unit) && !parseSize($row.find('.sizeData').val()))) valid = false;
        });
        if (!valid) { event.preventDefault(); showMessage('Complete every item. Quantity must be greater than 0 and price cannot be negative.', 'danger'); }
    });
    if (existingItems.length) existingItems.forEach(addRow); else addRow();
})(jQuery);
</script>
