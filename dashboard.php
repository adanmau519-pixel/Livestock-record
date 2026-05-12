<?php
session_start();
include "connection.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function getCount($conn, $purok, $type) {
    $stmt = $conn->prepare("SELECT SUM(stocks) total FROM record WHERE purok=? AND livestock_type=?");
    $stmt->bind_param("ss",$purok,$type);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    return $r['total'] ?? 0;
}

function getTotalAnimals($conn, $purok) {
    $stmt = $conn->prepare("SELECT SUM(stocks) total FROM record WHERE purok=?");
    $stmt->bind_param("s",$purok);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    return $r['total'] ?? 0;
}

$puroks = ["Purok 1","Purok 2","Purok 3","Purok 4"];
?>
<!DOCTYPE html>
<html>
<head>
<title>LIVESTOCK RECORDS</title>

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
    padding:25px 20px;   /* ✅ FIXED */
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
    transition:0.2s;
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
}


.card-container{
    display:flex;
    flex-wrap:wrap;
    gap:25px;
    justify-content:center;
}

.card{
    width:300px;
    background:white;
    padding:25px;
    border-radius:18px;
    box-shadow:0 6px 14px rgba(0,0,0,.15);
}

.flag{
    font-size:22px;
    font-weight:bold;
    text-align:center;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
}

.flag1{background:#b8e8dd;}
.flag2{background:#f7eaa0;}
.flag3{background:#f7c6bb;}
.flag4{background:#c8d7f7;}

.animal-row{
    display:flex;
    justify-content:space-between;
    padding:6px 0;
    border-bottom:1px solid #ccc;
}

button{
    width:100%;
    padding:10px;
    margin-top:10px;
    border:none;
    border-radius:10px;
    cursor:pointer;
}

.print-btn{background:#d1fae5;}
.add-btn{background:#bbf7d0;}
.view-btn{background:#a7f3d0;}
</style>
</head>

<body>

<div class="sidebar">
    <img src="images/logo.jpg">
    <h2>
        Livestock Monitoring System<br>
        Brgy. Abelo
    </h2>

    <a class="active" href="dashboard.php">Livestock Records</a>
    <a href="addlivestock.php">Add Livestock</a>
    <a href="report.php">Dashboard Overview</a>
    <a href="logout.php">Logout</a>
</div>


<div class="main">

<div class="header">Livestock Record</div><br>

<div class="card-container">
<?php foreach($puroks as $i=>$p): ?>
<?php
$pig   = getCount($conn,$p,"Pig");
$cow   = getCount($conn,$p,"Cow");
$goat  = getCount($conn,$p,"Goat");
$chick = getCount($conn,$p,"Chicken");
$total = getTotalAnimals($conn,$p);
?>
<div class="card">
    <div class="flag flag<?= $i+1 ?>"><?= $p ?></div>

    <?php if($pig): ?>
        <div class="animal-row"><span>🐖 Pig</span><span><?= $pig ?></span></div>
    <?php endif; ?>

    <?php if($cow): ?>
        <div class="animal-row"><span>🐄 Cow</span><span><?= $cow ?></span></div>
    <?php endif; ?>

    <?php if($chick): ?>
        <div class="animal-row"><span>🐔 Chicken</span><span><?= $chick ?></span></div>
    <?php endif; ?>

    <?php if($goat): ?>
        <div class="animal-row"><span>🐐 Goat</span><span><?= $goat ?></span></div>
    <?php endif; ?>

    <div class="animal-row" style="font-weight:bold;">
        <span>Total</span>
        <span><?= $total ?></span>
    </div>

    <button class="print-btn" onclick="printTable('<?= $p ?>')">PRINT</button>

    <a href="addlivestock.php?purok=<?= urlencode($p) ?>">
        <button class="add-btn">ADD</button>
    </a>

    <a href="viewrecord.php?purok=<?= urlencode($p) ?>">
        <button class="view-btn">VIEW</button>
    </a>
</div>
<?php endforeach; ?>
</div>

</div>

<script>
const purokData = {
<?php
foreach($puroks as $p){
    echo "'$p': `";
    $stmt=$conn->prepare("SELECT * FROM record WHERE purok=?");
    $stmt->bind_param("s",$p);
    $stmt->execute();
    $res=$stmt->get_result();
    while($r=$res->fetch_assoc()){
        echo "<tr>
            <td>{$r['id']}</td>
            <td>{$r['purok']}</td>
            <td>{$r['livestock_type']}</td>
            <td>{$r['breed']}</td>
            <td>{$r['stocks']}</td>
            <td>{$r['date_added']}</td>
        </tr>";
    }
    echo "`,"; 
}
?>
};

function printTable(purok){
let win=window.open('','','width=900,height=650');
win.document.write(`
<!DOCTYPE html>
<html>
<head>
<style>
@page{size:A4 portrait;margin:15mm;}
body{font-family:Arial;margin:0;}

.print-header{
    text-align:center;
    margin-bottom:10px;
}

.print-header img{
    width:80px;
}

table{
    width:100%;
    border-collapse:collapse;
    font-size:11px;
}

th,td{
    border:1px solid #000;
    padding:5px;
    text-align:center;
}

th{
    background:#0b5e2a;
    color:white;
}

tr{
    page-break-inside:avoid;
}
</style>
</head>
<body>

<div class="print-header">
    <img src="images/logo.jpg"><br><br>
	<b>Barangay Abelo<br>
	San Nicolas<b><br><br>
	<b>__________________________________________________________________________________________________________________________________<b>
	
</div>

<b>Livestock Records – ${purok}</b>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Purok</th>
    <th>Type</th>
    <th>Breed</th>
    <th>Stocks</th>
    <th>Date Added</th>
</tr>
</thead>
<tbody>
${purokData[purok]}
</tbody>
</table>

</body>
</html>
`);
win.document.close();
win.print();
}
</script>

</body>
</html>
