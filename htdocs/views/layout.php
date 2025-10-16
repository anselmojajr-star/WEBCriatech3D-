<?php
// views/layout.php

if (!isset($content)) {
    $content = "<h1>Conteúdo principal</h1>";
}
if (!isset($pageTitle)) {
    $pageTitle = "Dashboard";
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Criatech3D System | <?php echo htmlspecialchars($pageTitle); ?></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="<?= BASE_PATH ?>/dist/css/adminlte.min.css">

    <style>
        #map img {
            max-width: none !important;
        }

        .map-infowindow-content {
            font-size: 0.9rem;
        }

        .map-infowindow-content .form-group {
            margin-bottom: 10px;
        }

        .map-infowindow-content label {
            font-weight: bold;
            font-size: 0.8rem;
            margin-bottom: 2px;
        }

        .map-infowindow-content .btn-remove-marker {
            margin-top: 5px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php include 'partials/navbar.php'; ?>
        <?php include 'partials/sidebar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?php echo htmlspecialchars($pageTitle); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    <?php echo $content; ?>
                </div>
            </div>
        </div>

        <?php include 'partials/footer.php'; ?>

    </div>

    <script src="<?= BASE_PATH ?>/plugins/jquery/jquery.min.js"></script>
    <script src="<?= BASE_PATH ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_PATH ?>/plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="<?= BASE_PATH ?>/dist/js/adminlte.min.js"></script>
    <script src="<?= BASE_PATH ?>/dist/js/app-utils.js"></script>

    <?php if (isset($pageScripts) && is_array($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?= BASE_PATH ?>/<?= htmlspecialchars(ltrim($script, '/')) ?>" defer></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($loadGoogleMaps) && $loadGoogleMaps): ?>
        <script src="https://maps.googleapis.com/maps/api/js?key=xxxxxxxxxxxxxxxx&libraries=geometry,drawing&callback=initMap" defer></script>
    <?php endif; ?>

    <?php
    // =====================================================================
    // ===== BLOCO DE NOTIFICAÇÕES CORRIGIDO E APRIMORADO ==================
    // =====================================================================

    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $message = '';
        $icon = '';
        $isToast = true;

        switch ($status) {
            case 'success':
                $message = 'Operação realizada com sucesso!';
                $icon = 'success';
                break;
            case 'deleted':
                $message = 'Registro excluído com sucesso!';
                $icon = 'success';
                break;
            case 'error':
                $message = !empty($_GET['msg']) ? htmlspecialchars(urldecode($_GET['msg'])) : 'Ocorreu um erro.';
                $icon = 'error';
                $isToast = false;
                break;
            case 'no_permission':
                $message = 'Você não tem permissão para executar esta ação.';
                $icon = 'warning';
                $isToast = false;
                break;
        }

        if ($message) {
            // Converte a variável PHP $message para uma string JavaScript segura
            $jsonMessage = json_encode($message);
            $toastConfig = $isToast ? 'true' : 'false';

            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    toast: {$toastConfig},
                    position: {$toastConfig} ? 'top-end' : 'center',
                    icon: '{$icon}',
                    title: {$jsonMessage}, // <-- AQUI ESTÁ A CORREÇÃO
                    showConfirmButton: !({$toastConfig}),
                    timer: {$toastConfig} ? 3000 : undefined,
                    timerProgressBar: {$toastConfig}
                });
            });
        </script>";
        }
    }
    ?>
</body>

</html>