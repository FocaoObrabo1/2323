<?php

function formatPrice($price) {
    return number_format($price, 2, ',', '.');
}


function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}


function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}


function isLoggedIn() {
    return isset($_SESSION['admin_user_id']) && !empty($_SESSION['admin_user_id']);
}


function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}


function getCartTotal() {
    $total = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['preco'] * $item['quantidade'];
        }
    }
    return $total;
}


function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome 
                           FROM produtos p 
                           JOIN categorias c ON p.categoria_id = c.id 
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getCategoryById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getProductsByCategory($pdo, $categoryId) {
    $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome 
                           FROM produtos p 
                           JOIN categorias c ON p.categoria_id = c.id 
                           WHERE p.categoria_id = ?");
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getAllProducts($pdo) {
    $stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome 
                           FROM produtos p 
                           JOIN categorias c ON p.categoria_id = c.id 
                           ORDER BY p.nome");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getAllCategories($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}