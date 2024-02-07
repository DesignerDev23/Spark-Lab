<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Form</title>
</head>
<body>
    <form id="subscriberForm" method="POST">
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="subscriberName">Subscriber Name</label>
            <div class="col-sm-10">
                <input type="text" id="subscriberName" name="subscriberName" class="form-control" placeholder="John Doe" required>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="basic-default-email">Email</label>
            <div class="col-sm-10">
                <input type="email" id="basic-default-email" name="email" class="form-control" placeholder="john.doe@example.com" required>
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-2 col-form-label" for="amountSelect">Select Amount</label>
            <div class="col-sm-10">
                <select class="form-select" id="amountSelect" name="amount" aria-label="Select Amount">
                    <option selected disabled>Select an amount</option>
                    <option value="50000">Daily - NGN 500.00</option>
                    <option value="100000">Weekly - NGN 1,000.00</option>
                    <option value="200000">Monthly - NGN 2,000.00</option>
                    <option value="200000">Annually - NGN 2,000.00</option>
                    <!-- Add more options as needed -->
                </select>
            </div>
        </div>

        <!-- Paystack Payment Section -->
        <div class="row mb-3">
            <label class="col-sm-2 col-form-label">Payment</label>
            <div class="col-sm-10">
                <button type="button" id="paystackBtn" class="btn btn-primary">Make Payment</button>
            </div>
        </div>
    </form>

    <!-- Include Paystack script -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <!-- Include custom JavaScript -->
    <script src="script.js"></script>
    <script>
        // Handle Paystack payment
document.getElementById('paystackBtn').addEventListener('click', function() {
    var fullName = document.getElementById('subscriberName').value;
    var email = document.getElementById('basic-default-email').value;
    var amount = document.getElementById('amountSelect').value;

    // Initialize Paystack
    var handler = PaystackPop.setup({
        key: 'pk_test_12658c234f2075a824b3e5862ac5a6b31fc5cd4f',
        email: email,
        amount: amount,
        currency: 'NGN',
        ref: 'SUBSCR_' + Math.floor((Math.random() * 1000000000) + 1), // Generate a unique reference
        onClose: function() {
            alert('Payment closed');
        },
        callback: function(response) {
            // Handle successful payment
            var paymentReference = response.reference;

            // Proceed to form submission
            submitForm(fullName, email, amount, paymentReference);
        }
    });
    handler.openIframe();
});

// Function to submit form data after successful payment
function submitForm(fullName, email, amount, paymentReference) {
    var formData = new FormData();
    formData.append('fullName', fullName);
    formData.append('email', email);
    formData.append('amount', amount);
    formData.append('paymentReference', paymentReference);

    // Send form data to server
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'process_subscription.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Handle success
            console.log(xhr.responseText);
        } else {
            // Handle errors
            console.error('Error occurred while processing subscription: ' + xhr.statusText);
        }
    };
    xhr.send(formData);
}

    </script>
</body>
</html>
