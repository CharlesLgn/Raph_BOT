// Module Import
const tmi = require("tmi.js");
const commands = require('./commands.js');
const static_config = require('../config.json');
const reaction = require('./reaction.js');
const shout = require('./shout.js');

// Variable
var client = null;
var socket = null;
var config = null;

function init(config_init, socket_init) {
    config = config_init;
    socket = socket_init;

    // External lib
    commands.init(config, socket);
    reaction.init(config, socket);
    shout.init(config, socket);

    // TMI config
    const tmiConfig = {
        options: {
            debug: true
        },
        identity: {
            username: static_config["bot_name"],
            password: static_config["twitch_token"]
        },
        channels: [config["twitch_channel"]]
    };

    socket.log("[TWITCH] Connecting ...");
    client = new tmi.client(tmiConfig);
    client.connect();

    // Events
    client.on('connected', async (adress, port) => {
        socket.twitch_state(true);
        send(config["twitch_connection_message"]);
        socket.log("[TWITCH] Connected on : " + adress)

        if (parseInt(config["cmd_time_interval"]) > 0) {
            setInterval(async function () {
                var result = await commands.timeTrigger();
                if (result) {
                    send(result);
                }
            }, 60000);
        }
    });


    client.on('disconnected', function () {
        socket.twitch_state(false);
        socket.log("[TWITCH] Disconnected");
    });

    client.on('message', async (channel, user, message, isSelf) => {
        var result = null;

        // Do not react to himself
        if (isSelf) return;

        // Automatic command by number of messages
        if (parseInt(config["cmd_msg_interval"]) > 0) {
            result = await commands.msgTrigger();
            if (result) {
                send(result);
            }
        }

        // Commands
        result = await commands.run(user, message);
        if (result) {
            send(result);
            return;
        }

        // Reaction
        result = await reaction.run(user, message);
        if (result) {
            send(result);
            return;
        }

        // Shout
        result = await shout.run(user, message);
        if (result) {
            send(result);
            return;
        }

    });
}

function send(msg) {
    client.say(config["twitch_channel"], msg);
}

module.exports = { init }