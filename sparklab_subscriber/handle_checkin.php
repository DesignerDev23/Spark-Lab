<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $registrationId = $_POST['registrationId'];

    // Perform database connection
    $servername = "localhost";
    $username = "root"; // Update with your database username
    $password = ""; // Update with your database password
    $dbname = "sparklab";   // Update with your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check for an active subscription using the registration ID
    $subscriptionQuery = "SELECT * FROM subscriptions WHERE subscriber_id = '$registrationId' AND expiration_date > NOW()";
    $subscriptionResult = $conn->query($subscriptionQuery);

    if ($subscriptionResult && $subscriptionResult->num_rows > 0) {
        // Subscriber has an active subscription, proceed with storing check-in information

        // Retrieve additional form data
        $name = $_POST['fullName']; // Assuming you have a field for subscriber's full name in the form
        // Assuming you have other form fields such as check-in date and time

        // Prepare data for check-in table insertion
        $checkInDate = date('Y-m-d H:i:s'); // Current date and time
        // You can add more check-in information as needed

        // Insert check-in information into the check-in table
        $checkInSql = "INSERT INTO check_in (subscriber_id, name, email, check_in_date)
                       VALUES ('$registrationId', '$name', '$email', '$checkInDate')";

        if ($conn->query($checkInSql) === TRUE) {
            // Trigger JavaScript to display a Swift alert
            echo "<script>window.onload = function() { showSwiftAlert('Check-in information stored successfully!'); }</script>";
        } else {
            echo "Error: " . $checkInSql . "<br>" . $conn->error;
        }
    } else {
        // Trigger JavaScript to display a Swift alert
        echo "<script>window.onload = function() { showSwiftAlert('Subscriber does not have an active subscription.'); }</script>";
    }

    // Close database connection
    $conn->close();
}
?>
