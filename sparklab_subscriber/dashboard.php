<?php
// Include your database connection
include '../config/config.php';

// Start the session
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['email'])) {
    // Redirect the user to the login page
    header("Location: index.php");
    exit(); // Stop further execution
}

// Retrieve user data from the database
$email = $_SESSION['email'];
$sql = "SELECT * FROM subscribers WHERE email = '$email'";
$result = $conn->query($sql);

$subscriptions = []; // Initialize array to store subscriptions

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullName = $row['full_name'];
    $profilePhoto = $row['profile_photo'];

    // Fetch active subscription details for the dashboard
    $subscriberId = $row['registration_id'];
    $subscriptionSql = "SELECT * FROM subscriptions WHERE subscriber_id = '$subscriberId' AND expiration_date > NOW() ORDER BY expiration_date ASC";
    $subscriptionResult = $conn->query($subscriptionSql);

    if ($subscriptionResult && $subscriptionResult->num_rows > 0) {
        while ($subscriptionRow = $subscriptionResult->fetch_assoc()) {
            // Fetch subscription details
            $subscriptionDuration = $subscriptionRow['duration'];
            $expirationDate = $subscriptionRow['expiration_date'];
            $paymentReference = $subscriptionRow['payment_reference'];
            $amount = $subscriptionRow['amount'];
            
            // Add subscription details to the array
            $subscriptions[] = [
                'subscription_duration' => $subscriptionDuration,
                'expiration_date' => $expirationDate,
                'payment_reference' => $paymentReference,
                'amount' => $amount
            ];
        }
    }
}

// Calculate total paid amount by all subscribers
$totalPaidAmount = 0;

$allSubscribersSql = "SELECT * FROM subscribers";
$allSubscribersResult = $conn->query($allSubscribersSql);

if ($allSubscribersResult && $allSubscribersResult->num_rows > 0) {
    while ($subscriberRow = $allSubscribersResult->fetch_assoc()) {
        $subscriberId = $subscriberRow['registration_id'];
        $subscriberSubscriptionSql = "SELECT SUM(amount) AS total_amount FROM subscriptions WHERE subscriber_id = '$subscriberId'";
        $subscriberSubscriptionResult = $conn->query($subscriberSubscriptionSql);
        
        if ($subscriberSubscriptionResult && $subscriberSubscriptionResult->num_rows > 0) {
            $subscriberSubscriptionRow = $subscriberSubscriptionResult->fetch_assoc();
            $totalPaidAmount += $subscriberSubscriptionRow['total_amount'];
        }
    }
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

    <title>Dashboard | Spark Lab Hub</title>

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

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
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
                <span class="app-brand-text demo menu-text fw-bold text-13 ms-2 text-capitalize">Spark Lab Hub</span>

              </a>
  
              <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
              </a>
            </div>
  
            <div class="menu-inner-shadow"></div>
  
             <ul class="menu-inner py-1">
              <!-- Dashboards -->
              <li class="menu-item active">
                <a href="dashboard.php" class="menu-link ">
                  <i class="menu-icon  bx bx-home-circle"></i>
                  <div data-i18n="Dashboards">Dashboards</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>

              </li>

              <li class="menu-header small text-uppercase"><span class="menu-header-text">Subscription</span></li>
              <!-- Layouts -->
              <!-- <li class="menu-item ">
                <a href="add_subscriber.php" class="menu-link">
                  <i class="menu-icon  bx bx-user-plus"></i>
                  <div data-i18n="Dashboards">Add Subscriber</div>
                </a>
              </li> -->

              <li class="menu-item ">
                <a href="subscribe.php" class="menu-link ">
                  <i class="menu-icon  bx bx-credit-card"></i>
                  <div data-i18n="Dashboards">Subscribe</div>
                  <!-- <div class="badge bg-danger rounded-pill ms-auto">5</div> -->
                </a>
              </li>

              <!-- <li class="menu-item ">
                <a href="manage_subscribers.php" class="menu-link ">
                  <i class="menu-icon  bx bx-group"></i>
                  <div data-i18n="Dashboards">Manage Subscribers</div>
             
                </a>
              </li> -->

              <li class="menu-item ">
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

              <!-- <li class="menu-item ">
                <a href="active_user.php" class="menu-link ">
                  <i class="menu-icon  bx bx-user-check"></i>
                  <div data-i18n="Dashboards">Active Subscribers</div>
                </a>
              </li> -->
              
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
                  <div class="col-lg-12 mb-4 order-0">
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

                  <!-- countdown -->
                  <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                          <div class="card-body" data-expiration="<?php echo $expirationDate; ?>">
                              <div class="card-title d-flex align-items-start justify-content-between">
                                  <div class="avatar flex-shrink-0">
                                      <img src="assets/img/icons/unicons/usd-circle.svg" alt="chart success" class="rounded" />
                                  </div>
                                  <div class="dropdown">
                                      <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          <i class="bx bx-dots-vertical-rounded"></i>
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                          <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                          <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                      </div>
                                  </div>
                              </div>
                              <span class="fw-medium d-block mb-1">Subscription Status</span>
                              <h3 class="card-title mb-2">
                                <?php
                              if (!empty($subscriptions)) {
    foreach ($subscriptions as $subscription) {
        // Display subscription details
        echo "<p> " . $subscription['expiration_date'] . "</p>";
    }
} else {
    // No subscriptions found
    echo "Don't have and active Subsciption";
}
?>
                              </h3>
                              <small class="text-success fw-medium" id="expirationCountdown"></small>
                          </div>
                      </div>
                  </div>

                      <!-- History -->

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
                                  aria-haspopup="true"<
                                  aria-expanded="false">
                                  <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                  <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                  <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                </div>
                              </div>
                            </div>
                            <span class="fw-medium d-block mb-1">Total Spent</span>
                            <h3 class="card-title mb-2"><?php echo"$totalPaidAmount";?></h3>
                            <small class="text-success fw-medium"><!-- <i class="bx bx-up-arrow-alt"></i> -->+0.80%</small>
                          </div>
                        </div>
                      </div>


                </div>
                <div class="row">
                  <!-- Order Statistics -->
                  <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between pb-0">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">Subscrptions</h5>
                                <small class="text-muted">42.82k Total Sales</small>
                            </div>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                    <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                    <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                    <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                
                            </div>
                            <ul class="p-0 m-0">
                            <?php foreach ($subscriptions as $subscription): ?>
                                  <li class="d-flex mb-4 pb-1">
                                      <div class="avatar flex-shrink-0 me-3">
                                          <span class="avatar-initial rounded bg-label-primary">
                                              <i class="bx bx-dollar-circle"></i>
                                          </span>
                                      </div>
                                      <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                          <div class="me-2">
                                              <h6 class="mb-0"><?php echo $subscription['subscription_duration']; ?></h6>
                                              <small class="text-muted"><?php echo $subscription['expiration_date']; ?></small>
                                          </div>
                                          <div class="user-progress">
                                              <small class="fw-medium">Approved</small>
                                          </div>
                                      </div>
                                  </li>
                              <?php endforeach; ?>
                                <!-- Other list items -->
                            </ul>
                        </div>
                    </div>
                </div>

   
  
                  <!-- Transactions -->
                  <div class="col-md-6 col-lg-8 order-2 mb-4">
                    <div class="card h-100">
                      <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Transactions</h5>
                        <div class="dropdown">
                          <button
                            class="btn p-0"
                            type="button"
                            id="transactionID"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                            <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                            <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                            <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                          </div>
                        </div>
                      </div>
                      <div class="card-body">
                        <ul class="p-0 m-0">
                        <?php foreach ($subscriptions as $subscription): ?>
                          <li class="d-flex mb-4 pb-1">
                              <div class="avatar flex-shrink-0 me-3">
                                  <img src="assets/img/icons/unicons/wallet.png" alt="User" class="rounded" />
                              </div>
                              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                  <div class="me-2">
                                      <small class="text-muted d-block mb-1">Payment Reference</small>
                                      <h6 class="mb-0"><?php echo $subscription['payment_reference']; ?></h6>
                                  </div>
                                  <div class="user-progress d-flex align-items-center gap-1">
                                      <h6 class="mb-0"><?php echo $subscription['amount']; ?></h6>
                                      <span class="text-muted">NGN</span>
                                  </div>
                              </div>
                          </li>
                      <?php endforeach; ?>
                        </ul>
                      </div>
                    </div>
                  </div>
                  <!--/ Transactions -->
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
    <script>
    // Function to calculate remaining days until expiration
    function calculateRemainingDays(expirationDate) {
        // Convert expiration date to milliseconds since Unix epoch
        var expirationTimestamp = new Date(expirationDate).getTime();
        var currentDate = new Date().getTime();

        // Calculate remaining milliseconds
        var remainingMilliseconds = expirationTimestamp - currentDate;

        // Calculate remaining days
        var remainingDays = Math.floor(remainingMilliseconds / (1000 * 60 * 60 * 24));

        return remainingDays;
    }

    // Function to update the expiration countdown
    function updateExpirationCountdown() {
        var expirationDate = '<?php echo $expirationDate; ?>';
        var remainingDays = calculateRemainingDays(expirationDate);

        // Update the expiration countdown element
        var expirationCountdownElement = document.getElementById('expirationCountdown');
        if (remainingDays > 0) {
            expirationCountdownElement.textContent = remainingDays + ' days left';
        } else {
            expirationCountdownElement.textContent = 'Will expire by';
        }
    }

    // Update the expiration countdown when the page loads
    updateExpirationCountdown();
</script>

  </body>
</html>
