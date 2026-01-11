<?php
session_start();
include 'include/connection.php';

$message = "";

// --- DELETE LOGIC ---
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']); // Ensure ID is an integer for security

    // Prepare DELETE statement
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "<div class='alert success'>Doctor profile deleted successfully.</div>";
    } else {
        $message = "<div class='alert error'>Error deleting record: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// --- FETCH DOCTORS ---
$sql = "SELECT * FROM doctors ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Doctors</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- REUSING YOUR DASHBOARD CSS --- */
        body { margin: 0; font-family: 'Inter', sans-serif; background: #f4f6f9; }
        .header { background: white; padding: 15px 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .main { display: flex; height: calc(100vh - 75px); }
        .sidebar { width: 260px; background: #111827; padding: 25px; }
        .sidebar h2 { color: white; margin-bottom: 30px; }
        .sidebar a { display: block; padding: 12px 15px; margin-bottom: 10px; border-radius: 8px; text-decoration: none; color: #d1d5db; }
        .sidebar a:hover, .sidebar a.active { background: #2563eb; color: white; }
        .content { flex: 1; padding: 40px; overflow-y: auto; }
        .logout { color: #ef4444 !important; margin-top: auto; }

        /* --- TABLE SPECIFIC CSS --- */
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f9fafb; color: #374151; font-weight: 600; font-size: 14px; }
        tr:hover { background-color: #f9f9f9; }
        
        /* Action Button */
        .btn-delete {
            background-color: #ef4444; 
            color: white; 
            padding: 8px 12px; 
            text-decoration: none; 
            border-radius: 6px; 
            font-size: 13px;
            transition: background 0.2s;
        }
        .btn-delete:hover { background-color: #dc2626; }

        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <div class="header">
        <strong>Clinic Admin Panel</strong>
    </div>

    <div class="main">
        <div class="sidebar">
            <h2>Admin Portal</h2>
            <a href="admin_homepage.php">Dashboard</a>
            <a href="admin_appointment.php">Check Appointments</a>
            <a href="admin_add_appointment.php">Add Appointments</a>
            <a href="admin_doctor.php"class="active">Doctor Profile</a>
            <a href="admin_approval_login.php">Doctor Approvals</a>
            <a href="admin_manage_users.php">Admin Approvals</a>
            <a href="admin_view_review.php">Patient Feedback</a>
            <a href="admin_login.php" class="logout">Logout</a>
        </div>

        <div class="content">
            <h1>Manage Doctors</h1>
            <?php echo $message; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doctor Name</th>
                        <th>Specialization</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['specialization']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                            
                            // DELETE BUTTON with JavaScript Confirmation
                            echo "<td>
                                    <a href='admin_doctors.php?delete_id=" . $row['id'] . "' 
                                       class='btn-delete'
                                       onclick='return confirm(\"Are you sure you want to delete Dr. " . htmlspecialchars($row['name'], ENT_QUOTES) . "?\");'>
                                       Delete
                                    </a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:#777;'>No doctors found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>