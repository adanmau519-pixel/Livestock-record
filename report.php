<?php
session_start();
include "connection.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$animalTypes = ["Pig","Cow","Goat","Chicken"];
$animalCounts = [];

foreach($animalTypes as $type){
    $stmt = $conn->prepare("SELECT SUM(stocks) total FROM record WHERE livestock_type=?");
    $stmt->bind_param("s",$type);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $animalCounts[] = (int)$r['total'];
}
$totalAnimals = array_sum($animalCounts);

$dailyLabels = [];
$dailyCounts = [];
$q = $conn->query("
    SELECT DATE(date_added) d, SUM(stocks) t 
    FROM record 
    GROUP BY DATE(date_added)
");
while($r=$q->fetch_assoc()){
    $dailyLabels[] = $r['d'];
    $dailyCounts[] = $r['t'];
}

$records = $conn->query("SELECT * FROM record ORDER BY date_added ASC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard Overview</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
}


.print-header{
    display:none;
    text-align:center;
    margin-bottom:15px;
}
.print-header img{
    width:80px;
}


.stats-wrapper{
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:20px;
    margin:25px 0;
}

.stat-card{
    width:180px;
    padding:18px;
    border-radius:15px;
    text-align:center;
    color:white;
    background:linear-gradient(135deg,#0b5e2a,#2E804C,#0b5e2a);
    box-shadow:0 4px 12px rgba(0,0,0,.3);
}


.chart-wrapper{
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:20px;
}

.card{
    width:300px;
    padding:15px;
    border-radius:15px;
    background:linear-gradient(135deg,#0b5e2a,#2E804C,#0b5e2a);
    color:white;
    box-shadow:0 4px 12px rgba(0,0,0,.3);
}

.card.full{
    width:95%;
}

.card canvas{
    width:100% !important;
    height:220px !important;
}


.table-card{
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 4px 10px rgba(0,0,0,.2);
    margin-top:25px;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#0b5e2a;
    color:white;
}

th,td{
    border:1px solid #ccc;
    padding:8px;
    text-align:center;
}


.search-wrapper{
    display:flex;
    gap:10px;
    margin-bottom:15px;
}

.search-wrapper input{
    padding:8px 12px;
    width:250px;
    border:1px solid #ccc;
    border-radius:6px;
}

.search-wrapper button{
    padding:8px 14px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-weight:bold;
}

.btn-reset{
    background:#dc2626;
    color:white;
}


.action{
    padding:5px 10px;
    border-radius:5px;
    color:white;
    text-decoration:none;
    font-size:13px;
}
.edit{background:#2563eb;}
.delete{background:#dc2626;}


@page{
    size:A4 landscape;
    margin:15mm;
}

@media print{
    body{
        background:white;
        zoom:.8;
    }

    .sidebar,
    .no-print{
        display:none !important;
    }

    .print-header{
        display:block;
    }

    .main{
        margin:0;
        padding-top:15mm;
    }

    thead{display:table-header-group;}
    tr{page-break-inside:avoid;}

    table{
        font-size:10px;
    }

    th,td{
        padding:4px;
    }

    canvas{
        height:160px !important;
    }
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
    <a href="addlivestock.php">Add Livestock</a>
    <a class="active" href="report.php">Dashboard Overview</a>
    <a href="logout.php">Logout</a>
</div>


<div class="main">

<div class="print-header">
    <img src="images/logo.jpg"><br><br>
    <b>Barangay Abelo<br>San Nicolas</b><br><br>
    <b>_______________________________________________________________________________________</b>
</div>

<div class="header">DASHBOARD OVERVIEW</div><br>

<button class="no-print" onclick="window.print()">🖨️ PRINT</button>

<div class="stats-wrapper">
    <div class="stat-card"><h3>Total</h3><p><?= $totalAnimals ?></p></div>
    <div class="stat-card"><h3>Pigs</h3><p><?= $animalCounts[0] ?></p></div>
    <div class="stat-card"><h3>Cows</h3><p><?= $animalCounts[1] ?></p></div>
    <div class="stat-card"><h3>Goats</h3><p><?= $animalCounts[2] ?></p></div>
    <div class="stat-card"><h3>Chickens</h3><p><?= $animalCounts[3] ?></p></div>
</div>

<div class="chart-wrapper">
    <div class="card">
        <h3>Animal Ratio</h3>
        <canvas id="pie"></canvas>
    </div>

    <div class="card">
        <h3>Animal Count</h3>
        <canvas id="bar"></canvas>
    </div>
</div><br>

<div class="chart-wrapper">
    <div class="card full">
        <h3>Daily Records</h3>
        <canvas id="line"></canvas>
    </div>
</div>

<div class="table-card">
<h3>Overall Livestock Record</h3>

<div class="search-wrapper no-print">
    <input type="text" id="searchInput" placeholder="Search...">
    <button onclick="searchTable()">Search</button>
    <button class="btn-reset" onclick="resetSearch()">Reset</button>
</div>

<table id="recordTable">
<thead>
<tr>
    <th>ID</th>
    <th>Purok</th>
    <th>Type</th>
    <th>Breed</th>
    <th>Stocks</th>
    <th>Date Added</th>
    <th class="no-print">Action</th>
</tr>
</thead>
<tbody>
<?php while($r=$records->fetch_assoc()): ?>
<tr>
    <td><?= $r['id'] ?></td>
    <td><?= $r['purok'] ?></td>
    <td><?= $r['livestock_type'] ?></td>
    <td><?= $r['breed'] ?></td>
    <td><?= $r['stocks'] ?></td>
    <td><?= $r['date_added'] ?></td>
    <td class="no-print">
        <a class="action edit" href="edit.php?id=<?= $r['id'] ?>">Edit</a>
        <a class="action delete" href="delete.php?id=<?= $r['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>

<script>
const labels = <?= json_encode($animalTypes) ?>;
const counts = <?= json_encode($animalCounts) ?>;

new Chart(pie,{
    type:"pie",
    data:{labels,datasets:[{data:counts}]},
    options:{responsive:true,maintainAspectRatio:false}
});

new Chart(bar,{
    type:"bar",
    data:{labels,datasets:[{data:counts}]},
    options:{responsive:true,maintainAspectRatio:false,scales:{y:{beginAtZero:true}}}
});

new Chart(line,{
    type:"line",
    data:{
        labels:<?= json_encode($dailyLabels) ?>,
        datasets:[{
            data:<?= json_encode($dailyCounts) ?>,
            borderWidth:2,
            fill:false
        }]
    },
    options:{responsive:true,maintainAspectRatio:false}
});

function searchTable(){
    let input = document.getElementById("searchInput").value.toLowerCase();
    document.querySelectorAll("#recordTable tbody tr").forEach(row=>{
        row.style.display = row.innerText.toLowerCase().includes(input) ? "" : "none";
    });
}

function resetSearch(){
    document.getElementById("searchInput").value="";
    document.querySelectorAll("#recordTable tbody tr").forEach(row=>{
        row.style.display="";
    });
}
</script>

</body>
</html>
