/**
 * Notification - NodeJS server
 * @author UTC.KongLtn
 * Last update on Nov 19, 2015.
 */


var app = require("express")();
var http = require("http").Server(app);
var io = require("socket.io")(http);


/**
 * Object stores all socket.
 * @type {{sockets: {}, addSocket: Function, deleteSocket: Function, getSocketByName: Function, existsSocket: Function}}
 * @return {undefined}
 */
var allSockets = {
    /**
     * A storage object to hold the sockets
     */
    sockets: {},

    /**
     * Adds a socket to the storage object so it can be located by name
     * @param socket
     * @param name
     * @return {undefined}
     */
    addSocket: function (socket, name) {
        if (this.sockets[name] === undefined) {
            this.sockets[name] = [];
            console.log("Create new array socket");
        }
        this.sockets[name].push(socket);
        console.log("Add socket to array socket of client User:" + name + " - num socket in array: " + this.sockets[name].length);

    },
    /**
     * Delete socket by socketId
     * @param socketId
     * @return {undefined}
     */
    deleteSocket: function (socketId) {
        for (var userId in this.sockets) {
            if (this.sockets.hasOwnProperty(userId)) {
                var index = this.sockets[userId].indexOf(socketId);
                if (index !== -1) {
                    this.sockets[userId].splice(index, 1);
                    console.log(userId + "Delete socket" + socketId);
                }
            }
        }
    },
    /**
     * Throws an exception if the name is not valid
     * @param name
     * @returns {*} Returns a socket from the storage object based on its name
     */
    getSocketByName: function (name) {
        if (this.sockets.hasOwnProperty(name)) {
            return this.sockets[name];
        } else {
            return [];
        }
    },
    /**
     * Check exists of Socket by id
     * @param name
     * @returns {boolean}
     */
    existsSocket: function (name) {
        return (this.sockets[name] !== undefined);
    }
};


/**
 * Default Namespace
 */
app.get("/", function (req, res) {
    res.sendFile(__dirname + '/index.html');
});

/**
 *  This is auto initiated event when Client connects to Your server.
 */
io.on('connection', function (socket) {
    // When user login, f5, ...
    socket.on('getSocketUser', function (data) {
        allSockets.addSocket(socket.id, data.userId);
        //countMessage(data.userId, function (data2) {
        //    socket.emit("countMessages", data2);
        //});
        countClients();
    });

    // User create a new message
    socket.on('userCreateNewMessage', function (data) {
        //console.log(data);
        if (data.invitedUser !== undefined && data.invitedUser.length > 0) {
            data.invitedUser.forEach(function (userId) {
                //countMessForSocketsByUserId(userId, function () {
                //    emitByEvent("messageDataToNotifyClient", userId, data);
                //});
                emitByEvent("messageDataToNotifyClient", userId, data);
            });
        }
    });

    // Receive like data from client and emit to all socket
    socket.on("postLikeCount", function (data) {
        // Change data at all socket
        io.sockets.emit("serverPushLikeData", data);
        // Push notification to related socket
        if (data.invitedUser !== undefined && data.invitedUser.length > 0 && data.likeText == "Unlike") {
            data.invitedUser.forEach(function (userId) {
                if (data.ownerId != userId) {
                    emitByEvent("likeDataToNotifyClient", userId, data);
                }
            });
        }
    });

    // Get comment data from client
    socket.on("commentDataToServer", function (data) {
        //console.log(commentData);
        if (data.invitedUser !== undefined && data.invitedUser.length > 0) {
            data.invitedUser.forEach(function (userId) {
                emitByEvent("commentDataToNotifyClient", userId, data);
                //console.log("push comment data to notification ");
            });
        }
    });

    // Delete comment action from client
    socket.on("deleteCommentToServer", function (commentId) {
        io.sockets.emit("deleteCommentToAllClient", commentId);
    });

    // Socket delete notification by notifyId
    socket.on("delNotifyToServer", function (notifyId) {
        io.sockets.emit("delNotifyToClient", notifyId);
    });

    // Socket receive session data from Client (when user create or edit a session)
    socket.on("sessionDataToServer", function (sessionData) {
        //console.log(sessionData);
        if (sessionData.invitedUser !== undefined && sessionData.invitedUser.length > 0) {
            sessionData.invitedUser.forEach(function (userId) {
                if (userId != sessionData.ownerId) {
                    emitByEvent("sessionDataToNotifyClient", userId, sessionData);
                }
            });
        }
    });

    // Update the number of notification
    socket.on("countNotifyToServer", function (data) {
        if (data.invitedUser !== undefined && data.invitedUser.length > 0) {
            data.invitedUser.forEach(function (userId) {
                emitByEvent("countNotifyToClient", userId, data);
            });
        }
    });

    // When user exit
    socket.on('disconnect', function () {
        allSockets.deleteSocket(socket.id);
        console.log("socket " + socket.id + " disconnected");
        countClients();
    });
});

/**
 * Emit to related clients and push data to related clients by event name
 * @param eventName
 * @param userId
 * @param data
 */
function emitByEvent(eventName, userId, data) {
    if (allSockets.sockets.hasOwnProperty(userId) && allSockets.existsSocket(userId)) {
        var sockets = allSockets.getSocketByName(userId);
        sockets.forEach(function (socId) {
            if (io.sockets.connected[socId] !== undefined) {
                io.sockets.connected[socId].emit(eventName, data);
            }
        });
    }
}


/**
 * Count clients
 * @return {undefined}
 */
function countClients() {
    console.log("Clients " + io.engine.clientsCount);
    console.log(allSockets.sockets);
}


/**
 * Sever is running on port 3000
 */
http.listen(3000, function () {
    console.log("Listening on 3000");
});







