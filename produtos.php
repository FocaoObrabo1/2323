<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';  


$products = getAllProducts($pdo);

$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantidade'];
    }
}

$pageTitle = "Todos os Produtos - Loja Virtual";
include 'includes/header.php';
?>

<main>
    <div class="container section-padding">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <span>Produtos</span>
        </div>
        
        <h1 class="section-title">Todos os Produtos</h1>
        
        <?php if (count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($product['caminho_imagem'])): ?>
                            <img src="/2323-master/uploads/<?= htmlspecialchars($product['caminho_imagem']) ?>" alt="<?= htmlspecialchars($product['nome']) ?>">
                        <?php else: ?>
                            <img src="assets/images/products/placeholder.jpg" alt="<?= htmlspecialchars($product['nome']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?= htmlspecialchars($product['nome']) ?></h3>
                        <div class="product-category"><?= htmlspecialchars($product['categoria_nome']) ?></div>
                        <div class="product-price">R$ <?= formatPrice($product['preco']) ?></div>
                        <div class="product-actions">
                            <a href="produto.php?id=<?= $product['id'] ?>" class="btn btn-outline">Ver Detalhes</a>
                            <form action="carrinho_add.php" method="post">
                                <input type="hidden" name="produto_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="quantidade" value="1">
                                <button type="submit" class="btn btn-primary">Adicionar</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center mt-4">
                <p>Nenhum produto encontrado.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
