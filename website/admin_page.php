<?php
session_start();
if(!isset($_SESSION['email'])){


    header("location: main.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowie- User Dashboard</title>
    <link rel="stylesheet" href="main.css">

</head>
<body style="background:#fff;">
    <div class="box">
        <h1>Welcome<span><?=$_SESSION['name'];?></span></h1>
        <p>This is an span <span> user</span></p>
        <button onclick="window.location.href='logout.php'">logout<button>
</div>
    
</body>
</html>