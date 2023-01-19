const port = 3032
const secret = "Nil00f@r1869";
const repo = "/var/www/crm-kpzagros";

let http = require('http');
let crypto = require('crypto');

// const exec = require('child_process').exec;
var spawn = require('child_process').spawn;
const {execSync} = require('child_process');
const {promisify} = require('util');
// const exec = promisify(execWithCallback);

const fs = require("fs");


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
    const writeStream = fs.createWriteStream(`${repo}/storage/logs/webhooks/logs.txt`, {flags: 'a'});

    req.on('data', function (chunk) {
        let now = new Date();
        let date = `${now.getFullYear()}-${now.getMonth()}-${now.getDay()} ${now.getHours()}:${now.getMinutes()}:${now.getSeconds()}`;

        commands.map(function (command) {
            try {
                writeStream.write(`********** ${date} ********** \n`)
                writeStream.write(`${date} ${command} \n`)
                const error = execSync(command).toString();
                writeStream.write(`${date} ${error} \n \n`)
            } catch (error) {
                console.log('############################################')
                console.log(error)
                console.log('############################################')
            }
        })

        // let sig = "sha1=" + crypto.createHmac('sha1', secret).update(chunk.toString()).digest('hex');
        //
        // if (req.headers['x-hub-signature'] == sig) {
        //     writeStream.write(`Running Commands...`);
        //     exec(commands.join(' && '));
        // }
        writeStream.write(`--------------------------------------------------------------------------------------------------------------------------------------------------------------------- \n\n`);
        writeStream.end();

    });

    res.end();
}).listen(port);
