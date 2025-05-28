<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';


requireLogin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    

    if (empty($nome)) {
        $errors[] = 'O nome da categoria é obrigatório';
    }
    

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
            $stmt->execute([$nome]);
            
 
            header('Location: index.php?success=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Erro ao cadastrar categoria: ' . $e->getMessage();
        }
    }
}

$pageTitle = "Nova Categoria";
$currentPage = "categorias";
include '../includes/header.php';
?>

<div class="admin-header">
    <h1 class="admin-title">Nova Categoria</h1>
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
    <form action="create.php" method="post">
        <div class="form-group">
            <label for="nome">Nome da Categoria</label>
            <input type="text" id="nome" name="nome" class="form-control" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>