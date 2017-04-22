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
                      //console.log('Ligne : %s', lignes[i]);
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
}, 60000);