<?php

session_start();

include "../../config/db.php";

if(isset($_GET['search'])){

   $search = $_GET['search'];

   $sql = "SELECT * FROM requests
   WHERE category LIKE '%$search%'";

}else{

   $sql = "SELECT * FROM requests";
}

$result = mysqli_query($conn,$sql);

?>

<h2>Admin Dashboard</h2>
<form method="GET">

<input type="text"
name="search"
placeholder="Search Category">

<button type="submit">
Search
</button>

</form>
<table border="1" cellpadding="10">

<tr>
   <th>ID</th>
   <th>Name</th>
   <th>Category</th>
   <th>Priority</th>
   <th>Status</th>
   <th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['fullname']; ?></td>

<td><?php echo $row['category']; ?></td>

<td><?php echo $row['priority']; ?></td>

<td><?php echo $row['status']; ?></td>

<td>

<a href="../controller/UpdateController.php?id=<?php echo $row['id']; ?>&status=In Progress">

In Progress

</a>

|

<a href="../controller/UpdateController.php?id=<?php echo $row['id']; ?>&status=Resolved">

Resolved

</a>

</td>

</tr>

<?php } ?>

</table>