const port = 3032
const secret = "Nil00f@r1869";
const repo = "/var/www/crm-kpzagros";

let http = require('http');
let crypto = require('crypto');

const exec = require('child_process').exec;


const commands = [
    'cd ' + repo,
    'git pull origin master',
    'composer update',
    'php artisan config:cache',
    'php artisan migrate',
    'npm install',
    'npm run prod'
];


http.createServer(function (req, res) {
    req.on('data', function (chunk) {
        let sig = "sha1=" + crypto.createHmac('sha1', secret).update(chunk.toString()).digest('hex');

        if (req.headers['x-hub-signature'] == sig) {
            exec(commands.join(' && '));
        }
    });

    res.end();
}).listen(port);