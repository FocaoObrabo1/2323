<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';


requireLogin();



$stmt = $pdo->query("SELECT COUNT(*) FROM categorias");
$categoryCount = $stmt->fetchColumn();


$stmt = $pdo->query("SELECT COUNT(*) FROM produtos");
$productCount = $stmt->fetchColumn();


$stmt = $pdo->query("SELECT COUNT(*) FROM vendas");
$saleCount = $stmt->fetchColumn();


$stmt = $pdo->query("SELECT v.*, COUNT(vi.id) as item_count, SUM(p.preco * vi.quantidade) as total 
                    FROM vendas v 
                    JOIN vendas_itens vi ON v.id = vi.venda_id 
                    JOIN produtos p ON vi.produto_id = p.id 
                    GROUP BY v.id 
                    ORDER BY v.data_venda DESC 
                    LIMIT 5");
$recentSales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Dashboard";
$currentPage = "dashboard";
include 'includes/header.php';
?>

<div class="admin-header">
    <h1 class="admin-title">Dashboard</h1>
</div>

<div class="admin-dashboard">
    <div class="dashboard-card">
        <div class="dashboard-card-title">Total de Categorias</div>
        <div class="dashboard-card-value"><?= $categoryCount ?></div>
    </div>
    <div class="dashboard-card">
        <div class="dashboard-card-title">Total de Produtos</div>
        <div class="dashboard-card-value"><?= $productCount ?></div>
    </div>
    <div class="dashboard-card">
        <div class="dashboard-card-title">Total de Vendas</div>
        <div class="dashboard-card-value"><?= $saleCount ?></div>
    </div>
</div>

<div class="admin-card">
    <h2>Vendas Recentes</h2>
    
    <?php if (count($recentSales) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Itens</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentSales as $sale): ?>
                <tr>
                    <td>#<?= $sale['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($sale['data_venda'])) ?></td>
                    <td><?= $sale['item_count'] ?></td>
                    <td>R$ <?= formatPrice($sale['total']) ?></td>
                    <td>
                        <div class="admin-actions">
                            <a href="vendas/view.php?id=<?= $sale['id'] ?>" class="btn btn-outline btn-sm">Ver Detalhes</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhuma venda registrada.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>