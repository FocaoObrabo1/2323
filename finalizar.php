<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

 
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header('Location: carrinho.php');
    exit;
}


$cartTotal = 0;
$cartItems = [];

foreach ($_SESSION['cart'] as $item) {
    $cartTotal += $item['preco'] * $item['quantidade'];
    $cartItems[] = $item;
}

 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $endereco = isset($_POST['endereco']) ? trim($_POST['endereco']) : '';
    
    $errors = [];
    
    if (empty($nome)) {
        $errors[] = 'Nome é obrigatório';
    }
    
    if (empty($email) || !isValidEmail($email)) {
        $errors[] = 'Email inválido';
    }
    
    if (empty($telefone)) {
        $errors[] = 'Telefone é obrigatório';
    }
    
    if (empty($endereco)) {
        $errors[] = 'Endereço é obrigatório';
    }
    
 
    if (empty($errors)) {
        try {
    
            $pdo->beginTransaction();
            
        
            $stmt = $pdo->prepare("INSERT INTO vendas (data_venda) VALUES (NOW())");
            $stmt->execute();
            
            
            $vendaId = $pdo->lastInsertId();
            
             
            foreach ($cartItems as $item) {
                $stmt = $pdo->prepare("INSERT INTO vendas_itens (venda_id, produto_id, quantidade) VALUES (?, ?, ?)");
                $stmt->execute([$vendaId, $item['id'], $item['quantidade']]);
            }
            
             
            $pdo->commit();
            
             
            $_SESSION['cart'] = [];
            
            
            header('Location: obrigado.php?id=' . $vendaId);
            exit;
        } catch (Exception $e) {
           
            $pdo->rollBack();
            $errors[] = 'Erro ao processar pedido: ' . $e->getMessage();
        }
    }
}

 
$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['quantidade'];
}

$pageTitle = "Finalizar Compra - Loja Virtual";
include 'includes/header.php';
?>

<main>
    <div class="container section-padding">
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <a href="carrinho.php">Carrinho</a> &gt; 
            <span>Finalizar Compra</span>
        </div>
        
        <h1 class="section-title">Finalizar Compra</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="checkout-grid">
            <div class="checkout-form">
                <h2>Informações de Entrega</h2>
                <form action="finalizar.php" method="post">
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" class="form-control" required>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="endereco">Endereço Completo</label>
                        <textarea id="endereco" name="endereco" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <h2 class="mt-4">Informações de Pagamento</h2>
                    
                    <div class="form-group">
                        <label for="cartao">Número do Cartão</label>
                        <input type="text" id="cartao" name="cartao" class="form-control" required>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="validade">Validade</label>
                            <input type="text" id="validade" name="validade" class="form-control" placeholder="MM/AA" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="nome_cartao">Nome no Cartão</label>
                        <input type="text" id="nome_cartao" name="nome_cartao" class="form-control" required>
                    </div>
                    
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Finalizar Pedido</button>
                    </div>
                </form>
            </div>
            
            <div class="checkout-summary">
                <h2>Resumo do Pedido</h2>
                
                <div class="checkout-products">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="checkout-product">
                        <div class="checkout-product-name">
                            <?= htmlspecialchars($item['nome']) ?> x <?= $item['quantidade'] ?>
                        </div>
                        <div class="checkout-product-price">
                            R$ <?= formatPrice($item['preco'] * $item['quantidade']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="checkout-total">
                    <span>Total:</span>
                    <span>R$ <?= formatPrice($cartTotal) ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>