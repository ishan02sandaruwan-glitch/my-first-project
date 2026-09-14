<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register - My Software</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0,0,0,0.2); width: 300px; text-align: center; }
        input { width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        input[type="submit"] { background: #0047AB; color: white; border: none; cursor: pointer; font-weight: bold; }
        input[type="submit"]:hover { background: #003580; }
        .login-link { margin-top: 15px; font-size: 14px; display: block; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>ලියාපදිංචි වන්න 🔐</h2>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="පරිශීලක නම (Username)" required>
        <input type="password" name="password" placeholder="මුරපදය (Password)" required>
        <input type="submit" name="register" value="ගිණුම හදන්න">
    </form>

    <?php
        if (isset($_POST['register'])) {
            $conn = new mysqli('localhost', 'root', '', 'my_db');

            $user = $_POST['username'];
            $pass = $_POST['password'];
            
            // Password එක Encrypt කිරීම (ආරක්ෂාව සඳහා)
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, password) VALUES ('$user', '$hashed_password')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color:green;'>✅ ගිණුම සාර්ථකව හැදුවා!</p>";
            } else {
                echo "<p style='color:red;'>⚠️ මේ නම දැනටමත් භාවිතා කර ඇත.</p>";
            }
            $conn->close();
        }
    ?>

    <a href="login.php" class="login-link">ගිණුමක් තිබේද? ලොග් වන්න</a>
</div>

</body>
</html>