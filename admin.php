<?php

include "../../config/db.php";

class Admin {

   public function login($username,$password){

      global $conn;

      $sql = "SELECT * FROM admins
      WHERE username='$username'
      AND password='$password'";

      return mysqli_query($conn,$sql);
   }

}

?>