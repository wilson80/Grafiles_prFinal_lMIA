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
            <?php foreach ($archivos as $archivo): ?>
                <div class="text-center mx-3 my-2 position-relative"
                    oncontextmenu="onRightClick(event, '<?php echo $archivo['_id']; ?>','<?php echo $archivo['nombre']; ?>',2,'<?php echo $archivo['fechamod']; ?>'); return false;">
                    <a href="#" onclick="showFileModal('<?php echo $archivo['_id']; ?>')"
                        class="btn btn-link position-relative">
                        <?php if ($archivo['extension'] === '.jpg' || $archivo['extension'] === '.png'): ?>
                            <i class="bi bi-file-image" style="font-size: 4rem; color: #28a745;"></i>
                        <?php elseif ($archivo['extension'] === '.txt'): ?>
                            <i class="bi bi-file-earmark-text" style="font-size: 4rem; color: #ffc107;"></i>
                        <?php elseif ($archivo['extension'] === '.html'): ?>
                            <i class="bi bi-filetype-html" style="font-size: 4rem; color: #17a2b8;"></i>
                        <?php else: ?>
                            <i class="bi bi-file-earmark" style="font-size: 4rem; color: #6c757d;"></i>
                        <?php endif; ?>
                    </a>
                    <div><?php echo htmlspecialchars($archivo['nombre']);  ?><?php echo htmlspecialchars($archivo['extension']);  ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal para mostrar el contenido de un archivo-->
    <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Contenido del Archivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="cerrarModales()"></button>
                </div>
                <div class=" modal-body" id="fileContentMost">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="cerrarModales()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Menú contextual personalizado -->
    <div id="contextMenu" class="custom-context-menu">
        <a href="#" onclick="renombrarCarpeta(); return false;">Renombrar</a>
        <a href="#" onclick="moverAPapelera(); return false;">Mover a la papelera</a>
        <a href="#" onclick="mostrarPropiedades(); return false;">Propiedades</a>
    </div>

    <!-- Menú contextual personalizado de los archivos-->
    <div id="contextMenuFile" class="custom-context-menu">
        <a href="#" onclick="editarArchivo(); return false;">Editar</a>
        <a href="#" onclick="moverPapeleraArchivo(); return false;">Mover a la papelera</a>
        <a href="#" onclick="compartirArchivo(); return false;">Compartir</a>
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
                        <form id="addFolderForm" method="POST" action="http://localhost/grafiles_mia/?c=admin&a=GuardarCarpeta&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idC=<?= $idCarpeta ?>&ruta=<?= $ruta ?>">
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
                        <form id="addFileForm" method="POST" action="http://localhost/grafiles_mia/?c=admin&a=GuardarArchivo&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idC=<?= $idCarpeta ?>&ruta=<?= $ruta ?>">
                            <div class="mb-3">
                                <label for="fileName" class="form-label">Nombre del Archivo</label>
                                <input type="text" class="form-control" id="fileName" name="fileName" required>
                            </div>
                            <div class="mb-3">
                                <label for="fileExtension" class="form-label">Extensión</label>
                                <select class="form-select" id="fileExtension" name="fileExtension" required>
                                    <option value=".txt">.txt</option>
                                    <option value=".html">.html</option>
                                    <option value=".png">.png</option>
                                    <option value=".jpg">.jpg</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="fileContent" class="form-label">Contenido del Archivo</label>
                                <textarea class="form-control" id="fileContent" name="fileContent" rows="5" required></textarea>
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
                    <h5 class="modal-title" id="propertiesModalLabel">Propiedades</h5>
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


    <!-- Modal para editar archivo -->
    <div class="modal fade" id="editFileModal" tabindex="-1" aria-labelledby="editFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFileModalLabel">Editar Archivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editFileForm" method="POST" action="http://localhost/grafiles_mia/?c=admin&a=EditarArchivo&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idC=<?= $idCarpeta ?>&ruta=<?= $ruta ?>">
                        <input type="hidden" class="form-control" id="idEditFile" name="idEditFile">
                        <div class="mb-3">
                            <label for="editFileName" class="form-label">Nombre del Archivo</label>
                            <input type="text" class="form-control" id="editFileName" name="editFileName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editFileExtension" class="form-label">Extensión</label>
                            <select class="form-select" id="editFileExtension" name="editFileExtension" required>
                                <option value=".txt">.txt</option>
                                <option value=".html">.html</option>
                                <option value=".png">.png</option>
                                <option value=".jpg">.jpg</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editFileContent" class="form-label">Contenido del Archivo</label>
                            <textarea class="form-control" id="editFileContent" name="editFileContent" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para compartir archivo con usuarios -->
    <div class="modal fade" id="modalCompartirArchivos" tabindex="-1" aria-labelledby="modalCompartirArchivosLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCompartirArchivosLabel">Compartir Archivo con Usuarios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Username</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="shareFile('<?php echo $usuario['_id']; ?>')">Compartir</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script>
        const contextMenu = document.getElementById('contextMenu');
        const contextMenuFile = document.getElementById('contextMenuFile');
        const menuMostrarArchivo = document.getElementById('fileModal');
        let selectedItem = null;
        let nameFolder = 'null';
        let fechamod = null;
        const archivos = <?php echo json_encode($archivos); ?>;

        // Función para manejar el clic derecho (menú contextual)
        function onRightClick(event, itemId, itemName, itemType, fechamodC) {
            event.preventDefault(); // Previene el menú contextual predeterminado

            // Guardar el nombre del elemento seleccionado solo si no es null
            selectedItem = itemId || selectedItem;
            nameFolder = itemName || nameFolder;
            fechamod = fechamodC || fechamod;
            // Posiciona el menú contextual cerca del cursor

            if (itemType === 1) {
                contextMenuFile.style.display = 'none';
                contextMenu.style.display = 'block';
                contextMenu.style.left = `${event.pageX}px`;
                contextMenu.style.top = `${event.pageY}px`;
            } else if (itemType === 2) {
                contextMenu.style.display = 'none';
                contextMenuFile.style.display = 'block';
                contextMenuFile.style.left = `${event.pageX}px`;
                contextMenuFile.style.top = `${event.pageY}px`;
            }

        }

        // Oculta el menú contextual al hacer clic en cualquier parte
        document.addEventListener('click', function(event) {
            // Solo oculta el menú si se hace clic fuera de él
            if (!contextMenu.contains(event.target)) {
                contextMenu.style.display = 'none';
                contextMenuFile.style.display = 'none';

            }
        });

        function editarArchivo() {
            contextMenuFile.style.display = 'none';
            // Obtener el archivo seleccionado desde el arreglo archivos usando selectedItem
            const archivo = archivos.find(archivo => archivo._id === selectedItem);
            if (archivo) {
                // Rellenar el modal de edición con los datos del archivo seleccionado
                document.getElementById('idEditFile').value = selectedItem;
                document.getElementById('editFileName').value = archivo.nombre;
                document.getElementById('editFileExtension').value = archivo.extension;
                document.getElementById('editFileContent').value = archivo.contenido;

                // Mostrar el modal de edición
                const editFileModal = new bootstrap.Modal(document.getElementById('editFileModal'));
                editFileModal.show();
            } else {
                alert("Archivo no encontrado.");
            }
        }

        function moverPapeleraArchivo() {
            if (confirm("Desea mover a la papelera este archivo: " + nameFolder)) {
                window.location.href = `http://localhost/grafiles_mia/?c=admin&a=MoverPapeleraArchivo&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idCM=<?= $idCarpeta ?>&idA=${encodeURIComponent(selectedItem)}&ruta=<?= $ruta ?>`;
            }

            contextMenu.style.display = 'none'; // Oculta el menú después de la acción
        }

        function compartirArchivo() {
            contextMenuFile.style.display = 'none';
            const archivo = archivos.find(archivo => archivo._id === selectedItem);
            if (archivo) {
                $('#modalCompartirArchivos').modal('show');
            }
        }

        function shareFile(idUser) {
            window.location.href = `http://localhost/grafiles_mia/?c=admin&a=CompartirArchivo&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idCM=<?= $idCarpeta ?>&idA=${encodeURIComponent(selectedItem)}&ruta=<?= $ruta ?>&idU=${idUser}`;
            //alert("Archivo compartido con " + idUser + " id de archivo: " + selectedItem);
        }

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

            contextMenuFile.style.display = 'none'; // Oculta el menú después de la acción
        }

        function mostrarPropiedades() {
            const archivo = archivos.find(archivo => archivo._id === selectedItem);
            let nameRuta = `<?= $ruta ?>/${nameFolder}`;

            if (archivo) {
                // Agregar la extensión del archivo a la ruta solo si se selecciona un archivo
                nameRuta += `${archivo.extension}`;
            }

            // Información de la carpeta o archivo
            const folderInfo = {
                name: nameFolder, // Nombre de la carpeta
                path: nameRuta, // Ruta construida dinámicamente
                modificationDate: fechamod, // Fecha de modificación (sustituir por la fecha real)
                owner: `<?= $n ?>` // Nombre del propietario (sustituir con el valor real)
            };

            // Rellenar el modal con la información de la carpeta o archivo
            document.getElementById('nombreCarpeta').innerText = folderInfo.name;
            document.getElementById('folderPath').innerText = folderInfo.path;
            document.getElementById('folderModificationDate').innerText = folderInfo.modificationDate;
            document.getElementById('folderOwner').innerText = folderInfo.owner;

            // Mostrar el modal de propiedades
            const propertiesModal = new bootstrap.Modal(document.getElementById('propertiesModal'));
            propertiesModal.show();

            // Ocultar el menú contextual
            contextMenu.style.display = 'none';
            contextMenuFile.style.display = 'none';
        }



        function showFileModal(id) {
            const fileContent = document.getElementById('fileContentMost');
            fileContent.innerHTML = ''; // Limpia el contenido del modal

            // Buscar el archivo por ID
            const archivo = archivos.find(archivo => archivo._id === id);

            if (archivo) {
                const {
                    extension,
                    contenido
                } = archivo; // Asegúrate de que 'contenido' esté presente en tu arreglo.

                if (extension === '.jpg' || extension === '.png') {
                    fileContent.innerHTML = `<img src="${contenido}" alt="Imagen" style="width: 100%;">`;
                } else if (extension === '.txt') {
                    fileContent.innerHTML = `<pre>${contenido}</pre>`;
                } else if (extension === '.html') {
                    // Asegúrate de que el contenido HTML no esté escapado
                    fileContent.innerHTML = contenido; // Ya no es necesario usar <pre> si es HTML
                } else {
                    fileContent.innerHTML = 'Tipo de archivo no soportado para vista previa.';
                }

                new bootstrap.Modal(document.getElementById('fileModal')).show();
            } else {
                fileContent.innerHTML = 'Archivo no encontrado.';
            }
        }

        function cerrarModales() {
            // Cerrar el modal de archivo
            const fileModal = bootstrap.Modal.getInstance(document.getElementById('fileModal'));
            if (fileModal) {
                fileModal.hide();
            }
            // También puedes limpiar el contenido del modal si es necesario
            document.getElementById('fileContentMost').innerHTML = '';
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
    </script>

    <!-- Incluye Bootstrap JavaScript para el funcionamiento adecuado de los componentes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>