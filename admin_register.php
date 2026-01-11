<?php
include "include/connection.php";


class AdminRegistrar {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function registerUser($username, $email, $password) {

        $checkStmt = $this->db->prepare("SELECT id FROM adminusers WHERE username=? OR email=?");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $checkStmt->close();
            return "Username or Email already exists!";
        }
        $checkStmt->close();


        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


        $insertStmt = $this->db->prepare("INSERT INTO adminusers (username, email, password, status) VALUES (?, ?, ?, 'pending')");
        $insertStmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($insertStmt->execute()) {
            $insertStmt->close();
            return "Registration successful! Please wait for admin approval.";
        } else {
            $error = $this->db->error;
            $insertStmt->close();
            return "Registration failed: " . $error;
        }
    }
}


$message = "";

if (isset($_POST['register'])) {

    $registrar = new AdminRegistrar($conn);
    

    $message = $registrar->registerUser(
        $_POST['username'], 
        $_POST['email'], 
        $_POST['password']
    );
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: Arial, sans-serif;
            position: relative;
            overflow: hidden;
            background-color: cornflowerblue;
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
            margin: auto;
            margin-top: 100px; /* Added margin to push it down slightly */
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            color: #000;
            text-align: center;
        }

        .noaccount {
            text-decoration: underline;
            text-align: right;
            font-size: 9px;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] { /* Added email type support */
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
        }

        button[name="register"] {
            padding: 10px 20px;
            border: none;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            width: 100%; /* Made button full width for better UI */
        }
        
        button[name="register"]:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 15px;
            font-weight: bold;
            color: #333;
            background: rgba(255,255,255,0.5);
            padding: 5px;
            border-radius: 5px;
        }

        .box {
            position: relative;
            margin-top: 20px;
            text-align: right;
        }

        .signup {
            color: black;
            text-decoration: none;
            font-weight: bold;
        }
        
        .signup:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

<div class="signup-box">
    <form method="POST">    
        <h2>Admin Register</h2>
        
        <label style="float:left">Username:</label>
        <input type="text" name="username" required>
        
        <label style="float:left">Email:</label>
        <input type="email" name="email" required>
        
        <label style="float:left">Password:</label>
        <input type="password" name="password" required>
        
        <button type="submit" name="register">Register</button>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="box">
            <a href="admin_login.php" class="signup">Login &rarr;</a>
        </div>
    </form>
</div>

</body>
</html>