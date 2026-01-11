<?php
session_start();
include "include/connection.php";

class AdminAuth {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function login($username, $password) {

        $stmt = $this->db->prepare("SELECT * FROM adminusers WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute(); 
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                
                // Check Status
                if ($user['status'] == 'pending') {
                    return ['status' => 'PENDING', 'message' => 'Your account is still pending approval. Please contact the administrator.'];
                }

                return ['status' => 'SUCCESS', 'user' => $user];
            }
        }

        return ['status' => 'INVALID', 'message' => 'Invalid username or password!'];
    }
}

if (isset($_POST['login'])) {
    $auth = new AdminAuth($conn);
    $loginResult = $auth->login($_POST['username'], $_POST['password']);

    if ($loginResult['status'] === 'SUCCESS') {
        $_SESSION['admin_id'] = $loginResult['user']['id'];
        $_SESSION['username'] = $loginResult['user']['username'];
        

        header("Location: admin_homepage.php");
        exit();

    } else {
        echo "<script>alert('" . $loginResult['message'] . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login System</title>
    <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
    <style>
      body {
      margin: 0;
      padding: 0;
      height: 100vh;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      background-attachment: fixed;
    }

    body::before {
      content: "";
      position: fixed;
      left: 0; bottom: 0;
      width: 100%;
      height: 100%;
      background-image: url('photo/pku.jpeg');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center 3cm;
      filter: blur(8px); z-index: -1;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 20px;
      padding: 40px;
      width: 350px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      text-align: center;
      color: #fff;
    }

    .login-title {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 20px;
      color: black;
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

    .left-btn {
      left: 20px;
    }

    .side-btn:hover {
      background: #ffffff;
      color: #0077b6;
      transform: translateY(-50%) scale(1.05);
    }

    label {
      display: block;
      text-align: left;
      margin-top: 15px;
      font-size: 14px;
      font-weight: 500;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      border: none;
      border-radius: 8px;
      outline: none;
      font-size: 14px;
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      transition: 0.3s ease;
      box-sizing: border-box; /* Ensures padding doesn't break width */
    }

    input:focus {
      background: rgba(255, 255, 255, 0.3);
      box-shadow: 0 0 8px rgba(0,0,0,0.3);
    }

    button[name="login"] {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 25px;
      background: #fff;
      color: #0077b6;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 15px;
      transition: all 0.3s ease;
    }

    button[name="login"]:hover {
      background: #0077b6;
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .box {
      position: relative;
      margin-top: 15px; /* Added spacing */
    }

    .signup {
      /* Removed absolute positioning so it stays inside the box flow */
      color: black; 
      font-weight: bold;
      text-decoration: none;
      display: block;
      text-align: right;
    }
    .signup:hover {
        text-decoration: underline;
    }
    
    </style>
</head>
<body>

<div class="login-box">
    <div class="login-title">Admin Login</div>
    <form method="POST">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required />

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required />

      <button type="submit" name="login">Login</button>
    </form>
    
    <div class="box">
        <a href="admin_register.php" class="signup">Signup &rarr;</a>
    </div>
</div>

<a href="patient_login.php" class="side-btn left-btn">&larr; Patient Login</a>

</body>
</html>