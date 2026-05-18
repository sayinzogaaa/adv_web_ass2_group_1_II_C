<?php

include "../../config/db.php";

class Admin {

   public function login($username,$password){

      global $conn;

      $sql = "INSERT INTO admins (username, password)
VALUES ('admin', '1234')";
      

      return mysqli_query($conn,$sql);
   }

}

?>