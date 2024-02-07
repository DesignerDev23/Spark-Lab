<?php
include '../config/config.php'; // Make sure to include your database connection here
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM manager WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fullName = $row['name'];
        // $profilePhoto = $row['profile_photo'];

        // Close the database connection
        // $conn->close();
    }
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: index.php"); // Adjust the path based on your file structure
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve form data
  $fullName = $_POST['full_name'] ?? '';
  $areaOfInterest = $_POST['area_of_interest'] ?? '';
  $email = $_POST['basic-default-email'] ?? '';
  $phoneNo = $_POST['basic-default-phone'] ?? '';
  $contactAddress = $_POST['basic-default-contact-address'] ?? '';
  $meansOfIdentity = $_FILES['means_of_identity']['name'] ?? '';
  $profilePhoto = $_FILES['profile_photo']['name'] ?? '';
  $password = $_POST['password'] ?? '';

  // Check if the email already exists in the database
  $emailCheckQuery = "SELECT id FROM subscribers WHERE email = ?";
  $stmt = $conn->prepare($emailCheckQuery);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      // Email already exists, display an error message
      echo '<script>alert("Error: Email already exists.");</script>';
      header('Location: signup.php');
  }

  // Process file uploads (you may want to add more security checks)
  $meansOfIdentityDestination = '../uploads/identity/' . uniqid() . '_' . $meansOfIdentity;
  $profilePhotoDestination = '../uploads/photos/' . uniqid() . '_' . $profilePhoto;

  if (move_uploaded_file($_FILES['means_of_identity']['tmp_name'], $meansOfIdentityDestination) &&
      move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profilePhotoDestination)) {

      // Generate a registration number with a prefix and a random number
      $registrationPrefix = 'REG'; // You can customize the prefix
      $registrationNumber = $registrationPrefix . '-' . rand(1000, 9999);

      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      // Save data to the database
      $sql = "INSERT INTO subscribers (registration_id, full_name, area_of_interest, email, phone_no, contact_address, means_of_identity, profile_photo, password) 
      VALUES ('$registrationNumber', '$fullName', '$areaOfInterest', '$email', '$phoneNo', '$contactAddress', '$meansOfIdentityDestination', '$profilePhotoDestination', '$hashedPassword')";

      if ($conn->query($sql) === TRUE) {
          // Redirect to a success page or display a success message
          header('Location: index.php');
          exit();
      } else {
          die('Error: ' . $sql . '<br>' . $conn->error);
      }
  } else {
      die('Failed to upload file(s).');
  }
}

// Make sure to close the database connection if it's open
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

    <title>New Subscriber | Spark Lab Hub</title>

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
    <!-- <link rel="stylesheet" href="assets/vendor/libs/bs-stepper/bs-stepper.css" /> -->
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
              <li class="menu-item active">
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
                <a href="attendance.php" class="menu-link ">
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
                              <span class="fw-medium d-block">John Doe</span>
                              <small class="text-muted">Admin</small>
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
                            <span class="fw-medium d-block mb-1">Profit</span>
                            <h3 class="card-title mb-2">$12,628</h3>
                            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
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
                            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.42%</small>
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
                      <h5 class="mb-0">Add Subscriber</h5>
                      <small class="text-muted float-end">Create a new Subscriber</small>
                    </div>
                    <div class="card-body">
                    <form action="add_subscriber.php" method="post" enctype="multipart/form-data">
                  <div class="row mb-3">
                      <label class="col-sm-2 col-form-label" for="basic-default-name">Full Name</label>
                      <div class="col-sm-10">
                          <input type="text" class="form-control" id="basic-default-name" name="full_name" placeholder="John Doe" required/>
                      </div>
                  </div>
                  <div class="row mb-3">
                      <label class="col-sm-2 col-form-label" for="basic-default-company">Area of Interest</label>
                      <div class="col-sm-10">
                          <select class="form-select" id="exampleFormControlSelect1" name="area_of_interest" aria-label="Default select example" onchange="showTextArea()">
                              <option selected>-- Select an option --</option>
                              <option value="Creativity">Creativity</option>
                              <option value="Innovation">Innovation</option>
                              <option value="Others">Others</option>
                          </select>
                          <input class="form-control mt-2" id="otherTextArea" name="area_of_interest" style="display: none;" placeholder="Specify other area of interest">
                      </div>
                  </div>

                  <div class="row mb-3">
                      <label class="col-sm-2 col-form-label" for="basic-default-email">Email</label>
                      <div class="col-sm-10">
                          <div class="input-group input-group-merge">
                              <input
                                  type="text"
                                  id="basic-default-email"
                                  name="basic-default-email"
                                  class="form-control"
                                  placeholder="john.doe"
                                  aria-label="john.doe"
                                  required
                                  aria-describedby="basic-default-email2" />
                              <span class="input-group-text" id="basic-default-email2">@example.com</span>
                          </div>
                          <div class="form-text">You can use letters, numbers & periods</div>
                      </div>
                  </div>

                  <div class="row mb-3">
                      <label class="col-sm-2 col-form-label" for="basic-default-phone">Phone No</label>
                      <div class="col-sm-10">
                          <input
                              type="text"
                              id="basic-default-phone"
                              name="basic-default-phone"
                              class="form-control phone-mask"
                              placeholder="+234(0) 000 0000 000"
                              aria-label="658 799 8941"
                              aria-describedby="basic-default-phone" />
                      </div>
                  </div>

                  <div class="row mb-3">
                      <label class="col-sm-2 col-form-label" for="basic-default-phone">Contact Address</label>
                      <div class="col-sm-10">
                          <input
                              type="text"
                              id="basic-default-contact-address" 
                              name="basic-default-contact-address"
                              class="form-control phone-mask"
                              placeholder="Plot, st, suite"
                              aria-label="658 799 8941"
                              aria-describedby="basic-default-phone" />
                      </div>
                  </div>

                  <div class="row mb-3">
                      <label class="col-sm-2 col-form-label" for="means-of-identity">Means of Identity</label>
                      <div class="col-sm-10">
                          <input class="form-control" type="file" id="means-of-identity" name="means_of_identity" />
                          <div class="form-text">Upload National ID, Voter's Card, Passport (5mb)</div>
                      </div>
                  </div>

                  <div class="row mb-3">
                      <label class="col-sm-2 col-form-label" for="profile-photo">Profile Photo</label>
                      <div class="col-sm-10">
                          <input class="form-control" type="file" id="profile-photo" name="profile_photo" />
                          <div class="form-text">Upload National Profile Photo (5mb)</div>
                      </div>
                  </div>

                  <div class="row mb-3">
                      <label class="col-sm-2 col-form-label" for="basic-default-password">Password</label>
                      <div class="col-sm-10">
                          <input
                              type="password"
                              id="basic-default-password"
                              name="password"
                              class="form-control phone-mask"
                              placeholder="**********"
                              aria-label=""
                              aria-describedby="basic-default-phone" />
                          <div class="form-text">Not less than 8 characters</div>
                      </div>
                  </div>

                  <div class="row justify-content-end">
                      <div class="col-sm-10">
                          <button type="submit" class="btn btn-primary">Signup</button>
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

    <script>
        $(function () {
            'use strict';

            var dt_basic_table = $('.datatables-basic');

            // Sample Data
            var sampleData = [
                // Sample data here...
            ];

            if (dt_basic_table.length) {
                var dt_basic = dt_basic_table.DataTable({
                    data: sampleData,
                    columns: [
                        { data: '' },
                        { data: '' },
                        { data: 'id' },
                        { data: 'full_name' },
                        { data: 'email' },
                        { data: 'start_date' },
                        { data: 'salary' },
                        { data: 'status' },
                        { data: '' }
                    ],
                    columnDefs: [
                        // ... columnDefs configuration ...
                    ],
                    order: [[2, 'desc']],
                    dom:
                        '<"card-header"<"head-label text-center"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    displayLength: 7,
                    lengthMenu: [7, 10, 25, 50, 75, 100],
                    buttons: [
                        // ... buttons configuration ...
                    ],
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function (row) {
                                    var data = row.data();
                                    return 'Details of ' + data['full_name'];
                                }
                            }),
                            type: 'column',
                            renderer: function (api, rowIdx, columns) {
                                var data = $.map(columns, function (col, i) {
                                    return col.title !== ''
                                        ? '<tr data-dt-row="' +
                                        col.rowIndex +
                                        '" data-dt-column="' +
                                        col.columnIndex +
                                        '">' +
                                        '<td>' +
                                        col.title +
                                        ':' +
                                        '</td> ' +
                                        '<td>' +
                                        col.data +
                                        '</td>' +
                                        '</tr>'
                                        : '';
                                }).join('');

                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    }
                });

                $('div.head-label').html('<h5 class="card-title mb-0">DataTable with Buttons</h5>');
            }
        });
        function showTextArea() {
        var selectBox = document.getElementById("exampleFormControlSelect1");
        var otherTextArea = document.getElementById("otherTextArea");

        if (selectBox.value === "Others") {
            otherTextArea.style.display = "block";
        } else {
            otherTextArea.style.display = "none";
        }
    }
    </script>
  </body>
</html>
