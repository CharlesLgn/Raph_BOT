var socket = require('socket.io')
var fs = require('fs');
var stream_log = fs.createWriteStream(__dirname + "/lastest.log", {flags:'a'});

// Socket server
const io = new socket.Server(require('../www/src/port.json')['socket_port']);

// Variables
var web_client = null;
var web_client_connected = false;
var config = null;

// GUI info
var GUI = {
    twitch: false,
    shout: {current: 0, max: 0},
    trigger_time: {current: 0, max: 0, nb: 0},
    trigger_msg: {current: 0, max: 0, nb: 0},
};

function init(config_init){
    config = config_init;
    log("[CORE] Started (" + config["version"] + ") with UUID : '" + config["UUID"] + "'");
}

// When web_client is connected, update all info
io.sockets.on('connection', function (socket) {
    web_client = socket;
    web_client_connected = true;

    GUI_update();

    socket.on('stop-core', function(){
        log("[CORE] Halted");
        process.exit(0);
    });

    socket.on('disconnect', function () {
        web_client_connected = false;
    });

});

function GUI_update(){
    if(web_client_connected)
        web_client.emit('update', JSON.stringify(GUI));
}

function twitch_state(state){
    GUI['twitch'] = state;
    GUI_update(); 
}

function shout_update(current, max){
    GUI['shout'] = {current, max};
    GUI_update();
}

function time_trigger_update(current, max, nb){
    GUI['trigger_time'] = {current, max, nb};
    GUI_update();
}

function msg_trigger_update(current, max, nb){
    GUI['trigger_msg'] = {current, max, nb};
    GUI_update();
}

function log(msg){
    // Format
    var date = new Date;
    var time = ('0' + date.getHours()).slice(-2) + ":" + ('0' + date.getMinutes()).slice(-2) + ":" + ('0' + date.getSeconds()).slice(-2) + "." + ('00' + date.getMilliseconds()).slice(-3);
    msg = "[" + time + "] " + msg + "\n";

    // Write to file
    stream_log.write(msg);

    // Update UI
    if(web_client_connected)
        web_client.emit('log', msg);
}

function play_audio(file, volume){
    if(web_client_connected){
        var data = {file, volume};
        web_client.emit('play-audio', JSON.stringify(data));
    }
}


module.exports = {init, twitch_state, shout_update, time_trigger_update, msg_trigger_update, log, play_audio}