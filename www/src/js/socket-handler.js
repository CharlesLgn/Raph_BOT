var socket = null;
var core_state = false;

function twitch_state(state){
  if(state){
    document.getElementById('twitch-statut').className = "progress-bar progress-bar-success";
    document.getElementById('twitch-statut').innerHTML = "Connected";
  }
  else{
    document.getElementById('twitch-statut').className = "progress-bar progress-bar-danger";
    document.getElementById('twitch-statut').innerHTML = "Disconnected";
  }
}

function shout(data){
  document.getElementById('shout-bar').style.width = (data['current'] / data['max']) * 100 + "%";
  document.getElementById('shout-text').innerHTML = data['current'] + " / " + data['max'];
}

function trigger_time(data){
  document.getElementById('auto-cmd-time-bar').style.width = (data['current'] / data['max']) * 100 + "%";
  document.getElementById('auto-cmd-time-text').innerHTML = data['current'] + " / " + data['max'];
  document.getElementById('auto-cmd-time-counter').innerHTML = data['nb'];
}

function trigger_msg(data){
  document.getElementById('auto-cmd-msg-bar').style.width = (data['current'] / data['max']) * 100 + "%";
  document.getElementById('auto-cmd-msg-text').innerHTML = data['current'] + " / " + data['max'];
  document.getElementById('auto-cmd-msg-counter').innerHTML = data['nb'];
}

function start_stop(){
  if(core_state){
    swal({
      title: 'Stop',
      text: "Are you sure you want to stop the bot ?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
          socket.emit('stop-core');
        }
    })
  }
  else{
    $.post("src/php/core-exec.php", {action : "start"}, function(data){console.log(data)});
    document.getElementById("log").innerText = "";
    document.getElementById("btn-start-stop").disabled = true;
  }
}

$.getJSON("src/port.json", function(JSON){
  socket = io.connect('http://'+ window.location.hostname + ':' +  JSON['socket_port']);
}).done(function(){
  console.log("JSON OK");
  socket.on('connect', function(){
    document.getElementById('core-statut').className = "progress-bar progress-bar-success";
    document.getElementById('core-statut').innerHTML = "Connected";

    // Buttons
    document.getElementById("btn-start-stop").className = "btn btn-danger";
    document.getElementById("ico-start-stop").className = "glyphicon glyphicon-stop";
    document.getElementById("btn-start-stop").disabled = false;

    // Toggle
    core_state = true;
  })

  socket.on('disconnect', function(){
    document.getElementById('core-statut').className = "progress-bar progress-bar-danger";
    document.getElementById('core-statut').innerHTML = "Disconnected";

    document.getElementById('twitch-statut').className = "progress-bar progress-bar-warning";
    document.getElementById('twitch-statut').innerHTML = "Waiting for the core";

    // Buttons
    document.getElementById("btn-start-stop").className = "btn btn-success";
    document.getElementById("ico-start-stop").className = "glyphicon glyphicon-play";
    core_state = false;
  })

  // Update
  socket.on('update', function(json){
    data = JSON.parse(json);

    twitch_state(data['twitch']);
    shout(data['shout']);
    trigger_time(data['trigger_time']);
    trigger_msg(data['trigger_msg']);
  })

  // Log
  socket.on('log', function(msg){
    document.getElementById("log").innerText += msg;
  })

}).fail(function(){
  console.log("Unable to load config file.");
});

