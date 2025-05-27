<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['search_from'] = $_POST['from'];
    $_SESSION['search_to'] = $_POST['to'];
    $_SESSION['search_date'] = $_POST['date'];
    $_SESSION['search_passengers'] = $_POST['passengers'];

    header("Location: airlines.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Flights - Triviliki</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .search-container {
            background: #ffffff10;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            width: 400px;
        }
        .search-container h2 {
            margin-bottom: 25px;
            text-align: center;
        }
        input, select {
            width: 100%;
            padding: 10px 10px;
            margin: 10px 0;
            border: none;
            border-radius: 10px;
            background: #ffffffcc;
            color: #333;
        }
        input[type="submit"] {
            background-color: #00bfff;
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #009acd;
        }
    </style>
</head>
<body>
    <div class="search-container">
        <h2>Search Flights</h2>
        <form method="POST" action="search.php">
            <label for="from">From:</label>
            <select name="from" required>
                <option value="">Select departure</option>
                <option value="Jakarta">Jakarta</option>
                <option value="Surabaya">Surabaya</option>
                <option value="Medan">Medan</option>
                <option value="Bali">Bali</option>
                <option value="Makassar">Makassar</option>
            </select>

            <label for="to">To:</label>
            <select name="to" required>
                <option value="">Select destination</option>
                <option value="Jakarta">Jakarta</option>
                <option value="Surabaya">Surabaya</option>
                <option value="Medan">Medan</option>
                <option value="Bali">Bali</option>
                <option value="Makassar">Makassar</option>
            </select>

            <label for="date">Date:</label>
            <input type="date" name="date" required>

            <label for="passengers">Passengers:</label>
            <input type="number" name="passengers" min="1" max="10" value="1" required>

            <input type="submit" value="Search">
        </form>
    </div>
</body>
</html>
