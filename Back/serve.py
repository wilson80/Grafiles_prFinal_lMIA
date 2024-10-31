from flask import Blueprint, request, jsonify
from pymongo import MongoClient
from bson import ObjectId
import hashlib
import requests
from folders import carpetas_bp  # Asegúrate de que la ruta sea correcta

# Crear un blueprint para el manejo de usuarios
users_bp = Blueprint('users', __name__)

def serialize_user(user):
    """Convierte un documento de usuario a un formato JSON serializable."""
    if user is not None:
        user['_id'] = str(user['_id'])  # Convertir ObjectId a string
    return user

def create_routes(usuarios_collection):
    # AGREGAR NUEVO USUARIO
    @users_bp.route('/', methods=['POST'])
    def create_user():
        data = request.json
        username = data.get('username')
        nombre = data.get('nombre')
        password = data.get('password')
        rol = data.get('rol')

        if not username or not nombre or not password or not rol:
            return 'Todos los campos son obligatorios', 400

        password_encriptada = hashlib.md5(password.encode()).hexdigest()

        try:
            usuario_ant = usuarios_collection.find_one({'username': username})
            if usuario_ant:
                return 'No se permiten usuarios con el mismo username', 400

            result = usuarios_collection.insert_one({
                'username': username,
                'nombre': nombre,
                'password': password_encriptada,
                'rol': rol
            })
            nuevo_usuario = usuarios_collection.find_one({'_id': result.inserted_id})
            data1 ={'nombre':'raiz', 'idU':str(result.inserted_id),'ficheroMadre':'0000000000'}
            data2 ={'nombre':'compartida', 'idU':str(result.inserted_id),'ficheroMadre':'1111111111'}

            carpeta1 = requests.post('http://localhost:3500/carpetas', json=data1)  # Ajusta la URL según tu configuración
            carpeta2 = requests.post('http://localhost:3500/carpetas', json=data2)  # Ajusta la URL según tu configuración
            if carpeta1.status_code != 201:
                return f'Error al crear la carpeta "raiz": {carpeta1.text}', carpeta1.status_code

            # Manejar la respuesta de la segunda solicitud
            if carpeta2.status_code != 201:
                return f'Error al crear la carpeta "compartida": {carpeta2.text}', carpeta2.status_code


            return jsonify(serialize_user(nuevo_usuario)), 201
        except Exception as e:
            return f'Error al insertar el usuario: {str(e)}', 500

    # OBTENER TODOS LOS USUARIOS
    @users_bp.route('/', methods=['GET'])
    def get_users():
        try:
            usuarios = usuarios_collection.find()
            return jsonify([serialize_user(user) for user in usuarios]), 200
        except Exception as e:
            return f'Error al obtener los usuarios: {str(e)}', 500

    # OBTENER UN USUARIO POR ID
    @users_bp.route('/<string:user_id>', methods=['GET'])
    def get_user(user_id):
        print(user_id)
        try:
            usuario = usuarios_collection.find_one({'_id': ObjectId(user_id)})
            if not usuario:
                return 'Usuario no encontrado', 404
            return jsonify(serialize_user(usuario)), 200
        except Exception as e:
            return f'Error al obtener el usuario: {str(e)}', 500

    # ACTUALIZAR UN USUARIO
    @users_bp.route('/<string:user_id>', methods=['PUT'])
    def update_user(user_id):
        data = request.json
        username = data.get('username')
        nombre = data.get('nombre')
        password = data.get('password')
        rol = data.get('rol')

        if not username or not nombre or not password or not rol:
            return 'Todos los campos son obligatorios', 400

        password_encriptada = hashlib.md5(password.encode()).hexdigest()

        try:
            result = usuarios_collection.update_one(
                {'_id': ObjectId(user_id)},
                {'$set': {'username': username, 'nombre': nombre, 'password': password_encriptada, 'rol': rol}}
            )

            if result.matched_count == 0:
                return 'Usuario no encontrado', 404

            return jsonify({'_id': user_id}), 200
        except Exception as e:
            return f'Error al actualizar el usuario: {str(e)}', 500

    # ELIMINAR UN USUARIO
    @users_bp.route('/<string:user_id>', methods=['DELETE'])
    def delete_user(user_id):
        try:
            result = usuarios_collection.delete_one({'_id': ObjectId(user_id)})
            if result.deleted_count == 0:
                return 'Usuario no encontrado', 404
            return 'Usuario eliminado correctamente', 200
        except Exception as e:
            return f'Error al eliminar el usuario: {str(e)}', 500

    # RUTA PARA ACTUALIZAR LA CONTRASEÑA
    @users_bp.route('/update-password/<string:user_id>', methods=['PUT'])
    def update_password(user_id):
        data = request.json
        old_password = data.get('oldPassword')
        new_password = data.get('newPassword')

        if not old_password or not new_password:
            return 'Ambos campos son obligatorios', 400

        old_password_encriptada = hashlib.md5(old_password.encode()).hexdigest()
        new_password_encriptada = hashlib.md5(new_password.encode()).hexdigest()

        try:
            result = usuarios_collection.update_one(
                {
                    '_id': ObjectId(user_id),
                    'password': old_password_encriptada  # Verifica la contraseña anterior
                },
                {
                    '$set': {'password': new_password_encriptada}  # Actualiza la nueva contraseña
                }
            )

            if result.matched_count == 0:
                return 'Contraseña anterior incorrecta o usuario no encontrado', 401

            return jsonify({'new_password': new_password}), 200
        except Exception as e:
            return f'Error al actualizar la contraseña: {str(e)}', 500

