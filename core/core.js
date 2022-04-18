const version = "v6.0.0";
const UUID = process.argv.slice(2)[0];
const db = require('./db.js');
const socket = require('./socket.js');
const twitch = require('./twitch.js');

var current_config = null;

// Get UUID of instance
if(UUID == "" || UUID == undefined){
    console.error("UUID is undefined.");
    process.exit(1);
}

load_config();

async function load_config(){
    // Load config
    current_config = await db.load_config(UUID);
    current_config["version"] = version;
    current_config["UUID"] = UUID;

    console.log(current_config);

    // Init
    socket.init(current_config);
    twitch.init(current_config, socket);
}