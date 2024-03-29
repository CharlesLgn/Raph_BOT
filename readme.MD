# Raph_BOT

Raph_BOT is a multi-user and multi-instances twitch chat bot

![Raph_BOT](./screenshot.png)

# Prerequisite

Linux package :
- apache2 (or any web server)
- PHP7.0
- MariaDB / MySQL
- NodeJS (^v16)
- screen

Node module required (package.json provided): 
- console-stamp@3.0.3
- mysql.js@1.0.2
- socket.io@2.4.1
- tmi.js@1.8.5

# First time setup (Debian based distro)

- Move "config.json" from the template folder to the root folder (mv ./template/config.json ./config.json)
- Setup your database config (user, password, database) in the file "config.json"
- Use raph_bot.sql to configure your database with the required tables
- Configure your web server so it uses the 'www' folder as document root 
- Make sure the folder 'core/logs' can be written by the web server

# Configure bot
- Log into the dashboard, default user is 'admin' with password '0'

# Limitation / To Do

- One port per instance is required because of the current way users are handled.

# DISCLAIMER

This software is provided "as is", not warranty it work.
