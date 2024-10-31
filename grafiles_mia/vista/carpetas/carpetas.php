<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carpetas</title>
    <!-- Incluye Bootstrap CSS y Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .area-principal {
            padding: 10px;
        }

        .d-flex.flex-wrap {
            display: flex;
            flex-wrap: wrap;
        }

        .mx-3 {
            margin: 10px;
        }

        .my-2 {
            margin-top: 5px;
            margin-bottom: 5px;
        }

        /* Estilos del menú contextual */
        .custom-context-menu {
            display: none;
            position: absolute;
            z-index: 1000;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            border-radius: 5px;
            min-width: 150px;
        }

        .custom-context-menu a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
        }

        .custom-context-menu a:hover {
            background-color: #f1f1f1;
            color: #007bff;
        }
    </style>
</head>

<body>

    <div class="area-principal" oncontextmenu="onRightClick(event, null, 0); return false;">
        <div class="d-flex flex-wrap">
            <?php

            foreach ($carpetas as $carpeta): ?>
                <div class="text-center mx-3 my-2 position-relative">
                    <a href="http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idC=<?= $carpeta['_id']; ?>&ruta=<?= $ruta . "/" . $carpeta['nombre']; ?>" class="btn btn-link position-relative">
                        <i class="bi bi-folder-fill" style="font-size: 4rem; color: #007bff;"></i>
                    </a>
                    <div><?php echo htmlspecialchars($carpeta['nombre']); ?></div> <!-- Nombre de la carpeta debajo del ícono -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>





    <!-- Incluye Bootstrap JavaScript para el funcionamiento adecuado de los componentes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>