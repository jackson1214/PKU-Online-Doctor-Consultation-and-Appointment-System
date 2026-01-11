<?php
session_start();
include "include/connection.php";

class Appointment {
    private $conn;
    private $table_name = "appointments"; 

    public $name;
    public $email;
    public $date;
    public $time;
    public $reason;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (patient_name, patient_email, appointment_date, appointment_time, reason, status) 
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->time = htmlspecialchars(strip_tags($this->time));
        $this->reason = htmlspecialchars(strip_tags($this->reason));
        $this->status = "Approved"; 

        $stmt->bind_param("ssssss", $this->name, $this->email, $this->date, $this->time, $this->reason, $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

// --- 3. Execution Logic (Controller) ---
$message = ""; // Variable to hold success/error messages

if (isset($_POST['save'])) {
    $database = new Database();
    $db = $database->getConnection();

    $appointment = new Appointment($db);

    $appointment->name   = $_POST['name'];
    $appointment->email  = $_POST['email'];
    $appointment->date   = $_POST['date'];
    $appointment->time   = $_POST['time'];
    $appointment->reason = $_POST['reason'];

    if ($appointment->create()) {
        // Redirect to main appointment list on success
        header("Location: admin_appointment.php"); // Changed to match your sidebar link
        exit();
    } else {
        $message = "<div class='alert error'>Unable to save appointment. Please check inputs.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add Appointment</title>
    <link rel="icon" type="image/png" href="photo/images-removebg-preview.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- GLOBAL RESET & FONT --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; color: #333; }

        /* --- HEADER --- */
        .header { background: #ffffff; padding: 15px 30px; display: flex; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); justify-content: space-between; }
        .header strong { font-size: 18px; color: #111827; }

        /* --- LAYOUT --- */
        .main { display: flex; height: calc(100vh - 75px); }

        /* --- SIDEBAR --- */
        .sidebar { width: 260px; background: #111827; padding: 25px; }
        .sidebar h2 { color: white; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 12px 15px; margin-bottom: 10px; border-radius: 8px; text-decoration: none; color: #d1d5db; }
        .sidebar a:hover, .sidebar a.active { background: #2563eb; color: white; }
        .logout { color: #ef4444 !important; margin-top: auto; }

        /* --- CONTENT AREA --- */
        .content { flex: 1; padding: 40px; overflow-y: auto; }
        h1 { font-size: 24px; font-weight: 700; margin-bottom: 25px; color: #111827; }

        /* --- FORM CARD --- */
        .card { 
            background: white; 
            border-radius: 12px; 
            padding: 30px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
            max-width: 600px; /* Limit form width */
        }

        /* --- FORM ELEMENTS --- */
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #374151; }
        
        input, textarea { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 20px; 
            border: 1px solid #d1d5db; 
            border-radius: 8px; 
            font-size: 14px; 
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-submit { 
            width: 100%; 
            padding: 12px; 
            background-color: #2563eb; 
            color: white; 
            border: none; 
            border-radius: 8px; 
            font-size: 15px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: background 0.2s; 
        }

        .btn-submit:hover { background-color: #1d4ed8; }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }
        .btn-back:hover { color: #111827; }

        .alert.error { 
            background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; 
        }
    </style>
</head>

<body>

    <div class="header">
        <strong>Clinic Admin Panel</strong>
        <span style="font-size: 14px; color: #666;">Adding Appointment</span>
    </div>

    <div class="main">
        
        <div class="sidebar">
            <h2>Admin Portal</h2>
            <a href="admin_homepage.php">Dashboard</a>
            <a href="admin_appointment.php">Check Appointments</a>
            <a href="admin_add_appointment.php" class="active">Add Appointments</a>
            <a href="admin_doctor.php">Doctor Profile</a>
            <a href="admin_approval_login.php">Doctor Approvals</a>
            <a href="admin_manage_users.php">Admin Approvals</a>
            <a href="admin_view_review.php">Patient Feedback</a>
            <a href="admin_login.php" class="logout">Logout</a>
        </div>

        <div class="content">
            <a href="admin_appointment.php" class="btn-back">← Back to Appointments List</a>
            <h1>Create New Appointment</h1>
            
            <?php echo $message; ?>

            <div class="card">
                <form method="POST">
                    
                    <label>Patient Name</label>
                    <input type="text" name="name" placeholder="e.g. Ali bin Abu" required>

                    <label>Patient Email</label>
                    <input type="email" name="email" placeholder="e.g. ali@example.com" required>

                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label>Date</label>
                            <input type="date" name="date" required>
                        </div>
                        <div style="flex: 1;">
                            <label>Time</label>
                            <input type="time" name="time" required>
                        </div>
                    </div>

                    <label>Reason for Visit</label>
                    <textarea name="reason" rows="4" placeholder="Brief description of symptoms..." required></textarea>

                    <button type="submit" name="save" class="btn-submit">Save Appointment</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>