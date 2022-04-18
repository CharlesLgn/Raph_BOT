// Const declaration
const tools = require('./tools.js');
const db = require('./db.js');

// Global Var
var config = null;
var timer = 0;
var message_counter = 0;
var total_auto_cmd_time = 0;
var total_auto_cmd_msg = 0;
var last_auto_cmd = 0;
var time_interval = null; //Trigger by time (minutes)
var message_interval = null;  //Trigger by message (number of messages)
var UUID = null;

// Function declaration
function init(config_init, socket_init){
    socket = socket_init;
    config = config_init;

    time_interval = parseInt(config["cmd_time_interval"]);
    message_interval = parseInt(config["cmd_msg_interval"]);
    UUID = config["UUID"];
}

async function timeTrigger(){
	timer++;
	socket.time_trigger_update(timer, time_interval, total_auto_cmd_time);
	if(timer >= time_interval){
		timer = 0;
		total_auto_cmd_time++;
        socket.log("[COMMAND] Autocommand triggered by the timer");
		return await auto_command();
	}
	return false;
}

async function msgTrigger(){
	message_counter++;
	socket.msg_trigger_update(message_counter, message_interval, total_auto_cmd_msg);
	if(message_counter >= message_interval){
		message_counter = 0;
		total_auto_cmd_msg++;
        socket.log("[COMMAND] Autocommand triggered by number of messages");
		return await auto_command();
    }
	return false;
}

async function load_auto_command(){
    var sql = await db.query("SELECT `command` FROM `commands` WHERE `UUID` = '" + UUID + "' AND `auto` = 1");
    var result = [];

    try {
        sql.forEach(element => {result.push(element.command);});
        return result;
    }
    catch (err){
        console.error(err);
    }
}

async function auto_command(){
    var list = await load_auto_command();
    var index;

	do{
		index = Math.floor(Math.random() * Math.floor(list.length));
	}while(index == last_auto_cmd && list.length > 1)

    last_auto_cmd = index;

	return await run(null, config['cmd_prefix'] + list[index]);		
}

async function get_alias(request){
    var sql = "SELECT `command` FROM `alias_commands` WHERE `UUID` = '" + UUID + "' AND `alias` = '" + request + "'";
    var res = await db.query(sql);

    try {
        if(res[0])
            return res[0].command;
        else
            return request;
    }
    catch (err){
        console.error(err);
        return null;
    }
}

async function get_command(request){
    var sql = "SELECT `text` FROM `commands` WHERE `UUID` = '" + UUID + "' AND `command` = '" + request + "'";
    var res = await db.query(sql);

    try {
        if(res[0])
            return res[0].text;
        else
            return null;
    }
    catch (err){
        console.error(err);
        return null;
    }
}

async function run(user, message){
    var result = null;
	var fullCommand = tools.commandParser(message, config['cmd_prefix']);

    // Not a command
	if(!fullCommand) return null;

    // Sanitize
	var command = fullCommand[1].toLowerCase().trim().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
	var param = fullCommand[2].toLowerCase().trim().normalize('NFD').replace(/[\u0300-\u036f]/g, "");

    // Alias
    command = await get_alias(command);
    
    // Text
    result = await get_command(command);
    if (result){
        if(user) 
            result = result.replace("@username", user['display-name']);

        return result;
    }
        
    return null;
}

module.exports = {init, run, auto_command, timeTrigger, msgTrigger}