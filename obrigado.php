<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

 
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$orderId = (int)$_GET['id'];

 
$stmt = $pdo->prepare("SELECT * FROM vendas WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: index.php');
    exit;
}

 
$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Pedido Confirmado - Loja Virtual";
include 'includes/header.php';
?>

<main>
    <div class="container section-padding">
        <div class="thank-you">
            <div class="thank-you-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <h1>Pedido Confirmado!</h1>
            <p>Obrigado por sua compra. Seu pedido #<?= $orderId ?> foi recebido e está sendo processado.</p>
            <div>
                <a href="index.php" class="btn btn-primary">Voltar para a Loja</a>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>