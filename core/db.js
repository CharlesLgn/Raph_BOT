var mysql = require('mysql');
var config = require('../config.json');

var db = mysql.createConnection({
    host: config["db_host"],
    user: config["db_user"],
    password: config["db_pass"],
    database: config["db_name"]
});

db.connect(function(err) {
    if(err) throw err;
})

function query(sql){
    return new Promise(resolve => db.query(sql, function(err, result){
        if (err) {
            console.error(err);
            resolve(null);
        } 

        resolve(result);
    }));
}

async function load_config(UUID){
    var sql = await query("SELECT * FROM config WHERE `UUID` = '" + UUID + "'");
    var result = [];

    try {
        sql.forEach(element => {
            result[element.id] = element.value;
        });
        return result;
    }
    catch (err){
        console.error(err);
        process.exit(0);
    }
}

module.exports = {query, load_config}