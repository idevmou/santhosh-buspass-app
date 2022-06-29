<?php
// session_start();
// require 'config/db.php';
$errors1=array();
$username="";

if(isset($_POST['payment_btn'])){
    $userid=$_POST['userid'];
    $username=$_POST['user'];
    $passmonth=$_POST['month'];
    $route=$_POST['route'];
    $holdername=$_POST['holdername'];
    $accountno=$_POST['acnum'];
    $cvv=$_POST['cvv'];
    $expirymonth=$_POST['expiry'];
    $amount=$_POST['amount'];
    
    if(empty($username)){
        $errors1['user']="Username required";
    }
    if(empty($passmonth)){
        $errors1['month']="pass month required";
    }
    if(empty($route)){
        $errors1['route']="route required";
    }
    if(empty($holdername)){
        $errors1['holdername']="card holder name required";
    }
    if(empty($accountno)){
        $errors1['acnum']="account required";
    }
    if(empty($cvv)){
        $errors1['cvv']="cvv required";
    }
    if(empty($expirymonth)){
        $errors1['expiry']="expiry date required";
    }
    // if(empty($amount)){
    //     $errors1['amount']="amount required";
    // }
    // $idref= $_SESSION['id'];
    // $routequery ="SELECT *from payment WHERE route=?   LIMIT 1 ";
    // $stmt1 =$conn->prepare($routequery);
    // $stmt1->bind_param('s',$route);
    // $stmt1-> execute();
    // $result1= $stmt1->get_result();
    // $userCount1 = $result1->num_rows;

    // if($userCount1>0){
    //     $errors1['route']="you are already get pass for this route";
    // }

    if(count($errors1)===0){
        $amountpaid=TRUE;
        $cvv=bin2hex(random_bytes(50));

        $sql1= " INSERT INTO payment (userid,name,month,route, holder_name,account_number,cvv,card_expiry,amount,amountpaid) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $stmt =$conn->prepare($sql1);
        $stmt->bind_param('issssiisii',$userid,$username,$passmonth,$route,$holdername,$accountno,$cvv,$expirymonth,$amount,$amountpaid);

    
    if($stmt->execute()){
        $user_id=$conn->insert_id;
        $_SESSION['pay_id']=$user_id;
        $_SESSION['userid']=$userid;
        $_SESSION['name']=$username;
        $_SESSION['month']=$passmonth;
        $_SESSION['route']=$route;
        $_SESSION['holder_name']=$holdername;
        $_SESSION['account_number']=$accountno;
        $_SESSION['cvv']=$cvv;
        $_SESSION['card_expiry']=$expirymonth;
        $_SESSION['amount']=$amount;

        header('location:newpass.php');
        exit();

        
    }
    else {
        $errors1['db_error']="database error:payment registration failed";
    }
    }
}