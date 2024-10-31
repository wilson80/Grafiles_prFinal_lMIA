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
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Agregar</button>

        <div class="d-flex align-items-center mb-2">
            <button class="btn btn-secondary ms-2 mr-3" onclick="window.history.back();">
                <i class="bi bi-arrow-left"></i>
            </button>
            <strong><?= $ruta ?></strong>
        </div>


        <div class="d-flex flex-wrap">
            <?php foreach ($carpetas as $carpeta): ?>
                <div class="text-center mx-3 my-2 position-relative"
                    oncontextmenu="onRightClick(event, '<?php echo $carpeta['_id']; ?>','<?php echo $carpeta['nombre']; ?>',1,'<?php echo $carpeta['fechamod']; ?>'); return false;">
                    <a href="http://localhost/grafiles_mia/?c=admin&a=CarpetasSub&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idC=<?php echo $carpeta['_id']; ?>&ruta=<?= $ruta . "/" . $carpeta['nombre']; ?>" class="btn btn-link position-relative">
                        <i class="bi bi-folder-fill" style="font-size: 4rem; color: #007bff;"></i>
                    </a>
                    <div><?php echo htmlspecialchars($carpeta['nombre']); ?></div> <!-- Nombre de la carpeta debajo del ícono -->
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- Menú contextual personalizado -->
    <div id="contextMenu" class="custom-context-menu">
        <a href="#" onclick="renombrarCarpeta(); return false;">Renombrar</a>
        <a href="#" onclick="moverAPapelera(); return false;">Mover a la papelera</a>
        <a href="#" onclick="mostrarPropiedades(); return false;">Propiedades</a>
    </div>

    <!-- Modal para agregar carpeta o archivo -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Agregar Carpeta o Archivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <button class="btn btn-info" onclick="showFolderForm()">Carpeta</button>
                    <button class="btn btn-info" onclick="showFileForm()">Archivo</button>

                    <!-- Formulario para agregar carpeta -->
                    <div id="folderForm" style="display:none; margin-top: 20px;">
                        <h6>Crear Carpeta</h6>
                        <form id="addFolderForm" method="POST" action="http://localhost/grafiles_mia/?c=admin&a=GuardarCarpeta&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idC=<?= $idCarpeta ?>">
                            <div class="mb-3">
                                <label for="folderName" class="form-label">Nombre de la Carpeta</label>
                                <input type="text" class="form-control" id="folderName" name="folderName" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Carpeta</button>
                        </form>
                    </div>

                    <!-- Formulario para agregar archivo -->
                    <div id="fileForm" style="display:none; margin-top: 20px;">
                        <h6>Crear Archivo</h6>
                        <form id="addFileForm">
                            <div class="mb-3">
                                <label for="fileName" class="form-label">Nombre del Archivo</label>
                                <input type="text" class="form-control" id="fileName" required>
                            </div>
                            <div class="mb-3">
                                <label for="fileExtension" class="form-label">Extensión</label>
                                <select class="form-select" id="fileExtension" required>
                                    <option value=".txt">.txt</option>
                                    <option value=".html">.html</option>
                                    <option value=".png">.png</option>
                                    <option value=".jpg">.jpg</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="fileContent" class="form-label">Contenido del Archivo</label>
                                <textarea class="form-control" id="fileContent" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar Archivo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar propiedades de la carpeta -->
    <div class="modal fade" id="propertiesModal" tabindex="-1" aria-labelledby="propertiesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="propertiesModalLabel">Propiedades de la Carpeta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nombre:</strong> <span id="nombreCarpeta"></span></p>
                    <p><strong>Ruta:</strong> <span id="folderPath"></span></p>
                    <p><strong>Fecha de Modificación:</strong> <span id="folderModificationDate"></span></p>
                    <p><strong>Dueño:</strong> <span id="folderOwner"></span></p>
                </div>
            </div>
        </div>
    </div>


    <script>
        const contextMenu = document.getElementById('contextMenu');
        let selectedItem = null;
        let nameFolder = 'null';
        let fechamod = null;

        // Función para manejar el clic derecho (menú contextual)
        function onRightClick(event, itemId, itemName, itemType, fechamodC) {
            event.preventDefault(); // Previene el menú contextual predeterminado

            // Guardar el nombre del elemento seleccionado solo si no es null
            selectedItem = itemId || selectedItem;
            nameFolder = itemName || nameFolder;
            fechamod = fechamodC || fechamod;
            // Posiciona el menú contextual cerca del cursor
            contextMenu.style.display = 'block';
            contextMenu.style.left = `${event.pageX}px`;
            contextMenu.style.top = `${event.pageY}px`;
        }

        // Oculta el menú contextual al hacer clic en cualquier parte
        document.addEventListener('click', function(event) {
            // Solo oculta el menú si se hace clic fuera de él
            if (!contextMenu.contains(event.target)) {
                contextMenu.style.display = 'none';
            }
        });

        // Funciones para cada acción del menú contextual
        function renombrarCarpeta() {
            const renombre = prompt('Ingrese un nuevo nombre para la carpeta:', nameFolder);

            if (renombre && renombre.trim() !== "") {
                // Redirige a la URL con el nuevo nombre de la carpeta como parámetro
                window.location.href = `http://localhost/grafiles_mia/?c=admin&a=RenombrarCarpeta&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idCM=<?= $idCarpeta ?>&rename=${encodeURIComponent(renombre)}&idC=${encodeURIComponent(selectedItem)}&ruta=<?= $ruta ?>`;
            } else {
                alert('Por favor, ingrese un nombre válido para la carpeta.');
            }
            contextMenu.style.display = 'none'; // Oculta el menú después de la acción
        }


        function moverAPapelera() {
            if (confirm("Desea mover a la papelera esta carpeta: " + nameFolder)) {
                window.location.href = `http://localhost/grafiles_mia/?c=admin&a=MoverPapelera&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idCM=<?= $idCarpeta ?>&idC=${encodeURIComponent(selectedItem)}&ruta=<?= $ruta ?>`;

            }

            contextMenu.style.display = 'none'; // Oculta el menú después de la acción
        }

        function mostrarPropiedades() {
            // Suponiendo que tienes una forma de obtener la información de la carpeta
            const folderInfo = {
                name: nameFolder, // Este es el nombre de la carpeta que has guardado
                path: `<?= $ruta ?>/${nameFolder}`, // Aquí puedes construir la ruta de forma dinámica
                modificationDate: fechamod, // Aquí puedes reemplazarlo con la fecha real
                owner: 'Usuario Actual' // Aquí puedes reemplazarlo con el dueño real
            };

            // Rellenar el modal con la información
            document.getElementById('nombreCarpeta').innerText = folderInfo.name;
            document.getElementById('folderPath').innerText = folderInfo.path;
            document.getElementById('folderModificationDate').innerText = folderInfo.modificationDate;
            document.getElementById('folderOwner').innerText = folderInfo.owner;

            // Mostrar el modal
            const propertiesModal = new bootstrap.Modal(document.getElementById('propertiesModal'));
            propertiesModal.show();

            // Oculta el menú contextual
            contextMenu.style.display = 'none';
        }



        // Mostrar el formulario para agregar carpeta
        function showFolderForm() {
            document.getElementById('folderForm').style.display = 'block';
            document.getElementById('fileForm').style.display = 'none';
        }

        // Mostrar el formulario para agregar archivo
        function showFileForm() {
            document.getElementById('fileForm').style.display = 'block';
            document.getElementById('folderForm').style.display = 'none';
        }



        // Manejar el envío del formulario de archivo
        document.getElementById('addFileForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const fileName = document.getElementById('fileName').value;
            const fileExtension = document.getElementById('fileExtension').value;
            const fileContent = document.getElementById('fileContent').value;
            // Aquí puedes agregar la lógica para enviar los detalles del archivo al servidor
            alert('Archivo creado: ' + fileName + fileExtension);
            document.getElementById('addFileForm').reset();
            $('#addModal').modal('hide'); // Cierra el modal
        });
    </script>

    <!-- Incluye Bootstrap JavaScript para el funcionamiento adecuado de los componentes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>