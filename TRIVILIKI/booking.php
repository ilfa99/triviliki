<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if (!isset($_SESSION['passengers'])) {
    $_SESSION['passengers'] = [];
}

if (isset($_GET['id']) && isset($_GET['passengers'])) {
    $_SESSION['flight_id'] = $_GET['id'];
    $_SESSION['passenger_total'] = intval($_GET['passengers']);
    $_SESSION['passengers'] = []; 
}

$flight_id = $_SESSION['flight_id'] ?? null;
$passenger_total = $_SESSION['passenger_total'] ?? 0;

if (!$flight_id || $passenger_total <= 0) {
    header("Location: search.php");
    exit();
}

$current_index = count($_SESSION['passengers']) + 1;

$stmt = $conn->prepare("SELECT * FROM airlines WHERE id = ?");
$stmt->bind_param("s", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();

if (!$flight) {
    echo "<p style='color:white;'>Flight not found.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];

    $_SESSION['passengers'][] = [
        'name' => $name,
        'age' => $age,
        'gender' => $gender
    ];

    if (count($_SESSION['passengers']) >= $passenger_total) {
        header("Location: summary.php");
        exit();
    } else {
        header("Location: booking.php");
        exit();
    }
}
?>

<!DOCTYPE html>

<html>
<head>
    <title>Booking - Triviliki</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .box {
            background: #ffffff10;
            padding: 30px;
            border-radius: 15px;
            width: 360px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            margin-bottom: 20px;
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
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #009acd;
        }
        ul {
            padding-left: 20px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Flight: <?php echo $flight['airline_name']; ?></h2>
    <p><?php echo $flight['from_location'] . " → " . $flight['to_location']; ?></p>
    <p>Date: <?php echo $flight['departure_date']; ?></p>
    <p>Price per person: Rp<?php echo number_format($flight['price'], 0, ',', '.'); ?></p>
    <p><strong>Total Price: Rp<?php echo number_format($flight['price'] * $passenger_total, 0, ',', '.'); ?></strong></p>
</div>

<div class="box">
    <h3>Passenger <?php echo $current_index; ?> of <?php echo $passenger_total; ?></h3>
    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="age" placeholder="Age" required>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <button type="submit">Next</button>
    </form>
</div>

<?php if (!empty($_SESSION['passengers'])): ?>

<div class="box">
    <h3>Entered Passengers</h3>
    <ul>
        <?php foreach ($_SESSION['passengers'] as $p): ?>
            <li><?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['age']); ?>, <?php echo htmlspecialchars($p['gender']); ?>)</li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

</body>
</html>
