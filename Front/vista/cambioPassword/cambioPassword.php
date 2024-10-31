<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS-->
    <link rel="stylesheet" type="text/css" href="http://localhost/proyecto_final_ts1/assets/css/main.css">
    <title>Cambio de contraseña</title>
</head>

<body>
    <div>
        <div class="content-wrapper">
            <div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="row">
                            <div class="col-lg-11">
                                <div class="well bs-component">
                                    <form class="form-horizontal" method="POST" action="http://localhost/grafiles_mia/?c=admin&a=CambiarPassword&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>">
                                        <fieldset>
                                            <legend>Ingrese los datos correspondientes</legend>
                                            <div class="form-group">
                                                <input class="form-control" type="hidden"
                                                    name="idU" id="idU" value="<?= $id ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-2 control-label" for="passwordant">Anterior</label>
                                                <div class="col-lg-10">
                                                    <input class="form-control" id="passwordant" name="passwordant" type="password" placeholder="Contraseña anterior" required>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <label class="col-lg-2 control-label" for="password1">Nueva</label>
                                                <div class="col-lg-10">
                                                    <input class="form-control" id="password1" name="password1" type="password" placeholder="Contraseña nueva" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-2 control-label" for="password2">Confirme</label>
                                                <div class="col-lg-10">
                                                    <input class="form-control" id="password2" name="password2" type="password" placeholder="Confirme su contraseña" required>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-lg-15 col-lg-offset-4">
                                                    <button class="btn btn-default" type="button" onclick="window.location.href='http://localhost/grafiles_mia/?c=admin&a=Usuarios&n=<?= $n ?>&rol=<?= $rol ?>&id=<?= $id ?>'">Cancelar</button>
                                                    <button class="btn" id="submitBtn" type="submit" disabled style="background-color: #007bff; color: white;">Actualizar contraseña</button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Javascripts-->
    <script src="http://localhost/proyecto_final_ts1/assets/js/jquery-2.1.4.min.js"></script>
    <script src="http://localhost/proyecto_final_ts1/assets/js/bootstrap.min.js"></script>
    <script src="http://localhost/proyecto_final_ts1/assets/js/plugins/pace.min.js"></script>
    <script src="http://localhost/proyecto_final_ts1/assets/js/main.js"></script>

    <script>
        const password1 = document.getElementById('password1');
        const password2 = document.getElementById('password2');
        const submitBtn = document.getElementById('submitBtn');

        // Verificar si las contraseñas coinciden
        function checkPasswords() {
            if (password1.value === password2.value && password1.value.length > 0) {
                submitBtn.disabled = false; // Habilitar el botón si las contraseñas coinciden
            } else {
                submitBtn.disabled = true; // Deshabilitar el botón si no coinciden
            }
        }

        // Ejecutar la validación cuando se modifiquen las contraseñas
        password1.addEventListener('input', checkPasswords);
        password2.addEventListener('input', checkPasswords);
    </script>
</body>

</html>