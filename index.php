<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
   <title>Umuganda Platform</title>
   <style>
      * { margin: 0; padding: 0; box-sizing: border-box; }
      body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
      header { background: #007bff; color: white; padding: 20px; }
      nav { margin-top: 20px; }
      nav a { color: white; margin-right: 20px; text-decoration: none; font-weight: bold; }
      nav a:hover { text-decoration: underline; }
      .hero { text-align: center; padding: 60px 20px; background: #f8f9fa; }
      .hero h2 { font-size: 32px; margin-bottom: 10px; }
      .hero p { font-size: 18px; margin-bottom: 30px; }
      .btn { display: inline-block; padding: 12px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
      .btn:hover { background: #218838; }
   </style>
</head>
<body>
   <header>
      <div style="max-width: 1200px; margin: 0 auto;">
         <h1>Umuganda Smart Service Request Platform</h1>
         <nav>
            <a href="index.php">Home</a>
            <a href="../app/view/request_form.php">
Submit Request
</a>
            <?php if(isset($_SESSION['admin'])): ?>
               <a href="../app/view/dashboard.php">Dashboard</a>
               <a href="../app/view/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['admin']); ?>)</a>
            <?php else: ?>
               <a href="../app/view/login.php">Admin Login</a>
            <?php endif; ?>
         </nav>
      </div>
   </header>

   <section class="hero">
      <div style="max-width: 1200px; margin: 0 auto;">
         <h2>Report Community Service Issues Easily</h2>
         <p>Track and manage local service requests online.</p>
         <a href="../app/view/request_form.php" class="btn">Submit Request</a>
      </div>
   </section>
</body>
</html>