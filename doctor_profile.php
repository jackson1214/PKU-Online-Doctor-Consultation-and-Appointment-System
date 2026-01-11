<?php
session_start();
include 'include/connection.php';

class DoctorProfile {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function save($name, $spec, $phone, $bio) {
        $stmt = $this->db->prepare("INSERT INTO doctors (name, specialization, phone, bio) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $spec, $phone, $bio);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $error = $stmt->error;
            $stmt->close();
            return $error;
        }
    }
}
$message = "";
if (isset($_POST['submit'])) {
    $profile = new DoctorProfile($conn);
    
    $result = $profile->save(
        $_POST['name'], 
        $_POST['specialization'], 
        $_POST['phone'], 
        $_POST['bio']
    );

    if ($result === true) {
        $message = "<div class='alert success'>Profile created successfully!</div>";
    } else {
        $message = "<div class='alert error'>Error: " . htmlspecialchars($result) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Profile Setup</title>
    <link rel="icon" type="image/png" href="photo/images-removebg-preview.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* DASHBOARD & LAYOUT CSS */
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f9; }
        .header { background: white; padding: 15px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .user-info { font-size: 14px; color: #555; }
        .main { display: flex; height: calc(100vh - 75px); }
        .sidebar { width: 260px; background: #111827; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: white; margin-bottom: 30px; font-size: 20px; }
        .sidebar a { display: block; padding: 12px 15px; margin-bottom: 10px; border-radius: 8px; text-decoration: none; color: #d1d5db; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #2563eb; color: white; }
        .logout { color: #ef4444 !important; margin-top: 50px; }
        .logout:hover { background: rgba(239, 68, 68, 0.1) !important; }

        .content { flex: 1; padding: 40px; overflow-y: auto; }
        .content h1 { margin-bottom: 25px; color: #111827; }

        /* FORM STYLING */
        .card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); max-width: 600px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px; }
        input[type="text"], textarea {
            width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #d1d5db;
            border-radius: 8px; font-size: 14px; box-sizing: border-box;
            transition: 0.3s; font-family: 'Inter', sans-serif;
        }
        input:focus, textarea:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        button {
            width: 100%; padding: 12px; background: #2563eb; color: white; border: none;
            border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.3s;
        }
        button:hover { background: #1d4ed8; }

        /* NOTIFICATIONS */
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>

    <div class="header">
        <strong>Clinic Management System</strong>
        <div class="user-info">Dr. <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></div>
    </div>

    <div class="main">
        <div class="sidebar">
            <h2>Doctor Portal</h2>
            <a href="doctor_homepage.php">My Appointments</a>
            <a href="doctor_profile.php" class="active">Doctor Profile</a>
            <a href="doctor_view_review.php">Feedback</a>
            <a href="http://localhost:3000">Consultation</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>

        <div class="content">
            <h1>Setup Your Profile</h1>
            
            <?= $message; ?>

            <div class="card">
                <form method="POST" action="">
                    <label>Doctor Name</label>
                    <input type="text" name="name" required placeholder="e.g. Dr. John Doe">

                    <label>Specialization</label>
                    <input type="text" name="specialization" required placeholder="e.g. Cardiologist">

                    <label>Contact Number</label>
                    <input type="text" name="phone" required placeholder="e.g. 012-3456789">

                    <label>Short Bio</label>
                    <textarea name="bio" rows="5" placeholder="Write a brief description about your experience..."></textarea>

                    <button type="submit" name="submit">Save Profile</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>