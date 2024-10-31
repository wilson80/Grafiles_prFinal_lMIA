<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<div class="container">
    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Usuarios</h2>
            <!-- Botón de Agregar Usuario -->
            <a href="http://localhost/grafiles_mia/?c=admin&a=NuevoUsuario&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Agregar Usuario
            </a>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Username</th>
                    <th>Rol</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 1; // Inicializa el contador
                foreach ($this->usuarios as $usuario): ?>
                    <tr>
                        <td><?= $index++ ?></td> <!-- Imprimir el índice y luego incrementar -->
                        <td><?= $usuario['nombre'] ?></td>
                        <td><?= $usuario['username'] ?></td>
                        <td><?= $usuario['rol'] ?></td>
                        <td>
                            <!-- Botón para editar -->
                            <div class="text-center">
                                <a href="http://localhost/grafiles_mia/?c=admin&a=NuevoUsuario&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>&idU=<?= $usuario['_id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>