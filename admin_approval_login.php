<?php
session_start();
include "include/connection.php";

class DoctorManager {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function updateStatus($id, $status) {
        // Use Prepared Statements for security
        $stmt = $this->db->prepare("UPDATE doctorusers SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id); // 's' = string, 'i' = integer
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteDoctor($id) {
        $stmt = $this->db->prepare("DELETE FROM doctorusers WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAllDoctors() {
        $sql = "SELECT * FROM doctorusers 
                ORDER BY CASE WHEN status = 'Pending' THEN 1 ELSE 2 END, id DESC";
        
        $result = $this->db->query($sql);
        return $result;
    }
}

// --- INITIALIZATION & ACTION HANDLING ---

// Initialize the Manager Object
// Assuming 'include/connection.php' provides a variable named $conn
$doctorManager = new DoctorManager($conn);

// 1. Handle Approval
if (isset($_GET['approve'])) {
    $id = (int) $_GET['approve'];
    $doctorManager->updateStatus($id, 'Approved');
    header("Location: admin_approval_login.php");
    exit();
}

// 2. Handle Rejection
if (isset($_GET['reject'])) {
    $id = (int) $_GET['reject'];
    $doctorManager->updateStatus($id, 'Rejected');
    header("Location: admin_approval_login.php");
    exit();
}

// 3. Handle Deletion
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $doctorManager->deleteDoctor($id);
    header("Location: admin_approval_login.php");
    exit();
}

// --- FETCH DATA FOR VIEW ---
$result = $doctorManager->getAllDoctors();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Doctor Approvals</title>
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

        .content { flex: 1; padding: 40px; overflow-y: auto; }
        .content h1 { margin-bottom: 25px; }

        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; text-align: left; padding: 12px; font-weight: 600; }
        td { padding: 12px; border-bottom: 1px solid #e5e7eb; }

        /* Dynamic Status Colors */
        .status { padding: 5px 10px; border-radius: 6px; font-size: 14px; font-weight: 600; }
        .Approved { background: #dcfce7; color: #166534; }
        .Rejected { background: #fee2e2; color: #991b1b; }
        .Pending  { background: #fef9c3; color: #854d0e; }

        .action a { padding: 6px 10px; border-radius: 6px; font-size: 14px; text-decoration: none; margin-right: 5px; display: inline-block; }
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
            <h2>Admin Homepage</h2>
            <a href="admin_homepage.php">Dashboard</a>
            <a href="admin_appointment.php">Check Appointments</a>
            <a href="admin_add_appointment.php">Add Appointments</a>
            <a href="admin_doctor.php">Doctor Profile</a>
            <a href="admin_approval_login.php"class="active">Doctor Approvals</a>
            <a href="admin_manage_users.php">Admin Approvals</a>
            <a href="admin_view_review.php">Patient Feedback</a>
            <a href="admin_login.php" class="logout">Logout</a>
        </div>

        <div class="content">
            <h1>Doctor Account Approvals</h1>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Current Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0) { ?>
                            
                            <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td>#<?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= isset($row['email']) ? htmlspecialchars($row['email']) : 'N/A' ?></td>
                                
                                <td>
                                    <span class="status <?= $row['status'] ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                
                                <td class="action">
                                    <?php if($row['status'] == 'Pending'): ?>
                                        <a class="approve" href="?approve=<?= $row['id'] ?>">Approve</a>
                                        <a class="reject" href="?reject=<?= $row['id'] ?>">Reject</a>
                                    <?php else: ?>
                                        <span style="color:gray; font-size:12px; margin-right:5px;">Processed</span>
                                    <?php endif; ?>
                                    
                                    <a class="delete" href="?delete=<?= $row['id'] ?>" 
                                       onclick="return confirm('Permanently delete this doctor account?')">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:20px;">No doctor accounts found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>