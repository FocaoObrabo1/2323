<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in
requireLogin();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$saleId = (int)$_GET['id'];

// Get sale details
$stmt = $pdo->prepare("SELECT * FROM vendas WHERE id = ?");
$stmt->execute([$saleId]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);

// If sale doesn't exist, redirect
if (!$sale) {
    header('Location: index.php');
    exit;
}

// Get sale items
$stmt = $pdo->prepare("SELECT vi.*, p.nome as produto_nome, p.preco, c.nome as categoria_nome 
                       FROM vendas_itens vi 
                       JOIN produtos p ON vi.produto_id = p.id 
                       JOIN categorias c ON p.categoria_id = c.id 
                       WHERE vi.venda_id = ?");
$stmt->execute([$saleId]);
$saleItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = 0;
foreach ($saleItems as $item) {
    $total += $item['preco'] * $item['quantidade'];
}

$pageTitle = "Detalhes da Venda #" . $saleId;
$currentPage = "vendas";
include '../includes/header.php';
?>

<div class="admin-header">
    <h1 class="admin-title">Detalhes da Venda #<?= $saleId ?></h1>
    <a href="index.php" class="btn btn-outline">Voltar</a>
</div>

<div class="admin-card">
    <div class="sale-details">
        <div class="sale-info">
            <p><strong>ID da Venda:</strong> #<?= $saleId ?></p>
            <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($sale['data_venda'])) ?></p>
        </div>
        
        <h2>Itens do Pedido</h2>
        
        <?php if (count($saleItems) > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Preço Unitário</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($saleItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['produto_nome']) ?></td>
                        <td><?= htmlspecialchars($item['categoria_nome']) ?></td>
                        <td>R$ <?= formatPrice($item['preco']) ?></td>
                        <td><?= $item['quantidade'] ?></td>
                        <td>R$ <?= formatPrice($item['preco'] * $item['quantidade']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total:</strong></td>
                        <td><strong>R$ <?= formatPrice($total) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <p>Nenhum item encontrado para esta venda.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>