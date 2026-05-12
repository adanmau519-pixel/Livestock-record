<?php
session_start();
include "connection.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$login_success = false;
$error = "";

$remembered_username = $_COOKIE["remember_me"] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user = trim($_POST["username"]);
    $pass = $_POST["password"];
    $remember = isset($_POST["remember"]);

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->bind_result($hashed_pass);
        $stmt->fetch();

        if (password_verify($pass, $hashed_pass)) {

            if ($remember) {
                setcookie("remember_me", $user, time() + (86400 * 30), "/");
            } else {
                setcookie("remember_me", "", time() - 3600, "/");
            }

            $_SESSION["username"] = $user;
            $login_success = true;

        } else { $error = "Wrong password!"; }

    } else { $error = "User not found!"; }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Login</title>

<style>
*{margin:0;padding:0;box-sizing:border-box}

body{
  min-height:100vh;
  background: linear-gradient(135deg,#0b5e2a,#70c9ff,#0b5e2a);
  font-family:Inter,Arial;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
}

.wrapper{
  width:90%;
  max-width:1100px;
  display:flex;
  justify-content:center;
  gap:70px;
  z-index:4;
}

.left-area{
  width:400px;
  text-align:center;
  animation:leftSlide .9s ease-out;
}

.left-area .logo{
  width:200px;
  height:200px;
  border-radius:50%;
  object-fit:cover;
  box-shadow:0 8px 40px rgba(0,0,0,0.2);
  animation:float3D 5s ease-in-out infinite;
}

@keyframes float3D{
  0% {transform:translateY(0)}
  50% {transform:translateY(-12px)}
  100% {transform:translateY(0)}
}

@keyframes leftSlide{
  from{opacity:0; transform:translateX(-40px)}
  to{opacity:1; transform:translateX(0)}
}

.right-area{width:420px}

.box{
  padding:32px;
  border-radius:14px;
  background:rgba(255,255,255,0.25);
  border:1px solid rgba(255,255,255,0.3);
  backdrop-filter:blur(10px);
  box-shadow:0 10px 50px rgba(0,0,0,0.25);
  position:relative;
}

.input input{
  width:100%;
  padding:12px;
  margin-bottom:12px;
  border-radius:10px;
  border:1px solid rgba(0,0,0,0.15);
  background:rgba(255,255,255,0.7);
  font-size:15px;
}

.eye-btn{
  position:absolute;
  right:12px;
  top:12px;
  font-size:17px;
  cursor:pointer;
}

.btn{
  width:100%;
  padding:12px;
  border:0;
  background:linear-gradient(90deg,#70c9ff,#48b4ff);
  border-radius:10px;
  font-weight:700;
  cursor:pointer;
}

.error{
  background:rgba(255,0,0,0.15);
  padding:10px;
  border-radius:10px;
  color:#7a0000;
  margin-bottom:8px;
  border:1px solid rgba(255,0,0,0.3);
}
.shake{
  animation:shake .3s;
}
@keyframes shake{
  0%{transform:translateX(0)}
  25%{transform:translateX(-6px)}
  50%{transform:translateX(6px)}
  75%{transform:translateX(-6px)}
  100%{transform:translateX(0)}
}

.success-box{
  position:fixed;
  left:50%; top:40%;
  transform:translate(-50%,-50%) scale(.5);
  opacity:0;
  background:white;
  padding:20px 25px;
  border-radius:14px;
  box-shadow:0 8px 40px rgba(0,0,0,0.3);
  transition:.3s;
  z-index:9999;
}
.success-box.show{
  transform:translate(-50%,-50%) scale(1);
  opacity:1;
}

.loader{
  width:45px; height:45px;
  border-radius:50%;
  border:5px solid rgba(0,0,0,0.2);
  border-top:5px solid #48b4ff;
  animation:spin 1s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg)}}
 
.bubbles {
	position:absolute; inset:0;
	overflow:hidden; } 
   .bubble { position:absolute; bottom:-120px; background: radial-gradient(circle, rgba(255,255,255,0.9), rgba(255,255,255,0.5));
   border-radius:50%;
   animation: rise linear infinite; 
   }
   @keyframes rise { to { transform: translateY(-120vh); } } 
   @media (max-width:980px){ .wrapper { flex-direction:column; gap:30px; }}
</style>
</head>
<body>

</div>

<div class="wrapper">

<!-- LEFT -->
<div class="left-area">
  <img src="images\logo.jpg" class="logo">
  <h1>Livestock Record Monitoring System in Brgy. Abelo, San Nicolas</h1>
</div>

<!-- RIGHT -->
<div class="right-area">

<?php if ($login_success): ?>
  <div id="success" class="success-box">
    <div class="loader"></div>
    <h3>Login Successful</h3>
  </div>

  <script>
    document.getElementById("success").classList.add("show");
    setTimeout(()=>{ window.location.href="dashboard.php"; },1400);
  </script>
<?php endif; ?>

<div class="box">

  <?php if ($error): ?>
    <div class="error shake"><?= $error ?></div>
  <?php endif; ?>

  <h2 style="text-align:center; margin-bottom:10px;">Login Account</h2>

  <form method="POST">

    <div class="input">
      <input type="text" name="username" required placeholder="Username"
             value="<?= htmlspecialchars($remembered_username) ?>">
    </div>

    <div class="input" style="position:relative;">
      <input id="pass" type="password" name="password" required placeholder="Password">
      <span class="eye-btn" onclick="togglePass()">👁</span>
    </div>

    <label style="display:flex; gap:6px; margin:8px 0;">
      <input type="checkbox" name="remember" <?= $remembered_username ? "checked":"" ?>> Remember me
    </label>

    <button class="btn" type="submit">Log In</button>

    <p style="text-align:center; margin-top:12px;">
      Do you have an account? <a href="register.php" style="font-weight:600;">Register</a>
    </p>

  </form>

</div>
</div>
</div>

<script>
function togglePass(){
  const p = document.getElementById("pass");
  p.type = p.type === "password" ? "text" : "password";
}
</script>

</body>
</html>