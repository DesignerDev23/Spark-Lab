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
    $subscriberQuery = "SELECT id FROM subscribers WHERE email = '$email'";
    $subscriberResult = $conn->query($subscriberQuery);

    if ($subscriberResult->num_rows > 0) {
        $subscriberRow = $subscriberResult->fetch_assoc();
        $subscriberId = $subscriberRow['id'];

        // Extract other payment details
        $name = $paymentData['data']['metadata']['full_name'];
        $amount = $paymentData['data']['amount'] / 100; // Convert amount from kobo to naira
        $paymentReference = $paymentData['data']['reference'];
        $status = $paymentData['data']['status'];
        $date = date('Y-m-d H:i:s', strtotime($paymentData['data']['transaction_date']));

        // Insert the payment details into the subscription table
        $sql = "INSERT INTO subscription (subscriber_id, name, email, amount, payment_reference, status, date) 
                VALUES ('$subscriberId', '$name', '$email', '$amount', '$paymentReference', '$status', '$date')";

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
    // Payment verification failed
    echo "Payment verification failed. Status: " . $paymentData['status'];
}

// Make sure to close the database connection if it's open
$conn->close();
?>
