<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Pastikan data pencarian tersedia
if (!isset($_SESSION['search_from']) || !isset($_SESSION['search_to']) || !isset($_SESSION['search_date'])) {
    header("Location: search.php");
    exit();
}

$from = $_SESSION['search_from'];
$to = $_SESSION['search_to'];
$date = $_SESSION['search_date'];
$passengers = $_SESSION['search_passengers'];

// Ambil data penerbangan dari database
$stmt = $conn->prepare("SELECT * FROM airlines WHERE from_location=? AND to_location=? AND departure_date=?");
$stmt->bind_param("sss", $from, $to, $date);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Flight - Triviliki</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: #fff;
            padding: 40px;
        }
        .flight-box {
            background: #ffffff10;
            padding: 20px;
            margin: 15px auto;
            border-radius: 10px;
            max-width: 600px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }
        .select-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #00bfff;
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 10px;
        }
        .select-btn:hover {
            background-color: #009acd;
        }
    </style>
</head>
<body>
    <h2>Select a Flight</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="flight-box">
                <p><strong><?php echo $row['airline_name']; ?></strong></p>
                <p><?php echo $row['from_location']; ?> → <?php echo $row['to_location']; ?></p>
                <p>Date: <?php echo $row['departure_date']; ?></p>
                <p>Price: Rp<?php echo number_format($row['price'], 0, ',', '.'); ?> / person</p>
                <a class="select-btn" href="booking.php?id=<?php echo $row['id']; ?>&passengers=<?php echo $passengers; ?>">Select</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No flights found for your search.</p>
    <?php endif; ?>
</body>
</html>
