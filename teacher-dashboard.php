<?php
session_start();
require_once "connect.php";

if (($_SESSION["logged_in_role"] ?? "") !== "teacher") {
    header("Location: teacher-login.php");
    exit;
}

$teacherEmail = $_SESSION["teacher_email"];
$message = "";
$today = date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "attendance") {
    $attendanceDate = $_POST["attendance_date"] ?? $today;
    $statuses = $_POST["status"] ?? [];

    $save = $conn->prepare(
        "INSERT INTO attendance_records
        (student_roll_number, attendance_date, status, teacher_email)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status), teacher_email = VALUES(teacher_email)"
    );

    foreach ($statuses as $roll => $status) {
        $save->bind_param("ssss", $roll, $attendanceDate, $status, $teacherEmail);
        $save->execute();
    }

    $save->close();
    $message = "Attendance saved successfully.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "assignment") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $dueDate = $_POST["due_date"] ?? "";

    if ($title !== "" && $description !== "" && $dueDate !== "") {
        $save = $conn->prepare(
            "INSERT INTO assignments (title, description, due_date, teacher_email)
             VALUES (?, ?, ?, ?)"
        );
        $save->bind_param("ssss", $title, $description, $dueDate, $teacherEmail);
        $save->execute();
        $save->close();
        $message = "Assignment created successfully.";
    }
}

$students = [];
$result = $conn->query("SELECT roll_number FROM studentlogin ORDER BY roll_number");
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$assignmentStatement = $conn->prepare(
    "SELECT title, description, due_date
     FROM assignments
     WHERE teacher_email = ?
     ORDER BY id DESC"
);
$assignmentStatement->bind_param("s", $teacherEmail);
$assignmentStatement->execute();
$assignments = $assignmentStatement->get_result()->fetch_all(MYSQLI_ASSOC);
$assignmentStatement->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Dashboard — Hackathon</title>
  <style>
    :root{--green:#10b88b;--sky:#55b7ff;--ink:#17213f;--muted:#66708c;--line:#e4e7f4;--red:#e14e60}
    *{box-sizing:border-box}
    body{min-height:100vh;margin:0;font-family:"Segoe UI",Arial,sans-serif;color:var(--ink);background:linear-gradient(135deg,#f9f7ff,#edf7ff 55%,#fff7ee)}
    header{width:min(1180px,92%);margin:18px auto;padding:13px 17px;display:flex;justify-content:space-between;align-items:center;gap:15px;border-radius:18px;background:rgba(255,255,255,.9);box-shadow:0 10px 30px rgba(34,42,99,.1)}
    .brand{display:flex;align-items:center;gap:11px;color:var(--ink);text-decoration:none}
    .school-logo{width:48px;height:48px;padding:3px;object-fit:contain;border-radius:50%;background:#fff;box-shadow:0 3px 10px rgba(23,33,63,.15)}
    .portal-name{display:block;font-size:18px;font-weight:800}.brand small{display:block;margin-top:3px;color:var(--muted);font-size:12px;font-weight:400}
    .logout{padding:10px 14px;border-radius:11px;color:var(--red);text-decoration:none;font-size:13px;font-weight:800;background:#fff0f2}
    main{width:min(1180px,92%);margin:36px auto 55px}h1{margin:0 0 5px;font-size:31px}p{color:var(--muted)}
    .summary{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin:26px 0}.summary-card,.panel{padding:22px;border-radius:20px;background:rgba(255,255,255,.9);box-shadow:0 15px 35px rgba(48,57,120,.1)}
    .number{margin-top:7px;font-size:30px;font-weight:800}.layout{display:grid;grid-template-columns:1.25fr .75fr;gap:20px}
    h2{margin:0 0 5px;font-size:21px}.sub{margin-top:0;font-size:13px}.message{margin:18px 0;padding:12px 14px;border-radius:11px;color:#087b5d;background:#dcf8ef;font-size:13px;font-weight:700}
    table{width:100%;border-collapse:collapse;margin-top:16px}th,td{padding:11px 9px;border-bottom:1px solid var(--line);text-align:left;font-size:13px}th{color:var(--muted);font-size:12px}
    select,input,textarea{width:100%;padding:11px;border:1px solid var(--line);border-radius:10px;outline:none;background:#fbfbff;font:inherit}textarea{resize:vertical;min-height:95px}
    label{display:block;margin:14px 0 6px;font-size:13px;font-weight:800}button{border:0;padding:12px 15px;border-radius:11px;color:#fff;font-weight:800;cursor:pointer;background:linear-gradient(135deg,var(--green),var(--sky))}
    .assignment{padding:13px 0;border-bottom:1px solid var(--line)}.assignment:last-child{border-bottom:0}.assignment b{display:block}.assignment small{color:var(--muted)}
    @media(max-width:800px){.summary,.layout{grid-template-columns:1fr}}@media(max-width:500px){main,header{width:90%}.school-logo{width:40px;height:40px}.portal-name{font-size:15px}.brand small{display:none}th,td{padding:9px 5px;font-size:12px}.panel{padding:17px}}
	
	.brand{
  display:flex;
  align-items:center;
  gap:11px;
  color:#17213f;
  text-decoration:none;
}

.school-logo{
  width:52px;
  height:52px;
  padding:3px;
  object-fit:contain;
  border-radius:50%;
  background:#fff;
  box-shadow:0 3px 10px rgba(23,33,63,.15);
}

.portal-name{
  display:block;
  font-size:18px;
  font-weight:800;
}

.brand small{
  display:block;
  margin-top:3px;
  color:#66708c;
  font-size:12px;
  font-weight:400;
}

@media(max-width:500px){
  .school-logo{width:42px;height:42px}
  .portal-name{font-size:15px}
  .brand small{display:none}
}
  </style>
</head>
<body>
  <header>
    <a class="brand" href="hackathonindex.php">
      <img src="images/school-logo.jpg" alt="School logo" class="school-logo">
      <div>
        <span class="portal-name">Hackathon Teacher Portal</span>
        <small><?php echo htmlspecialchars($teacherEmail); ?></small>
      </div>
    </a>
    <a class="logout" href="logout.php">Log out</a>
  </header>

  <main>
    <h1>Teacher dashboard</h1>
    <p>Manage student attendance and assignments from one place.</p>

    <?php if ($message): ?><div class="message"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

    <section class="summary">
      <article class="summary-card">👨‍🎓<div class="number"><?php echo count($students); ?></div><p>Registered students</p></article>
      <article class="summary-card">📝<div class="number"><?php echo count($assignments); ?></div><p>Assignments created</p></article>
      <article class="summary-card">📅<div class="number"><?php echo date("d M"); ?></div><p>Today's date</p></article>
    </section>

    <section class="layout">
      <article class="panel">
        <h2>Mark attendance</h2>
        <p class="sub">Student roll numbers come from the student login database.</p>

        <form method="post">
          <input type="hidden" name="action" value="attendance">

          <label for="attendance_date">Attendance date</label>
          <input id="attendance_date" type="date" name="attendance_date" value="<?php echo $today; ?>">

          <table>
            <thead><tr><th>Student roll number</th><th>Attendance</th></tr></thead>
            <tbody>
              <?php if (count($students) === 0): ?>
                <tr><td colspan="2">No students are registered yet.</td></tr>
              <?php else: ?>
                <?php foreach ($students as $student): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($student["roll_number"]); ?></td>
                    <td>
                      <select name="status[<?php echo htmlspecialchars($student["roll_number"]); ?>]">
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                      </select>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>

          <?php if (count($students) > 0): ?>
            <button type="submit" style="margin-top:18px;">Save attendance</button>
          <?php endif; ?>
        </form>
      </article>

      <aside>
        <article class="panel">
          <h2>Create assignment</h2>
          <p class="sub">Assignments are saved in the database.</p>

          <form method="post">
            <input type="hidden" name="action" value="assignment">

            <label for="title">Assignment title</label>
            <input id="title" name="title" required placeholder="Mathematics worksheet">

            <label for="description">Instructions</label>
            <textarea id="description" name="description" required placeholder="Enter assignment instructions"></textarea>

            <label for="due_date">Due date</label>
            <input id="due_date" type="date" name="due_date" required>

            <button type="submit" style="margin-top:18px;">Create assignment</button>
          </form>
        </article>

        <article class="panel" style="margin-top:20px;">
          <h2>Your assignments</h2>

          <?php if (count($assignments) === 0): ?>
            <p class="sub">No assignments created yet.</p>
          <?php else: ?>
            <?php foreach ($assignments as $assignment): ?>
              <div class="assignment">
                <b><?php echo htmlspecialchars($assignment["title"]); ?></b>
                <small>Due: <?php echo htmlspecialchars($assignment["due_date"]); ?></small>
                <p><?php echo htmlspecialchars($assignment["description"]); ?></p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </article>
      </aside>
    </section>
  </main>
</body>
</html>