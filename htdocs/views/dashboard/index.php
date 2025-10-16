<?php
// views/dashboard/index.php (Versão Correta e Limpa)

$username = htmlspecialchars($_SESSION['username'] ?? 'Usuário');
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bem-vindo, <?php echo $username; ?>!</h5>
                <p class="card-text">
                    Esta é a sua área de trabalho. O conteúdo completo será implementado aqui.
                </p>
            </div>
        </div>
    </div>
</div>