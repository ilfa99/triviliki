<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['flight_id']) || empty($_SESSION['passengers'])) {
    header("Location: search.php");
    exit();
}

$flight_id = $_SESSION['flight_id'];
$passenger_total = $_SESSION['passenger_total'];

$stmt = $conn->prepare("SELECT * FROM airlines WHERE id = ?");
$stmt->bind_param("s", $flight_id);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();

$total_price = $flight['price'] * $passenger_total;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment - Triviliki</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: #ffffff10;
            padding: 25px;
            border-radius: 15px;
            width: 360px;
            max-width: 90%;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 10px;
            background: #ffffffcc;
            color: #333;
        }
        button {
            background-color: #00bfff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #009acd;
        }
        #ewallet_type_container {
            display: none;
        }
    </style>
    <script>
        function updatePlaceholder() {
            const method = document.getElementById('payment_method').value;
            const ewalletContainer = document.getElementById('ewallet_type_container');
            const paymentInfo = document.getElementById('payment_info');

            if (method === 'ewallet') {
                ewalletContainer.style.display = 'block';
                updateEwalletPlaceholder();
            } else {
                ewalletContainer.style.display = 'none';
                if (method === 'bank_transfer') {
                    paymentInfo.placeholder = 'Enter your bank account number';
                } else if (method === 'credit_card') {
                    paymentInfo.placeholder = 'Enter your credit card number';
                } else {
                    paymentInfo.placeholder = 'Enter your payment info';
                }
            }
        }

        function updateEwalletPlaceholder() {
            const ewalletType = document.getElementById('ewallet_type').value;
            const paymentInfo = document.getElementById('payment_info');
            if (ewalletType === 'ovo') {
                paymentInfo.placeholder = 'Enter your OVO number';
            } else if (ewalletType === 'dana') {
                paymentInfo.placeholder = 'Enter your DANA number';
            } else if (ewalletType === 'gopay') {
                paymentInfo.placeholder = 'Enter your Gopay number';
            } else {
                paymentInfo.placeholder = 'Enter your E-Wallet number';
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Payment</h2>
    <p><strong><?php echo $flight['airline_name']; ?></strong></p>
    <p><?php echo $flight['from_location']; ?> → <?php echo $flight['to_location']; ?></p>
    <p>Date: <?php echo $flight['departure_date']; ?></p>
    <p><strong>Total: Rp<?php echo number_format($total_price, 0, ',', '.'); ?></strong></p>

    <form action="ticket.php" method="post">
        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" id="payment_method" onchange="updatePlaceholder()" required>
            <option value="">-- Select --</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="ewallet">E-Wallet</option>
            <option value="credit_card">Credit Card</option>
        </select>

        <div id="ewallet_type_container">
            <label for="ewallet_type">E-Wallet Type:</label>
            <select name="ewallet_type" id="ewallet_type" onchange="updateEwalletPlaceholder()">
                <option value="">-- Select E-Wallet --</option>
                <option value="ovo">OVO</option>
                <option value="dana">DANA</option>
                <option value="gopay">Gopay</option>
            </select>
        </div>

        <label for="payment_info">Payment Info:</label>
        <input type="text" id="payment_info" name="payment_info" placeholder="Enter your payment info" required>

        <button type="submit">Pay Now</button>
    </form>
</div>

</body>
</html>
