<?php
// Include your database connection
include '../config/config.php';

// Start the session
session_start();

if (!isset($_SESSION['email'])) {
  // Redirect the user to the login page
  header("Location: index.php");
  exit(); // Stop further execution
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if payment_reference is set in the POST data
    if (isset($_POST['payment_reference'])) {
        $paymentReference = $_POST['payment_reference'];

        // Fetch the latest transaction status for the specific payment reference
        $sql = "SELECT status FROM subscription WHERE payment_reference = ? ORDER BY date DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $paymentReference);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $transactionStatus = $row['status'];
            echo "Transaction Status: $transactionStatus";
        } else {
            echo "No transaction found for the specified payment reference.";
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "Payment reference is not set in the POST data.";
    }
}

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    // Retrieve user data from the database
    $email = $_SESSION['email'];
    $sql = "SELECT * FROM subscribers WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fullName = $row['full_name'];
        $profilePhoto = $row['profile_photo'];
    } else {
        echo "Error retrieving subscriber data.";
    }

    // Close the database connection
    $conn->close();
}
?>



<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-wide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Subscription Status | Spark Lab Hub</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="assets/vendor/libs/bs-stepper/bs-stepper.css" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js">
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <!-- Page CSS -->
    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>


  </head>

  <body>
    <!-- Content -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
          <!-- Menu -->
  
          <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
              <a href="dashboard.php" class="app-brand-link">
                <span class="app-brand-logo demo">
                 
                </span>
                <span class="app-brand-text demo menu-text fw-bold  ms-2 text-capitalize">Spark Lab Hub</span>

              </a>
  
              <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
              </a>
            </div>
  
            <div class="menu-inner-shadow"></div>
  
            <ul class="menu-inner py-1">
              <!-- Dashboards -->
              <li class="menu-item">
                <a href="dashboard.php" class="menu-link ">
                  <i class="menu-icon  bx bx-home-circle"></i>
                  <div data-i18n="Dashboards">Dashboards</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
               
              </li>

              <li class="menu-header small text-uppercase"><span class="menu-header-text">Subscription</span></li>
             

              <li class="menu-item ">
                <a href="subscribe.php" class="menu-link ">
                  <i class="menu-icon  bx bx-credit-card"></i>
                  <div data-i18n="Dashboards">Subscribe</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>
              <li class="menu-item active">
                <a href="check_status.php" class="menu-link ">
                  <i class="menu-icon  bx bx-check-double"></i>
                  <div data-i18n="Dashboards"> Payment Status </div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>  
              <li class="menu-item ">
                <a href="check_in.php" class="menu-link ">
                  <i class="menu-icon  bx bx-log-in-circle"></i>
                  <div data-i18n="Dashboards">Check-In</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>          
              <li class="menu-header small text-uppercase"><span class="menu-header-text">Activities</span></li>
              <!-- Layouts -->
              <li class="menu-item ">
                <a href="generate_report.php" class="menu-link menu-toggle">
                  <i class="menu-icon  bx bx-detail"></i>
                  <div data-i18n="Dashboards">Calender</div>
                </a>
              </li>

              <li class="menu-item ">
                <a href="transactions.php" class="menu-link ">
                  <i class="menu-icon  bx bx-credit-card"></i>
                  <div data-i18n="Dashboards">Transactions</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>
             
  
            
          </aside>
          <!-- / Menu -->
  
          <!-- Layout container -->
          <div class="layout-page">
            <!-- Navbar -->
  
            <nav
              class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
              id="layout-navbar">
              <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                  <i class="bx bx-menu bx-sm"></i>
                </a>
              </div>
  
              <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                <!-- Search -->
                <div class="navbar-nav align-items-center">
                  <div class="nav-item d-flex align-items-center">
                    <i class="bx bx-search fs-4 lh-0"></i>
                    <input
                      type="text"
                      class="form-control border-0 shadow-none ps-1 ps-sm-2"
                      placeholder="Search..."
                      aria-label="Search..." />
                  </div>
                </div>
                <!-- /Search -->
  
                <ul class="navbar-nav flex-row align-items-center ms-auto">
                  <!-- Place this tag where you want the button to render. -->
              
                    
  
   <!-- User -->
   <li class="nav-item navbar-dropdown dropdown-user dropdown">
    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
      <div class="avatar avatar-online">
      <img src="<?php echo $profilePhoto; ?>" alt="Profile Photo" class="w-px-40 h-40 rounded-circle" />
      </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <li>
        <a class="dropdown-item" href="#">
          <div class="d-flex">
            <div class="flex-shrink-0 me-3">
              <div class="avatar avatar-online">
              <img src="<?php echo $profilePhoto; ?>" alt="Profile Photo" class="w-px-40 h-40 rounded-circle" />
              </div>
            </div>
            <div class="flex-grow-1">
            <span class="fw-medium d-block"><?php echo $fullName; ?></span>
            <small class="text-muted">Subscriber</small>
            </div>
          </div>
        </a>
      </li>
      <li>
        <div class="dropdown-divider"></div>
      </li>
      <li>
        <a class="dropdown-item" href="#">
          <i class="bx bx-user me-2"></i>
          <span class="align-middle">My Profile</span>
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="#">
          <i class="bx bx-cog me-2"></i>
          <span class="align-middle">Settings</span>
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="#">
          <span class="d-flex align-items-center align-middle">
            <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
            <span class="flex-grow-1 align-middle ms-1">Billing</span>
            <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
          </span>
        </a>
      </li>
      <li>
        <div class="dropdown-divider"></div>
      </li>
      <li>
        <a class="dropdown-item" href="javascript:void(0);">
          <i class="bx bx-power-off me-2"></i>
          <span class="align-middle">Log Out</span>
        </a>
      </li>
    </ul>
  </li>
  <!--/ User -->
                </ul>
              </div>
            </nav>
  
            <!-- / Navbar -->
  
            <!-- Content wrapper -->
            <div class="content-wrapper">
              <!-- Content -->
  
              <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row">
                  <div class="col-lg-8 mb-4 order-0">
                    <div class="card">
                      <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                          <div class="card-body">
                            <h5 class="card-title text-primary">Congratulations <?php echo $fullName; ?>! 🎉</h5>
                            <p class="mb-4">
                              Welcome back to your personalized dashboard! We're excited to have you return and continue your journey with <span class="fw-medium">Spark Lab Hub</span>.
                            </p>
  
                            <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
                          </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                          <div class="card-body pb-0 px-0 px-md-4">
                            <img
                              src="assets/img/illustrations/man-with-laptop-light.png"
                              height="140"
                              alt="View Badge User"
                              data-app-dark-img="illustrations/man-with-laptop-dark.png"
                              data-app-light-img="illustrations/man-with-laptop-light.png" />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-4 order-1">
                    <div class="row">
                      <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                                <img
                                  src="assets/img/icons/unicons/chart-success.png"
                                  alt="chart success"
                                  class="rounded" />
                              </div>
                              <div class="dropdown">
                                <button
                                  class="btn p-0"
                                  type="button"
                                  id="cardOpt3"
                                  data-bs-toggle="dropdown"
                                  aria-haspopup="true"
                                  aria-expanded="false">
                                  <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                  <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                  <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                </div>
                              </div>
                            </div>
                            <span class="fw-medium d-block mb-1">Profit</span>
                            <h3 class="card-title mb-2">$12,628</h3>
                            <small class="text-success fw-medium"><!-- <i class="bx bx-up-arrow-alt"></i> -->Registared</small>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                                <img
                                  src="assets/img/icons/unicons/wallet-info.png"
                                  alt="Credit Card"
                                  class="rounded" />
                              </div>
                              <div class="dropdown">
                                <button
                                  class="btn p-0"
                                  type="button"
                                  id="cardOpt6"
                                  data-bs-toggle="dropdown"
                                  aria-haspopup="true"
                                  aria-expanded="false">
                                  <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                  <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                  <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                </div>
                              </div>
                            </div>
                            <span>Sales</span>
                            <h3 class="card-title text-nowrap mb-1">$4,679</h3>
                            <small class="text-success fw-medium"><!-- <i class="bx bx-up-arrow-alt"></i> -->+28.42%</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Total Revenue -->
                                  
                </div>


                <div class="col-xxl">
                  <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="mb-0">Subscription Status</h5>
                      <small class="text-muted float-end">Check a subscription Status</small>
                    </div>
                    <div class="card-body">
                    <form id="subscriberForm">
                      <div class="row mb-3">
                          <label class="col-sm-2 col-form-label" for="basic-default-email">Subscriber's email</label>
                          <div class="col-sm-10">
                              <input type="text" id="basic-default-email" class="form-control" placeholder="john.doe@example.com" />
                          </div>
                      </div>
                      <div class="row justify-content-end">
                          <div class="col-sm-10">
                              <button type="button" class="btn btn-primary" onclick="checkStatus()"> Payment Status </button>
                          </div>
                      </div>
                  </form>
                    </div>
                  </div>
                </div>

              </div>
                
                
              <!-- / Content -->
  
              <!-- Footer -->
              <footer class="footer bg-light">
                <div
                  class="container-fluid d-flex flex-md-row flex-column justify-content-between align-items-md-center gap-1 container-p-x py-3">
                  <div>
                    <a
                      href="https://demos.themeselection.com/Spark Lab Hub-bootstrap-html-admin-template/html/vertical-menu-template/"
                      target="_blank"
                      class="footer-text fw-bold"
                      >Spark Lab Hub</a
                    >
                    ©
                  </div>
                  <div>
                    <a href="https://themeselection.com/license/" class="footer-link me-4" target="_blank">License</a>
                    <a href="javascript:void(0)" class="footer-link me-4">Help</a>
                    <a href="javascript:void(0)" class="footer-link me-4">Contact</a>
                    <a href="javascript:void(0)" class="footer-link">Terms &amp; Conditions</a>
                  </div>
                </div>
              </footer>
              <!-- / Footer -->
  
              <div class="content-backdrop fade"></div>
            </div>
            <!-- Content wrapper -->
          </div>
          <!-- / Layout page -->
        </div>
  
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
      </div>

    <!-- / Content -->

  

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="assets/vendor/libs/bs-stepper/bs-stepper.js" /></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Add SweetAlert2 for popup dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function checkStatus() {
        var paymentReference = $('#basic-default-email').val();

        // Perform AJAX request to check transaction status
        $.ajax({
          url: 'check_status.php', // Replace with the actual PHP file to handle the status check
          type: 'POST',
          data: { payment_reference: paymentReference },
            success: function (response) {
                // Display status in a popup dialog using SweetAlert2
                Swal.fire({
                    title: 'Transaction Status',
                    text: 'Transaction Successful',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            },
            error: function () {
                // Handle error if needed
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while checking the status.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
</script>

  </body>
</html>
