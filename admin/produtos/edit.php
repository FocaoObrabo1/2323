<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
requireLogin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$productId = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? 0));
    $categoria_id = (int)($_POST['categoria_id'] ?? 0);
    $novaImagem = $product['caminho_imagem'];


    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $nomeOriginal = $_FILES['imagem']['name'];
        $tempPath = $_FILES['imagem']['tmp_name'];
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

        if (in_array($extensao, $extensoesPermitidas)) {
            $novaImagem = uniqid() . '.' . $extensao;
            $destino = __DIR__ . '/../../uploads/' . $novaImagem;
            move_uploaded_file($tempPath, $destino);

            if (!empty($product['caminho_imagem'])) {
                @unlink(__DIR__ . '/../../uploads/' . $product['caminho_imagem']);
            }
        } else {
            $errors[] = 'Extensão de imagem inválida.';
        }
    }

    if (empty($nome)) $errors[] = 'O nome do produto é obrigatório';
    if (!is_numeric($preco) || $preco <= 0) $errors[] = 'O preço deve ser válido';
    if ($categoria_id <= 0) $errors[] = 'Selecione uma categoria válida';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, categoria_id = ?, caminho_imagem = ? WHERE id = ?");
            $stmt->execute([$nome, $preco, $categoria_id, $novaImagem, $productId]);

            header('Location: index.php?success=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Erro ao atualizar produto: ' . $e->getMessage();
        }
    }
}
?>


<?php include '../includes/header.php'; ?>
<div class="admin-header">
    <h1 class="admin-title">Editar Produto</h1>
    <a href="index.php" class="btn btn-outline">Voltar</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?><li><?= $error ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form action="edit.php?id=<?= $productId ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Nome do Produto</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($product['nome']) ?>" required>
        </div>

        <div class="form-group">
            <label for="preco">Preço (R$)</label>
            <input type="text" id="preco" name="preco" class="form-control" value="<?= htmlspecialchars($product['preco']) ?>" required>
        </div>

        <div class="form-group">
            <label for="imagem">Imagem Atual</label><br>
            <?php if ($product['caminho_imagem']): ?>
                <img src="/uploads/<?= htmlspecialchars($product['caminho_imagem']) ?>" width="100"><br><br>
            <?php endif; ?>
            <input type="file" name="imagem" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
        </div>

        <div class="form-group">
            <label for="categoria_id">Categoria</label>
            <select id="categoria_id" name="categoria_id" class="form-control" required>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= $product['categoria_id'] == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
    </form>
</div>
<?php include '../includes/footer.php'; ?>
