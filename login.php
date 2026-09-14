<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'my_db');

$error = "";

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Database එකෙන් අදාළ Username එක හොයාගැනීම
    $sql = "SELECT * FROM users WHERE username = '$user'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Password එක හරිද කියලා පරික්ෂා කිරීම
        if (password_verify($pass, $row['password'])) {
            $_SESSION['username'] = $user;
            header("Location: index.php"); // ලොග් වුණාම Schedule සිස්ටම් එකට යැවීම
            exit();
        } else {
            $error = "⚠️ මුරපදය වැරදියි!";
        }
    } else {
        $error = "⚠️ මෙგن ගිණුමක් හමුවී නැත!";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - My Software</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0,0,0,0.2); width: 300px; text-align: center; }
        input { width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        input[type="submit"] { background: #28a745; color: white; border: none; cursor: pointer; font-weight: bold; }
        input[type="submit"]:hover { background: #218838; }
        .error { color: red; font-size: 14px; }
        .reg-link { margin-top: 15px; font-size: 14px; display: block; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>ලොග් වන්න 🔓</h2>
    
    <?php if($error != "") { echo "<p class='error'>$error</p>"; } ?>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="පරිශීලක නම (Username)" required>
        <input type="password" name="password" placeholder="මුරපදය (Password)" required>
        <input type="submit" name="login" value="ලොග් වන්න">
    </form>

    <a href="register.php" class="reg-link">ගිණුමක් නැද්ද? ලියාපදිංචි වන්න</a>
</div>

</body>
</html>