<?php

include "../../config/db.php";

class Request {

   public function getRequests(){

      global $conn;

      $sql = "SELECT * FROM requests";

      return mysqli_query($conn, $sql);
   }

}

?>