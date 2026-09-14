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
    </style>
</head>
<body>

<div class="container">
    <h1>📅 මගේ Scheduling සිස්ටම් එක</h1>

    <!-- දත්ත ඇතුලත් කරන Form එක -->
    <form method="POST" action="">
        <input type="text" name="user_name" placeholder="ඔයාගේ නම" required>
        <input type="date" name="schedule_date" required>
        <input type="time" name="schedule_time" required>
        <input type="text" name="reason" placeholder="හේතුව (උදා: Meeting, Class)" required>
        <input type="submit" name="submit" value="Schedule කරන්න">
    </form>

    <?php
        // Database එකට සම්බන්ධ වීම
        $conn = new mysqli('localhost', 'root', '', 'my_db');

        // Form එක Submit කළාම දත්ත Database එකට යැවීම
        if (isset($_POST['submit'])) {
            $name = $_POST['user_name'];
            $date = $_POST['schedule_date'];
            $time = $_POST['schedule_time'];
            $reason = $_POST['reason'];

            $sql = "INSERT INTO schedules (user_name, schedule_date, schedule_time, reason) VALUES ('$name', '$date', '$time', '$reason')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color:green; font-weight:bold;'>✅ අලුත් Schedule එක සාර්ථකව ඇතුලත් කළා!</p>";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    ?>

    <hr>
    <h3>📌 ඇතුලත් කර ඇති Schedules ලැයිස්තුව</h3>

    <!-- Database එකේ තියෙන දත්ත පෙන්වන වගුව (Table) -->
    <table>
        <tr>
            <th>නම</th>
            <th>දිනය</th>
            <th>වේලාව</th>
            <th>හේතුව</th>
        </tr>

        <?php
            // Database එකෙන් දත්ත කියවීම (Read)
            $result = $conn->query("SELECT * FROM schedules ORDER BY schedule_date ASC");

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["user_name"] . "</td>
                            <td>" . $row["schedule_date"] . "</td>
                            <td>" . $row["schedule_time"] . "</td>
                            <td>" . $row["reason"] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>තාම කිසිම දෙයක් Schedule කරලා නැහැ.</td></tr>";
            }
            $conn->close();
        ?>
    </table>
</div>

</body>
</html>