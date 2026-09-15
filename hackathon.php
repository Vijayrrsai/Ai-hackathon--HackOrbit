<?php
session_start();
require_once "connect.php";

if (($_SESSION["logged_in_role"] ?? "") === "student") {
    header("Location: hackathon-interference%20for%20student.php");
    exit;
}

$loginError = "";
$registeredMessage = isset($_GET["registered"])
    ? "Registration successful. Please log in."
    : "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rollNumber = trim($_POST["roll_number"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($rollNumber === "" || $password === "") {
        $loginError = "Enter your roll number and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, roll_number, password
             FROM studentlogin
             WHERE roll_number = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $rollNumber);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$student || !password_verify($password, $student["password"])) {
            $loginError = "Invalid roll number or password.";
        } else {
            session_regenerate_id(true);

            $_SESSION["logged_in_role"] = "student";
            $_SESSION["student_id"] = $student["roll_number"];
            $_SESSION["student_name"] = "Student";

            header("Location: hackathon-interference%20for%20student.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hackathon — Student Login</title>
  <style>
    :root{
      --purple:#6c4cff;
      --pink:#ff4f8b;
      --mint:#35d4a3;
      --ink:#17213f;
      --muted:#66708c;
      --line:#e4e7f4;
    }

    *{box-sizing:border-box}

    body{
      min-height:100vh;
      margin:0;
      font-family:"Segoe UI",Arial,sans-serif;
      color:var(--ink);
      background:
        radial-gradient(circle at 8% 15%,rgba(255,159,67,.26),transparent 26%),
        radial-gradient(circle at 90% 10%,rgba(108,76,255,.2),transparent 30%),
        linear-gradient(135deg,#f9f7ff,#edf7ff 55%,#fff7ee);
    }

    .site-header{
      width:min(1160px,92%);
      margin:18px auto 0;
      padding:12px 16px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:16px;
      border:1px solid rgba(255,255,255,.8);
      border-radius:20px;
      background:rgba(255,255,255,.78);
      box-shadow:0 10px 30px rgba(34,42,99,.08);
      backdrop-filter:blur(15px);
    }

    .brand{
      display:flex;
      align-items:center;
      gap:11px;
    }

    .brand-icon{
      width:45px;
      height:45px;
      display:grid;
      place-items:center;
      border-radius:14px;
      color:white;
      font-size:22px;
      background:linear-gradient(135deg,var(--purple),var(--pink));
      box-shadow:0 8px 18px rgba(108,76,255,.3);
    }

    .brand-name{
      display:block;
      font-size:19px;
      font-weight:800;
    }

    .brand-tag{
      display:block;
      margin-top:2px;
      color:var(--muted);
      font-size:12px;
    }

    .auth-tabs{
      display:flex;
      gap:5px;
      padding:4px;
      border-radius:14px;
      background:#eef0fb;
    }

    .auth-tab{
      padding:9px 15px;
      border-radius:10px;
      color:var(--muted);
      text-decoration:none;
      font-size:13px;
      font-weight:800;
      transition:.2s ease;
    }

    .auth-tab:hover{
      color:var(--purple);
      transform:translateY(-1px);
    }

    .auth-tab.active{
      color:white;
      background:linear-gradient(135deg,var(--purple),var(--pink));
      box-shadow:0 5px 12px rgba(108,76,255,.25);
    }

    main{
      width:min(1060px,90%);
      min-height:calc(100vh - 145px);
      margin:45px auto;
      display:grid;
      grid-template-columns:1.15fr .85fr;
      gap:55px;
      align-items:center;
    }

    .badge{
      display:inline-flex;
      gap:7px;
      align-items:center;
      padding:7px 11px;
      border-radius:999px;
      color:#5d43db;
      background:#ece9ff;
      font-size:13px;
      font-weight:800;
    }

    .dot{
      width:8px;
      height:8px;
      border-radius:50%;
      background:var(--mint);
    }

    h1{
      max-width:620px;
      margin:15px 0;
      font-size:clamp(38px,5vw,62px);
      line-height:1.05;
      letter-spacing:-2px;
    }

    h1 span{
      background:linear-gradient(90deg,var(--purple),var(--pink),#ff9f43);
      -webkit-background-clip:text;
      background-clip:text;
      color:transparent;
    }

    .lede{
      max-width:530px;
      color:var(--muted);
      font-size:17px;
      line-height:1.65;
    }

    .features{
      display:grid;
      grid-template-columns:repeat(2,1fr);
      gap:14px;
      margin-top:28px;
    }

    .feature{
      padding:17px;
      border-radius:18px;
      background:rgba(255,255,255,.8);
      box-shadow:0 14px 30px rgba(48,57,120,.1);
    }

    .feature b{
      display:block;
      margin-top:7px;
    }

    .feature small{
      color:var(--muted);
    }

    .login-card{
      padding:32px;
      border:1px solid rgba(255,255,255,.9);
      border-radius:27px;
      background:rgba(255,255,255,.9);
      box-shadow:0 25px 60px rgba(43,48,110,.17);
    }

    .role{
      display:inline-block;
      padding:6px 10px;
      border-radius:999px;
      color:#5f43dd;
      background:#edeaff;
      font-size:11px;
      font-weight:900;
      letter-spacing:1px;
    }

    h2{
      margin:14px 0 7px;
      font-size:28px;
    }

    .login-card p{
      margin:0 0 22px;
      color:var(--muted);
      font-size:14px;
      line-height:1.55;
    }

    .message{
      margin-bottom:16px;
      padding:11px 13px;
      border-radius:11px;
      font-size:13px;
      font-weight:600;
    }

    .error{
      color:#b52f3d;
      background:#ffe8eb;
    }

    .success{
      color:#087b5d;
      background:#dcf8ef;
    }

    label{
      display:block;
      margin:16px 0 7px;
      font-size:13px;
      font-weight:800;
    }

    input{
      width:100%;
      padding:13px;
      border:1px solid var(--line);
      border-radius:12px;
      outline:none;
      background:#fbfbff;
      transition:.2s;
    }

    input:focus{
      border-color:var(--purple);
      background:white;
      box-shadow:0 0 0 4px rgba(108,76,255,.12);
    }

    .password-wrap{
      position:relative;
    }

    .password-wrap input{
      padding-right:45px;
    }

    .show-password{
      position:absolute;
      right:9px;
      top:50%;
      transform:translateY(-50%);
      border:0;
      background:transparent;
      cursor:pointer;
      font-size:17px;
    }

    .login-btn{
      width:100%;
      margin-top:23px;
      padding:14px;
      border:0;
      border-radius:13px;
      color:white;
      font-weight:800;
      cursor:pointer;
      background:linear-gradient(135deg,var(--purple),var(--pink));
      box-shadow:0 10px 18px rgba(108,76,255,.28);
      transition:.2s;
    }

    .login-btn:hover{
      transform:translateY(-3px);
    }

    .register-note{
      margin-top:18px !important;
      text-align:center;
    }

    .register-note a{
      color:var(--purple);
      font-weight:800;
    }

    footer{
      padding:15px 5% 30px;
      color:var(--muted);
      text-align:center;
      font-size:12px;
    }

    @media(max-width:800px){
      main{
        grid-template-columns:1fr;
        gap:30px;
      }

      .login-card{
        max-width:520px;
        width:100%;
        margin:auto;
      }
    }

    @media(max-width:500px){
      .brand-tag{display:none}
      .auth-tab{padding:8px 10px}
      .site-header{padding:10px}
      .login-card{padding:25px 20px}
      .features{grid-template-columns:1fr}
    }
  </style>
</head>
<body>

<header class="site-header">
  <div class="brand">
    <div class="brand-icon">⚡</div>
    <div>
      <span class="brand-name">Hackathon</span>
      <span class="brand-tag">School Management System</span>
    </div>
  </div>

  <nav class="auth-tabs">
    <a class="auth-tab active" href="hackathon.php">Login</a>
    <a class="auth-tab" href="hackathonregister.php">Register</a>
  </nav>
</header>

<main>
  <section>
    <div class="badge"><span class="dot"></span> Student learning portal</div>
    <h1>Learn, create and <span>shine brighter.</span></h1>
    <p class="lede">
      Log in to manage your school experience, access academic information and stay connected.
    </p>

    <div class="features">
      <div class="feature">🎯<b>Attendance</b><small>Track your progress.</small></div>
      <div class="feature">📝<b>Assignments</b><small>Never miss a deadline.</small></div>
      <div class="feature">📅<b>Timetable</b><small>Plan your school day.</small></div>
      <div class="feature">📢<b>Updates</b><small>Stay informed.</small></div>
    </div>
  </section>

  <section class="login-card">
    <span class="role">STUDENT LOGIN</span>
    <h2>Welcome back! 👋</h2>
    <p>Log in using your registered roll number and password.</p>

    <?php if ($loginError): ?>
      <div class="message error"><?php echo htmlspecialchars($loginError); ?></div>
    <?php endif; ?>

    <?php if ($registeredMessage): ?>
      <div class="message success"><?php echo htmlspecialchars($registeredMessage); ?></div>
    <?php endif; ?>

    <form method="post">
      <label for="roll_number">Roll Number</label>
      <input
        type="text"
        id="roll_number"
        name="roll_number"
        required
        placeholder="Example: STU-2026-014"
      >

      <label for="password">Password</label>
      <div class="password-wrap">
        <input
          type="password"
          id="password"
          name="password"
          required
          placeholder="Enter your password"
        >
        <button type="button" class="show-password" onclick="togglePassword()">👁</button>
      </div>

      <button class="login-btn" type="submit">Log in →</button>
    </form>

    <p class="register-note">
      New student? <a href="register.php">Create an account</a>
    </p>
  </section>
</main>

<footer>
  &copy; 2026 Hackathon School Portal. All rights reserved.
</footer>

<script>
  function togglePassword() {
    const input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
  }
</script>

</body>
</html>