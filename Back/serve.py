from flask import Flask, request, jsonify
from pymongo import MongoClient
from flask_cors import CORS
from users import users_bp, create_routes
from folders import carpetas_bp, create_routescarpetas
from files import files_bp,create_routesfiles
import hashlib

app = Flask(__name__)
CORS(app)

# Configuración de la conexión a MongoDB
uri = 'mongodb://pr2mia2024:1234@localhost:27018/proyecto2Mia?authSource=admin'
client = MongoClient(uri)
db = client['proyecto2Mia']
usuarios_collection = db['users']
carpetas_collection = db["folders"]
archivos_collection = db["files"]

# Crear rutas de usuarios y pasar la colección
create_routes(usuarios_collection)

# Registrar el blueprint de usuarios
app.register_blueprint(users_bp, url_prefix='/usuarios')
app.register_blueprint(create_routescarpetas(carpetas_collection, archivos_collection), url_prefix='/carpetas')
app.register_blueprint(create_routesfiles(archivos_collection),url_prefix='/archivos')
def serialize_user(user):
    """Convierte un documento de usuario a un formato JSON serializable."""
    if user is not None:
        user['_id'] = str(user['_id'])  # Convertir ObjectId a string
    return user

@app.route('/connect', methods=['POST'])
def connect():
    data = request.json
    username = data.get('username')
    password = data.get('password')
    password_encriptada = hashlib.md5(password.encode()).hexdigest()

    try:
        # Buscar el usuario en la colección 'usuarios'
        usuario = usuarios_collection.find_one({'username': username, 'password': password_encriptada})
        
        if usuario:
            return jsonify(serialize_user(usuario)), 200  # Serializar antes de devolver
        else:
            return 'Usuario no encontrado', 404
    except Exception as e:
        return f'Error al conectar a MongoDB: {str(e)}', 500

@app.route('/disconnect', methods=['POST'])
def disconnect():
    try:
        client.close()  # Cerrar la conexión con MongoDB
        return 'Conexión a MongoDB cerrada', 200
    except Exception as e:
        return f'Error al cerrar la conexión: {str(e)}', 500

# Maneja el cierre de la conexión al detener el servidor
import signal
import sys

def close_all_connections(signal, frame):
    client.close()  # Cerrar la conexión con MongoDB
    print('\nConexión a MongoDB cerrada')
    sys.exit(0)

signal.signal(signal.SIGINT, close_all_connections)

if __name__ == '__main__':
    app.run(port=3500, debug=True)
