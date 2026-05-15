<?php

session_start();

include "../../config/db.php";

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] === 'POST'){

   // Get and sanitize input
   $username = isset($_POST['username']) ? trim($_POST['username']) : '';
   $password = isset($_POST['password']) ? $_POST['password'] : '';

   // Validate input
   if(empty($username) || empty($password)){
      $_SESSION['error'] = "Username and password are required";
      header("Location: ../../app/view/login.php");
      exit();
   }

   // Use prepared statement to prevent SQL injection
   $sql = "SELECT * FROM admins WHERE username = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("s", $username);
   $stmt->execute();
   $result = $stmt->get_result();

   if($result->num_rows > 0){
      $admin = $result->fetch_assoc();
      
      // Verify password using password_verify
      if(password_verify($password, $admin['password'])){
         $_SESSION['admin'] = $username;
         $_SESSION['admin_id'] = $admin['id'];
         header("Location: ../../app/view/dashboard.php");
         exit();
      } else {
         $_SESSION['error'] = "Invalid credentials";
         header("Location: ../../app/view/login.php");
         exit();
      }
   } else {
      $_SESSION['error'] = "Invalid credentials";
      header("Location: ../../app/view/login.php");
      exit();
   }

   $stmt->close();
}

?>