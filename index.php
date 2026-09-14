<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'my_db');
$current_user = $_SESSION['username'];

// පරිශීලක දත්ත ලබා ගැනීම
$user_res = $conn->query("SELECT * FROM users WHERE username = '$current_user'");
$user_data = $user_res->fetch_assoc();
$profile_img = (!empty($user_data['profile_pic']) && file_exists("uploads/" . $user_data['profile_pic'])) ? "uploads/" . $user_data['profile_pic'] : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';

// නව Schedule එකක් ඇතුළත් කිරීම
if (isset($_POST['submit'])) {
    $date = $_POST['schedule_date'];
    $time = $_POST['schedule_time'];
    $reason = $_POST['reason'];
    $activity_type = $_POST['activity_type'];

    $sql = "INSERT INTO schedules (username, schedule_date, schedule_time, reason, activity_type, status) VALUES ('$current_user', '$date', '$time', '$reason', '$activity_type', 'Pending')";
    $conn->query($sql);
    header("Location: index.php?section=schedules");
    exit();
}

// Status එක Update කිරීම (Pending <-> Completed)
if (isset($_GET['toggle_id'])) {
    $tid = $_GET['toggle_id'];
    $curr_status = $_GET['status'];
    $new_status = ($curr_status == 'Pending') ? 'Completed' : 'Pending';
    $conn->query("UPDATE schedules SET status = '$new_status' WHERE id = $tid AND username = '$current_user'");
    header("Location: index.php?section=schedules");
    exit();
}

// PHPMailer හරහා ඊමේල් යැවීම සහ Log කිරීම
require_once 'phpmailer/Exception.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';

$mail_msg = "";
if (isset($_POST['send_email'])) {
    $to_email = $_POST['to_email'];
    $mail_subject = $_POST['mail_subject'];
    $mail_message = $_POST['mail_message'];

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'timeshedule@gmail.com'; 
        $mail->Password   = 'gxocwjgwuoitvpvu';    
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('timeshedule@gmail.com', $_SESSION['username']);
        $mail->addAddress($to_email);

        $mail->isHTML(true);
        $mail->Subject = $mail_subject;
        $mail->Body    = nl2br($mail_message);

        $mail->send();
        
        // ඊමේල් ඉතිහාසය (Email Log) ඩේටාබේස් එකේ සේව් කිරීම
        $stmt = $conn->prepare("INSERT INTO email_logs (username, to_email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $current_user, $to_email, $mail_subject, $mail_message);
        $stmt->execute();

        $mail_msg = "✅ Email sent successfully & logged!";
    } catch (Exception $e) {
        $mail_msg = "⚠️ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// ඩිලීට් කිරීම
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM schedules WHERE id = $id AND username = '$current_user'");
    header("Location: index.php?section=schedules");
    exit();
}

$active_section = isset($_GET['section']) ? $_GET['section'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Scheduling Dashboard</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; color: #333; }
        .dashboard-container { max-width: 1000px; margin: 20px auto; background: #ffffff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        
        .top-bar { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f0f2f5; padding-bottom: 15px; margin-bottom: 20px; }
        .user-profile { display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit; }
        .user-profile img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #0047AB; }
        .user-info h4 { margin: 0; font-size: 14px; color: #555; }
        .user-info h2 { margin: 0; font-size: 16px; color: #0047AB; }
        .logout-btn { background-color: #fff1f2; color: #e11d48; padding: 6px 14px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 13px; }

        .nav-sections { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 25px; background: #f8fafc; padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0; }
        .nav-sections a { padding: 8px 14px; background: white; color: #334155; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600; border: 1px solid #cbd5e1; transition: 0.2s; }
        .nav-sections a:hover, .nav-sections a.active { background: #0047AB; color: white; border-color: #0047AB; }

        .section-box { background: #f8fafc; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
        input, select, textarea { padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; width: 100%; margin-bottom: 15px; background: white; }
        input[type="submit"], .custom-btn { background: #0047AB; color: white; border: none; cursor: pointer; font-weight: 600; padding: 12px 20px; border-radius: 8px; transition: 0.2s; }
        input[type="submit"]:hover { background: #003380; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
        th, td { padding: 12px 15px; text-align: left; font-size: 14px; border-bottom: 1px solid #e2e8f0; }
        th { background-color: #f1f5f9; color: #475569; font-size: 12px; }
        
        .edit-btn { background-color: #fef3c7; color: #d97706; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .delete-btn { background-color: #fee2e2; color: #dc2626; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-decoration: none; display: inline-block; }
        .status-pending { background: #fef9c3; color: #ca8a04; }
        .status-completed { background: #dcfce7; color: #16a34a; }

        .filter-bar { display: flex; gap: 10px; margin-bottom: 15px; }
        .lang-container { background: #fff; padding: 5px 10px; border-radius: 15px; border: 1px solid #cbd5e1; font-size: 13px; display: inline-block; }
        .goog-te-combo { border: none; background: transparent; outline: none; cursor: pointer; }
        .goog-logo-link, .goog-te-banner-frame { display: none !important; }
        body { top: 0 !important; }
    </style>
    <script>
        function applyTemplate(val) {
            let subject = document.getElementById('mail_subject');
            let message = document.getElementById('mail_message');
            if (val === 'meeting') {
                subject.value = 'Meeting Reminder: Upcoming Task Schedule';
                message.value = 'Dear Colleague,\n\nThis is a gentle reminder regarding our scheduled task/meeting. Please be prepared.\n\nBest Regards,\n<?php echo $current_user; ?>';
            } else if (val === 'project') {
                subject.value = 'Project Update & Status Report';
                message.value = 'Hello Team,\n\nHere is the latest update regarding our project task progress. Everything is on track.\n\nBest Regards,\n<?php echo $current_user; ?>';
            } else {
                subject.value = '';
                message.value = '';
            }
        }
    </script>
</head>
<body>

<div class="dashboard-container">
    
    <div class="top-bar">
        <a href="index.php?section=profile" class="user-profile">
            <img src="<?php echo $profile_img; ?>" alt="Profile">
            <div class="user-info">
                <h4>Welcome,</h4>
                <h2><?php echo $_SESSION['username']; ?></h2>
            </div>
        </a>

        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="lang-container">
                🌐 <div id="google_translate_element" style="display:inline-block;"></div>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="nav-sections">
        <a href="index.php?section=home" class="<?php echo ($active_section == 'home') ? 'active' : ''; ?>">🏠 Overview</a>
        <a href="index.php?section=add" class="<?php echo ($active_section == 'add') ? 'active' : ''; ?>">➕ Add Schedule</a>
        <a href="index.php?section=schedules" class="<?php echo ($active_section == 'schedules') ? 'active' : ''; ?>">📋 Schedules & Teams</a>
        <a href="index.php?section=email" class="<?php echo ($active_section == 'email') ? 'active' : ''; ?>">📧 Email & Mailbox</a>
        <a href="index.php?section=profile" class="<?php echo ($active_section == 'profile') ? 'active' : ''; ?>">👤 Profile</a>
        <a href="index.php?section=tools" class="<?php echo ($active_section == 'tools') ? 'active' : ''; ?>">🌐 Tools</a>
    </div>

    <!-- 1. OVERVIEW -->
    <?php if ($active_section == 'home'): ?>
        <div class="section-box" style="text-align: center; padding: 40px;">
            <h1 style="color: #0047AB; margin-top: 0;">👋 Welcome to Advanced Dashboard</h1>
            <p style="color: #64748b; font-size: 16px;">Manage tasks, update statuses, filter schedules, send templated emails, and maintain activity logs efficiently.</p>
            <div style="margin-top: 25px;">
                <a href="index.php?section=add" class="custom-btn" style="text-decoration: none; margin-right: 10px;">Add New Schedule</a>
                <a href="index.php?section=email" class="custom-btn" style="text-decoration: none; background: #0ea5e9;">Send Email</a>
            </div>
        </div>

    <!-- 2. ADD SCHEDULE -->
    <?php elseif ($active_section == 'add'): ?>
        <div class="section-box">
            <h3>📅 Add New Schedule or Team Activity</h3>
            <form method="POST" action="">
                <label>Date:</label>
                <input type="date" name="schedule_date" required>
                
                <label>Time:</label>
                <input type="time" name="schedule_time" required>
                
                <label>Reason / Activity Name:</label>
                <input type="text" name="reason" placeholder="e.g., Client Meeting, Project Sync" required>
                
                <label>Activity Type:</label>
                <select name="activity_type">
                    <option value="Personal">Personal Activity</option>
                    <option value="Team Work">Team Work / Collaboration</option>
                </select>

                <input type="submit" name="submit" value="Save Schedule">
            </form>
        </div>

    <!-- 3. SCHEDULES & TEAMS (Search, Filter & Status Update) -->
    <?php elseif ($active_section == 'schedules'): ?>
        <div class="section-box">
            <h3>📌 Schedules, Search & Status Management</h3>
            
            <!-- Search & Filter Form -->
            <form method="GET" action="index.php" class="filter-bar">
                <input type="hidden" name="section" value="schedules">
                <input type="text" name="search" placeholder="Search by reason..." value="<?php echo $_GET['search'] ?? ''; ?>">
                <select name="filter_type" style="width: 200px;">
                    <option value="">All Types</option>
                    <option value="Personal" <?php if(isset($_GET['filter_type']) && $_GET['filter_type']=='Personal') echo 'selected'; ?>>Personal</option>
                    <option value="Team Work" <?php if(isset($_GET['filter_type']) && $_GET['filter_type']=='Team Work') echo 'selected'; ?>>Team Work</option>
                </select>
                <input type="submit" value="Filter" style="width: 100px; padding: 10px;">
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Reason</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $search = $_GET['search'] ?? '';
                        $filter_type = $_GET['filter_type'] ?? '';
                        
                        $query = "SELECT * FROM schedules WHERE username = '$current_user'";
                        if (!empty($search)) {
                            $query .= " AND reason LIKE '%$search%'";
                        }
                        if (!empty($filter_type)) {
                            $query .= " AND activity_type = '$filter_type'";
                        }
                        $query .= " ORDER BY schedule_date ASC";

                        $result = $conn->query($query);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $st = $row['status'] ?? 'Pending';
                                $st_class = ($st == 'Completed') ? 'status-completed' : 'status-pending';
                                echo "<tr>
                                        <td>" . $row["schedule_date"] . "</td>
                                        <td>" . $row["schedule_time"] . "</td>
                                        <td>" . $row["reason"] . "</td>
                                        <td><span style='background:#e2e8f0; padding:3px 8px; border-radius:4px; font-size:12px;'>" . $row["activity_type"] . "</span></td>
                                        <td><a href='index.php?section=schedules&toggle_id=" . $row["id"] . "&status=" . $st . "' class='status-badge $st_class' title='Click to toggle status'>$st 🔄</a></td>
                                        <td>
                                            <a href='edit.php?id=" . $row["id"] . "' class='edit-btn'>Edit</a>
                                            <a href='index.php?delete_id=" . $row["id"] . "' class='delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; color:#94a3b8;'>No schedules found matching criteria.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>

    <!-- 4. EMAIL & MAILBOX (Templates & Sent Log) -->
    <?php elseif ($active_section == 'email'): ?>
        <div class="section-box">
            <h3>📧 Send Email with Templates & Sent Log</h3>
            <?php if(!empty($mail_msg)) echo "<p style='font-weight:bold; margin-bottom:15px; color:#0047AB;'>" . $mail_msg . "</p>"; ?>
            
            <label><b>Choose Quick Template:</b></label>
            <select id="template_select" onchange="applyTemplate(this.value)">
                <option value="">-- Select Template (Optional) --</option>
                <option value="meeting">Meeting Reminder Template</option>
                <option value="project">Project Update Template</option>
            </select>

            <form method="POST" action="">
                <label>Recipient Email Address:</label>
                <input type="email" name="to_email" placeholder="colleague@example.com" required>
                
                <label>Subject:</label>
                <input type="text" id="mail_subject" name="mail_subject" placeholder="Meeting Reminder" required>
                
                <label>Message Content:</label>
                <textarea id="mail_message" name="mail_message" rows="5" placeholder="Write your schedule or team details here..." required></textarea>
                
                <input type="submit" name="send_email" value="Send Email Now">
            </form>

            <h4 style="margin-top: 30px; color:#334155;">📜 Email History / Sent Log</h4>
            <table>
                <thead>
                    <tr>
                        <th>Recipient</th>
                        <th>Subject</th>
                        <th>Sent Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $log_res = $conn->query("SELECT * FROM email_logs WHERE username = '$current_user' ORDER BY sent_at DESC LIMIT 5");
                        if ($log_res->num_rows > 0) {
                            while($log = $log_res->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $log['to_email'] . "</td>
                                        <td>" . $log['subject'] . "</td>
                                        <td>" . $log['sent_at'] . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; color:#94a3b8;'>No sent email logs found.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>

    <!-- 5. PROFILE & CONTACTS -->
    <?php elseif ($active_section == 'profile'): ?>
        <div class="section-box" style="text-align: center;">
            <h3>👤 Profile & Contact Management</h3>
            <img src="<?php echo $profile_img; ?>" alt="Profile" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:3px solid #0047AB; margin-bottom:15px;">
            
            <p><b>Username:</b> <?php echo $user_data['username']; ?></p>
            <p><b>Email:</b> <?php echo $user_data['email'] ?? 'Not Provided'; ?></p>
            <p><b>Contact No:</b> <?php echo $user_data['contact_no'] ?? 'Not Provided'; ?></p>
            
            <hr style="margin: 20px 0; border:0; border-top:1px solid #cbd5e1;">

            <form action="profile.php" method="GET">
                <input type="submit" value="Edit Profile / Upload Picture & Contacts" style="background: #0ea5e9; width: auto;">
            </form>
        </div>

    <!-- 6. MULTI-LANGUAGE & TOOLS -->
    <?php elseif ($active_section == 'tools'): ?>
        <div class="section-box" style="text-align: center;">
            <h3>🌐 Multi-Language & System Configuration</h3>
            <p>You can seamlessly switch the entire dashboard language using the global selector on the top bar.</p>
            <p style="margin-top: 20px;"><b>Supported Languages:</b> English, Sinhala (සිංහල), Tamil (தமிழ்)</p>
            <div style="margin-top: 30px; font-size: 24px;">
                🇱🇰 🇬🇧 🇮🇳
            </div>
        </div>
    <?php endif; ?>

</div>

<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'en', 
    includedLanguages: 'en,si,ta', 
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>
</html>