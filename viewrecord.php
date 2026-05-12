<?php
session_start();
include "connection.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_GET['purok']) || empty($_GET['purok'])) {
    echo "<h2>Please select a Purok to view records.</h2>";
    echo "<a href='dashboard.php'>Go Back</a>";
    exit();
}

$purok = $_GET['purok'];

$stmt = $conn->prepare("
    SELECT id, purok, livestock_type, breed, stocks, date_added
    FROM record 
    WHERE purok = ?
    ORDER BY id DESC
");
$stmt->bind_param("s", $purok);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Livestock Records - <?php echo $purok; ?></title>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #333;
            text-align: center;
        }
        table th {
            background: green;
            color: white;
        }
        h2 {
            color: green;
        }

        .btn-edit {
            background: #4CAF50;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-delete {
            background: #E53935;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-edit:hover {
            background: #45a049;
        }
        .btn-delete:hover {
            background: #d32f2f;
        }

        .back-dashboard {
            display: inline-block;
            padding: 12px 22px;
            background: #0b5e2a;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 10px;
            font-size: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: 0.3s ease;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .back-dashboard:hover {
            background: #094c22;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>

<h2>Livestock Records for <?php echo $purok; ?></h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Purok</th>
            <th>Type</th>
            <th>Breed</th>
            <th>Stocks</th>
            <th>Date Added</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php if ($result->num_rows > 0) { ?>
            <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['purok']; ?></td>
                    <td><?php echo $row['livestock_type']; ?></td>
                    <td><?php echo $row['breed']; ?></td>
                    <td><?php echo $row['stocks']; ?></td>
                    <td><?php echo $row['date_added']; ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this record?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="7">No records found.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<a href="dashboard.php" class="back-dashboard">⟵ Back to Dashboard</a>

</body>
</html>
