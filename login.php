<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
   <title>Admin Login</title>
   <style>
      body { font-family: Arial; margin: 50px; }
      .container { max-width: 400px; margin: 0 auto; }
      .error { color: red; margin: 10px 0; padding: 10px; background: #ffe0e0; border: 1px solid red; }
      .success { color: green; margin: 10px 0; padding: 10px; background: #e0ffe0; border: 1px solid green; }
      form { border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
      input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; }
      button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; }
      button:hover { background: #0056b3; }
   </style>
</head>
<body>
   <div class="container">
      <h2>Admin Login</h2>
      
      <?php
      if(isset($_SESSION['error'])){
         echo "<div class='error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
         unset($_SESSION['error']);
      }
      if(isset($_SESSION['success'])){
         echo "<div class='success'>" . htmlspecialchars($_SESSION['success']) . "</div>";
         unset($_SESSION['success']);
      }
      ?>

      <form method="POST" action="../controller/LoginController.php">
         <input type="text" name="username" placeholder="Username" required>
         <input type="password" name="password" placeholder="Password" required>
         <button type="submit">Login</button>
      </form>
   </div>
</body>
</html>