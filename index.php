<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My First Project</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; background-color: #f4f4f9; }
        .container { background: white; padding: 30px; border-radius: 10px; width: 50%; margin: auto; box-shadow: 0px 4px 8px rgba(0,0,0,0.2); }
        h1 { color: #0047AB; }
        input[type="text"] { padding: 10px; width: 60%; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 16px; }
        input[type="submit"] { padding: 10px 20px; background: #0047AB; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        input[type="submit"]:hover { background: #003580; }
        .greeting { color: #ff5733; font-size: 22px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h1>මගේ පළමු IT ප්‍රොජෙක්ට් එක! 🚀</h1>

    <!-- දත්ත ඇතුලත් කරන Form එක -->
    <form method="POST" action="">
        <input type="text" name="username" placeholder="ඔයාගේ නම ඇතුලත් කරන්න..." required>
        <br>
        <input type="submit" value="Submit (ඇතුලත් කරන්න)">
    </form>

    <?php
        // Button එක එබුවට පස්සේ ක්‍රියාත්මක වෙන කොටස
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = htmlspecialchars($_POST['username']);
            echo "<p class='greeting'>ආයුබෝවන් $name! ඔයාගේ ගමනට සුබ පැතුම්! 🎉</p>";
        }
    ?>

    <br><hr><br>

    <?php
        // Database එකට සම්බන්ධ වීම (පරණ කොටස)
        $conn = new mysqli('localhost', 'root', '', 'my_db');

        if($conn->connect_error) { 
            echo "<p style='color:red;'>Database Error: සම්බන්ධ වීමට නොහැක!</p>"; 
        } else { 
            echo "<p style='color:green;'>Database Connected successfully!</p>"; 
        }
    ?>
</div>

</body>
</html>