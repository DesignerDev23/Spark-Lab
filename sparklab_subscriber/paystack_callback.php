<?php
// Include your database configuration here
include '../config/config.php';

// Verify the payment response from Paystack
$reference = $_GET['reference'];
$paystack_secret_key = 'sk_test_cc20824a5bc9e5a3771d289406179f2e1c3f4a84'; // Replace with your actual Paystack secret key

$ch = curl_init('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $paystack_secret_key,
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$paymentData = json_decode($response, true);

if ($paymentData['status'] === true) {
    // Payment verification successful
    $email = $paymentData['data']['metadata']['email'];

    // Retrieve the subscriber ID based on the email
    $subscriberQuery = "SELECT registration_id FROM subscribers WHERE email = '$email'";
    $subscriberResult = $conn->query($subscriberQuery);

    if ($subscriberResult) {
        // Check if subscriber exists
        if ($subscriberResult->num_rows > 0) {
            $subscriberRow = $subscriberResult->fetch_assoc();
            $subscriberId = $subscriberRow['registration_id'];

            // Extract other payment details
            $name = $paymentData['data']['metadata']['full_name'];
            $email = $paymentData['data']['metadata']['email'];
            $amount = $paymentData['data']['amount'] / 100; // Convert amount from kobo to naira
            $paymentReference = $paymentData['data']['reference'];
            $status = $paymentData['data']['status'];
            $date = date('Y-m-d H:i:s', strtotime($paymentData['data']['transaction_date']));

            // Manually set subscription duration based on the selected amount
            $subscriptionDuration = 'Default'; // Set your default duration

            // Determine subscription duration based on the amount
            switch ($amount) {
                case 1500:
                    $subscriptionDuration = 'Daily';
                    break;
                case 6500:
                    $subscriptionDuration = 'Weekly';
                    break;
                case 25000:
                    $subscriptionDuration = 'Monthly';
                    break;
                // Add more cases as needed
                default:
                    // Handle other cases or set a default duration
            }

            // Set expiration date based on subscription duration
            switch ($subscriptionDuration) {
                case 'Daily':
                    $expirationDate = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($date)));
                    break;
                case 'Weekly':
                    $expirationDate = date('Y-m-d H:i:s', strtotime('+1 week', strtotime($date)));
                    break;
                case 'Monthly':
                    $expirationDate = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($date)));
                    break;
                // Add more cases as needed
                default:
                    // Handle other cases or set a default expiration date
            }

            // Insert the payment details into the subscription table
            $sql = "INSERT INTO subscription (subscriber_id, name, email, amount, payment_reference, status, date, subscription_duration, expiration_date) 
                    VALUES ('$subscriberId', '$name', '$email', '$amount', '$paymentReference', '$status', '$date', '$subscriptionDuration', '$expirationDate')";

            if ($conn->query($sql) === TRUE) {
                // Payment details successfully inserted into the database
                echo "Payment successful. Transaction Reference: " . $paymentReference;
            } else {
                // Error inserting payment details into the database
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            // Subscriber not found
            echo "Error: Subscriber not found for email $email";
        }
    } else {
        // Query execution error
        echo "Error executing query: " . $conn->error;
    }
} else {
    // Payment verification failed
    echo "Payment verification failed. Status: " . $paymentData['status'];
}

// Make sure to close the database connection if it's open
$conn->close();
?>
