<?php

require 'constants.php';

$conn=new mysqli(host,user,password,dbname);

if($conn->connect_error){
    die('Database error:'.$conn->connect_error);
}