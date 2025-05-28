<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if user is logged in
requireLogin();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$categoryId = (int)$_GET['id'];

// Get category details
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
$stmt->execute([$categoryId]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

// If category doesn't exist, redirect
if (!$category) {
    header('Location: index.php');
    exit;
}

$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    
    // Validate input
    if (empty($nome)) {
        $errors[] = 'O nome da categoria é obrigatório';
    }
    
    // If no errors, update the database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE categorias SET nome = ? WHERE id = ?");
            $stmt->execute([$nome, $categoryId]);
            
            // Redirect to category list
            header('Location: index.php?success=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Erro ao atualizar categoria: ' . $e->getMessage();
        }
    }
}

$pageTitle = "Editar Categoria";
$currentPage = "categorias";
include '../includes/header.php';
?>

<div class="admin-header">
    <h1 class="admin-title">Editar Categoria</h1>
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
    <form action="edit.php?id=<?= $categoryId ?>" method="post">
        <div class="form-group">
            <label for="nome">Nome da Categoria</label>
            <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($category['nome']) ?>" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>