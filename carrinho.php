<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';


if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


$cartTotal = 0;
$cartCount = 0;
$cartItems = [];

if (count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $item) {
        $cartTotal += $item['preco'] * $item['quantidade'];
        $cartCount += $item['quantidade'];
        $cartItems[] = $item;
    }
}


$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Carrinho - RoyalKings";
include 'includes/header.php';
?>

<main>
    <div class="container section-padding">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <span>Carrinho</span>
        </div>
        
        <h1 class="section-title">Seu Carrinho</h1>
        
        <?php if (count($cartItems) > 0): ?>
            <div class="cart-container">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $index => $item): ?>
                        <tr>
                            <td>
                                <div class="cart-product">
                                    <div class="cart-product-image">
                                      
                                    </div>
                                    <div class="cart-product-info">
                                        <div class="cart-product-name"><?= htmlspecialchars($item['nome']) ?></div>
                                        <div class="cart-product-category"><?= htmlspecialchars($item['categoria_nome']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>R$ <?= formatPrice($item['preco']) ?></td>
                            <td>
                                <div class="cart-quantity">
                                    <form action="carrinho_update.php" method="post" class="cart-update-form">
                                        <input type="hidden" name="index" value="<?= $index ?>">
                                        <button type="button" class="cart-quantity-btn" data-action="decrease">-</button>
                                        <input type="number" name="quantidade" value="<?= $item['quantidade'] ?>" min="1" class="cart-quantity-input">
                                        <button type="button" class="cart-quantity-btn" data-action="increase">+</button>
                                        <button type="submit" class="btn btn-outline btn-sm" style="margin-left: 8px;">Atualizar</button>
                                    </form>
                                </div>
                            </td>
                            <td>R$ <?= formatPrice($item['preco'] * $item['quantidade']) ?></td>
                            <td>
                                <form action="carrinho_remove.php" method="post">
                                    <input type="hidden" name="index" value="<?= $index ?>">
                                    <button type="submit" class="cart-remove">Remover</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-summary">
                    <h2>Resumo do Pedido</h2>
                    <div class="cart-total">
                        <span>Total:</span>
                        <span>R$ <?= formatPrice($cartTotal) ?></span>
                    </div>
                    <div class="cart-actions">
                        <a href="produtos.php" class="btn btn-outline">Continuar Comprando</a>
                        <a href="finalizar.php" class="btn btn-primary">Finalizar Compra</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center mt-4">
                <p>Seu carrinho está vazio.</p>
                <div class="mt-4">
                    <a href="produtos.php" class="btn btn-primary">Continuar Comprando</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>