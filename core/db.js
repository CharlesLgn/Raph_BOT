var mysql = require('mysql');
var config = require('../config.json');

var db = mysql.createConnection({
    host: config["db_host"],
    user: config["db_user"],
    password: config["db_pass"],
    database: config["db_name"]
});

db.connect(function (err) {
    if (err) throw err;
})

function query(sql, values) {
    return new Promise(resolve => db.query(sql, values, function (err, result) {
        if (err) {
            console.error(err);
            resolve(null);
        }

        resolve(result);
    }));
}

async function load_config(UUID) {
    var result = [];

    // General config
    var sql = await query("SELECT * FROM config WHERE UUID = ?", [UUID]);
    try {
        sql.forEach(element => {
            result[element.id] = element.value;
        });
    }
    catch (err) {
        console.error(err);
        process.exit(0);
    }

    // Socket port
    var sql = await query("SELECT port FROM ports WHERE UUID = ?", [UUID]);
    try {
        result["port"] = sql[0].port;
    }
    catch (err) {
        console.error(err);
        process.exit(0);
    }

    return result;
}

module.exports = { query, load_config }