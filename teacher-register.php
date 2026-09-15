<?php
session_start();
require_once "connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($email === "" || $password === "" || $confirmPassword === "") {
        $error = "Please complete every field.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must contain at least 6 characters.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare(
            "SELECT id FROM teacherlogin WHERE email = ? LIMIT 1"
        );
        $check->bind_param("s", $email);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $error = "This email is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare(
                "INSERT INTO teacherlogin (email, password1) VALUES (?, ?)"
            );
            $insert->bind_param("ss", $email, $hashedPassword);

            if ($insert->execute()) {
                $insert->close();
                header("Location: teacher-login.php?registered=1");
                exit;
            }

            $error = "Registration failed. Please try again.";
            $insert->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Register — Hackathon</title>
  <style>
    :root{--purple:#6c4cff;--mint:#35d4a3;--sky:#55b7ff;--ink:#17213f;--muted:#66708c;--line:#e4e7f4}
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
    input{width:100%;padding:13px;border:1px solid var(--line);border-radius:12px;outline:none;background:#fbfbff}
    input:focus{border-color:#10b88b;box-shadow:0 0 0 4px rgba(16,184,139,.12)}
    button{width:100%;margin-top:22px;padding:14px;border:0;border-radius:13px;color:#fff;font-weight:800;cursor:pointer;background:linear-gradient(135deg,#10b88b,#55b7ff)}
    .error{margin-top:15px;padding:11px 13px;border-radius:10px;color:#b52f3d;background:#ffe8eb;font-size:13px;font-weight:600}
  </style>
</head>
<body>
  <main class="card">
    <div class="logo">👩‍🏫</div>
    <h1>Create teacher account</h1>
    <p>Register once, then use this email and password to log in.</p>

    <nav class="tabs">
      <a href="teacher-login.php">Login</a>
      <a class="active" href="teacher-register.php">Register</a>
    </nav>

    <?php if ($error): ?><div class="error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <form method="post">
      <label for="email">Teacher Email</label>
      <input id="email" name="email" type="email" required placeholder="name@school.edu">

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required placeholder="Minimum 6 characters">

      <label for="confirm_password">Confirm Password</label>
      <input id="confirm_password" name="confirm_password" type="password" required placeholder="Enter the password again">

      <button type="submit">Create teacher account →</button>
    </form>
  </main>
</body>
</html>