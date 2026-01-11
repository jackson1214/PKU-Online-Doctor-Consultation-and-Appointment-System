<?php
session_start();
include "include/connection.php";

class Appointment {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    /**
     * Fetch all approved appointments
     * You can add a WHERE clause for doctor_id if your table supports it
     */
    public function getApprovedAppointments() {
        $sql = "SELECT * FROM appointments WHERE status = 'Approved' ORDER BY appointment_date ASC";
        $result = $this->db->query($sql);
        
        $appointments = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
        }
        return $appointments;
    }
}
$appointmentObj = new Appointment($conn);
$appointments = $appointmentObj->getApprovedAppointments();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f9; }

        .header { background: white; padding: 15px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .user-info { font-size: 14px; color: #555; }

        .main { display: flex; height: calc(100vh - 75px); }

        .sidebar { width: 260px; background: #111827; padding: 25px; }
        .sidebar h2 { color: white; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 12px 15px; margin-bottom: 10px; border-radius: 8px; text-decoration: none; color: #d1d5db; }
        .sidebar a:hover, .sidebar a.active { background: #2563eb; color: white; }

        .content { flex: 1; padding: 40px; overflow-y: auto; }
        .content h1 { margin-bottom: 10px; }
        .subtitle { margin-bottom: 30px; color: #666; font-size: 14px; }

        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; text-align: left; padding: 12px; font-weight: 600; color: #374151; }
        td { padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 14px; }

        .status { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #dcfce7; color: #166534; display: inline-block;}
        
        .btn-record { 
            background: #2563eb; 
            color: white; 
            padding: 8px 12px; 
            border-radius: 6px; 
            text-decoration: none; 
            font-size: 13px; 
            font-weight: 600;
        }
        .btn-record:hover { background: #1d4ed8; }
        
        .email-link { color: #2563eb; text-decoration: none; }
        .email-link:hover { text-decoration: underline; }

        .logout { color: #ef4444 !important; margin-top: 50px; }
        .logout:hover { background: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; }
    </style>
</head>

<div class="header">
        <strong>Clinic Management System</strong>
        <div class="user-info">Welcome, Dr. <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></div>
    </div>

    <div class="main">
        <div class="sidebar">
            <h2>Doctor Portal</h2>
            <a href="doctor_homepage.php" class="active">My Appointments</a>
            <a href="doctor_profile.php">Doctor Profile</a>
            <a href="doctor_view_review.php">Feedback</a>
            <a href="http://localhost:3000">Consultation</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>

        <div class="content">
            <h1>Upcoming Appointments</h1>
            <p class="subtitle">These appointments have been confirmed by the Admin.</p>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Patient Name</th>
                            <th>Email</th> 
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($appointments)): ?>
                            <?php foreach ($appointments as $row): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($row['appointment_date'])) ?></td>
                                <td><?= date('h:i A', strtotime($row['appointment_time'])) ?></td>
                                <td style="font-weight: 600; color: #111827;">
                                    <?= htmlspecialchars($row['patient_name']) ?>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($row['patient_email']) ?>" class="email-link">
                                        <?= htmlspecialchars($row['patient_email']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td><span class="status">Approved</span></td>
                                <td>
                                    <a href="doctor_add_record.php?patient_id=<?= $row['id'] ?>&email=<?= urlencode($row['patient_email']) ?>" class="btn-record">
                                        + Add Record
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:30px; color:#888;">
                                    No upcoming approved appointments.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>