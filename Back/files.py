from flask import Blueprint, request, jsonify
from bson import ObjectId
from datetime import datetime

files_bp = Blueprint('files', __name__)
def serialize_archivo(archivo):
    """Convierte un documento de archivo a un formato JSON serializable."""
    if archivo is not None:
        archivo['_id'] = str(archivo['_id'])  # Convertir ObjectId a string
        archivo['id_fichero_madre'] = str(archivo['id_fichero_madre'])  # Convertir ObjectId a string
        archivo['id_usuario'] = str(archivo['id_usuario'])  # Convertir ObjectId a string
    return archivo

def create_routesfiles(collection):
    # Función para agregar nuevo archivo
    @files_bp.route('/', methods=['POST'])
    def add_file():
        data = request.json
        extension = data.get('extension')
        nombre = data.get('nombre')
        contenido = data.get('contenido')
        idFM = data.get('idFM')
        idU = data.get('idU')

        if not extension or not nombre or not contenido or not idFM or not idU:
            return 'Todos los campos son obligatorios', 400

        try:
            archant = collection.find_one({'nombre': nombre, 'extension': extension, 'id_fichero_madre': idFM, 'id_usuario': idU, 'eliminado': False})
            
            if archant:
                return jsonify(message="No se permiten archivos con el mismo nombre en la misma carpeta"), 400

            result = collection.insert_one({
                'nombre': nombre,
                'extension': extension,
                'contenido': contenido,
                'id_fichero_madre': idFM,
                'fechamod': datetime.utcnow(),
                'eliminado': False,
                'id_usuario': idU
            })

            nuevo_archivo = collection.find_one({'_id': result.inserted_id})
            nuevo_archivo['_id'] = str(nuevo_archivo['_id'])
            return jsonify(nuevo_archivo), 201
        except Exception as e:
            return f'Error al insertar el archivo: {str(e)}', 500

    # Función para obtener todos los archivos
    @files_bp.route('/<idC>/<idU>', methods=['GET'])
    def get_files(idC, idU):
        try:
            archivos = list(collection.find({'id_fichero_madre': idC, 'eliminado': False, 'id_usuario': idU}).sort('nombre', 1))
            archivos = [serialize_archivo(archivo) for archivo in archivos]
            return jsonify(archivos), 200
        except Exception as e:
            return f'Error al obtener los archivos: {str(e)}', 500


    # Función para obtener todos los archivos eliminados
    @files_bp.route('/eliminados/<idC>', methods=['GET'])
    def get_deleted_files(idC):
        try:
            archivos = list(collection.find({'eliminado': True}).sort('nombre', 1))
            archivos = [serialize_archivo(archivo) for archivo in archivos]
            return jsonify(archivos), 200
        except Exception as e:
            return f'Error al obtener los archivos eliminados: {str(e)}', 500

    # Función para obtener todos los archivos compartidos
    @files_bp.route('/archivos-compartidos/<idC>/<idU>', methods=['GET'])
    def get_shared_files(idC, idU):
        if not idC or not idU:
            return 'Campos obligatorios', 400
        try:
            archivos = list(collection.find({'id_fichero_madre': idC, 'id_usuario': idU, 'compartido': True}).sort('nombre', 1))
            archivos = [serialize_archivo(archivo) for archivo in archivos]
            return jsonify(archivos), 200
        except Exception as e:
            return f'Error al obtener los archivos compartidos: {str(e)}', 500

    # Función para mover archivo a la papelera (eliminación lógica)
    @files_bp.route('/papelera/<id>', methods=['DELETE'])
    def delete_file(id):
        try:
            result = collection.update_one({'_id': ObjectId(id)}, {'$set': {'eliminado': True}})
            if result.modified_count == 0:
                return 'Archivo no encontrado o ya eliminado', 404
            return jsonify(message='Archivo movido a la papelera', id=id), 200
        except Exception as e:
            return f'Error al mover a la papelera el archivo: {str(e)}', 500

    # Función para editar un archivo
    @files_bp.route('/editar', methods=['PUT'])
    def edit_file():
        data = request.json
        extension = data.get('extension')
        nombre = data.get('nombre')
        contenido = data.get('contenido')
        idArchivo = data.get('idArchivo')
        idFM = data.get('idFM')

        if not extension or not nombre or not contenido or not idArchivo or not idFM:
            print({extension,nombre,contenido,idArchivo,idFM})
            print("Todos los campos son obligatorios")
            return 'Todos los campos son obligatorios', 400

        try:
            result = collection.update_one(
                {'_id': ObjectId(idArchivo)},
                {'$set': {'nombre': nombre, 'extension': extension, 'contenido': contenido, 'fechamod': datetime.utcnow()}}
            )

            if result.modified_count == 0:
                print("Archivo no encontrado")
                return 'Archivo no encontrado', 404
            return jsonify(message="Archivo actualizado exitosamente", idArchivo=idArchivo), 200
        except Exception as e:
            print('Error al actualizar el archivo: {str(e)}',  {str(e)})
            return f'Error al actualizar el archivo: {str(e)}', 500

    # Función para compartir un archivo
    @files_bp.route('/compartir', methods=['POST'])
    def share_file():
        data = request.json
        idFM = data.get('idFM')
        idU = data.get('idU')
        idUC = data.get('idUC')
        idA = data.get('idA')

        if not idU or not idA or not idFM or not idUC:
            print("Todos los datos son obligatorios")
            console.log({idFM,idU,idUC,idA})
            return 'Todos los campos son obligatorios', 400

        try:
            archivoOriginal = collection.find_one({'_id': ObjectId(idA)})
            if not archivoOriginal:
                return 'Archivo original no encontrado', 404

            nuevoNombre = archivoOriginal['nombre']
            nuevoExtension = archivoOriginal['extension']
            contador = 1

            while collection.find_one({'nombre': nuevoNombre, 'extension': nuevoExtension, 'id_fichero_madre': idFM, 'id_usuario': idU}):
                nuevoNombre = f"{archivoOriginal['nombre']}_{contador}"
                contador += 1

            result = collection.insert_one({
                'nombre': archivoOriginal['nombre'],
                'extension': archivoOriginal['extension'],
                'contenido': archivoOriginal['contenido'],
                'id_fichero_madre': idFM,
                'fechamod': archivoOriginal['fechamod'],
                'compartido': True,
                'fecha_compartido': datetime.utcnow(),
                'usuario_que_compartio': idUC,
                'id_usuario': idU
            })

            nuevo_archivo = collection.find_one({'_id': result.inserted_id})
            nuevo_archivo['_id'] = str(nuevo_archivo['_id'])
            return jsonify(nuevo_archivo), 201
        except Exception as e:
            return f'Error al compartir el archivo: {str(e)}', 500

    # Función para eliminar permanentemente un archivo del sistema
    @files_bp.route('/eliminar-del-sistema/<id>', methods=['DELETE'])
    def delete_file_permanently(id):
        try:
            result = collection.delete_one({'_id': ObjectId(id)})
            if result.deleted_count == 0:
                return jsonify(message='Archivo no encontrado o ya eliminado'), 404
            return jsonify(message='Archivo eliminado correctamente', id=id), 200
        except Exception as e:
            return f'Error al eliminar el archivo: {str(e)}', 500

    return files_bp
