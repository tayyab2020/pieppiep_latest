var express = require( 'express' );
var fs = require("fs");
const path = require('path');
require('dotenv').config({ path: path.resolve(__dirname, '../vendor/markury/src/.env') });
global.__basedir = __dirname;
var protocol = process.env.NODE_HTTP;
const privateKey = process.env.SSL_PRIVATE_KEY;
const certificate = process.env.SSL_CERTIFICATE;
const PORT = process.env.NODE_PORT;
const HOST = process.env.NODE_HOST;

var app = express();
app.use(express.static(__dirname + '/uploads'));

if(protocol == "https")
{
    app.use(function (req, res, next) {
        res.setHeader('Access-Control-Allow-Origin', 'https://pieppiep.com');
        res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
        res.setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
        res.setHeader('Access-Control-Allow-Credentials', true);
        next();
    });

    const credentials = {
        key: privateKey,
        cert: certificate,
    };

    var https = require( 'https' ).createServer( credentials,app );
    
    var io = require( 'socket.io' )( https, {
        cors: {
          origin: "https://pieppiep.com",
          credentials: true
        }
    });

    https.listen( PORT, HOST, function() {
        console.log( `Listening on ${process.env.NODE_HTTP}://${HOST}:${PORT}` );
    });
}
else
{
    if(HOST == "localhost")
    {
        $cors_allow = 'http://127.0.0.1:8000';
    }
    else
    {
        $cors_allow = 'https://pieppiep.com';
    }

    console.log($cors_allow);

    app.use(function (req, res, next) {
        res.setHeader('Access-Control-Allow-Origin', $cors_allow);
        res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
        res.setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
        res.setHeader('Access-Control-Allow-Credentials', true);
        next();
    });

    const credentials = {};

    var http = require( 'http' ).createServer( credentials,app );

    var io = require( 'socket.io' )( http, {
        cors: {
          origin: $cors_allow,
          credentials: true
        }
    });

    http.listen( PORT, HOST, function() {
        console.log( `Listening on ${process.env.NODE_HTTP}://${HOST}:${PORT}` );
    });
}

const socketEvents = require('./utils/socket');

io.on( 'connection', function( socket ) {
    console.log( 'a user has connected!' );
});

new socketEvents(io).socketConfig();