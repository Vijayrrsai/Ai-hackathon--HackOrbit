<?php
$remember = isset($_POST["remember"]);

session_set_cookie_params([
    "lifetime" => $remember ? 60 * 60 * 24 * 7 : 0,
    "path" => "/",
    "httponly" => true,
    "samesite" => "Lax"
]);

session_start();
require_once "connect.php";

if (($_SESSION["logged_in_role"] ?? "") === "teacher") {
    header("Location: teacher-dashboard.php");
    exit;
}

$error = "";
$success = isset($_GET["registered"])
    ? "Teacher account created. Please log in."
    : "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Enter your email and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, email, password1 FROM teacherlogin WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $teacher = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$teacher || !password_verify($password, $teacher["password1"])) {
            $error = "Invalid email or password.";
        } else {
            session_regenerate_id(true);

            $_SESSION["logged_in_role"] = "teacher";
            $_SESSION["teacher_email"] = $teacher["email"];

            header("Location: teacher-dashboard.php");
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
  <title>Teacher Login — Hackathon</title>
  <style>
    :root{--purple:#6c4cff;--pink:#ff4f8b;--mint:#35d4a3;--ink:#17213f;--muted:#66708c;--line:#e4e7f4}
    *{box-sizing:border-box}
    body{min-height:100vh;margin:0;display:grid;place-items:center;padding:25px;font-family:"Segoe UI",Arial,sans-serif;color:var(--ink);background:linear-gradient(135deg,#f9f7ff,#edf7ff 55%,#fff7ee)}
    .card{width:min(450px,100%);padding:32px;border-radius:27px;background:rgba(255,255,255,.93);box-shadow:0 25px 60px rgba(43,48,110,.17)}
    .logo{width:48px;height:48px;display:grid;place-items:center;border-radius:15px;color:#fff;font-size:22px;background:linear-gradient(135deg,#10b88b,#55b7ff)}
    .tabs{display:flex;gap:5px;padding:4px;margin:20px 0;background:#eef0fb;border-radius:13px}
    .tabs a{width:50%;padding:10px;border-radius:10px;color:var(--muted);text-align:center;text-decoration:none;font-size:13px;font-weight:800}
    .tabs a.active{color:#fff;background:linear-gradient(135deg,#10b88b,#55b7ff)}
    h1{margin:17px 0 7px;font-size:29px}
    p{color:var(--muted);font-size:14px;line-height:1.55}
    label{display:block;margin:16px 0 7px;font-size:13px;font-weight:800}
    input[type="email"],input[type="password"]{width:100%;padding:13px;border:1px solid var(--line);border-radius:12px;outline:none;background:#fbfbff}
    input:focus{border-color:#10b88b;box-shadow:0 0 0 4px rgba(16,184,139,.12)}
    .remember{display:flex;align-items:center;gap:7px;margin-top:16px;color:var(--muted);font-size:13px}
    button{width:100%;margin-top:22px;padding:14px;border:0;border-radius:13px;color:#fff;font-weight:800;cursor:pointer;background:linear-gradient(135deg,#10b88b,#55b7ff)}
    .message{margin-top:15px;padding:11px 13px;border-radius:10px;font-size:13px;font-weight:600}
    .error{color:#b52f3d;background:#ffe8eb}.success{color:#087b5d;background:#dcf8ef}
  </style>
</head>
<body>
  <main class="card">
    <div class="logo">👩‍🏫</div>
    <h1>Teacher Portal</h1>
    <p>Log in to manage student attendance and assignments.</p>

    <nav class="tabs">
      <a class="active" href="teacher-login.php">Login</a>
      <a href="teacher-register.php">Register</a>
    </nav>

    <?php if ($error): ?><div class="message error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="message success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <form method="post">
      <label for="email">Teacher Email</label>
      <input id="email" name="email" type="email" required placeholder="name@school.edu">

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required placeholder="Enter your password">

      <label class="remember">
        <input type="checkbox" name="remember"> Remember me for 7 days
      </label>

      <button type="submit">Log in as Teacher →</button>
    </form>
  </main>
</body>
</html>