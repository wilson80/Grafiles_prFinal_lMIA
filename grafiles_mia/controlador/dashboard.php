<?php
// Asegúrate de que el archivo tenga la extensión .php y no sólo HTML

// En dashboard.php, puedes hacer algo como esto:
echo "<script>
        const usuarioJson = localStorage.getItem('usuario'); // Recuperar la cadena JSON
        const usuario = usuarioJson ? JSON.parse(usuarioJson) : null; // Parsear a objeto

        console.log('Usuario recuperado:', usuario);
        
        if (usuario) {
            // Verificar el rol y redirigir según sea necesario
            if (usuario.rol === 'administrador') {
                window.location.href = 'http://localhost/grafiles_mia/?c=admin&a=Inicio&n=' + usuario.nombre + '&rol=' + usuario.rol + '&id=' + usuario._id;  // Redirigir a un dashboard de administrador
            } else if (usuario.rol === 'empleado') {
                window.location.href = 'http://localhost/grafiles_mia/?c=admin&a=Inicio&n=' + usuario.nombre + '&rol=' + usuario.rol + '&id=' + usuario._id;            
            } else {
                window.location.href = 'http://localhost/grafiles_mia'; // Redirigir a un dashboard de usuario normal
            }
        } else {
            // Manejo de error si no hay usuario en localStorage
            console.error('No hay usuario en localStorage.');
            window.location.href = 'http://localhost/grafiles_mia'; // Redirigir a login si no hay usuario
        }
      </script>";

exit();
