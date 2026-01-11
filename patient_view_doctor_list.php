<?php include 'include/connection.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor List</title>

    <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
    
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            background: #eef2f5;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .card {
            background: white;
            width: 300px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-top: 5px solid #007bff;
        }

        .card h3 {
            background: black;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            color: white;
        }

        .spec {
            background: #E8E9EB;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            color:#405182;
        }

        .bio {
            background: #E8E9EB;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            color:#405182;
        }

        .contact {
            background: #E8E9EB;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            color:#405182;
        
        }

        .btn-back {
            display: block;
            width: 150px;
            margin: 20px auto;
            text-align: center;
            padding: 10px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        header {
            background: #ffffff;
            display: flex;
            align-items: center;
            padding: 15px 40px;
            border-bottom: 2px solid #eaeaea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            }

        .logo {
            height: 50px;
        }


        .topbar {
            width: 100%;
            height: 70px;
            background: linear-gradient(90deg, #007bff, #00b4ff);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .topbar-title {
            color: white;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

    </style>
</head>
<body>

  <header>
    <img src="photo/download.png" alt="UTHM Logo" class="logo">
  </header>

    <div class="topbar">
        <div class="topbar-title">Doctors List</div>
    </div><br><br>

<div class="grid">
    <?php
    // Fetch doctors from database
    $sql = "SELECT * FROM doctors ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<div class='card'>";
            echo "<h3>" . htmlspecialchars($row["name"]) . "</h3>";
            echo "<p class='spec'>" . htmlspecialchars($row["specialization"]) . "</p>";
            echo "<div class='contact'> " . htmlspecialchars($row["phone"]) . "</div>";
            echo "<p class='bio'>" . htmlspecialchars($row["bio"]) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No doctors found.</p>";
    }
    ?>
</div>

</body>
</html>