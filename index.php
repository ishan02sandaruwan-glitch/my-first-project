<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My First Project</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        h1 { color: #0047AB; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <h1>මගේ පළමු IT ප්‍රොජෙක්ට් එක! 🚀</h1>
    <p>මම සාර්ථකව මගේ පළවෙනි කෝඩ් එක ලිව්වා.</p>

    <?php
        // Database එකට සම්බන්ධ වීම
        $conn = new mysqli('localhost', 'root', '', 'my_db');

        if($conn->connect_error) { 
            echo "<p class='error'>Database Error: සම්බන්ධ වීමට නොහැක!</p>"; 
        } else { 
            echo "<p class='success'>Database Connected successfully!</p>"; 
        }
    ?>

</body>
</html>