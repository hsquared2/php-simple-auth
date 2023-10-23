<?php

session_start();

include_once('init.php');

$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;

if($token !== null) {
  unsetAuth();
  deleteSession($token);
  
  redirect('index.php');
}

redirect("index.php");
