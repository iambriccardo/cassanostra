<?php
require_once '../access/loginUtils.php';

session_start();

checkAccessAndRedirect(NULL, "../access/login.php");
?>