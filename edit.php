<?php
include "connection.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_GET['id'])) {
    die("Invalid record ID.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM record WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$record = $stmt->get_result()->fetch_assoc();

if (!$record) {
    die("Record not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $purok = $_POST['purok'];
    $type = $_POST['type'];
    $breed = $_POST['breed'];
    $stocks = $_POST['stocks'];

    $update = $conn->prepare("
        UPDATE record 
        SET purok=?, livestock_type=?, breed=?, stocks=? 
        WHERE id=?
    ");
    $update->bind_param("sssii", $purok, $type, $breed, $stocks, $id);
    $update->execute();

    header("Location: viewrecord.php?purok=" . urlencode($purok) . "&updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Livestock</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #eef1f5;
        padding: 40px;
        display: flex;
        justify-content: center;
    }

    .card {
        background: white;
        width: 450px;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    h2 {
        text-align: center;
        color: #1b5e20;
        margin-bottom: 20px;
        font-size: 26px;
    }

    label {
        font-weight: bold;
        margin-top: 15px;
        display: block;
        color: #1b5e20;
    }

    input, select {
        width: 100%;
        padding: 12px;
        margin-top: 6px;
        border-radius: 8px;
        border: 1px solid #aaa;
        font-size: 15px;
        background: #f9f9f9;
    }

    input:focus, select:focus {
        outline: none;
        border: 1px solid #1b5e20;
        background: #ffffff;
    }

    .btn-save {
        width: 100%;
        margin-top: 25px;
        padding: 12px;
        background: #2e7d32;
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 17px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-save:hover {
        background: #1b5e20;
    }

    .back-btn {
        display: block;
        text-align: center;
        margin-top: 20px;
        background: #ccc;
        padding: 10px;
        border-radius: 8px;
        color: #333;
        text-decoration: none;
        font-weight: bold;
    }

    .back-btn:hover {
        background: #b3b3b3;
    }
</style>

</head>
<body>

<div class="card">

    <h2>Edit Livestock Record</h2>

    <form method="POST">

        <label>Purok</label>
        <select name="purok" required>
            <option <?= $record['purok']=="Purok 1"?"selected":"" ?>>Purok 1</option>
            <option <?= $record['purok']=="Purok 2"?"selected":"" ?>>Purok 2</option>
            <option <?= $record['purok']=="Purok 3"?"selected":"" ?>>Purok 3</option>
            <option <?= $record['purok']=="Purok 4"?"selected":"" ?>>Purok 4</option>
        </select>

        <label>Livestock Type</label>
        <select name="type" required>
            <option <?= $record['livestock_type']=="Pig"?"selected":"" ?>>Pig</option>
            <option <?= $record['livestock_type']=="Cow"?"selected":"" ?>>Cow</option>
            <option <?= $record['livestock_type']=="Goat"?"selected":"" ?>>Goat</option>
            <option <?= $record['livestock_type']=="Chicken"?"selected":"" ?>>Chicken</option>
        </select>

        <label>Breed</label>
        <input type="text" name="breed" value="<?= $record['breed'] ?>" required>

        <label>Stocks</label>
        <input type="number" name="stocks" value="<?= $record['stocks'] ?>" required>

        <button type="submit" class="btn-save">Save Changes</button>
    </form>

    <a href="viewrecord.php?purok=<?= urlencode($record['purok']) ?>" class="back-btn">⟵ Back to Records</a>

</div>

</body>
</html>
