<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

 
$stmt = $pdo->prepare("SELECT p.*, c.nome as categoria_nome 
                      FROM produtos p 
                      LEFT JOIN categorias c ON p.categoria_id = c.id 
                      ORDER BY p.id DESC LIMIT 6");
$stmt->execute();
$featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantidade'];
    }
}

$pageTitle = "Home - RoyalKings";
include 'includes/header.php';
?>

<main>
    

<section class="carousel-section section-padding">
  <div class="container">
    <h2 class="section-title">Destaques</h2>

    <div class="carousel-css-only">

      <input type="radio" name="carousel" id="slide1" checked>
      <input type="radio" name="carousel" id="slide2">
      <input type="radio" name="carousel" id="slide3">

      <div class="carousel-slides">
        <div class="carousel-slide s1">
          <img src="assets/images/products/produto1.jpg" alt="Produto 1">
        </div>
        <div class="carousel-slide s2">
          <img src="assets/images/products/produto2.jpg" alt="Produto 2">
        </div>
        <div class="carousel-slide s3">
          <img src="assets/images/products/produto3.jpg" alt="Produto 3">
        </div>
      </div>

      <div class="carousel-navigation">
        <label for="slide1" class="nav-dot"></label>
        <label for="slide2" class="nav-dot"></label>
        <label for="slide3" class="nav-dot"></label>
      </div>

    </div>
  </div>
</section>

  
    <section class="hero-banner">
        <div class="container">
            <div class="hero-content">
                <h1>Bem-vindo à Nossa Loja</h1>
                <p>Encontre os melhores produtos pelos melhores preços</p>
                <a href="produtos.php" class="btn btn-primary">Ver Todos Produtos</a>
            </div>
        </div>
    </section>

    

 
    <section class="categories section-padding">
        <div class="container">
            <h2 class="section-title">Categorias</h2>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                <a href="categoria.php?id=<?= $category['id'] ?>" class="category-card">
                    <div class="category-name"><?= htmlspecialchars($category['nome']) ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

 
    <section class="products section-padding">
        <div class="container">
            <h2 class="section-title">Produtos em Destaque</h2>
            <div class="products-grid">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($product['caminho_imagem'])): ?>
    <img src="/2323-master/uploads/<?= htmlspecialchars($product['caminho_imagem']) ?>" alt="<?= htmlspecialchars($product['nome']) ?>">
<?php else: ?>
    <img src="assets/images/products/placeholder.jpg" alt="Sem imagem">
<?php endif; ?>

                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?= htmlspecialchars($product['nome']) ?></h3>
                        <div class="product-category"><?= htmlspecialchars($product['categoria_nome'] ?? 'Sem categoria') ?></div>
                        <div class="product-price">R$ <?= number_format($product['preco'], 2, ',', '.') ?></div>
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
            <div class="text-center mt-4">
                <a href="produtos.php" class="btn btn-outline-primary">Ver Todos Produtos</a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>