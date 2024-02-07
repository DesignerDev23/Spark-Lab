<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $amountKobo = $_POST['amount'];
    $paymentReference = $_POST['paymentReference'];

    // Convert amount from kobo to Naira
    $amountNaira = $amountKobo / 100;

    // Perform database connection
    $servername = "localhost";
    $username = "sparklab_portal"; // Update with your database username
    $password = "sparklab_portal"; // Update with your database password
    $dbname = "sparklab_portal";   // Update with your database name

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
            if ($amountNaira == 1500) { // Daily amount in Naira
                $duration = 'daily';
                $expirationDate = date('Y-m-d H:i:s', strtotime('+1 day'));
            } elseif ($amountNaira == 6500) { // Weekly amount in Naira
                $duration = 'weekly';
                $expirationDate = date('Y-m-d H:i:s', strtotime('+1 week'));
            } elseif ($amountNaira == 25000) { // Monthly amount in Naira
                $duration = 'monthly';
                $expirationDate = date('Y-m-d H:i:s', strtotime('+1 month'));
            }

            // Prepare data for database insertion
            $status = 'paid'; // Assuming the initial status is pending
            $data = ''; // You can fill this with any additional data as needed

            // Insert subscription details into database
            $sql = "INSERT INTO subscriptions (subscriber_id, email, amount, payment_reference, status, duration, expiration_date)
                    VALUES ('$subscriberId', '$email', '$amountNaira', '$paymentReference', '$status', '$duration', '$expirationDate')";

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
