<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';


requireLogin();


$stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome 
                       FROM produtos p 
                       JOIN categorias c ON p.categoria_id = c.id 
                       ORDER BY p.nome");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Gerenciar Produtos";
$currentPage = "produtos";
include '../includes/header.php';
?>

<div class="admin-header">
    <h1 class="admin-title">Gerenciar Produtos</h1>
    <a href="create.php" class="btn btn-primary">Novo Produto</a>
</div>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success">
        Operação realizada com sucesso!
    </div>
<?php endif; ?>

<div class="admin-card">
    <?php if (count($products) > 0): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Categoria</th>
                    <th>Ações</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['nome']) ?></td>
                    <td>R$ <?= formatPrice($product['preco']) ?></td>
                    <td><?= htmlspecialchars($product['categoria_nome']) ?></td>
                    
                    <td>
                        <div class="admin-actions">
                            <a href="edit.php?id=<?= $product['id'] ?>" class="btn-icon btn-edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <a href="delete.php?id=<?= $product['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum produto cadastrado.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>