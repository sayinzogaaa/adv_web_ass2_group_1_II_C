<?php
/**
 * Setup script - Run this once to initialize the database
 * Visit http://localhost/umuganda_project/setup.php in your browser
 */

include "config/db.php";

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}

// Read and execute schema.sql
$schema = file_get_contents("database/schema.sql");
$queries = array_filter(array_map('trim', explode(';', $schema)));

$success = true;
$messages = [];

foreach($queries as $query){
    if(!empty($query)){
        if(mysqli_query($conn, $query)){
            $messages[] = "✓ Query executed successfully";
        } else {
            $messages[] = "✗ Error: " . mysqli_error($conn);
            $success = false;
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Setup - Umuganda Platform</title>
    <style>
        body { font-family: Arial; margin: 50px; }
        .container { max-width: 600px; margin: 0 auto; }
        .success { color: green; padding: 15px; background: #e0ffe0; border: 1px solid green; border-radius: 5px; }
        .error { color: red; padding: 15px; background: #ffe0e0; border: 1px solid red; border-radius: 5px; }
        .message { margin: 10px 0; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Setup Complete</h2>
        <?php if($success): ?>
            <div class="success">
                <h3>✓ Database initialized successfully!</h3>
                <p>Default Admin Credentials:</p>
                <ul>
                    <li><strong>Username:</strong> admin</li>
                    <li><strong>Password:</strong> admin123</li>
                </ul>
                <p><strong>Important:</strong> Please change the default password after first login.</p>
                <p><a href="public/index.php">Go to Home Page</a></p>
            </div>
        <?php else: ?>
            <div class="error">
                <h3>✗ Setup failed</h3>
                <p>Check the errors below:</p>
                <?php foreach($messages as $msg): ?>
                    <div class="message"><?php echo htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
