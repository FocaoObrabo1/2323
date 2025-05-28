<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


if (!isset($_POST['produto_id']) || empty($_POST['produto_id']) || 
    !isset($_POST['quantidade']) || empty($_POST['quantidade'])) {
    header('Location: index.php');
    exit;
}

$productId = (int)$_POST['produto_id'];
$quantity = (int)$_POST['quantidade'];


if ($quantity <= 0) {
    $quantity = 1;
}


$product = getProductById($pdo, $productId);


if (!$product) {
    header('Location: index.php');
    exit;
}


if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


$productExists = false;
foreach ($_SESSION['cart'] as $key => $item) {
    if ($item['id'] == $productId) {
        // Update quantity
        $_SESSION['cart'][$key]['quantidade'] += $quantity;
        $productExists = true;
        break;
    }
}


if (!$productExists) {
    $_SESSION['cart'][] = [
        'id' => $product['id'],
        'nome' => $product['nome'],
        'preco' => $product['preco'],
        'quantidade' => $quantity,
        'categoria_nome' => $product['categoria_nome']
    ];
}


header('Location: carrinho.php');
exit;