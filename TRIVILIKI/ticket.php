<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['flight_id']) || !isset($_SESSION['passengers'])) {
    header("Location: search.php");
    exit();
}

$flight_id = $_SESSION['flight_id'];
$passenger_total = $_SESSION['passenger_total'];
$passengers = $_SESSION['passengers'];

$stmt = $conn->prepare("SELECT * FROM airlines WHERE id = ?");
$stmt->bind_param("s", $flight_id);
$stmt->execute();
$flight = $stmt->get_result()->fetch_assoc();

$total_price = $flight['price'] * $passenger_total;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket - Triviliki</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: white;
            margin: 0;
            padding: 20px;
        }
        .ticket-container {
            max-width: 800px;
            margin: auto;
            background: #ffffff10;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        h2 {
            color: #fff;
            text-align: center;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            margin-bottom: 10px;
            color: #00ffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff20;
        }
        table, th, td {
            border: 1px solid #ffffff33;
        }
        th, td {
            padding: 8px;
            text-align: left;
            color: #fff;
        }
        button {
            background-color: #00bfff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #009acd;
        }
        .button-group {
            text-align: center;
            margin-top: 30px;
        }
        .cancel-form {
            display: inline;
        }
        .cancel-btn {
            background-color: #ff4d4d;
        }
        .cancel-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>

<div class="ticket-container">
    <h2>Your Flight Ticket</h2>

    <div class="section">
        <h3>Flight Information</h3>
        <p><strong><?php echo $flight['airline_name']; ?></strong></p>
        <p><?php echo $flight['from_location']; ?> → <?php echo $flight['to_location']; ?></p>
        <p>Departure Date: <?php echo $flight['departure_date']; ?></p>
        <p>Total Price: Rp<?php echo number_format($total_price, 0, ',', '.'); ?></p>
    </div>

    <div class="section">
        <h3>Passengers</h3>
        <table>
            <tr>
                <th>No</th>
                <th>Full Name</th>
                <th>Gender</th>
                <th>Age</th>
            </tr>
            <?php foreach ($passengers as $index => $p): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['gender']); ?></td>
                <td><?php echo htmlspecialchars($p['age']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h3>Payment Info</h3>
        <p>Info: Provided</p>
        <p>Status: <strong style="color: #00ff00;">Success</strong></p>
    </div>

    <div class="button-group">
        <button onclick="window.print()">Print Ticket</button>

        <form action="cancel.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');" class="cancel-form">
            <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">
            <input type="hidden" name="passenger_total" value="<?php echo $passenger_total; ?>">
            <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
            <input type="hidden" name="payment_info" value="Provided">
            <button type="submit" class="cancel-btn">Cancel Flight</button>
        </form>

        <button onclick="window.location.href='reschedule.php'">Reschedule</button>
    </div>
</div>

</body>
</html>
