<?php
session_start();
include "include/connection.php";

class MedicalRecord {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function save($p_email, $doctor_name, $diagnosis, $treatment) {
        $stmt = $this->db->prepare("INSERT INTO medical_records (patient_email, doctor_name, diagnosis, treatment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $p_email, $doctor_name, $diagnosis, $treatment);
        
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}

$doctor_name = $_SESSION['username'] ?? 'Doctor';
$email_val = $_GET['email'] ?? '';

if (isset($_POST['save_record'])) {
    $recordObj = new MedicalRecord($conn);
    $isSaved = $recordObj->save(
        $_POST['patient_email'], 
        $doctor_name, 
        $_POST['diagnosis'], 
        $_POST['treatment']
    );

    if ($isSaved) {
        echo "<script>alert('Medical Record Saved Successfully!'); window.location.href='doctor_homepage.php';</script>";
    } else {
        echo "<script>alert('Error saving record');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Medical Record</title>
    <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            margin: 0; 
            font-family: 'Poppins', sans-serif; 
            background: #f4f6f9; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }
        .form-box { 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            width: 100%;
            max-width: 500px; 
        }
        h2 { margin-top: 0; color: #333; }
        label { font-weight: 600; font-size: 14px; color: #555; display: block; margin-top: 15px; }
        input, textarea { 
            width: 100%; 
            padding: 12px; 
            margin-top: 5px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-family: inherit;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }
        button { 
            margin-top: 25px; 
            width: 100%; 
            padding: 12px; 
            background: #2563eb; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-weight: bold; 
            cursor: pointer; 
            transition: background 0.3s;
        }
        button:hover { background: #1d4ed8; }
        .back { 
            display: block; 
            text-align: center; 
            margin-top: 15px; 
            text-decoration: none; 
            color: #666; 
            font-size: 14px; 
        }
        .back:hover { color: #333; }
        strong { color: #2563eb; }
    </style>
</head>
<body>

    <div class="form-box">
        <h2>Patient Medical Record</h2>
        <p>Attending: <strong>Dr. <?= htmlspecialchars($doctor_name) ?></strong></p>
        
        <form method="POST">
            <label>Patient Email:</label>
            <input type="email" name="patient_email" value="<?= htmlspecialchars($email_val) ?>" required>

            <label>Diagnosis:</label>
            <input type="text" name="diagnosis" required placeholder="e.g. Influenza, Migraine">

            <label>Treatment / Medicine:</label>
            <textarea name="treatment" rows="4" required placeholder="e.g. Paracetamol 500mg, Rest for 3 days"></textarea>

            <button type="submit" name="save_record">Save Record</button>
        </form>
        
        <a href="doctor_homepage.php" class="back">Cancel & Back</a>
    </div>

</body>
</html>