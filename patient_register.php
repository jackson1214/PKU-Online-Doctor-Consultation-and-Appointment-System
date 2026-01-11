<?php
class User {
    private $db;

    // Inject the database connection via constructor
    public function __construct($conn) {
        $this->db = $conn;
    }

    public function register($username, $email, $password) {
        // Check if user exists using Prepared Statements (Security)
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return "Username or Email already exists!";
        }

        // Hash password and Insert
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($insertStmt->execute()) {
            return true; // Success
        } else {
            return "Registration failed!";
        }
    }
}

// Execution Logic
include "include/connection.php";
$message = "";

if (isset($_POST['register'])) {
    $userObj = new User($conn);
    $result = $userObj->register($_POST['username'], $_POST['email'], $_POST['password']);

    if ($result === true) {
        header("Location: patient_login.php");
        exit();
    } else {
        $message = $result;
    }
}
?>
  <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      font-family: Arial, sans-serif;
      position: relative;
      overflow: hidden;
      background-color:cornflowerblue;
    }

    body::before {
      content: "";
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
      height: 100%;
      background-image: url('photo/pku.jpeg');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center 5cm;
      filter: blur(8px);
      z-index: -1;
    }

    .signup-box {
      background: rgba(255, 255, 255, 0.3); /* Transparent white box */
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 10px;
      padding: 50px;
      max-width: 400px;
      margin:auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      color: #000;
      text-align: center;
    }

    .noaccount{
    text-decoration: underline;
    text-align: right;
    font-size: 9px;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      margin-bottom: 15px;
      border: none;
      border-radius: 5px;
    }

    button[name="register"] {
      padding: 10px 20px;
      border: none;
      background-color: #007BFF;
      color: white;
      border-radius: 5px;
      cursor: pointer;
    }

    #message {
      margin-top: 15px;
      font-weight: bold;
    }
    .side-btn {
      position: fixed;
      top: 50%;
      transform: translateY(-50%);
      padding: 12px 20px;
      border-radius: 30px;
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      color: #fff;
      font-weight: 600;
      text-decoration: none;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
      z-index: 10;
    }

    .box {
      position: relative;
    }

    .signup {
      position: absolute;
      top: 5px;
      left: 5px;
      color:black;
    }

    .left-btn {
      left: 20px;
    }

    .right-btn {
      right: 20px;
    }


    .side-btn:hover {
      background: #ffffff;
      color: #0077b6;
      transform: translateY(-50%) scale(1.05);
    }
  </style>
<div class="signup-box">
<form method="POST">
    <h2>Patient Register</h2>
    Username: <input type="text" name="username" required><br><br>
    Email: <input type="text" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit" name="register">Register</button>

    <div class="box">
    <a href="patient_login.php" class="signup">Login→</a>
    </div>
</form>
</div>
