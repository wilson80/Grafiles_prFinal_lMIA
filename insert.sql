
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
db.createCollection("forders");
db.createCollection("files");

db.users.insertOne({username:'admin1', nombre:'User admin 1', password:'81dc9bdb52d04dc20036dbd8313ed055',rol:'administrador'});






--  configurar el mongo en docker en el puerto 27017 
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



db.folders.insertMany([
    {
        nombre: 'raiz',
        id_usuario: '6721872dfc21576372fe6911',
        ficheroMadre: '0000000000',
        unica:true
    },
    {
        nombre: 'compartida',
        id_usuario: '6721872dfc21576372fe6911',
        ficheroMadre: '1111111111',
        unica:true
    }
]);