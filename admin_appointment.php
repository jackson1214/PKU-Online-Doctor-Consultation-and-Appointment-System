<?php
session_start();
include "include/connection.php";

class AppointmentManager {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function updateStatus($id, $status) {
        $id = (int) $id;
        $status = mysqli_real_escape_string($this->conn, $status);
        $sql = "UPDATE appointments SET status='$status' WHERE id=$id";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($id) {
        $id = (int) $id;
        $sql = "DELETE FROM appointments WHERE id=$id";
        return mysqli_query($this->conn, $sql);
    }

    public function getAllAppointments() {
        // Order by Date DESC so new appointments appear first
        $sql = "SELECT * FROM appointments ORDER BY appointment_date DESC, appointment_time DESC";
        return mysqli_query($this->conn, $sql);
    }

    public function redirect($url) {
        header("Location: $url");
        exit();
    }
}

$manager = new AppointmentManager($conn);

// --- Handle Admin Actions ---
if (isset($_GET['approve'])) {
    $manager->updateStatus($_GET['approve'], 'Approved');
    $manager->redirect('admin_appointment.php');
}

if (isset($_GET['reject'])) {
    $manager->updateStatus($_GET['reject'], 'Rejected');
    $manager->redirect('admin_appointment.php');
}

if (isset($_GET['delete'])) {
    $manager->delete($_GET['delete']);
    $manager->redirect('admin_appointment.php');
}

$result = $manager->getAllAppointments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Appointments</title>
    <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f9; }

        .header { background: white; padding: 15px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

        .main { display: flex; height: calc(100vh - 75px); }

        .sidebar { width: 260px; background: #111827; padding: 25px; }
        .sidebar h2 { color: white; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 12px 15px; margin-bottom: 10px; border-radius: 8px; text-decoration: none; color: #d1d5db; }
        .sidebar a:hover, .sidebar a.active { background: #2563eb; color: white; }
        .logout { color: #ef4444 !important; margin-top: auto; }

        .content { flex: 1; padding: 40px; overflow-y: auto; } /* Added overflow-y for scrolling */
        .content h1 { margin-bottom: 25px; }

        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; text-align: left; padding: 12px; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #e5e7eb; }

        /* STATUS BADGES */
        .status { padding: 5px 10px; border-radius: 6px; font-size: 14px; font-weight: 600; display: inline-block; }
        .Approved { background: #dcfce7; color: #166534; }
        .Rejected { background: #fee2e2; color: #991b1b; }
        .Pending  { background: #fef9c3; color: #854d0e; }
        /* NEW: Cancelled Status Style */
        .Cancelled { background: #e5e7eb; color: #374151; text-decoration: line-through; }

        .action a { padding: 6px 10px; border-radius: 6px; font-size: 12px; text-decoration: none; margin-right: 5px; display: inline-block; margin-bottom: 2px; }
        .approve { background: #22c55e; color: white; }
        .reject  { background: #f97316; color: white; }
        .delete  { background: #ef4444; color: white; }
        .action a:hover { opacity: 0.85; }
    </style>
</head>

<body>

    <div class="header">
        <strong>Admin Dashboard</strong>
    </div>

    <div class="main">

        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="admin_homepage.php">Dashboard</a>
            <a href="admin_appointment.php" class="active">Check Appointments</a>
            <a href="admin_add_appointment.php">Add Appointments</a>
            <a href="admin_doctor.php">Doctor Profile</a>
            <a href="admin_approval_login.php">Doctor Approvals</a>
            <a href="admin_manage_users.php">Admin Approvals</a>
            <a href="admin_view_review.php">Patient Feedback</a>
            <a href="admin_login.php" class="logout">Logout</a>
        </div>

        <div class="content">
            <h1>Appointment Management</h1>

            <div class="card">
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['patient_name']) ?></td>
                        <td><?= htmlspecialchars($row['patient_email']) ?></td>
                        <td style="white-space: nowrap;"><?= $row['appointment_date'] ?></td>
                        <td style="white-space: nowrap;"><?= date('h:i A', strtotime($row['appointment_time'])) ?></td>
                        <td><?= htmlspecialchars($row['reason']) ?></td>
                        <td>
                            <span class="status <?= $row['status'] ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td class="action" style="white-space: nowrap;">
                            <?php if ($row['status'] != 'Cancelled') { ?>
                                <a class="approve" href="?approve=<?= $row['id'] ?>">Approve</a>
                                <a class="reject" href="?reject=<?= $row['id'] ?>">Reject</a>
                            <?php } else { ?>
                                <span style="font-size:12px; color:#777; margin-right:5px;">(Patient Cancelled)</span>
                            <?php } ?>
                            
                            <a class="delete" href="?delete=<?= $row['id'] ?>" 
                               onclick="return confirm('Delete this appointment record?')">
                               Delete
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

    </div>

</body>
</html>