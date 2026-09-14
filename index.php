<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'my_db');

// Form එක Submit කළාම දත්ත Database එකට යැවීම
if (isset($_POST['submit'])) {
    $user_name = $_SESSION['username']; // ලොග් වී සිටින කෙනාගේ නම ස්වයංක්‍රීයව ලබා ගැනීම
    $date = $_POST['schedule_date'];
    $time = $_POST['schedule_time'];
    $reason = $_POST['reason'];

    $sql = "INSERT INTO schedules (username, schedule_date, schedule_time, reason) VALUES ('$user_name', '$date', '$time', '$reason')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green; font-weight:bold; text-align:center;'>✅ අලුත් Schedule එක සාර්ථකව ඇතුලත් කළා!</p>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Scheduling Software</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; }
        .container { background: white; padding: 20px; border-radius: 10px; width: 60%; margin: 20px auto; box-shadow: 0px 4px 8px rgba(0,0,0,0.2); }
        h1 { color: #0047AB; }
        input, select { padding: 10px; width: 80%; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; }
        input[type="submit"] { background: #28a745; color: white; border: none; cursor: pointer; font-size: 16px; font-weight: bold; }
        input[type="submit"]:hover { background: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #0047AB; color: white; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h3>👋 ආයුබෝවන්, <?php echo $_SESSION['username']; ?></h3>
        <a href="logout.php" style="color:red; text-decoration:none; font-weight:bold;">🚪 ඉවත් වන්න (Logout)</a>
    </div>

    <h1>📅 මගේ Scheduling සිස්ටම් එක</h1>

    <!-- දත්ත ඇතුලත් කරන Form එක -->
    <form method="POST" action="">
        <input type="date" name="schedule_date" required>
        <input type="time" name="schedule_time" required>
        <input type="text" name="reason" placeholder="හේතුව (උදා: Meeting, Class)" required>
        <input type="submit" name="submit" value="Schedule කරන්න">
    </form>

    <hr>
    <h3>📌 ඔබේ Schedules ලැයිස්තුව</h3>

    <table>
        <tr>
            <th>දිනය</th>
            <th>වේලාව</th>
            <th>හේතුව</th>
        </tr>

        <?php
            // ලොග් වී සිටින පරිශීලකයාට අදාළ දත්ත පමණක් ලබා ගැනීම
            $current_user = $_SESSION['username'];
            $result = $conn->query("SELECT * FROM schedules WHERE username = '$current_user' ORDER BY schedule_date ASC");

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["schedule_date"] . "</td>
                            <td>" . $row["schedule_time"] . "</td>
                            <td>" . $row["reason"] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>තාම කිසිම දෙයක් Schedule කරලා නැහැ.</td></tr>";
            }
            $conn->close();
        ?>
    </table>
</div>

</body>
</html>