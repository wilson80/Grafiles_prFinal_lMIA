<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Enlace al CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS personalizado para estilo Facebook -->
    <link rel="stylesheet" href="assets/css/login.css">
    <script src="assets/js/controlBtnAtras.js"></script>
</head>

<body>

    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Iniciar Sesión</h3>
                        <form action="?c=login&a=Loguearse" method="POST" class="needs-validation" novalidate>
                            <!-- Campo de ID de Usuario -->
                            <div class="mb-3">
                                <label for="userId" class="form-label">ID de Usuario</label>
                                <input type="text" class="form-control" id="userId" name="userId" placeholder="Ingrese su ID de Usuario" required>
                                <div class="invalid-feedback">
                                    El ID de usuario es requerido.
                                </div>
                            </div>

                            <!-- Campo de Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" required>
                                <div class="invalid-feedback">
                                    La contraseña es requerida.
                                </div>
                            </div>

                            <!-- Botón de Iniciar Sesión -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-facebook btn-lg">Iniciar Sesión</button>
                            </div>

                            <!-- Enlace de contraseña olvidada -->
                            <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                        </form>

                        <!-- Enlace de registrarse -->
                        <div class="signup-link">
                            <p>¿No tienes una cuenta? <a href="vista/registrarse/registrarse.php">Regístrate</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlace al JavaScript de Bootstrap y validación de formulario -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación de formulario de Bootstrap
        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>

</html>