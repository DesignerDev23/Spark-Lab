<?php
// Include your database configuration here
include '../config/config.php';

// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM manager WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();


    $totalUsersSql = "SELECT COUNT(*) AS totalUsers FROM subscribers";
    $totalUsersResult = $conn->query($totalUsersSql);

    if ($totalUsersResult && $totalUsersResult->num_rows > 0) {
        $totalUsersRow = $totalUsersResult->fetch_assoc();
        $totalUsers = $totalUsersRow['totalUsers'];
    }

    // Retrieve the count of active users
    $activeUsersSql = "SELECT COUNT(*) AS activeUsers FROM subscriptions WHERE expiration_date > NOW()";
    $activeUsersResult = $conn->query($activeUsersSql);

    if ($activeUsersResult && $activeUsersResult->num_rows > 0) {
        $activeUsersRow = $activeUsersResult->fetch_assoc();
        $activeUsers = $activeUsersRow['activeUsers'];
    }

    // Retrieve the count of check-ins
    $checkInCountSql = "SELECT COUNT(*) AS totalCheckIns FROM check_in";
    $checkInCountResult = $conn->query($checkInCountSql);

    if ($checkInCountResult && $checkInCountResult->num_rows > 0) {
        $checkInCountRow = $checkInCountResult->fetch_assoc();
        $totalCheckIns = $checkInCountRow['totalCheckIns'];
    }

    // Retrieve the total amount from the subscriptions table
    $totalAmountSql = "SELECT SUM(amount) AS totalAmount FROM subscriptions";
    $totalAmountResult = $conn->query($totalAmountSql);

    if ($totalAmountResult && $totalAmountResult->num_rows > 0) {
        $totalAmountRow = $totalAmountResult->fetch_assoc();
        $totalAmount = $totalAmountRow['totalAmount'];
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fullName = $row['name'];
        // $profilePhoto = $row['profile_photo'];
    } else {
        // Redirect to the login page if the user is not found
        header("Location: index.php"); // Adjust the path based on your file structure
        exit();
    }

    // Fetch all transactions
    $transactionQuery = "SELECT id, amount, payment_reference, status FROM subscriptions";
    $result = $conn->query($transactionQuery);

    if ($result) {
        $transactions = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // Handle the error, if needed
    }

    // Close the statement
    $stmt->close();
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: index.php"); // Adjust the path based on your file structure
    exit();
}

// Close the database connection
$conn->close();
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

    <title>Transactions | Spark Lab Hub </title>

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
     <!-- Add Bootstrap CSS -->
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.1/css/bootstrap.min.css">

    <!-- Add DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

    <!-- Add DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">

    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Add DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    <!-- Add DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>



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
                                    <svg
                    width="120"
                    height="120"
                    viewBox="0 0 120 120"
                    version="1.0"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <!-- Background -->
                    <rect width="120%" height="120%" fill="#ffffff" />
                    <!-- Original paths -->
                    <g>
                      <path style="opacity:0.966" fill="#f6ae40" d="M 61.5,-0.5 C 63.1667,-0.5 64.8333,-0.5 66.5,-0.5C 70.3852,1.42966 72.3852,4.59633 72.5,9C 70.7391,19.9725 68.4057,30.8058 65.5,41.5C 61.1471,30.6072 57.4804,19.4406 54.5,8C 55.2675,3.74411 57.6008,0.910778 61.5,-0.5 Z"/>
                      <path style="opacity:0.966" fill="#f4ad40" d="M 116.5,14.5 C 116.5,15.5 116.5,16.5 116.5,17.5C 115.947,20.5867 114.28,23.0867 111.5,25C 102.667,30.5 93.8333,36 85,41.5C 84.8333,41.1667 84.6667,40.8333 84.5,40.5C 90.0395,29.9203 95.7062,19.4203 101.5,9C 108.486,5.83773 113.486,7.67106 116.5,14.5 Z"/>
                      <path style="opacity:0.965" fill="#f6af40" d="M 19.5,20.5 C 23.514,20.56 26.6806,22.2267 29,25.5C 34.0838,34.0029 39.4171,42.3363 45,50.5C 45.6877,51.3317 45.521,51.9984 44.5,52.5C 34.1825,47.6758 24.1825,42.3425 14.5,36.5C 10.151,29.2158 11.8177,23.8824 19.5,20.5 Z"/>
                      <path style="opacity:0.969" fill="#f6ae40" d="M -0.5,82.5 C -0.5,80.5 -0.5,78.5 -0.5,76.5C 1.29818,72.7632 4.29818,70.7632 8.5,70.5C 19.6627,72.2991 30.6627,74.7991 41.5,78C 30.6466,81.9633 19.6466,85.4633 8.5,88.5C 4.24042,88.2153 1.24042,86.2153 -0.5,82.5 Z"/>
                      <path style="opacity:0.97" fill="#f7af41" d="M 43.5,105.5 C 44.2389,105.369 44.9056,105.536 45.5,106C 40.5542,115.391 35.7209,124.891 31,134.5C 27.1457,138.671 22.6457,139.504 17.5,137C 14.4278,133.268 13.7612,129.101 15.5,124.5C 24.553,117.649 33.8864,111.315 43.5,105.5 Z"/>
                      <path style="opacity:0.959" fill="#f8b041" d="M 68.5,148.5 C 66.5,148.5 64.5,148.5 62.5,148.5C 58.9749,146.799 57.3082,143.965 57.5,140C 58.9776,129.284 61.1443,118.784 64,108.5C 67.8571,118.91 71.0238,129.577 73.5,140.5C 73.3276,144.193 71.6609,146.859 68.5,148.5 Z"/>
                      <path style="opacity:0.969" fill="#f7af41" d="M 116.5,128.5 C 116.5,129.833 116.5,131.167 116.5,132.5C 113.394,137.527 109.061,139.027 103.5,137C 96.5498,128.269 90.2165,119.102 84.5,109.5C 84.8333,109.167 85.1667,108.833 85.5,108.5C 94.5,113.333 103.5,118.167 112.5,123C 114.469,124.5 115.802,126.333 116.5,128.5 Z"/>
                    </g>
                    <!-- Additional graphical elements can be added here -->
                  </svg>
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

              <li class="menu-header small text-uppercase"><span class="menu-header-text">Subscribers</span></li>
              <!-- Layouts -->
              <li class="menu-item ">
                <a href="add_subscriber.php" class="menu-link">
                  <i class="menu-icon  bx bx-user-plus"></i>
                  <div data-i18n="Dashboards">Add Subscriber</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>

              <li class="menu-item ">
                <a href="subscribe.php" class="menu-link ">
                  <i class="menu-icon  bx bx-credit-card"></i>
                  <div data-i18n="Dashboards">Subscribe</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>

              <li class="menu-item ">
                <a href="manage_subscribers.php" class="menu-link ">
                  <i class="menu-icon  bx bx-group"></i>
                  <div data-i18n="Dashboards">Manage Subscribers</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>

              <li class="menu-item ">
                <a href="check_status.php" class="menu-link ">
                  <i class="menu-icon  bx bx-check-double"></i>
                  <div data-i18n="Dashboards"> Payment Status </div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>

              <li class="menu-item ">
                <a href="active_user.php" class="menu-link ">
                  <i class="menu-icon  bx bx-user-check"></i>
                  <div data-i18n="Dashboards">Active Subscribers</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>
              
              <li class="menu-header small text-uppercase"><span class="menu-header-text">Activities</span></li>
              <!-- Layouts -->
              <li class="menu-item ">
                <a href="generate_report.php" class="menu-link menu-toggle">
                  <i class="menu-icon  bx bx-detail"></i>
                  <div data-i18n="Dashboards">Generate Report</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>

              <li class="menu-item active">
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
                  <!-- <li class="nav-item lh-1 me-3">
                    <a
                      class="github-button"
                      href="https://github.com/themeselection/Spark Lab Hub-html-admin-template-free"
                      data-icon="octicon-star"
                      data-size="large"
                      data-show-count="true"
                      aria-label="Star themeselection/Spark Lab Hub-html-admin-template-free on GitHub"
                      >Star</a
                    >
                  </li> -->
  
                  <!-- User -->
                  <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                      <div class="avatar avatar-online">
                        <img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                      </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li>
                        <a class="dropdown-item" href="#">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar avatar-online">
                                <img src="assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <span class="fw-medium d-block"><?php echo $fullName; ?></span>
                              <small class="text-muted">Manager</small>
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
                        <a class="dropdown-item" href="logout.php">
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
                            <h5 class="card-title text-primary">Congratulations <?php echo $fullName; ?> 🎉</h5>
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
                            <span class="fw-medium d-block mb-1">Total Users</span>
                            <h3 class="card-title mb-2"><?php echo"$totalUsers"?></h3>
                            <small class="text-success fw-medium">Registered</small>
                            <!-- <!-- <i class="bx bx-up-arrow-alt"></i> -->
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
                            <span>Active Users</span>
                            <h3 class="card-title text-nowrap mb-1"><?php echo"$activeUsers";?></h3>
                            <small class="text-success fw-medium">Online</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Total Revenue -->


                
               
                  
                </div>
                <div class="col-xxl">
                  <div class="card mb-4">
                    <div class="card-header align-items-center justify-content-between">
                                       <table id="dataTable" class="datatables-basic">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Amount</th>
                            <th>Reference</th>
                            <th>Status</th>
                            <th>Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction) : ?>
                            <tr>
                                <td><?php echo $transaction['id']; ?></td>
                                <td><?php echo $transaction['amount']; ?></td>
                                <td><?php echo $transaction['payment_reference']; ?></td>
                                <td><?php echo $transaction['status']; ?></td>
                                <td>
                                    <button class="btn btn-primary" onclick="viewTransaction(<?php echo $transaction['id']; ?>)">Approved</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
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
    	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
      // Initialize DataTable
      $(document).ready(function() {
          var table = $('#dataTable').DataTable({
              dom: 'Bfrtip',
              buttons: [
                  'copy', 'csv', 'excel', 'pdf', 'print'
              ],
              paging: true,
              // searching: true
          });
  
          // Add event listener for view and delete buttons
          $('#dataTable tbody').on('click', '.btn-view', function() {
              var data = table.row($(this).parents('tr')).data();
              alert('View clicked for: ' + data[0]);
          });
  
          $('#dataTable tbody').on('click', '.btn-delete', function() {
              var data = table.row($(this).parents('tr')).data();
              alert('Delete clicked for: ' + data[0]);
          });
      });
  </script>

  </body>
</html>
