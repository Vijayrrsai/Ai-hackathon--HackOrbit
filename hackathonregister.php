<?php
session_start();
require_once "connect.php";

if (($_SESSION["logged_in_role"] ?? "") === "student") {
    header("Location: hackathon-interference%20for%20student.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rollNumber = trim($_POST["roll_number"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($rollNumber === "" || $password === "" || $confirmPassword === "") {
        $error = "Please fill in every field.";
    } elseif (strlen($password) < 6) {
        $error = "Password must contain at least 6 characters.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare(
            "SELECT id FROM studentlogin WHERE roll_number = ? LIMIT 1"
        );
        $check->bind_param("s", $rollNumber);
        $check->execute();
        $alreadyExists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($alreadyExists) {
            $error = "This roll number is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insert = $conn->prepare(
                "INSERT INTO studentlogin (roll_number, password) VALUES (?, ?)"
            );
            $insert->bind_param("ss", $rollNumber, $hashedPassword);

            if ($insert->execute()) {
                $insert->close();
                header("Location: hackathon.php?registered=1");
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
  <title>Register — Hackathon</title>
  <style>
    :root{
      --purple:#6c4cff;
      --pink:#ff4f8b;
      --ink:#17213f;
      --muted:#66708c;
      --line:#e4e7f4;
    }

    *{box-sizing:border-box}

    body{
      min-height:100vh;
      margin:0;
      padding:25px;
      display:grid;
      place-items:center;
      font-family:"Segoe UI",Arial,sans-serif;
      color:var(--ink);
      background:
        radial-gradient(circle at 8% 15%,rgba(255,159,67,.26),transparent 26%),
        radial-gradient(circle at 90% 10%,rgba(108,76,255,.2),transparent 30%),
        linear-gradient(135deg,#f9f7ff,#edf7ff 55%,#fff7ee);
    }

    .card{
      width:min(440px,100%);
      padding:32px;
      border:1px solid rgba(255,255,255,.9);
      border-radius:27px;
      background:rgba(255,255,255,.92);
      box-shadow:0 25px 60px rgba(43,48,110,.17);
    }

    .logo{
      width:48px;
      height:48px;
      display:grid;
      place-items:center;
      border-radius:15px;
      color:white;
      font-size:22px;
      background:linear-gradient(135deg,var(--purple),var(--pink));
    }

    h1{margin:18px 0 7px;font-size:29px}
    p{color:var(--muted);font-size:14px;line-height:1.55}

    .error{
      margin:16px 0;
      padding:11px 13px;
      border-radius:11px;
      color:#b52f3d;
      background:#ffe8eb;
      font-size:13px;
      font-weight:600;
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
    }

    input:focus{
      border-color:var(--purple);
      background:white;
      box-shadow:0 0 0 4px rgba(108,76,255,.12);
    }

    button{
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
    }

    .login-note{text-align:center;margin-top:18px}
    a{color:var(--purple);font-weight:800}
  </style>
</head>
<body>

<main class="card">
  <div class="logo">⚡</div>
  <h1>Create your account</h1>
  <p>Register your roll number once, then log in to access the student portal.</p>

  <?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="post">
    <label for="roll_number">Roll Number</label>
    <input
      id="roll_number"
      name="roll_number"
      required
      placeholder="Example: STU-2026-014"
      value="<?php echo htmlspecialchars($_POST["roll_number"] ?? ""); ?>"
    >

    <label for="password">Password</label>
    <input
      id="password"
      name="password"
      type="password"
      required
      placeholder="Minimum 6 characters"
    >

    <label for="confirm_password">Confirm Password</label>
    <input
      id="confirm_password"
      name="confirm_password"
      type="password"
      required
      placeholder="Enter the same password again"
    >

    <button type="submit">Create account →</button>
  </form>

  <p class="login-note">
    Already registered? <a href="hackathon.php">Log in</a>
  </p>
</main>

</body>
</html>