// App Node.js de communication bleutooth avec les stations méteo

// ======================
// INTERFACE HTTP
var express = require('express');
var app = express();

app
    .use(express.static(__dirname + '/http'))
    .use(function(req, res, next){
      // 404
        res.setHeader('Content-Type', 'text/plain');
        res.status(404).send('Page introuvable !');
    });
app.on('error', function(){
  console.log('Erreur Expess');
});

var serveur = app.listen(8080);
serveur.on('error', function(){ console.log('== ERREUR SERVEUR =='); });
console.log("Interface ouverte sur http://localhost:8080");


// ======================
// BLUETOOTH
var btSerial = new (require('bluetooth-serial-port')).BluetoothSerialPort();
var bufferSerial = "";

btSerial.on('found', function (address, name) {
    console.log('Peripherique trouve : %s (%s)', name, address);

    btSerial.findSerialPortChannel(address, function (channel) {
        console.log('Port série trouve sur %s (%s)', name, address);

        btSerial.connect(address, channel, function () {
            console.log('Connexion etablie avec %s (%s)', name, address);

            btSerial.write(new Buffer('{Hello:Hello', 'utf-8'), function (err, bytesWritten) {
                if (err) console.log(err);
            });

            btSerial.on('data', function (buffer) {

                bufferSerial += buffer.toString('utf-8');
  
                  var endLine = bufferSerial.indexOf('\n');
                  if(endLine > -1 ){
                    var lignes = bufferSerial.split('\n');
                    for(var i=0; i<lignes.length-2; i++){
                      console.log('BLUETOOTH << %s', lignes[i]);
                      // quand ca fonctionnera, envoyer les ligne aux clients web
                    }
                  }
                  
                  dateFormat = getDateStr();
                  
                  /*console.log("__");
                  console.log('Donnees recues : %s', data);
                  console.log("buffer : %s", bufferSerial);*/
                  var sep = bufferSerial.indexOf(';');
                  while(sep > -1){
                    var dataBrut = bufferSerial.substr(0, sep);
                    bufferSerial = bufferSerial.substring(sep + 1, bufferSerial.lenght);
                    //console.log('sep = %d', sep);
                    //console.log("dataBrut = %s",dataBrut);
                    //console.log("buffer = %s", bufferSerial);
                  
                    var sepPos = dataBrut.indexOf('=');
                    if(sepPos > 0){
                      var decomposition = dataBrut.split('=');
                      console.log('%s : POST -> %s = %s', dateFormat, decomposition[0], decomposition[1]);
                      
                      // enregistrement csv
                      /*var donneeFormat = dateFormat + ';' + decomposition[0] + ';' + decomposition[1] + "\r\n";
                      fs.appendFile('data.csv', donneeFormat, function (err) {
                        if (err) console.log("Erreur d'écriture dans le fichier data.csv");
                      });*/
                  
                      // envois http
                      PostData(decomposition[0], decomposition[1], dateFormat);
                  
                    }
                  
                    sep = bufferSerial.indexOf(';');
                  }
            });
        }, function () {
            console.log('cannot connect');
        });

        // close the connection when you're ready
        btSerial.close();
    }, function () {
        console.log('Pas de connexion disponible sur %s (%s)', name, address);
    });
});

function scanBluetooth(){
    console.log('recherche bluetooth ...');
    btSerial.inquire();
}
    

setInterval(function(){
    scanBluetooth();
}, 600000);
scanBluetooth();


function PostData(donnee, valeur, dateStr) {
  //console.log('envoi de données');
  var querystring = require('querystring');
    var http = require('http');
  
  // Build the post string from an object
  var post_data = querystring.stringify({
    'action': 'datalizer_setData',
    'donnee' : donnee,
    'valeur' : valeur,
    'date' :   dateStr
  });

  // An object of options to indicate where to post to
  var post_options = {
      host: 'raspi',
      port: '80',
      path: "/wp-admin/admin-ajax.php",
      method: 'POST',
      headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'Content-Length': post_data.length
      }
  };

  console.log("Envoi de la requette POST sur %s%s", post_options.host, post_options.path);

  // Set up the request
  var post_req = http.request(post_options, function(res) {
      res.setEncoding('utf8');
      res.on('data', function (chunk) {
          //console.log('Response: ' + chunk);
      });
  });
  post_req.on('error', function(e) {
    console.log("%s : Erreur de la requette POST: %s", getDateStr(), e.message);
    //console.log(e);
    // ça ne passe pas, on réessaye un peu plus tard
    if(e.code == 'ECONNRESET'){
      var delayPost = function(){
        console.log("%s : Nouvelle tentative d'envoi de %s: %d", getDateStr(), donnee, valeur);
        PostData(donnee, valeur, dateStr);
      }
      setTimeout(delayPost, 5000);
    }
  });

  // post the data
  post_req.write(post_data);
  post_req.end();

}

function getDateStr(){
  var now = new Date();
  var annee   = now.getFullYear();
  var mois    = now.getMonth() + 1;
  var jour    = now.getDate();
  var dateFormat = annee + '-' + mois + '-' + jour;
  dateFormat += ' ' + now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds();
  return dateFormat;
}
