<?php
session_start();
require 'config/db.php';

$errors=array();
$username="";
$email="";

//if user click sign up button
 if(isset($_POST['sign_up'])){
    $username=$_POST['username'];
    $email=$_POST['email'];
    $password=$_POST['password1'];
    $passwordcnf=$_POST['password2'];

    // validatioin
    if(empty($username)){
        $errors['username']="Username required";
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['email']='Email address is invalid';
    }

    if(empty($email)){
        $errors['email']="Email required";
    }
    if(empty($password)){
        $errors['password1']="Password required";
    }
    if($password !==$passwordcnf){
        $errors['password']="The two password do not match ";
    }


    $emailquery ="SELECT *from signup WHERE email=? LIMIT 1 ";
    $stmt =$conn->prepare($emailquery);
    $stmt->bind_param('s',$email);
    $stmt-> execute();
    $result= $stmt->get_result();
    $userCount = $result->num_rows;

    if($userCount>0){
        $errors['email']="Email is already exists";
    }

    if(count($errors)===0){
        $password =password_hash($password,PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(50));
        $verified =false;

        $sql= " INSERT INTO signup (username,email,verified,token, password) VALUES (?,?,?,?,?)";
        $stmt =$conn->prepare($sql);
        $stmt->bind_param('ssbss',$username,$email,$verified,$token,$password);

        if($stmt->execute()){
            //login user
            $user_id=$conn->insert_id;
            $_SESSION['id']=$user_id;
            $_SESSION['username']=$username;
            $_SESSION['email']=$email;
            $_SESSION['verified']=$verified;

            // set flash message
            $_SESSION['message']="you are now logged in";
            $_SESSION['alert_class']="alert-success";

            header('location:index.php');
            exit();


        }
        else{
            $errors['db_error']="database error:failed to register";
        }
    }

 }

//  login page
if(isset($_POST['log_in'])){
    $username=$_POST['username'];
    $password=$_POST['password'];

    // validatioin
    if(empty($username)){
        $errors['username']="Username required";
    }
 
    if(empty($password)){
        $errors['password']="Password required";
    }

    if (count($errors) ===0) {
        # code...
        $sql="SELECT *from signup WHERE email=? Or username=? limit 1";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('ss',$username,$username);
        $stmt->execute();
        $result= $stmt->get_result();
        $user=$result->fetch_assoc(); 

    if(password_verify($password,$user['password'])){
        // login success
        
            $_SESSION['id']=$user['id'];
            $_SESSION['username']=$user['username'];
            $_SESSION['email']=$user['email'];
            $_SESSION['verified']=$user['verified'];

            // set flash message
            $_SESSION['message']="you are now logged in";
            $_SESSION['alert=class']="alert-success";

            header('location:index.php');
            exit();
    }
    else{
        $errors['login_fail']="wrong credentials";
    }
    }
}

// logout
if(isset($_GET['logout'])){
    session_destroy();
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['email']);
    unset($_SESSION['verified']);
    header('location: login.php');
    exit();
}