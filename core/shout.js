const db = require('./db.js');
var socket = null;

// Counter
var shout_interval = null;
var shout_counter = 0;
var UUID = null;

function init(config_init, socket_init) {
    socket = socket_init;
    shout_interval = config_init["shout_interval"];
    UUID = config_init['UUID'];
}

async function load_header() {
    var res = await db.query("SELECT replacement FROM shout WHERE UUID = ? AND original = '#HEADER'", [UUID]);

    try {
        if (res[0]) {
            return res[0].replacement;
        }
        return null;
    }
    catch (err) {
        console.error(err);
    }
}

async function load_language() {
    var res = await db.query("SELECT replacement FROM shout WHERE UUID = ? AND original = '#LANGUAGE'", [UUID]);

    try {
        if (res[0]) {
            return res[0].replacement;
        }
        return null;
    }
    catch (err) {
        console.error(err);
    }
}

async function load_words() {
    var sql = await db.query("SELECT * FROM shout WHERE UUID = ?", [UUID]);
    var result = [];

    try {
        sql.forEach(element => { result[element.original] = element.replacement });
        return result;
    }
    catch (err) {
        console.error(err);
    }
}

async function run(user, message) {
    var res = null;

    // Shout disabled
    if (shout_interval == 0)
        return null;

    shout_counter++;
    socket.shout_update(shout_counter, shout_interval);

    if (shout_counter > shout_interval) {
        switch (await load_language()) {
            case "EN":
                res = run_english(user, message);
                break;

            case "FR":
                res = run_french(user, message);
                break;

            default:
                socket.log("[SHOUT] Language not supported");
                return;
        }

        if (res) {
            shout_counter = 0;
            return res;
        }
        else {
            shout_counter--;
            return null;
        }
    }
}

async function run_french(user, message) {
    // Load header
    var shout_header = await load_header();

    // Load shout remplacement
    var shout_words = await load_words();

    //Split words of the sentence
    var word_array = message.toLowerCase().split(" ");

    //Do not take sentences too long
    if (word_array.length > 15)
        return false;

    message = "";
    var replaced_word = "";

    for (var word of word_array) {
        //If the word can not be replaced it does not change, otherwise it is modified
        replaced_word = (shout_words[word] ? shout_words[word] : word);

        //If the word contains "'", special treatment to replace the left and right part
        if (word.includes("'")) {
            //Split word with "'" in it
            var word_split = word.split("'");
            var replaced_word_L = shout_words[word_split[0]];
            var replaced_word_R = shout_words[word_split[1]];

            //If left side and right side of the word can be replaced
            if (replaced_word_L && replaced_word_R) {
                replaced_word = replaced_word_L + "'" + replaced_word_R;;
            }
        }

        //Add the word to the message
        message += replaced_word + " ";
    }

    return shout_header.toUpperCase() + user["display-name"] + ", " + message.toUpperCase() + "!";
}

async function run_english(user, message) {
    // Load header
    var shout_header = await load_header();

    // Load shout remplacement
    var shout_words = await load_words();

    //Split words of the sentence
    var word_array = message.toLowerCase().split(" ");

    //Do not take sentences too long
    if (word_array.length > 15)
        return false;

    message = "";
    var replaced_word = "";

    for (var word of word_array) {
        //If the word can not be replaced it does not change, otherwise it is modified
        replaced_word = (shout_words[word] ? shout_words[word] : word);

        //If the word contains "'", special treatment to replace the left and right part
        if (word.includes("'")) {
            //Split word with "'" in it
            var word_split = word.split("'");
            var replaced_word_L = shout_words[word_split[0]];
            var replaced_word_R = shout_words[word_split[1]];

            //If left side and right side of the word can be replaced
            if (replaced_word_L && replaced_word_R) {
                replaced_word = replaced_word_L + " " + replaced_word_R;;
            }
        }

        //Add the word to the message
        message += replaced_word + " ";
    }

    return shout_header.toUpperCase() + user["display-name"] + ", " + message.toUpperCase() + "!";
}

module.exports = { init, run }