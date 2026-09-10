<?php
if (checkPermissions('Financial Year') != true) {
    echo '<script>window.location="'.LINK_PATH.'dashboard.html"</script>'; die;
}
$statement = $conn->query('SELECT * FROM quotations ORDER BY quotation_date DESC, id DESC');
$quotations = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title"><span class="page-title-icon bg-gradient-primary text-white mr-2"><i class="mdi mdi-file-document-outline menu-icon"></i></span>Quotations</h3>
      <a href="<?php echo LINK_PATH.'add-quotation.html'; ?>" class="btn btn-gradient-success btn-sm mb-2 pull-right" style="margin-top: 21px;">Create Quotation</a>
    </div>
    <div class="row"><div class="col-md-12 grid-margin stretch-card"><div class="card"><div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead><tr><th>#</th><th>Quotation No.</th><th>Date</th><th>Customer</th><th>Contact</th><th>Total Amount</th><th>Action</th></tr></thead>
          <tbody>
          <?php foreach ($quotations as $index => $quotation) { ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo htmlspecialchars($quotation['quotation_no'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo date('d-m-Y', strtotime($quotation['quotation_date'])); ?></td>
              <td><?php echo htmlspecialchars($quotation['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($quotation['customer_contact'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td>₹ <?php echo number_format($quotation['total_amount'], 2); ?></td>
              <td>
                <a href="<?php echo LINK_PATH.'add-quotation.html?rowID='.base64_encode($quotation['id']); ?>" class="btn btn-xs btn-primary" title="Edit"><i class="mdi mdi-pencil-box"></i></a>
                <a href="<?php echo LINK_PATH.'print-quotation.html?rowID='.base64_encode($quotation['id']); ?>" class="btn btn-xs btn-success" target="_blank" title="Print"><i class="mdi mdi-printer"></i></a>
                <form method="post" action="<?php echo LINK_PATH.'delete-quotation.html'; ?>" style="display:inline" onsubmit="return confirm('Delete this quotation?');">
                  <input type="hidden" name="quotation_id" value="<?php echo (int) $quotation['id']; ?>">
                  <button type="submit" class="btn btn-xs btn-danger" title="Delete"><i class="mdi mdi-delete"></i></button>
                </form>
              </td>
            </tr>
          <?php } if (!$quotations) { ?>
            <tr><td colspan="7" class="text-center">No quotations found.</td></tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div></div></div>
  </div>
</div>