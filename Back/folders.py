from flask import Blueprint, request, jsonify
from pymongo import MongoClient
from bson import ObjectId
import datetime

# Crear un blueprint para el manejo de carpetas
carpetas_bp = Blueprint('carpetas', __name__)
def serialize_carpeta(carpeta):
    """Convierte un documento de carpeta a un formato JSON serializable."""
    if carpeta is not None:
        carpeta['_id'] = str(carpeta['_id'])  # Convertir ObjectId a string
    return carpeta

def create_routescarpetas(collection, collection_archivos):
    # AGREGAR NUEVA CARPETA CARPETAS RAICES
    @carpetas_bp.route('/', methods=['POST'])
    def create_carpeta():
        data = request.json
        nombre = data.get('nombre')
        idU = data.get('idU')
        ficheroMadre = data.get('ficheroMadre')

        if not nombre or not idU or not ficheroMadre:
            return 'Todos los campos son obligatorios', 400

        try:
            carpeta_ant = collection.find_one({'nombre': nombre, 'id_fichero_madre': ficheroMadre, 'id_usuario':idU})
            if carpeta_ant:
                return 'No se permiten nombres duplicados en un mismo directorio', 400

            result = collection.insert_one({
                'nombre': nombre,
                'fechamod': datetime.datetime.now(),
                'id_usuario': idU,
                'ficheroMadre': ficheroMadre,
                'unica':True
            })
            nueva_carpeta = collection.find_one({'_id': result.inserted_id})
            return jsonify(serialize_carpeta(nueva_carpeta)), 201

        except Exception as e:
            return f'Error al insertar la carpeta: {str(e)}', 500
        
        
    # AGREGAR NUEVA DIFERNETE A LA RAIZ
    @carpetas_bp.route('/newCarpetInCarpet/', methods=['POST'])
    def create_carpetaInCarpet():
        data = request.json
        nombre = data.get('nombre')
        idU = data.get('idU')
        ficheroMadre = data.get('ficheroMadre')

        if not nombre or not idU or not ficheroMadre:
            return 'Todos los campos son obligatorios', 400

        try:
            carpeta_ant = collection.find_one({'nombre': nombre, 'id_fichero_madre': ficheroMadre, 'eliminada': False, 'id_usuario':idU})
            if carpeta_ant:
                return 'No se permiten nombres duplicados en un mismo directorio', 400

            result = collection.insert_one({
                'nombre': nombre,
                'fechamod': datetime.datetime.now(),
                'id_usuario': idU,
                'id_fichero_madre': ficheroMadre,
                'eliminada': False
            })
            nueva_carpeta = collection.find_one({'_id': result.inserted_id})
            return jsonify(serialize_carpeta(nueva_carpeta)), 201

        except Exception as e:
            return f'Error al insertar la carpeta: {str(e)}', 500

    # COPIAR UNA CARPETA A OTRA CARPETA INCLUYENDO SUS ARCHIVOS
    async def copiar_carpeta_recursivamente(idCarpetaOrigen, idCarpetaDestino, idU):
        carpeta_origen = collection.find_one({'_id': ObjectId(idCarpetaOrigen), 'eliminada': False})
        if not carpeta_origen:
            raise Exception('Carpeta de origen no encontrada')

        nuevo_nombre = carpeta_origen['nombre']
        contador = 1
        carpeta_ant = collection.find_one({'nombre': nuevo_nombre, 'id_fichero_madre': idCarpetaDestino, 'eliminada': False})

        while carpeta_ant:
            nuevo_nombre = f"{carpeta_origen['nombre']}_{contador}"
            contador += 1
            carpeta_ant = collection.find_one({'nombre': nuevo_nombre, 'id_fichero_madre': idCarpetaDestino, 'eliminada': False})

        nueva_carpeta_result = collection.insert_one({
            'nombre': nuevo_nombre,
            'fechamod': datetime.datetime.now(),
            'id_usuario': idU,
            'id_fichero_madre': idCarpetaDestino,
            'eliminada': False
        })

        nueva_carpeta_id = str(nueva_carpeta_result.inserted_id)

        archivos = collection_archivos.find({'id_fichero_madre': idCarpetaOrigen, 'eliminado': False})
        nuevas_copias_archivos = [
            {
                'nombre': archivo['nombre'],
                'extension': archivo['extension'],
                'contenido': archivo['contenido'],
                'eliminado': archivo['eliminado'],
                'fechamod': datetime.datetime.now(),
                'id_fichero_madre': nueva_carpeta_id
            }
            for archivo in archivos
        ]

        if nuevas_copias_archivos:
            collection_archivos.insert_many(nuevas_copias_archivos)

        subcarpetas = collection.find({'id_fichero_madre': idCarpetaOrigen, 'eliminada': False})
        for subcarpeta in subcarpetas:
            await copiar_carpeta_recursivamente(str(subcarpeta['_id']), nueva_carpeta_id, idU)

        return nueva_carpeta_id

    @carpetas_bp.route('/copiar-carpeta/<string:idFM>', methods=['POST'])
    async def copy_carpeta(idFM):
        data = request.json
        nombre = data.get('nombre')
        idU = data.get('idU')
        ficheroMadre = data.get('ficheroMadre')

        if not nombre or not idU or not ficheroMadre:
            return 'Todos los campos son obligatorios', 400

        try:
            nueva_carpeta_id = await copiar_carpeta_recursivamente(ficheroMadre, idFM, idU)
            nueva_carpeta = collection.find_one({'_id': ObjectId(nueva_carpeta_id)})
            return jsonify(nueva_carpeta), 201
        except Exception as e:
            return f'Error al copiar la carpeta: {str(e)}', 500

    @carpetas_bp.route('/carpeta-raiz', methods=['GET'])
    def get_carpeta_raiz():
        idU = request.args.get('idU')  # Obtén el parámetro de la URL
        try:
          # Cambia find_many a find
            carpetas = collection.find({"unica": True, "id_usuario": idU})
        
        # Convierte el cursor a una lista para serializar
            return jsonify([serialize_carpeta(c) for c in carpetas]), 200
        except Exception as e:
            return f'Error al obtener la carpeta raíz: {str(e)}', 500
        
    @carpetas_bp.route('/carpeta-compartida', methods=['GET'])
    def get_carpeta_compartida():
        idU = request.args.get('idU')  # Obtén el parámetro de la URL
        try:
          # Cambia find_many a find
            carpeta = collection.find_one({"ficheroMadre": "1111111111", "id_usuario": idU})
        
        # Convierte el cursor a una lista para serializar
            return jsonify(serialize_carpeta(carpeta)), 200
        except Exception as e:
            return f'Error al obtener la carpeta raíz: {str(e)}', 500
        
    # Función para obtener las carpetas eliminadas
    @carpetas_bp.route('/carpetas-eliminadas', methods=['GET'])
    def get_deleted_folders():
        try:
            carpetas = list(collection.find({'eliminada': True}).sort('nombre', 1))
            carpetas = [serialize_carpeta(c) for c in carpetas]
            return jsonify(carpetas), 200
        except Exception as e:
            return f'Error al obtener las carpetas eliminadas: {str(e)}', 500

    @carpetas_bp.route('/eliminar-carpeta/<id>', methods=['DELETE'])
    def delete_folder(id):
        try:
        # Intentar convertir el ID a ObjectId
            carpeta_id = ObjectId(id)
            result = collection.delete_one({'_id': carpeta_id})
        
            if result.deleted_count == 1:
                return jsonify({"message": "Carpeta eliminada exitosamente"}), 200
            else:
                return jsonify({"message": "Carpeta no encontrada"}), 404
        except Exception as e:
            return f'Error al eliminar la carpeta: {str(e)}', 500

    # OBTENER CARPETAS O FICHEROS DE una carpeta
    @carpetas_bp.route('/<string:idU>/<string:idC>', methods=['GET'])
    def get_carpetas(idU, idC):
        try:
            carpetas = collection.find({'id_fichero_madre': idC, 'id_usuario': idU, 'eliminada': False}).sort('nombre', 1)
            return jsonify([serialize_carpeta(c) for c in carpetas]), 200
        except Exception as e:
            return f'Error al obtener carpetas: {str(e)}', 500

    # Actualizar el nombre de una carpeta
    @carpetas_bp.route('/nombre-actualizar/<string:id>', methods=['PUT'])
    def update_carpeta_nombre(id):
        data = request.json
        nombre = data.get('nombre')
        ficheroMadre = data.get('ficheroMadre')

        if not nombre or not ficheroMadre:
            return 'El nombre y fichero madre son obligatorios', 400

        try:
            carpeta_ant = collection.find_one({'nombre': nombre, 'id_fichero_madre': ficheroMadre, 'eliminada': False})
            if carpeta_ant:
                return 'No se permiten nombres duplicados en un mismo directorio', 400

            result = collection.update_one(
                {'_id': ObjectId(id)},
                {'$set': {'nombre': nombre}}
            )

            if result.matched_count == 0:
                return 'Carpeta no encontrada', 404

            return jsonify({'message': 'Nombre de carpeta actualizado', 'id': id}), 200
        except Exception as e:
            return f'Error al actualizar la carpeta: {str(e)}', 500

    # MOVER UNA CARPETA
    @carpetas_bp.route('/mover_carpeta/<string:id>', methods=['PUT'])
    def mover_carpeta(id):
        data = request.json
        nombre = data.get('nombre')
        nuevoFicheroMadre = data.get('nuevoFicheroMadre')

        if not nombre or not nuevoFicheroMadre:
            return 'El nombre y fichero madre son obligatorios', 400

        try:
            nuevo_nombre = nombre
            contador = 1
            carpeta_ant = collection.find_one({'nombre': nuevo_nombre, 'id_fichero_madre': nuevoFicheroMadre, 'eliminada': False})
            carp_original = collection.find_one({'_id': ObjectId(id)})

            if carp_original['id_fichero_madre'] != nuevoFicheroMadre:
                while carpeta_ant:
                    nuevo_nombre = f"{nombre}_{contador}"
                    contador += 1
                    carpeta_ant = collection.find_one({'nombre': nuevo_nombre, 'id_fichero_madre': nuevoFicheroMadre, 'eliminada': False})

            result = collection.update_one(
                {'_id': ObjectId(id)},
                {'$set': {'id_fichero_madre': nuevoFicheroMadre, 'nombre': nuevo_nombre}}
            )

            if result.matched_count == 0:
                return 'Carpeta no encontrada', 404

            return jsonify({'message': 'Carpeta movida exitosamente', 'id': id, 'nuevoNombre': nuevo_nombre}), 200
        except Exception as e:
            return f'Error al mover la carpeta: {str(e)}', 500

    # Función para eliminar recursivamente una carpeta y sus archivos
    def eliminar_carpeta_recursivamente(idCarpeta):
        collection_archivos.update_many({'id_fichero_madre': idCarpeta}, {'$set': {'eliminado': True}})
        subcarpetas = collection.find({'id_fichero_madre': idCarpeta, 'eliminada': False})

        for subcarpeta in subcarpetas:
            eliminar_carpeta_recursivamente(str(subcarpeta['_id']))

        collection.update_one({'_id': ObjectId(idCarpeta)}, {'$set': {'eliminada': True}})

    @carpetas_bp.route('/<string:id>', methods=['DELETE'])
    def delete_carpeta(id):
        try:
            eliminar_carpeta_recursivamente(id)
            return jsonify({'message': 'Carpeta eliminada exitosamente'}), 204
        except Exception as e:
            return f'Error al eliminar la carpeta: {str(e)}', 500

    return carpetas_bp


