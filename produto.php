<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

 
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$productId = (int)$_GET['id'];

 
$product = getProductById($pdo, $productId);

 
if (!$product) {
    header('Location: index.php');
    exit;
}

 
$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart count
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantidade'];
    }
}

$pageTitle = htmlspecialchars($product['nome']) . " - Loja Virtual";
include 'includes/header.php';
?>

<main>
    <div class="container section-padding">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <a href="categoria.php?id=<?= $product['categoria_id'] ?>"><?= htmlspecialchars($product['categoria_nome']) ?></a> &gt; 
            <span><?= htmlspecialchars($product['nome']) ?></span>
        </div>
        
        <div class="product-detail">
            <div class="product-detail-image">
               <?php if (!empty($product['caminho_imagem'])): ?>
    <img src="/2323-master/uploads/<?= htmlspecialchars($product['caminho_imagem']) ?>" alt="<?= htmlspecialchars($product['nome']) ?>">
<?php else: ?>
    <img src="assets/images/products/placeholder.jpg" alt="Sem imagem">
<?php endif; ?>

            </div>
            <div class="product-detail-info">
                <h1><?= htmlspecialchars($product['nome']) ?></h1>
                <div class="product-detail-category">Categoria: <?= htmlspecialchars($product['categoria_nome']) ?></div>
                <div class="product-detail-price">R$ <?= formatPrice($product['preco']) ?></div>
                <div class="product-detail-description">
                    <p></p>
                </div>
                
                <form action="carrinho_add.php" method="post" class="product-detail-form">
                    <input type="hidden" name="produto_id" value="<?= $product['id'] ?>">
                    <div class="form-group">
                        <label for="quantidade">Quantidade:</label>
                        <input type="number" id="quantidade" name="quantidade" value="1" min="1" class="quantity-input">
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>