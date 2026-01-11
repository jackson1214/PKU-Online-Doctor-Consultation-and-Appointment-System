<?php
include 'include/connection.php';

class Feedback {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    /**
     * Saves feedback to the database
     * @return bool|string Returns true on success, or error message on failure
     */
    public function submit($name, $rating, $message) {
        $stmt = $this->db->prepare("INSERT INTO feedback (patient_name, rating, message) VALUES (?, ?, ?)");
        // 'sis' stands for string, integer, string
        $stmt->bind_param("sis", $name, $rating, $message);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $error = $this->db->error;
            $stmt->close();
            return $error;
        }
    }
}

$success_msg = "";
if (isset($_POST['submit_feedback'])) {
    $feedbackObj = new Feedback($conn);
    
    $result = $feedbackObj->submit(
        $_POST['patient_name'],
        $_POST['rating'],
        $_POST['message']
    );

    if ($result === true) {
        $success_msg = "Thank you! Your feedback has been sent to PKU.";
    } else {
        $success_msg = "Error: " . $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PKU Patient Feedback</title>
    <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 0;
        }

        header {
            background: #ffffff;
            display: flex; align-items: center; justify-content: space-between;
            padding: 15px 40px; border-bottom: 2px solid #eaeaea;
        }

        .logo { height: 50px; }

        .topbar {
            width: 100%; height: 70px;
            background: linear-gradient(90deg, #007bff, #00b4ff);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .topbar-title {
            color: white; font-size: 24px; font-weight: bold;
        }

        .back-btn {
            text-decoration: none; color: #007bff; font-weight: 600;
            padding: 8px 20px; border: 2px solid #007bff; border-radius: 20px;
            transition: all 0.3s ease;
        }

        .back-btn:hover { background: #007bff; color: white; }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h2 { color: #333; margin-top: 0; text-align: center; }

        label { display: block; margin-top: 15px; font-weight: 600; color: #555; }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            transition: 0.3s;
        }

        button:hover { background-color: #0056b3; }

        .alert {
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <header>
        <img src="photo/download.png" alt="UTHM Logo" class="logo">
        <a href="patient_homepage.php" class="back-btn">← Home</a>
    </header>

    <div class="topbar">
        <div class="topbar-title">Patient Feedback</div>
    </div>

    <div class="container">
        <h2>PKU Feedback Form</h2>

        <?php if ($success_msg): ?>
            <div class="alert"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Your Name (Optional)</label>
            <input type="text" name="patient_name" placeholder="Enter your name or '-'" required>

            <label>Rating</label>
            <select name="rating">
                <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                <option value="4">⭐⭐⭐⭐ (Good)</option>
                <option value="3">⭐⭐⭐ (Average)</option>
                <option value="2">⭐⭐ (Poor)</option>
                <option value="1">⭐ (Very Bad)</option>
            </select>

            <label>Your Feedback</label>
            <textarea name="message" rows="5" placeholder="Tell us about your experience..." required></textarea>

            <button type="submit" name="submit_feedback">Submit Feedback</button>
        </form>
    </div>

</body>
</html>