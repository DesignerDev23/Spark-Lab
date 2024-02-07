<?php
include '../config/config.php'; // Make sure to include your database connection here

// Check if the user is not logged in


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
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Register | Spark Lab Hub</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />
    <!-- Add this in the head section of your HTML -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
    <!-- Page -->
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl p-30">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="col-8">
          <!-- Register Card -->
          <div class="card p-20">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="dashboard.php" class="app-brand-link gap-2">
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
                  <span class="app-brand-text demo text-body fw-bold">Spark Lab Hub</span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Adventure starts here 🚀</h4>
              <p class="mb-4">Make your app management easy and fun!</p>

              <div class="card-body">
              <form action="signup.php" method="post" enctype="multipart/form-data">
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

              <p class="text-center">
                <span>Already have an account?</span>
                <a href="index.php">
                  <span>Sign in instead</span>
                </a>
              </p>
            </div>
          </div>
          <!-- Register Card -->
        </div>
      </div>
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

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>


    <script>
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
