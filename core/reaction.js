const db = require('./db.js');
const tools = require('./tools.js');

var exclude_reactions = [];
var socket = null;
var UUID = null;

function init(config_init, socket_init) {
    socket = socket_init;
    UUID = config_init['UUID'];
}

async function query_reaction(words) {
    const reactions_key_in = words.map(word => "?").join(",")
    const reactions_key_not_in = exclude_reactions.map(word => "?").join(",")

    const values = [UUID]
    values.push(reactions_key_in)
    values.push(reactions_key_not_in)

    var res = await db.query(`SELECT key, reaction, frequency, timeout
                                  FROM reactions
                                  WHERE UUID = ?
                                    AND reactions.key IN (${reactions_key_in})
                                    AND reactions.key NOT IN (${reactions_key_not_in})
                                  ORDER BY RAND() LIMIT 1`, values);
    try {
        if (res[0]) {
            return res[0];
        }
        return;
    }
    catch (err) {
        console.error(err);
        return null;
    }
}

async function run(user, message) {
    var words = message.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "").replace(/['"]+/g, ' ').split(" ");
    var result = await query_reaction(words);

    try {
        if (result) {
            if (tools.getRandomInt(100) <= result.frequency) {
                if (result.timeout > 0) {
                    exclude_reactions.push(result.key);

                    setTimeout(function () {
                        exclude_reactions.splice(exclude_reactions.indexOf(result.key), 1);
                        socket.log("[REACTION] '" + result.key + "' has been removed from the exclusion list");
                    }, result.timeout * 1000);

                    socket.log("[REACTION] '" + result.key + "' has been excluded for " + result.timeout + "s");
                }
                return result.reaction.replace("@username", user['display-name']);
            }
        }
        return;
    }
    catch (err) {
        console.error(err);
        return null;
    }
}

module.exports = { init, run }