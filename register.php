<?php
session_start();
include "connection.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {

        $check = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username already exists!";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $insert->bind_param("ss", $username, $hashed);

            if ($insert->execute()) {
                $success = true;
            } else {
                $error = "Something went wrong!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register</title>

<style>

body {
    margin: 0;
    height: 100vh;
    background: linear-gradient(135deg, #0b5e2a, #70c9ff);
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    font-family: Arial, sans-serif;
}

.shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.25);
    filter: blur(10px);
    animation: float 6s infinite ease-in-out alternate;
}
.shape1 { width: 180px; height: 180px; top: 10%; left: 15%; }
.shape2 { width: 260px; height: 260px; bottom: 8%; right: 12%; }
.shape3 { width: 140px; height: 140px; top: 60%; left: 30%; }

@keyframes float {
    from { transform: translateY(0); }
    to { transform: translateY(-40px); }
}

.box {
    width: 380px;
    padding: 25px 35px;
    background: rgba(255,255,255,0.13);
    border-radius: 20px;
    backdrop-filter: blur(10px);
    box-shadow: 0 0 25px rgba(0,0,0,0.3);
    text-align: center;
    animation: pop 0.6s ease;
}
@keyframes pop {
    0% { transform: scale(0.6); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

h2 {
    margin-bottom: 10px;
    color: white;
    letter-spacing: 1px;
}

input {
    width: 90%;
    padding: 12px;
    margin: 10px 0;
    border: none;
    outline: none;
    border-radius: 10px;
    font-size: 15px;
}

button {
    width: 95%;
    padding: 12px;
    background: #1e90ff;
    border: none;
    cursor: pointer;
    margin-top: 10px;
    color: white;
    font-size: 17px;
    border-radius: 10px;
    transition: 0.3s;
}
button:hover {
    background: #0f5bb5;
}

/* === ERROR SHAKE === */
.error {
    color: #ff4444;
    font-weight: bold;
}
.shake { animation: shakeAnim 0.3s ease-in-out 2; }
@keyframes shakeAnim {
    0% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    50% { transform: translateX(6px); }
    75% { transform: translateX(-6px); }
    100% { transform: translateX(0); }
}

.success-box {
    position: fixed;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.5);
    background: rgba(255,255,255,0.95);
    padding: 25px 40px;
    border-radius: 20px;
    box-shadow: 0 0 25px rgba(0,255,150,0.8);
    text-align: center;
    opacity: 0;
    transition: 0.3s;
    z-index: 99999;
}
.success-box.show {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

.loader {
    border: 5px solid #ddd;
    border-top: 5px solid #00c26b;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    animation: spin 0.9s linear infinite;
    margin: 0 auto;
}
@keyframes spin { 100% { transform: rotate(360deg); } }

.eye-btn {
    position: absolute;
    right: 15px;
    top: 52%;
    cursor: pointer;
}

</style>

</head>
<body>
<?php if ($success): ?>
<div id="success" class="success-box">
    <div class="loader"></div>
    <h2 style="color:green;">Registered Successfully!</h2>
    <p>Redirecting to login...</p>
</div>

<script>
document.getElementById("success").classList.add("show");
setTimeout(() => {
    window.location.href = "login.php";
}, 1500);
</script>
<?php endif; ?>

<div class="box" id="registerBox">

    <h2>Register</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
        <script>
            document.getElementById("registerBox").classList.add("shake");
            setTimeout(() => {
                document.getElementById("registerBox").classList.remove("shake");
            }, 400);
        </script>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="username" placeholder="Username" required>

        <div style="position:relative;">
            <input type="password" id="pass1" name="password" placeholder="Password" required>
            <span class="eye-btn" onclick="togglePass('pass1')">👁</span>
        </div>

        <div style="position:relative;">
            <input type="password" id="pass2" name="confirm" placeholder="Confirm Password" required>
            <span class="eye-btn" onclick="togglePass('pass2')">👁</span>
        </div>

        <button type="submit">Register</button>

        <p style="color:white;">Already have an account? <a href="login.php" style="color:yellow;">Login</a></p>

    </form>

</div>

<script>
function togglePass(id) {
    let p = document.getElementById(id);
    p.type = (p.type === "password") ? "text" : "password";
}
</script>

</body>
</html>
