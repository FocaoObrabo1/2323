<?php
session_start();


if (!isset($_POST['index'])) {
    header('Location: carrinho.php');
    exit;
}

$index = (int)$_POST['index'];


if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && isset($_SESSION['cart'][$index])) {

    array_splice($_SESSION['cart'], $index, 1);
}


header('Location: carrinho.php');
exit;