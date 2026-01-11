<?php
include "include/connection.php";

$records_result = null; 
$result = null;
$message = "";

// --- 1. HANDLE CANCELLATION ---
if (isset($_POST['cancel_appt'])) {
    $appt_id = $_POST['appt_id'];
    $email = $_POST['email']; // Get email again to refresh the list

    // Update status to 'Cancelled'
    // NOTE: Make sure your table primary key is named 'id'. If it is 'appointment_id', change it below.
    $stmt_cancel = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
    $stmt_cancel->bind_param("i", $appt_id);
    
    if($stmt_cancel->execute()) {
        $message = "Appointment cancelled successfully.";
    } else {
        $message = "Error cancelling appointment.";
    }
}

// --- 2. FETCH DATA (Runs on Check OR after Cancel) ---
if (isset($_POST['check']) || isset($_POST['cancel_appt'])) {
    
    // If we didn't just cancel, get email from the main form
    if (!isset($email)) {
        $email = $_POST['email'];
    }

    // Get Appointments
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE patient_email = ? ORDER BY appointment_date DESC, appointment_time DESC");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Get Medical Records
    $stmt2 = $conn->prepare("SELECT * FROM medical_records WHERE patient_email = ? ORDER BY record_date DESC");
    $stmt2->bind_param("s", $email);
    $stmt2->execute();
    $records_result = $stmt2->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Check Appointment Status</title>
<link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">

<style>
/* --- KEEPING YOUR EXISTING STYLES --- */
body { margin: 0; font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #4facfe, #00f2fe); min-height: 100vh; }
body::before { content: ""; position: fixed; inset: 0; background-image: url('photo/pku.jpeg'); background-size: cover; background-repeat: no-repeat; background-position: center 3cm; filter: blur(8px); z-index: -1; }
header { background: #ffffff; display: flex; align-items: center; justify-content: space-between; padding: 15px 40px; border-bottom: 2px solid #eaeaea; }
.logo { height: 50px; }
.back-btn { text-decoration: none; color: #007bff; font-weight: 600; padding: 8px 20px; border: 2px solid #007bff; border-radius: 20px; transition: all 0.3s ease; }
.back-btn:hover { background: #007bff; color: white; }
.topbar { width: 100%; height: 70px; background: linear-gradient(90deg, #007bff, #00b4ff); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
.topbar-title { color: white; font-size: 24px; font-weight: bold; }
.wrapper { display: flex; justify-content: center; margin-top: 40px; padding-bottom: 50px; }
.card { width: 700px; /* Widened slightly */ background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }

/* Form Elements */
input { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box; }
.main-btn { width: 100%; padding: 12px; background: linear-gradient(90deg, #007bff, #00b4ff); color: white; border: none; font-size: 15px; border-radius: 8px; cursor: pointer; font-weight: bold; }
.main-btn:hover { background: linear-gradient(90deg, #0056b3, #0099cc); }

/* Table Styles */
table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 30px; }
th { background: #f1f5f9; padding: 10px; text-align: left; color: #444; }
td { padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 14px; color: #333; }
h3 { border-left: 5px solid #007bff; padding-left: 10px; color: #333; margin-bottom: 15px; margin-top: 30px; }

/* Status Badges */
.status { padding: 5px 10px; border-radius: 6px; font-weight: bold; font-size: 12px; }
.Approved { background: #dcfce7; color: #166534; }
.Rejected { background: #fee2e2; color: #991b1b; }
.Pending  { background: #fef9c3; color: #854d0e; }
.Cancelled { background: #f3f4f6; color: #6b7280; text-decoration: line-through; }

/* New Cancel Button Style */
.cancel-btn {
    background: #ff4d4d; color: white; border: none;
    padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;
}
.cancel-btn:hover { background: #cc0000; }
.msg-box { padding: 10px; background: #d1fae5; color: #065f46; border-radius: 6px; margin-bottom: 15px; text-align: center; }
</style>
</head>

<body>

<header>
  <img src="photo/download.png" class="logo">
  <a href="patient_homepage.php" class="back-btn">← Home</a>
</header>

<div class="topbar">
  <div class="topbar-title">My Health Dashboard</div>
</div>

<div class="wrapper">
  <div class="card">

    <?php if ($message) { echo "<div class='msg-box'>$message</div>"; } ?>

    <form method="POST">
      <label>Enter Email to Check:</label>
      <input type="email" name="email" required placeholder="e.g. ali@gmail.com" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
      <button type="submit" name="check" class="main-btn">View Records</button>
    </form>

    <?php if (isset($result)) { ?>
      
      <h3>📅 Appointment History</h3>
      <?php if (mysqli_num_rows($result) > 0) { ?>
          <table>
            <tr>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
              <th>Action</th> </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><?= $row['appointment_date'] ?></td>
              <td><?= date('h:i A', strtotime($row['appointment_time'])) ?></td>
              <td>
                <span class="status <?= $row['status'] ?>">
                  <?= $row['status'] ?>
                </span>
              </td>
              <td>
                <?php if ($row['status'] == 'Pending' || $row['status'] == 'Approved') { ?>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');" style="margin:0;">
                        <input type="hidden" name="appt_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="email" value="<?= $email ?>">
                        <button type="submit" name="cancel_appt" class="cancel-btn">Cancel</button>
                    </form>
                <?php } else { ?>
                    <span style="color:#aaa; font-size:12px;">-</span>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </table>
      <?php } else { echo "<p>No appointments found.</p>"; } ?>


      <h3>🩺 Medical Records</h3>
      <?php if ($records_result && mysqli_num_rows($records_result) > 0) { ?>
          <table>
            <tr>
              <th width="20%">Date</th>
              <th width="25%">Doctor</th>
              <th>Diagnosis & Treatment</th>
            </tr>
            <?php while ($rec = mysqli_fetch_assoc($records_result)) { ?>
            <tr>
              <td style="vertical-align: top;">
                  <?= date('d M Y', strtotime($rec['record_date'])) ?>
              </td>
              <td style="vertical-align: top;">
                  Dr. <?= htmlspecialchars($rec['doctor_name']) ?>
              </td>
              <td>
                  <strong>Diagnosis:</strong> <?= htmlspecialchars($rec['diagnosis']) ?><br>
                  <div style="margin-top:5px; color:#555; font-size:13px;">
                      <em>Rx: <?= htmlspecialchars($rec['treatment']) ?></em>
                  </div>
              </td>
            </tr>
            <?php } ?>
          </table>
      <?php } else { echo "<p>No medical records found for this email.</p>"; } ?>

    <?php } ?>

  </div>
</div>

</body>
</html>