    </main>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Local Tourism Spot Directory System. Built with PHP & MySQL.</p>
        </div>
    </footer>
    <script src="<?= (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) ? '../' : '' ?>assets/js/script.js"></script>
</body>
</html>
