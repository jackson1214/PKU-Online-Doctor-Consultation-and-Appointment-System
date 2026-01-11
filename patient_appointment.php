<?php
include "include/connection.php";

class Appointment {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function create($name, $email, $date, $time, $reason) {

        $stmt = $this->db->prepare("INSERT INTO appointments 
                (patient_name, patient_email, appointment_date, appointment_time, reason) 
                VALUES (?, ?, ?, ?, ?)");
        
        $stmt->bind_param("sssss", $name, $email, $date, $time, $reason);

        if ($stmt->execute()) {
            return true;
        } else {
            return "Error: " . $this->db->error;
        }
    }
}

$success_message = false;

if (isset($_POST['submit'])) {
    $appointmentObj = new Appointment($conn);
    
    $result = $appointmentObj->create(
        $_POST['name'],
        $_POST['email'],
        $_POST['date'],
        $_POST['time'],
        $_POST['reason']
    );

    if ($result === true) {
        $success_message = true;
    } else {
        echo "<script>alert('Failed to book: " . addslashes($result) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <link rel="icon" type="image/png" href="photo/images-removebg-preview.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            margin: 0; padding: 0; font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            min-height: 100vh; position: relative;
        }

        body::before {
            content: ""; position: fixed; inset: 0;
            background: url('photo/pku.jpeg') no-repeat center 3cm / cover;
            filter: blur(8px); z-index: -1;
        }

        header {
            background: #ffffff; display: flex; align-items: center;
            justify-content: space-between; padding: 15px 40px;
            border-bottom: 2px solid #eaeaea; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .logo { height: 50px; }

        .back-btn {
            text-decoration: none; color: #007bff; font-weight: 600;
            padding: 8px 20px; border: 2px solid #007bff;
            border-radius: 20px; transition: 0.3s;
        }

        .back-btn:hover { background: #007bff; color: white; }

        .topbar {
            width: 100%; height: 70px;
            background: linear-gradient(90deg, #007bff, #00b4ff);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .topbar-title { color: white; font-size: 24px; font-weight: bold; }

        .form-wrapper { display: flex; justify-content: center; margin: 40px 0; }

        form {
            width: 420px; background: #ffffff; padding: 25px;
            border-radius: 10px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #333; }

        input, textarea {
            width: 100%; padding: 10px; margin-bottom: 15px;
            border-radius: 6px; border: 1px solid #ccc;
            font-size: 14px; box-sizing: border-box;
        }

        textarea { resize: none; height: 80px; }

        button {
            width: 100%; background: linear-gradient(90deg, #007bff, #00b4ff);
            color: white; border: none; padding: 12px;
            font-size: 15px; border-radius: 8px; cursor: pointer;
            transition: 0.3s; font-weight: bold;
        }

        button:hover { transform: translateY(-1px); filter: brightness(1.1); }
    </style>
</head>
<body>

    <?php if ($success_message): ?>
        <script>
            alert("Appointment submitted. Waiting for admin confirmation");
            window.location.href = "patient_homepage.php";
        </script>
    <?php endif; ?>

    <header>
        <img src="photo/download.png" alt="Logo" class="logo">
        <a href="patient_homepage.php" class="back-btn">← Back to Home</a>
    </header>

    <div class="topbar">
        <div class="topbar-title">Appointment Booking</div>
    </div>

    <div class="form-wrapper">
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" required placeholder="Enter your full name">

            <label>Email:</label>
            <input type="email" name="email" required placeholder="Enter your email">

            <label>Date:</label>
            <input type="date" name="date" required>

            <label>Time:</label>
            <input type="time" name="time" required>

            <label>Reason:</label>
            <textarea name="reason" required placeholder="Describe your symptoms..."></textarea>

            <button type="submit" name="submit">Submit Appointment</button>
        </form>
    </div>

</body>
</html>