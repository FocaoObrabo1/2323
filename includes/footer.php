<footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Royal Kings</h3>
                    <p>Sua loja online completa com os melhores produtos pelos melhores preços.</p>
                
      
                    <ul>
                        <?php 
                       
                        if (!isset($categories)) {
                            require_once 'config/database.php';
                            $stmt = $pdo->prepare("SELECT * FROM categorias ORDER BY nome LIMIT 5");
                            $stmt->execute();
                            $footerCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } else {
                            $footerCategories = array_slice($categories, 0, 5);
                        }
                        
                        foreach ($footerCategories as $category): 
                        ?>
                            <li><a href="categoria.php?id=<?= $category['id'] ?>"><?= htmlspecialchars($category['nome']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Projeto em PHP, HTML e CSS</h3>
                    <p>Nomes: Gabriel Paiva Souza Lima Benez, David de Souza Zentil </p>
                    <p>Ra: Gabriel: 010624069, David: 020124007</p>
                  
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> RoyalKings. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>