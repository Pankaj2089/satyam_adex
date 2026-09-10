
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white mr-2">
                  <i class="mdi mdi-home"></i>
                </span> Dashboard
              </h3>
              <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                  <li class="breadcrumb-item active" aria-current="page">
                    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                  </li>
                </ul>
              </nav>
            </div>
            <div class="row">
              <div class="col-md-3 stretch-card grid-margin">
              <div class="card bg-gradient-primary card-img-holder text-white">
                  <div class="card-body">
                    <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Products<i class="mdi mdi-chart-line mdi-24px float-right"></i>
                    </h4>
                    <h2 class="mb-5">
                      <?php
                        $sql = "SELECT * from blood_component_types";
                        $statement = $conn->prepare($sql);
                        if(!$statement->execute()){//execute returns false if failed
                        $returned_data['response_code'] = "-2";
                        $returned_data['response_message'] = "Server error code -2(failed query)";
                        }
                        echo $statement->rowCount(); 
                      ?>
                     </h2>
                  </div>
                </div>
              </div>

              
              <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-success card-img-holder text-white">
                  <div class="card-body">
                    <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Invoices<i class="mdi mdi-chart-bar mdi-24px float-right"></i>
                    </h4>
                    <h2 class="mb-5">
                    	<?php
						$sql = "SELECT * from registrations";
						$statement = $conn->prepare($sql);
						if(!$statement->execute()){//execute returns false if failed
						$returned_data['response_code'] = "-2";
						$returned_data['response_message'] = "Server error code -2(failed query)";
						}
						echo $statement->rowCount(); 
					 ?>
                    </h2>
                  </div>
                </div>
              </div>
              <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-dark card-img-holder text-white">
                  <div class="card-body">
                    <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Transactions <i class="mdi mdi-cash-multiple mdi-24px float-right"></i>
                    </h4>
                    <h2 class="mb-5">
                    <?php
					$totalAmount = 0;
					if ($statement->rowCount() > 0){
						$statement->setFetchMode(PDO::FETCH_ASSOC);
						$rows =$statement->fetchAll();
						foreach($rows as $key => $row){
							$totalAmount += $row['total_amount'];
						}
					}
					echo '₹'.number_format($totalAmount, 2)
					?>
                    </h2>
                  </div>
                </div>
              </div>
              </div>
              <div class="row d-none">
              <div class="col-md-3 stretch-card grid-margin">
                <div class="card bg-gradient-info card-img-holder text-white">
                  <div class="card-body">
                    <img src="assets/images/dashboard/circle.svg" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Total Transactions <i class="mdi mdi-currency-inr mdi-24px float-right"></i>
                    </h4>
                    <h2 class="mb-5"><i class="mdi mdi-currency-inr"></i>436.99</h2>
                  </div>
                </div>
              </div>
            </div>         
            
          </div>
          <?php
$conn = null;
?>