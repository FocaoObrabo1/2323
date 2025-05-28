<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in
requireLogin();

// Get all sales with summary
$stmt = $pdo->query("SELECT v.*, 
                     COUNT(vi.id) as item_count, 
                     SUM(p.preco * vi.quantidade) as total 
                     FROM vendas v 
                     JOIN vendas_itens vi ON v.id = vi.venda_id 
                     JOIN produtos p ON vi.produto_id = p.id 
                     GROUP BY v.id 
                     ORDER BY v.data_venda DESC");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Gerenciar Vendas";
$currentPage = "vendas";
include '../includes/header.php';
?>

<div class="admin-header">
    <h1 class="admin-title">Gerenciar Vendas</h1>
</div>

<div class="admin-card">
    <?php if (count($sales) > 0): ?>
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
                <?php foreach ($sales as $sale): ?>
                <tr>
                    <td>#<?= $sale['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($sale['data_venda'])) ?></td>
                    <td><?= $sale['item_count'] ?></td>
                    <td>R$ <?= formatPrice($sale['total']) ?></td>
                    <td>
                        <div class="admin-actions">
                            <a href="view.php?id=<?= $sale['id'] ?>" class="btn btn-outline btn-sm">Ver Detalhes</a>
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

<?php include '../includes/footer.php'; ?>