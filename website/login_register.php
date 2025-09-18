<?php

session_start();
require_once 'config.php';

if(isset($_POST['register'])){
    $name =$_POST['name'];
    $email =$_POST['email'];
    $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
    $role=$_POST['role'];


    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0){
        $_SESSION['register_error'] = 'Email is already registered';
        $_SESSION['active_form'] = 'register';

    }else{
        $conn->query("INSERT INTO users(name,email,password,role) VALUES ('$name','$email','$password','$role')");
    }
    header("location: main.php");
    exit();
}
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password']; // REMOVE password_hash() here!

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0){
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])){ // Compare plain text with hash
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email']; // Fixed: should be email, not password
            $_SESSION['role'] = $user['role']; // Store role in session

            if($user['role'] === 'admin'){ // Changed from 'user_type' to 'role'
                header("location: admindash.php");
            } else {
                header("location: userdash.php");
            }
            exit();
        }
    }
    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("location: main.php");
    exit();
}
?>