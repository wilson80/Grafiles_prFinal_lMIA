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
         <div class="d-flex align-items-center mb-2">
             <button class="btn btn-secondary ms-2 mr-3" onclick="window.history.back();">
                 <i class="bi bi-arrow-left"></i>
             </button>
             <strong><?= $ruta ?></strong>
         </div>

         <div class="d-flex flex-wrap">
             <?php foreach ($archivosC as $archivo): ?>
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




     <!-- Menú contextual personalizado de los archivos-->
     <div id="contextMenuFile" class="custom-context-menu">
         <a href="#" onclick="moverPapeleraArchivo(); return false;">Eliminar</a>
         <a href="#" onclick="mostrarPropiedades(); return false;">Propiedades</a>
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

     <script>
         const contextMenu = document.getElementById('contextMenu');
         const contextMenuFile = document.getElementById('contextMenuFile');
         let selectedItem = null;
         let nameFolder = 'null';
         let fechamod = null;
         const archivos = <?php echo json_encode($archivosC); ?>;

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
                 contextMenuFile.style.display = 'block';
                 contextMenuFile.style.left = `${event.pageX}px`;
                 contextMenuFile.style.top = `${event.pageY}px`;
             }

         }

         // Oculta el menú contextual al hacer clic en cualquier parte
         document.addEventListener('click', function(event) {
             // Solo oculta el menú si se hace clic fuera de él
             if (!contextMenu.contains(event.target)) {
                 contextMenuFile.style.display = 'none';

             }
         });


         function moverPapeleraArchivo() {
             if (confirm("Desea mover a la papelera este archivo: " + nameFolder)) {
                 window.location.href = `http://localhost/grafiles_mia/?c=admin&a=EliminarArchivosDelSistema&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idC=<?= $idCarpeta ?>&idA=${encodeURIComponent(selectedItem)}&ruta=<?= $ruta ?>`;
             }

             contextMenu.style.display = 'none'; // Oculta el menú después de la acción
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
             contextMenuFile.style.display = 'none';
         }



         function showFileModal(id) {
             const fileContent = document.getElementById('fileContentMost');
             fileContent.innerHTML = ''; // Limpia el contenido del modal

             // Buscar el archivo por ID
             const archivo = archivos.find(archivo => archivo._id === id);
             console.log(archivo);
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
     </script>

     <!-- Incluye Bootstrap JavaScript para el funcionamiento adecuado de los componentes -->
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

 </body>

 </html>