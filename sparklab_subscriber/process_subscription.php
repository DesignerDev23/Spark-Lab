<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $amount = $_POST['amount'];
    $paymentReference = $_POST['paymentReference'];

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

    // Retrieve the subscriber ID based on the email
    $subscriberQuery = "SELECT registration_id FROM subscribers WHERE email = '$email'";
    $subscriberResult = $conn->query($subscriberQuery);

    if ($subscriberResult) {
        // Check if subscriber exists
        if ($subscriberResult->num_rows > 0) {
            $subscriberRow = $subscriberResult->fetch_assoc();
            $subscriberId = $subscriberRow['registration_id'];

            // Calculate subscription duration and expiration date based on amount
            if ($amount == 150000) { // Daily amount in kobo
                $duration = 'daily';
                $expirationDate = date('Y-m-d H:i:s', strtotime('+1 day'));
            } elseif ($amount == 650000) { // Weekly amount in kobo
                $duration = 'weekly';
                $expirationDate = date('Y-m-d H:i:s', strtotime('+1 week'));
            } elseif ($amount == 2500000) { // Monthly amount in kobo
                $duration = 'monthly';
                $expirationDate = date('Y-m-d H:i:s', strtotime('+1 month'));
            }
            

            // Prepare data for database insertion
            $status = 'pending'; // Assuming the initial status is pending
            $data = ''; // You can fill this with any additional data as needed

            // Insert subscription details into database
            $sql = "INSERT INTO subscriptions (subscriber_id, email, amount, payment_reference, status, duration, expiration_date)
                    VALUES ('$subscriberId', '$email', $amount, '$paymentReference', '$status', '$duration', '$expirationDate')";

            if ($conn->query($sql) === TRUE) {
                echo "Subscription processed successfully!";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Subscriber not found for email: $email";
        }
    } else {
        echo "Error: " . $subscriberQuery . "<br>" . $conn->error;
    }

    // Close database connection
    $conn->close();
}
?>
