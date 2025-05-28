<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';


requireLogin();


$stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $preco = isset($_POST['preco']) ? str_replace(',', '.', trim($_POST['preco'])) : 0; // Handle both comma and dot as decimal separator
    $categoria_id = isset($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : 0;

    $nomeImagem = null;

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $nomeOriginal = $_FILES['imagem']['name'];
    $tempPath = $_FILES['imagem']['tmp_name'];
    $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

    if (in_array($extensao, $extensoesPermitidas)) {
        $nomeImagem = uniqid() . '.' . $extensao;
        $destino = __DIR__ . '/../../uploads/' . $nomeImagem;
        move_uploaded_file($tempPath, $destino);
    } else {
        $errors[] = 'Extensão de imagem inválida.';
    }
}


    

    if (empty($nome)) {
        $errors[] = 'O nome do produto é obrigatório';
    }
    
    if (!is_numeric($preco) || $preco <= 0) {
        $errors[] = 'O preço deve ser um número válido maior que zero';
    }
    
    if ($categoria_id <= 0) {
        $errors[] = 'Selecione uma categoria válida';
    }
    

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, categoria_id, caminho_imagem) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $preco, $categoria_id, $nomeImagem]);

            
            // Redirect to product list
            header('Location: index.php?success=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Erro ao cadastrar produto: ' . $e->getMessage();
        }
    }
}

$pageTitle = "Novo Produto";
$currentPage = "produtos";
include '../includes/header.php';
?>

<div class="admin-header">
    <h1 class="admin-title">Novo Produto</h1>
    <a href="index.php" class="btn btn-outline">Voltar</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form action="create.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Nome do Produto</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?= isset($nome) ? htmlspecialchars($nome) : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label for="preco">Preço (R$)</label>
            <input type="text" id="preco" name="preco" class="form-control" value="<?= isset($preco) ? htmlspecialchars($preco) : '' ?>" required>
        </div>

        <div class="form-group">
        <label for="imagem">Imagem do Produto</label>
        <input type="file" id="imagem" name="imagem" class="form-control" accept=".jpg, .jpeg, .png, .gif" required>
        </div>
        
        <div class="form-group">
            <label for="categoria_id">Categoria</label>
            <select id="categoria_id" name="categoria_id" class="form-control" required>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= isset($categoria_id) && $categoria_id == $category['id'] ? 'selected' : '' ?>>
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



 