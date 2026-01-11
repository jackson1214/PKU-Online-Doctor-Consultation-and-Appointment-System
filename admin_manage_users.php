<?php
session_start();
include "include/connection.php";

class AdminManager {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }


    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE adminusers SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteAdmin($id) {
        $stmt = $this->db->prepare("DELETE FROM adminusers WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAllAdmins() {
        $sql = "SELECT * FROM adminusers 
                ORDER BY CASE WHEN status = 'pending' THEN 1 ELSE 2 END, id DESC";
        
        $result = $this->db->query($sql);
        return $result;
    }
}

$adminManager = new AdminManager($conn);

if (isset($_GET['approve'])) {
    $id = (int) $_GET['approve'];
    $adminManager->updateStatus($id, 'approved');
    header("Location: admin_manage_users.php");
    exit();
}

if (isset($_GET['reject'])) {
    $id = (int) $_GET['reject'];
    $adminManager->updateStatus($id, 'rejected'); 
    header("Location: admin_manage_users.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $adminManager->deleteAdmin($id);
    header("Location: admin_manage_users.php");
    exit();
}

$result = $adminManager->getAllAdmins();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Approvals</title>
    <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f9; }

        .header { background: white; padding: 15px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }

        .main { display: flex; height: calc(100vh - 75px); }

        .sidebar { width: 260px; background: #111827; padding: 25px; }
        .sidebar h2 { color: white; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 12px 15px; margin-bottom: 10px; border-radius: 8px; text-decoration: none; color: #d1d5db; }
        .sidebar a:hover, .sidebar a.active { background: #2563eb; color: white; }
        .logout { color: #ef4444 !important; margin-top: 50px; }

        .content { flex: 1; padding: 40px; overflow-y: auto; }
        .content h1 { margin-bottom: 25px; }

        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; text-align: left; padding: 12px; font-weight: 600; color: #374151; }
        td { padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563; }

        /* Dynamic Status Colors */
        .status { padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .approved { background: #dcfce7; color: #166534; }
        .rejected { background: #fee2e2; color: #991b1b; }
        .pending  { background: #fef9c3; color: #854d0e; }

        .action a { padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; margin-right: 5px; display: inline-block; font-weight: 600; transition: opacity 0.2s;}
        .approve { background: #22c55e; color: white; }
        .delete  { background: #ef4444; color: white; }
        .action a:hover { opacity: 0.85; }
    </style>
</head>

<body>

    <div class="header">
        <strong>Clinic Management System</strong>
        <span>Current User: <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
    </div>

    <div class="main">

        <div class="sidebar">
            <h2>Admin Portal</h2>
            <a href="admin_homepage.php">Dashboard</a>
            <a href="admin_appointment.php">Check Appointments</a>
            <a href="admin_add_appointment.php">Add Appointments</a>
            <a href="admin_doctor.php">Doctor Profile</a>
            <a href="admin_approval_login.php">Doctor Approvals</a>
            <a href="admin_manage_users.php" class="active">Admin Approvals</a>
            <a href="admin_view_review.php">Patient Feedback</a>
            <a href="admin_login.php" class="logout">Logout</a>
        </div>

        <div class="content">
            <h1>Manage Admin Approvals</h1>

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
                                    <span class="status <?= strtolower($row['status']) ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                
                                <td class="action">
                                    <?php if($row['status'] == 'pending'): ?>
                                        <a class="approve" href="?approve=<?= $row['id'] ?>">Approve</a>
                                    <?php endif; ?>
                                    
                                    <a class="delete" href="?delete=<?= $row['id'] ?>" 
                                       onclick="return confirm('Are you sure you want to delete this admin user?')">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:20px;">No admin users found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>