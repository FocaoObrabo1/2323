<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';


requireLogin();


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$categoryId = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$categoryId]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: index.php');
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produtos WHERE categoria_id = ?");
    $stmt->execute([$categoryId]);
    $productCount = $stmt->fetchColumn();
    
    if ($productCount > 0) {

        header('Location: index.php?error=1');
        exit;
    }
    

    $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
    $stmt->execute([$categoryId]);
    

    header('Location: index.php?success=1');
    exit;
} catch (PDOException $e) {

    header('Location: index.php?error=2');
    exit;
}