<?php
session_start();

if (($_SESSION["logged_in_role"] ?? "") !== "student") {
    header("Location: hackathon.php");
    exit;
}

$studentId = $_SESSION["student_id"];
$studentName = $_SESSION["student_name"] ?? "Student";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard — Hackathon</title>
  <style>
    *{box-sizing:border-box}
    body{min-height:100vh;margin:0;font-family:"Segoe UI",Arial,sans-serif;color:#17213f;background:linear-gradient(135deg,#f9f7ff,#edf7ff 55%,#fff7ee)}
    header{width:min(1100px,92%);margin:18px auto;padding:13px 17px;display:flex;justify-content:space-between;align-items:center;gap:15px;border-radius:18px;background:rgba(255,255,255,.9);box-shadow:0 10px 30px rgba(34,42,99,.1)}
    .brand{display:flex;align-items:center;gap:11px;color:#17213f;text-decoration:none}
    .school-logo{width:48px;height:48px;padding:3px;object-fit:contain;border-radius:50%;background:#fff;box-shadow:0 3px 10px rgba(23,33,63,.15)}
    .portal-name{display:block;font-size:18px;font-weight:800}.brand small{display:block;margin-top:2px;color:#66708c;font-size:12px}
    .logout{padding:10px 14px;border-radius:11px;color:#d33e51;text-decoration:none;font-size:13px;font-weight:800;background:#fff0f2}
    main{width:min(1100px,92%);margin:45px auto}h1{margin-bottom:7px}p{color:#66708c}
    .cards{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:28px}
    .card{padding:22px;border-radius:19px;background:rgba(255,255,255,.88);box-shadow:0 15px 35px rgba(48,57,120,.11);transition:.25s ease}
    .card:hover{transform:translateY(-7px);box-shadow:0 22px 42px rgba(48,57,120,.19)}
    .number{margin-top:9px;font-size:29px;font-weight:800}
    @media(max-width:700px){.cards{grid-template-columns:1fr}}
    @media(max-width:500px){main,header{width:90%}.school-logo{width:40px;height:40px}.portal-name{font-size:15px}.brand small{display:none}}
  </style>
</head>
<body>
  <header>
    <a class="brand" href="hackathonindex.php">
      <img src="images/school-logo.jpg" alt="School logo" class="school-logo">
      <div>
        <span class="portal-name">Hackathon Student Portal</span>
        <small>School Management System</small>
      </div>
    </a>
    <a class="logout" href="logout.php">Log out</a>
  </header>

  <main>
    <h1>Welcome, <?php echo htmlspecialchars($studentName); ?> 👋</h1>
    <p>Your roll number: <strong><?php echo htmlspecialchars($studentId); ?></strong></p>

    <section class="cards">
      <article class="card">🎯<div class="number">96%</div><p>Attendance</p></article>
      <article class="card">📝<div class="number">4</div><p>Assignments due</p></article>
      <article class="card">📅<div class="number">3</div><p>Upcoming events</p></article>
    </section>
  </main>
</body>
</html>