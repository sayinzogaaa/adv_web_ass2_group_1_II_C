<?php

session_start();

include "../../config/db.php";

$username = $_POST['username'];

$password = $_POST['password'];

$sql = "SELECT * FROM admins
WHERE username=? AND password=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ss", $username, $password);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0){

    $_SESSION['admin'] = $username;

    header("Location: ../view/dashboard.php");

} else {

    echo "Invalid credentials";

}

?>