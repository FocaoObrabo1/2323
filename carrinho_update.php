<?php
session_start();


if (!isset($_POST['index']) || !isset($_POST['quantidade'])) {
    header('Location: carrinho.php');
    exit;
}

$index = (int)$_POST['index'];
$quantity = (int)$_POST['quantidade'];


if ($quantity <= 0) {
    $quantity = 1;
}


if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && isset($_SESSION['cart'][$index])) {

    $_SESSION['cart'][$index]['quantidade'] = $quantity;
}

header('Location: carrinho.php');
exit;