<?php
include "connection.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_GET['id'])) {
    die("Invalid record ID.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT purok FROM record WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res) {
    die("Record not found.");
}

$purok = $res['purok'];

$delete = $conn->prepare("DELETE FROM record WHERE id=?");
$delete->bind_param("i", $id);
$delete->execute();

header("Location: viewrecord.php?purok=" . urlencode($purok));
exit();
?>
