<?php
session_start();

$role = $_SESSION["logged_in_role"] ?? "";

$studentLink = $role === "student"
    ? "hackathon-interference%20for%20student.php"
    : "hackathon.php";

$teacherLink = $role === "teacher"
    ? "teacher-dashboard.php"
    : "teacher-login.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hackathon — Choose Portal</title>
  <style>
    :root{--purple:#6c4cff;--pink:#ff4f8b;--mint:#35d4a3;--sky:#55b7ff;--ink:#17213f;--muted:#66708c}
    *{box-sizing:border-box}
    body{
      min-height:100vh;margin:0;overflow-x:hidden;font-family:"Segoe UI",Arial,sans-serif;color:var(--ink);
      background:radial-gradient(circle at 8% 15%,rgba(255,159,67,.32),transparent 25%),
      radial-gradient(circle at 90% 10%,rgba(108,76,255,.22),transparent 30%),
      linear-gradient(135deg,#f9f7ff,#edf7ff 55%,#fff7ee);
    }
    header{
      width:min(1160px,92%);margin:18px auto 0;padding:12px 16px;display:flex;align-items:center;gap:11px;
      border:1px solid rgba(255,255,255,.8);border-radius:20px;background:rgba(255,255,255,.78);
      box-shadow:0 10px 30px rgba(34,42,99,.08);backdrop-filter:blur(15px);
    }
    .school-logo{width:48px;height:48px;padding:3px;object-fit:contain;border-radius:50%;background:#fff;box-shadow:0 3px 10px rgba(23,33,63,.15)}
    .brand-name{display:block;font-size:19px;font-weight:800}.brand-tag{display:block;margin-top:2px;color:var(--muted);font-size:12px}
    main{width:min(1120px,92%);min-height:calc(100vh - 105px);display:flex;align-items:center;justify-content:center;padding:55px 0}
    .intro{width:100%;text-align:center}
    .badge{display:inline-flex;align-items:center;gap:7px;padding:7px 12px;border-radius:999px;color:#5d43db;background:#ece9ff;font-size:13px;font-weight:800}
    .dot{width:8px;height:8px;border-radius:50%;background:var(--mint)}
    h1{margin:16px 0 10px;font-size:clamp(38px,5vw,58px);letter-spacing:-2px}.intro>p{max-width:540px;margin:0 auto;color:var(--muted);font-size:16px;line-height:1.6}
    .role-grid{display:grid;grid-template-columns:repeat(2,minmax(290px,340px));justify-content:center;gap:48px;width:100%;margin:40px auto 0}
    .role-card{
      position:relative;min-height:280px;overflow:hidden;padding:32px;border:1px solid rgba(255,255,255,.9);
      border-radius:25px;color:var(--ink);text-align:left;text-decoration:none;background:rgba(255,255,255,.88);
      box-shadow:0 18px 42px rgba(48,57,120,.13);transition:.25s ease;
    }
    .role-card:hover{transform:translateY(-10px);box-shadow:0 28px 48px rgba(48,57,120,.22)}
    .role-card::after{content:"";position:absolute;width:155px;height:155px;right:-62px;bottom:-72px;border-radius:50%;opacity:.25}
    .student::after{background:linear-gradient(135deg,var(--purple),var(--pink))}.teacher::after{background:linear-gradient(135deg,#10b88b,var(--sky))}
    .role-icon{width:60px;height:60px;display:grid;place-items:center;margin-bottom:18px;border-radius:18px;font-size:29px}.student .role-icon{background:#eeeaff}.teacher .role-icon{background:#dcf8ef}
    .role-card h2{margin:0 0 9px;font-size:26px}.role-card p{position:relative;z-index:1;margin:0 0 23px;color:var(--muted);font-size:14px;line-height:1.6}
    .continue{position:relative;z-index:1;font-weight:800}.student .continue{color:var(--purple)}.teacher .continue{color:#0a9671}
    footer{padding:0 20px 25px;color:var(--muted);text-align:center;font-size:12px}
    @media(max-width:850px){.role-grid{grid-template-columns:repeat(2,minmax(250px,310px));gap:28px}.role-card{min-height:255px;padding:27px}}
    @media(max-width:600px){
      header{width:92%;margin-top:12px;padding:10px 12px}.school-logo{width:40px;height:40px}.brand-name{font-size:17px}.brand-tag{display:none}
      main{width:90%;padding:45px 0 30px}h1{font-size:38px}.intro>p{font-size:14px}.role-grid{grid-template-columns:minmax(270px,370px);gap:23px;margin-top:30px}.role-card{min-height:220px;padding:25px}.role-card:hover{transform:translateY(-5px)}
    }
  </style>
</head>
<body>
  <header>
    <img src="images/school-logo.jpg" alt="School logo" class="school-logo">
    <div>
      <span class="brand-name">Hackathon</span>
      <span class="brand-tag">School Management System</span>
    </div>
  </header>

  <main>
    <section class="intro">
      <div class="badge"><span class="dot"></span> Welcome to the school portal</div>
      <h1>Who are you?</h1>
      <p>Select your portal to continue with your school account.</p>

      <div class="role-grid">
        <a class="role-card student" href="<?php echo $studentLink; ?>">
          <div class="role-icon">🎓</div>
          <h2>Student</h2>
          <p>Access attendance, assignments, timetables and your academic dashboard.</p>
          <span class="continue">Continue as Student →</span>
        </a>

        <a class="role-card teacher" href="<?php echo $teacherLink; ?>">
          <div class="role-icon">👩‍🏫</div>
          <h2>Teacher</h2>
          <p>Manage student attendance, classes, academic records and school updates.</p>
          <span class="continue">Continue as Teacher →</span>
        </a>
      </div>
    </section>
  </main>

  <footer>&copy; 2026 Hackathon School Management System</footer>
</body>
</html>