
-- primero configurar el mondo en docker en el puerto 27018
use proyecto2Mia;

db.createUser({
    user: "pr2mia2024",
    pwd: "1234",
    roles: [
      { role: "readWrite", db: "proyecto2Mia" }
    ]
});
db.createCollection("users");
db.createCollection("folders");
db.createCollection("files");

db.users.insertOne({username:'admin1', nombre:'User admin 1', password:'81dc9bdb52d04dc20036dbd8313ed055',rol:'administrador'});
db.users.find();

        /*el id que salga del usuario*/

db.folders.insertMany([
    {
        nombre: 'raiz',
        id_usuario: "6723cca54deb5d9df9d5ac54",
        ficheroMadre: '0000000000',
        unica:true
    },
    {
        nombre: 'compartida',
        id_usuario:  "6723cca54deb5d9df9d5ac54",
        ficheroMadre: '1111111111',
        unica:true
    }
]);




6723cca54deb5d9df9d5ac54