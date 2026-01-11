<?php
session_start();
include 'include/connection.php';


class FeedbackManager {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function getAllFeedback() {
        $sql = "SELECT * FROM feedback ORDER BY date_submitted DESC";
        // OOP style query execution
        $result = $this->db->query($sql);
        return $result;
    }
}

$feedbackManager = new FeedbackManager($conn);
$result = $feedbackManager->getAllFeedback();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Patient Feedback</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #007bff; padding-bottom: 10px; color: #333; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        
        .star { color: gold; font-size: 1.2em; }
        .empty-star { color: #ccc; }
        .no-data { text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>

<div class="container">
    <h2>📢 Patient Feedback List</h2>
    
    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="20%">Patient Name</th>
                <th width="15%">Rating</th>
                <th width="40%">Feedback Message</th>
                <th width="20%">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // OOP: Check the num_rows property directly
            if ($result && $result->num_rows > 0) {
                
                // OOP: Use fetch_assoc() method
                while ($row = $result->fetch_assoc()) {
                    // Logic to display stars visually
                    $stars = str_repeat("★", $row['rating']);
                    $empty_stars = str_repeat("☆", 5 - $row['rating']);
                    ?>
                    
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['patient_name']) ?></td>
                        <td>
                            <span class='star'><?= $stars ?></span>
                            <span class='empty-star'><?= $empty_stars ?></span> 
                            (<?= $row['rating'] ?>/5)
                        </td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= date("d M Y, h:i A", strtotime($row['date_submitted'])) ?></td>
                    </tr>

                <?php 
                } 
            } else { 
            ?>
                <tr><td colspan='5' class='no-data'>No feedback submitted yet.</td></tr>
            <?php 
            } 
            ?>
        </tbody>
    </table>
    
    <br>
    <a href="admin_homepage.php" style="text-decoration: none; color: blue;">&larr; Back to Dashboard</a>
</div>

</body>
</html>