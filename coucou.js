//on importe toutes les librairies http 
var http = require('http');


//req -> la requete qu'on envoie au serveur
//result -> réponse du serveur
httpServer = http.createServer(function (req, result){
	console.log("un utilisateur a affiché la page");
	result.end("hello world");
});

//on demande au serveur d'écouter sur le port 1337; adresse => localhost::1337
httpServer.listen(1337);

//ouvrir un socket : ouvrir un parallèle entre notre page web et le serveur nodejs
//socket io -> permet de gérer la compatibilité avec le navigateur

//installer un module en node js : 
/*
** - on va dans l'invit de commande et on se place dans notre repertoire 
** - on lance la commande npm install socket.io
*/

var io = require('socket.io').listen(httpServer);
