<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'my_db');
$current_user = $_SESSION['username'];

$msg = "";
if (isset($_POST['update_profile'])) {
    $email = $_POST['email'];
    $contact_no = $_POST['contact_no'];

    // පින්තූරයක් අප්ලෝඩ් කර ඇත්නම් එය යාවත්කාලීන කිරීම
    if (!empty($_FILES["profile_pic"]["name"])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $imageFileType = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
        $new_filename = $current_user . "_" . time() . "." . $imageFileType;
        $final_target = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $final_target)) {
            $conn->query("UPDATE users SET profile_pic = '$new_filename', email = '$email', contact_no = '$contact_no' WHERE username = '$current_user'");
        }
    } else {
        // පින්තූරයක් නැතත් ඊමේල් සහ කන්ටැක්ට් අංකය පමණක් වුවද යාවත්කාලීන කිරීම
        $conn->query("UPDATE users SET email = '$email', contact_no = '$contact_no' WHERE username = '$current_user'");
    }
    $msg = "✅ Profile updated successfully!";
}

$result = $conn->query("SELECT * FROM users WHERE username = '$current_user'");
$user = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile & Contacts</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 500px; margin: 40px auto; background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center; }
        .profile-img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #0047AB; margin-bottom: 15px; }
        label { display: block; text-align: left; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #475569; }
        input[type="text"], input[type="email"], input[type="file"] { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 15px; font-size: 14px; outline: none; background: #fff; }
        input[type="submit"] { background: #0047AB; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; transition: 0.2s; }
        input[type="submit"]:hover { background: #003380; }
        .back-link { display: inline-block; margin-top: 15px; text-decoration: none; color: #0047AB; font-weight: 600; }
    </style>
</head>
<body>

<div class="container">
    <h2>👤 Edit Profile & Contacts</h2>
    <?php if(!empty($msg)) echo "<p style='color:green; font-weight:bold; margin-bottom:15px;'>$msg</p>"; ?>
    
    <?php 
        $profile_img = (!empty($user['profile_pic']) && file_exists("uploads/" . $user['profile_pic'])) ? "uploads/" . $user['profile_pic'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
    ?>
    <img src="<?php echo $profile_img; ?>" alt="Profile" class="profile-img">

    <form method="POST" action="" enctype="multipart/form-data">
        <label>Email Address:</label>
        <input type="email" name="email" value="<?php echo $user['email'] ?? ''; ?>" placeholder="Enter your email" required>

        <label>Contact Number:</label>
        <input type="text" name="contact_no" value="<?php echo $user['contact_no'] ?? ''; ?>" placeholder="Enter phone number" required>

        <label>Change Profile Picture:</label>
        <input type="file" name="profile_pic" accept="image/*">

        <input type="submit" name="update_profile" value="Update Profile">
    </form>

    <a href="index.php" class="back-link">⬅ Back to Dashboard</a>
</div>

</body>
</html>