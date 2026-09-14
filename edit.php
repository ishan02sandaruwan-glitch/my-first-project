<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'my_db');
$current_user = $_SESSION['username'];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM schedules WHERE id = $id AND username = '$current_user'");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        header("Location: index.php");
        exit();
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $date = $_POST['schedule_date'];
    $time = $_POST['schedule_time'];
    $reason = $_POST['reason'];

    $conn->query("UPDATE schedules SET schedule_date = '$date', schedule_time = '$time', reason = '$reason' WHERE id = $id AND username = '$current_user'");
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Schedule</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background-color: #f4f4f9; }
        .container { background: white; padding: 20px; border-radius: 10px; width: 40%; margin: 50px auto; box-shadow: 0px 4px 8px rgba(0,0,0,0.2); }
        input { padding: 10px; width: 80%; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; }
        input[type="submit"] { background: #ffc107; color: black; border: none; cursor: pointer; font-size: 16px; font-weight: bold; }
        .back-link { display: inline-block; margin-top: 10px; text-decoration: none; color: #0047AB; font-weight: bold; }
        #google_translate_element { margin-bottom: 15px; display: inline-block; }
    </style>
</head>
<body>

<div class="container">
    <div style="text-align: right;">
        🌐 භාෂාව තෝරන්න: <div id="google_translate_element"></div>
    </div>

    <h2>✏️ Schedule එක වෙනස් කරන්න (Edit)</h2>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <input type="date" name="schedule_date" value="<?php echo $row['schedule_date']; ?>" required>
        <input type="time" name="schedule_time" value="<?php echo $row['schedule_time']; ?>" required>
        <input type="text" name="reason" value="<?php echo $row['reason']; ?>" required>
        <input type="submit" name="update" value="යාවත්කාලීන කරන්න">
    </form>
    <a href="index.php" class="back-link">უკපස් යන්න</a>
</div>

<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'si,ta,en,hi,fr,es', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>
</html>