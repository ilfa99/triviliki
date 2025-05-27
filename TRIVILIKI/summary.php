<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if (!isset($_SESSION['flight_id']) || !isset($_SESSION['passenger_total']) || empty($_SESSION['passengers'])) {
    header("Location: search.php");
    exit();
}

$flight_id = $_SESSION['flight_id'];
$passenger_total = $_SESSION['passenger_total'];
$passengers = $_SESSION['passengers'];

$stmt = $conn->prepare("SELECT * FROM airlines WHERE id = ?");
$stmt->bind_param("s", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();

if (!$flight) {
    echo "<p style='color:white;'>Flight not found.</p>";
    exit();
}

$total_price = $flight['price'] * $passenger_total;

$stmt = $conn->prepare("INSERT INTO bookings (airline_id, airline_name, flight_date, passenger_count, total_price, payment_status) VALUES (?, ?, ?, ?, ?, 'On Going')");
$stmt->bind_param("sssii", $flight_id, $flight['airline_name'], $flight['departure_date'], $passenger_total, $total_price);
$stmt->execute();
$booking_id = $stmt->insert_id;
$_SESSION['booking_id'] = $booking_id;

foreach ($passengers as $p) {
    $stmt = $conn->prepare("INSERT INTO passengers (booking_id, name, age, gender) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $booking_id, $p['name'], $p['age'], $p['gender']);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flight Summary - Triviliki</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: white;
            margin: 0;
            padding: 40px 20px;
        }
        .box {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 25px;
            margin: 20px auto;
            border-radius: 15px;
            max-width: 600px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }
        h2, h3 {
            margin-top: 0;
            color: #ffdd57;
        }
        ul {
            padding-left: 20px;
        }
        .btn-next {
            display: inline-block;
            padding: 12px 24px;
            background-color: #00bfff;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .btn-next:hover {
            background-color: #009acd;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>✈ Flight Summary</h2>
    <p><strong>Airline:</strong> <?php echo htmlspecialchars($flight['airline_name']); ?></p>
    <p><strong>Route:</strong> <?php echo htmlspecialchars($flight['from_location']) . " → " . htmlspecialchars($flight['to_location']); ?></p>
    <p><strong>Date:</strong> <?php echo htmlspecialchars($flight['departure_date']); ?></p>
    <p><strong>Price per Passenger:</strong> Rp<?php echo number_format($flight['price'], 0, ',', '.'); ?></p>
    <p><strong>Total Price:</strong> Rp<?php echo number_format($total_price, 0, ',', '.'); ?></p>
</div>

<div class="box">
    <h3>👤 Passenger Details</h3>
    <ul>
        <?php foreach ($passengers as $index => $p): ?>
            <li><?php echo ($index + 1) . ". " . htmlspecialchars($p['name']) . " (" . htmlspecialchars($p['age']) . " y.o, " . htmlspecialchars($p['gender']) . ")"; ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="box" style="text-align:center;">
    <a class="btn-next" href="payment.php">Proceed to Payment</a>
</div>

</body>
</html>