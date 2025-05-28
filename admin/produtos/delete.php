<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';


requireLogin();


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$productId = (int)$_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM vendas_itens WHERE produto_id = ?");
    $stmt->execute([$productId]);
    $saleCount = $stmt->fetchColumn();
    
    if ($saleCount > 0) {

        header('Location: index.php?error=1');
        exit;
    }
    

    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$productId]);
    

    header('Location: index.php?success=1');
    exit;
} catch (PDOException $e) {

    header('Location: index.php?error=2');
    exit;
}