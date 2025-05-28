<?php
session_start();


$_SESSION = array();


if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}


header('Location: login.php');
exit;