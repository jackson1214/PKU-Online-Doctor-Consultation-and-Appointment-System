  <?php
  session_start();
  include "include/connection.php";

  class DashboardStats {
      private $db;

      public function __construct($conn) {
          $this->db = $conn;
      }

      public function getPatientCount() {
          $sql = "SELECT COUNT(id) AS total FROM users";
          $result = $this->db->query($sql);
          
          if ($result) {
              $row = $result->fetch_assoc();
              return $row['total'];
          }
          return 0;
      }

      public function getDoctorCount() {
          $sql = "SELECT COUNT(id) AS total FROM doctorusers";
          $result = $this->db->query($sql);
          
          if ($result) {
              $row = $result->fetch_assoc();
              return $row['total'];
          }
          return 0;
      }
  }

  $dashboard = new DashboardStats($conn);

  $totalPatients = $dashboard->getPatientCount();
  $totalDoctors = $dashboard->getDoctorCount();
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <title>Admin Dashboard</title>
      <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

      <style>
          * { box-sizing: border-box; margin: 0; padding: 0; }
          body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; color: #333; }

          /* Header */
          .header { background: #ffffff; padding: 15px 30px; display: flex; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
          .header img { height: 45px; }

          /* Layout */
          .main { display: flex; height: calc(100vh - 75px); }

          /* Sidebar */
          .sidebar { width: 260px; background-color: #111827; padding: 25px; }
          .sidebar h2 { font-size: 22px; margin-bottom: 30px; font-weight: 700; color: white; }
          .sidebar a { display: block; padding: 12px 15px; margin-bottom: 10px; border-radius: 8px; text-decoration: none; color: #d1d5db; transition: 0.3s; }
          .sidebar a:hover, .sidebar a.active { background-color: #2563eb; color: white; }
          .logout { color: #ef4444 !important; margin-top: auto; }

          /* Content */
          .content { flex: 1; padding: 40px; }
          .dashboard-title { font-size: 28px; font-weight: 700; margin-bottom: 30px; }

          /* Cards */
          .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px; }
          .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); transition: transform 0.2s; }
          .card:hover { transform: translateY(-5px); }
          .card h3 { font-size: 16px; font-weight: 600; color: #6b7280; margin-bottom: 10px; }
          .card p { font-size: 36px; font-weight: 700; color: #2563eb; }
      </style>
  </head>

  <body>

  <div class="header">
    <img src="photo/download.png" alt="UTHM Logo">
  </div>

  <div class="main">

    <div class="sidebar">
          <h2>Admin Homepage</h2>
            <a href="admin_homepage.php"  class="active">Dashboard</a>
            <a href="admin_appointment.php">Check Appointments</a>
            <a href="admin_add_appointment.php">Add Appointments</a>
            <a href="admin_doctor.php">Doctor Profile</a>
            <a href="admin_approval_login.php">Doctor Approvals</a>
            <a href="admin_manage_users.php">Admin Approvals</a>
            <a href="admin_view_review.php">Patient Feedback</a>
            <a href="admin_login.php" class="logout">Logout</a>
      </div>

    <div class="content"> 
      <div class="dashboard-title">Dashboard Overview</div>

      <div class="cards">
        <div class="card">
          <h3>Total Patients</h3>
          <p><?php echo $totalPatients; ?></p>
        </div>

        <div class="card">
          <h3>Total Doctors</h3>
          <p><?php echo $totalDoctors; ?></p>
        </div>
      </div>
    </div>

  </div>

  </body>
  </html>