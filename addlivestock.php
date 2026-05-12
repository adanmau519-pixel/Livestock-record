<?php
session_start();
include "connection.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


if (isset($_POST['submit'])) {

    $purok          = $_POST['purok'];
    $livestock_type = $_POST['livestock_type'];
    $breed          = $_POST['breed'];
    $stocks         = $_POST['stocks'];
    $date_added     = date("Y-m-d");

    $stmt = $conn->prepare("
        INSERT INTO record (purok, livestock_type, breed, stocks, date_added)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param("sssss", $purok, $livestock_type, $breed, $stocks, $date_added);
    $stmt->execute();

    echo "<script>
            alert('Livestock Record Added Successfully!');
            window.location='viewrecord.php?purok=$purok';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Livestock</title>

<style>
body{
    margin:0;
    font-family:Arial, sans-serif;
    background:#eef1f5;
}

.sidebar{
    width:240px;
    height:100vh;
    position:fixed;
    background:#0b5e2a;
    padding:25px 20px;
    color:white;
    display:flex;
    flex-direction:column;
    align-items:center;
    box-sizing:border-box;
}

.sidebar img{
    width:120px;
    height:120px;
    border-radius:50%;
    margin-bottom:15px;
}

.sidebar h2{
    text-align:center;
    font-size:17px;
    line-height:1.4;
    margin-bottom:20px;
}

.sidebar a{
    width:100%;
    padding:12px;
    margin:6px 0;
    background:rgba(255,255,255,.15);
    color:white;
    text-decoration:none;
    border-radius:6px;
    text-align:center;
    transition:.2s;
}

.sidebar a:hover{
    background:rgba(255,255,255,.3);
}

.sidebar a.active{
    background:white;
    color:#0b5e2a;
    font-weight:bold;
}

.main{
    margin-left:260px;
    padding:30px;
}

.header{
    background:#1c5e20;
    color:white;
    padding:20px;
    font-size:22px;
    text-align:center;
    border-radius:10px;
    margin-bottom:30px;
}

.container-wrapper{
    display:flex;
    justify-content:center;
}

.container{
    width:50%;
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 6px 14px rgba(0,0,0,.15);
}

label{
    font-weight:bold;
}

input, select{
    width:100%;
    padding:10px;
    margin:7px 0 18px;
    border:1px solid #aaa;
    border-radius:6px;
}

button{
    width:100%;
    padding:12px;
    background:#bbf7d0;
    border:none;
    border-radius:14px;
    cursor:pointer;
    font-size:16px;
}

button:hover{
    background:#a7f3d0;
}

@media(max-width:768px){
    .container{width:90%;}
}
</style>
</head>

<body>

<div class="sidebar">
    <img src="images/logo.jpg">
    <h2>
        Livestock Monitoring System<br>
        Brgy. Abelo
    </h2>

    <a href="dashboard.php">Livestock Records</a>
    <a class="active" href="add_livestock.php">Add Livestock</a>
    <a href="report.php">Dashboard Overview</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">
    <div class="header">ADD LIVESTOCK RECORD</div>

    <div class="container-wrapper">
        <div class="container">
            <form method="POST">
                <label>Purok</label>
                <select name="purok" required>
                    <option value="">Select Purok</option>
                    <option>Purok 1</option>
                    <option>Purok 2</option>
                    <option>Purok 3</option>
                    <option>Purok 4</option>
                </select>

                <label>Livestock Type</label>
                <select name="livestock_type" required>
                    <option value="">Select Type</option>
                    <option>Pig</option>
                    <option>Cow</option>
                    <option>Goat</option>
                    <option>Chicken</option>
                </select>

                <label>Breed</label>
                <input type="text" name="breed" required>

                <label>Stocks</label>
                <input type="number" name="stocks" required>

                <button type="submit" name="submit">Add Record</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
