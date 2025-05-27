<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

// Cek login dan booking_id
if (!isset($_SESSION['user']) || !isset($_GET['booking_id'])) {
    header("Location: search.php");
    exit();
}

$booking_id = $_GET['booking_id'];

// Proses reschedule jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_airline_id'])) {
        $new_airline_id = $_POST['new_airline_id'];

        $stmt = $conn->prepare("UPDATE bookings SET airline_id = ? WHERE id = ?");
        $stmt->bind_param("ss", $new_airline_id, $booking_id);

        if ($stmt->execute()) {
            $_SESSION['airline_id'] = $new_airline_id;
            $_SESSION['booking_id'] = $booking_id;
            header("Location: ticket.php");
            exit();
        } else {
            die("Reschedule failed: " . $conn->error);
        }
    }
}

// Ambil daftar penerbangan
$flights = $conn->query("SELECT * FROM airlines");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reschedule Flight - Triviliki</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #0f62fe, #0072f5);
            color: white;
        }
        .container {
            max-width: 700px;
            margin: 100px auto;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        select, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            font-size: 16px;
        }
        select {
            background-color: white;
            color: #333;
        }
        button {
            background-color: #0050db;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #003cb3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reschedule Your Flight</h2>
        <form method="POST">
            <label for="new_airline_id">Select New Flight:</label>
            <select name="new_airline_id" id="new_airline_id" required>
                <option value="">-- Choose a new flight --</option>
                <?php while ($row = $flights->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>">
                        <?= $row['airline_name'] ?> - <?= $row['from_location'] ?> to <?= $row['to_location'] ?> on <?= $row['departure_date'] ?> (Rp<?= number_format($row['price'], 0, ',', '.') ?>)
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Confirm Reschedule</button>
        </form>
    </div>
</body>
</html>
