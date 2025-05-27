<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_id = $_POST['flight_id'];
    $passenger_total = $_POST['passenger_total'];
    $total_price = $_POST['total_price'];

    $_SESSION['cancelled'] = true;

    $stmt = $conn->prepare("SELECT * FROM airlines WHERE id = ?");
    $stmt->bind_param("s", $flight_id);
    $stmt->execute();
    $flight = $stmt->get_result()->fetch_assoc();

    $passengers = $_SESSION['passengers'];
} else {
    header("Location: search.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Flight Cancelled - Triviliki</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #434343, #000000);
            color: white;
            padding: 40px 20px;
            margin: 0;
        }
        .box {
            background-color: rgba(255, 255, 255, 0.08);
            padding: 25px;
            margin: 20px auto;
            border-radius: 15px;
            max-width: 700px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }
        h2, h3 {
            color: #ff4d4d;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #888;
            padding: 8px;
            color: white;
        }
        .btn-back {
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
        .btn-back:hover {
            background-color: #009acd;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>🚫 Flight Cancelled</h2>
    <p><strong>Airline:</strong> <?php echo htmlspecialchars($flight['airline_name']); ?></p>
    <p><strong>From:</strong> <?php echo htmlspecialchars($flight['from_location']); ?> → <strong>To:</strong> <?php echo htmlspecialchars($flight['to_location']); ?></p>
    <p><strong>Departure:</strong> <?php echo htmlspecialchars($flight['departure_date']); ?></p>
    <p><strong>Total Passengers:</strong> <?php echo $passenger_total; ?></p>
    <p><strong>Total Price:</strong> Rp<?php echo number_format($total_price, 0, ',', '.'); ?></p>
    <p><strong>Status:</strong> <span style="color: red; font-weight: bold;">Cancelled</span></p>
</div>

<div class="box">
    <h3>👥 Passenger List</h3>
    <table>
        <tr><th>No</th><th>Name</th><th>Gender</th><th>Age</th></tr>
        <?php foreach ($passengers as $i => $p): ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td><?php echo htmlspecialchars($p['gender']); ?></td>
            <td><?php echo htmlspecialchars($p['age']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="box" style="text-align: center;">
    <a href="search.php" class="btn-back">🔙 Back to Search</a>
</div>

</body>
</html>
