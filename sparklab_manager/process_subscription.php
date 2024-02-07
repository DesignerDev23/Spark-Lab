<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $amount = $_POST['amount'];
    $paymentReference = $_POST['paymentReference'];

    // Calculate subscription duration and expiration date based on amount
    if ($amount == 50000) {
        $duration = 'daily';
        $expirationDate = date('Y-m-d H:i:s', strtotime('+1 day'));
    } elseif ($amount == 100000) {
        $duration = 'weekly';
        $expirationDate = date('Y-m-d H:i:s', strtotime('+1 week'));
    } elseif ($amount == 200000) {
        $duration = 'monthly';
        $expirationDate = date('Y-m-d H:i:s', strtotime('+1 month'));
    }

    // Prepare data for database insertion
    $status = 'paid'; // Assuming the initial status is pending
    $data = 'Paid'; // You can fill this with any additional data as needed

    // Perform database operations (insert subscription details)
    // Replace this with your actual database connection and query
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sparklab";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    
    // Insert subscription details into database
    $sql = "INSERT INTO subscriptions (subscriber_id, email, amount, payment_reference, status, duration, expiration_date)
            VALUES ('$fullName', '$email', $amount, '$paymentReference', '$status', '$duration', '$expirationDate')";

    if ($conn->query($sql) === TRUE) {
        echo "Subscription processed successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
