/**
 * Socket IO client
 * @author UTC.KongLtn
 * LastUpdate Dec 21, 2015.
 */


/**
 * Variables of socket Event
 * @type {{server: string, wrapId: string, createMessagesButton: string, messageMenu: string, observationMenu: string, createMessagesForm: string, allNotify: string, commentRow: string, containerCommentReply: string, dismissJoinOption: string, join: string, dismiss: string, notificationId: string, clickDelNotification: string, showNumberNotify: string, countAllNotify: string, invitationWrapper: string}}
 */
var socketEventId = {
    server: 'http://app2.unioffice.vn:3000',
    wrapId: "#notify_wrap",
    createMessagesButton: "#createMessagesButton",
    messageMenu: "#messageMenu",
    observationMenu: "#observationMenu",
    createMessagesForm: "#createMessagesForm",
    allNotify: "#allNotify_UL",
    commentRow: "#commentRow",
    containerCommentReply: "#containerCommentReply",
    dismissJoinOption: "#dismissJoinOption_",
    join: ".clickJoinTo",
    dismiss: ".clickDismiss",
    notificationId: "#notificationId",
    clickDelNotification: ".clickDelNotification",
    showNumberNotify: "#showNumberNotify",
    countAllNotify: "#countAllNotify",
    invitationWrapper: "#invitationWrapper"
};
/**
 * Current Socket
 * @type {null}
 */
var socket = null;
/**
 * Current User Id
 */
var isGuestId = null;

/**
 * Socket Event
 * @type {{init: Function, connectSocket: Function, socketEmitMessage: Function, showNumberOfCounter: Function, socketEmitCommentToServer: Function, socketEmitSessionToServer: Function, addNotificationItem: Function, socketEmitDeleteComment: Function, clickJoinOrDismissInNotification: Function, clickDeleteNotification: Function, countNotifyToRelatedSocket: Function, loadAjaxCountNotify: Function, showTheNumberOfNotification: Function, clickToNotifyDetail: Function, pushNotifyToMobile: Function, removeJoinDismissWrapper: Function, pushSessionJsonToInvitation: Function}}
 */
var socketEvent = {
    /**
     * Init function
     * @return {undefined}
     */
    init: function () {
        var isGuest = $('#isGuest').val();
        isGuestId = isGuest;
        if (isGuest !== "0") {
            this.connectSocket(isGuest);
            //this.clickJoinOrDismissInNotification();
        }
    },
    /**
     * Create socket connect to nodeJS server
     * @param userId
     * @return {undefined}
     */
    connectSocket: function (userId) {
        // Create a connection to NodeJS server port 3000
        socket = io.connect(socketEventId.server);

        /**
         * Post userId to NodeJS server
         * @return {undefined}
         */
        socket.on('connect', function () {
            socket.emit('getSocketUser', {userId: userId});
        });

        /**
         * Show message total
         * @param result
         * @return {undefined}
         */
        socket.on('countMessages', function (result) {
            var data = JSON.parse(result);
            var count = data[0].numMess;
            socketEvent.showNumberOfCounter(socketEventId.messageMenu, count);
        });

        /**
         * Push message to related sockets by ajax
         * @param idMessage
         * @return {undefined}
         */
        socket.on('messageDataToNotifyClient', function (data) {
            infoMessagesFormEvent.pushContentMessage(data.idMessage);
            if (data.ownerId !== isGuestId) {
                socketEvent.addNotificationItem(data);
            }
            socketEvent.clickDeleteNotification();
            socketEvent.loadAjaxCountNotify();

        });

        /**
         * Count the number of user who liked
         * @param likeData
         * @return {undefined}
         */
        socket.on('serverPushLikeData', function (likeData) {
            // console.log(likeData);
            // Show the number of users who liked
            $(likeId.countLike + likeData.idComment).text(likeData.countLike);
            // Change text of like action
            var me = $(likeId.clickLike + '[cmtid="' + likeData.idComment + '"]');
            if (isGuestId == likeData.ownerId) {
                me.text(likeData.likeText);
            }

            //console.log(likeData.statusForUserId);
            var listLikeDiv = $(likeId.clickLike + '[cmtid="' + likeData.idComment + '"]').parent().find(".listLiker .listLiker-div");
            if (likeData.likeText == "Unlike") {
                listLikeDiv.append("<p>" + likeData.fullNameOfCreator + "<p>");
            }
            if (likeData.likeText == "Like") {
                listLikeDiv.find("p").each(function () {
                    if ($(this).text().trim() === likeData.fullNameOfCreator.trim() || $(this).text().trim() == "") {
                        $(this).remove();
                    }
                });
            }
        });

        socket.on('likeDataToNotifyClient', function (data) {
            //console.log(data);
            socketEvent.addNotificationItem(data);
            socketEvent.clickDeleteNotification();
            socketEvent.loadAjaxCountNotify();
        });

        /**
         * Show the number of notification of Observation
         * @return {undefined}
         */
        socket.on('countObsNotification', function (data) {
            var objData = JSON.parse(data);
            socketEvent.showNumberOfCounter(socketEventId.observationMenu, objData[0].numMess);

        });

        /**
         * Push notification when user has changed about comment action
         * @return {undefined}
         */
        socket.on('commentDataToNotifyClient', function (data) {
            //console.log(data);
            // Push to notification
            if (data.ownerId !== isGuestId) {
                socketEvent.addNotificationItem(data);
            }
            // Show data comment
            if (data.typeComment == "text") {
                infoCommentTextEvent.reloadContentCommentText(data.idComment, data.idCommentParent, data.typeAction, data.idSession);
            } else if (data.typeComment == "picture") {
                infoCommentPictureEvent.reloadContentCommentImage(data.idComment, data.typeAction, data.idSession);
            } else if (data.typeComment == "video") {
                infoFormVideoEvent.reloadContentCommentVideo(data.idComment, data.typeAction, data.idSession);
            }
            socketEvent.clickDeleteNotification();
            socketEvent.loadAjaxCountNotify();

        });


        /**
         * Push notification when user create a new session
         * @return {undefined}
         */
        socket.on("sessionDataToNotifyClient", function (data) {
            //console.log(data);
            var json = JSON.stringify(data);
            socketEvent.addNotificationItem(data);
            socketEvent.clickJoinOrDismissInNotification();
            socketEvent.clickDeleteNotification();
            socketEvent.loadAjaxCountNotify();
            socketEvent.pushSessionJsonToInvitation(json);
        });

        /**
         * Delete Comment at all client (remove DOM)
         * @return {undefined}
         */
        socket.on("deleteCommentToAllClient", function (commentId) {
            $(socketEventId.commentRow + commentId).slideUp('fast', function () {
                $(this).remove();
            });
            $(socketEventId.containerCommentReply + commentId).slideUp('fast', function () {
                $(this).remove();
            });
        });

        /**
         * Display the number of notification all related socket
         * @return {undefined}
         */
        socket.on("countNotifyToClient", function (data) {
            //console.log(data);
            socketEvent.showTheNumberOfNotification(data.countNotify);
            if (data.info && data.info.selector !== undefined && data.info.html !== undefined) {
                socketEvent.removeJoinDismissWrapper(data.info);
            }
        });

        /**
         * Delete a notification at all socket
         * @return {undefined}
         */
        socket.on("delNotifyToClient", function (notifyId) {
            //console.log(notifyId);
            $(socketEventId.notificationId + '_' + isGuestId + '_' + notifyId).remove();
            socketEvent.loadAjaxCountNotify();
            $(socketEventId.invitationWrapper + ' ' + socketEventId.notificationId + '_' + isGuestId + '_' + notifyId).remove();
        });
    },
    /**
     * Client socket emit message to invited userIds
     * @param objData
     */
    socketEmitMessage: function (objData) {
        socket.emit('userCreateNewMessage', objData);
        socketEvent.pushNotifyToMobile(objData);
    },
    /**
     * Show the number of notification in left menu
     * @param keyId
     * @param count
     */
    showNumberOfCounter: function (keyId, count) {
        $(keyId + ' > span').remove();
        if (count !== undefined && count > 0) {
            $(keyId).append("<span>" + count + "</span>");
        }
    },
    /**
     * Socket push data from client to nodeJS server
     * @param objData
     * @return {undefined}
     */
    socketEmitCommentToServer: function (objData) {
        //console.log(objData);
        socket.emit('commentDataToServer', objData);
    },
    /**
     * Socket emit session data to Server (when user create or edit a session)
     * @param sesObj
     * @return {undefined}
     */
    socketEmitSessionToServer: function (sesObj) {
        //console.log(sesObj);
        socket.emit('sessionDataToServer', sesObj);
    },
    /**
     * Add a notify message into drop-down notification
     * @param data
     * @return {undefined}
     */
    addNotificationItem: function (data) {
        //console.log(data);
        $.ajax({
            url: getBaseUrl() + '/site/getTime',
            cache: false,
            type: 'POST',
            success: function (result) {
                var objData = $.parseJSON(result);

                // Get time
                var txtTime = "";
                var current = parseInt(objData.time) / 1000;
                current = parseInt(current);
                var timeMinus = current - data.time;
                timeMinus = (timeMinus < 1) ? 2 : timeMinus;

                if (timeMinus <= 24 * 3600) {
                    var interval = Math.floor(timeMinus / 3600);
                    if (interval == 1) txtTime = interval + " hour ago";
                    else if (interval > 1) txtTime = interval + " hours ago";
                    else {
                        interval = Math.floor(timeMinus / 60);
                        if (interval == 1) txtTime = interval + "minute ago";
                        else if (interval > 1) txtTime = interval + " minutes ago";
                        else txtTime = Math.floor(timeMinus) + " seconds ago";
                    }
                } else {
                    var createDate = new Date(data.time * 1000);
                    var dd = createDate.getDate(),
                        mm = createDate.getMonth(),
                        y = createDate.getFullYear(),
                        hh = createDate.getHours(),
                        ii = createDate.getMinutes();
                    txtTime = (dd + '.' + mm + '.' + y + ' ' + hh + ':' + ii);
                }
                // # Get time

                // Push Notification
                var html;
                var type = data.type.toLowerCase().trim();
                var content = data.content;
                // insert avatar path
                html = '<li id="notificationId_' + isGuestId + '_' + data.notifyId + '">' +
                '<div class="img-notif"><img src="' + data.avaPath + '">';

                // Insert logo type of notification
                if (type === "mess") {
                    html += '<span class="notif-message"><img src="/images/notif.png"></span>';
                } else if (type == "sess") {
                    html += '<span class="notif-message notif-user"><i class="fa fa-users"></i></span>';
                }
                html += '</div>';

                // insert name, time, content
                html += '' +
                '<div class="notifi-box">' +
                '<h4>' + data.fullNameOfCreator + '</h4>' +
                '<p class="notif-time">' + txtTime + '</p>' +
                '<i class="fa fa-remove notif-remove clickDelNotification" data-id="' + data.notifyId + '"></i>' +
                '<p>' + content + '</p>';

                // insert option selector for inviting user
                if (type === "sess" && data.typeAction === "add") {
                    html += '' +
                    '<div class="notif-join" id="dismissJoinOption_' + data.idSession + '">' +
                    '<div class="dismiss clickDismiss" data-id="' + data.idSession + '"><span><i class="fa fa-remove"></i></span> DISMISS</div>' +
                    '<div class="join clickJoinTo" data-id="' + data.idSession + '"><span><i class="fa fa-users"></i></span> Join</div>' +
                    '</div>';
                }

                html += '</div>';
                html += '<div class="clickToNotifyDetail" data-link="' + data.redirect + '"></div>';
                html += '</li>';
                $(socketEventId.allNotify + ' > li:nth-child(1)').after(html);
                // # Push Notification

                socketEvent.clickToNotifyDetail();
                socketEvent.clickJoinOrDismissInNotification();

            }
        });


    },

    /**
     * Socket emit delete comment action to NodeJS server
     * @param id
     * @return {undefined}
     */
    socketEmitDeleteComment: function (id) {
        socket.emit("deleteCommentToServer", id);
    },

    /**
     * Click join button or dismiss button in notification
     * @return {undefined}
     */
    clickJoinOrDismissInNotification: function () {
        $(".clickDismiss").click(function () {
            var sesId = parseInt($(this).attr('data-id'));
            var notifyId = $(this).parent().parent().parent().attr('id').split('_')[2];

            if (sesId > 0) {
                $.ajax({
                    url: getBaseUrl() + '/sessions/dismiss',
                    cache: false,
                    type: 'POST',
                    data: {
                        session_id: sesId,
                        user_id: isGuestId
                    },
                    success: function (result) {
                        if (isJsonString(result)) {
                            var objData = JSON.parse(result);
                            //console.log(objData);
                            var data = {
                                'notifyId': notifyId,
                                'selector': socketEventId.dismissJoinOption + objData.sessionId,
                                'html': '<p>You dismissed this session.</p>'
                            };
                            updateAfterClickJoinOrDismiss(data);
                        }
                    }
                });
            }
        });

        $(".clickJoinTo").click(function () {
            var sesId = parseInt($(this).attr('data-id'));
            var notifyId = $(this).parent().parent().parent().attr('id').split('_')[2];
            if (sesId > 0) {
                $.ajax({
                    url: getBaseUrl() + '/sessions/joinTo',
                    cache: false,
                    type: 'POST',
                    data: {
                        session_id: sesId,
                        user_id: isGuestId
                    },
                    success: function (result) {
                        if (isJsonString(result)) {
                            var objData = JSON.parse(result);
                            var data = {
                                'notifyId': notifyId,
                                'selector': socketEventId.dismissJoinOption + objData.sessionId,
                                'html': '<p>You joined to this session.</p>'
                            };
                            updateAfterClickJoinOrDismiss(data);
                            var urlDetail = 'window.location.replace("/observation/detail/id/' + sesId + '")';
                            setTimeout(urlDetail, 2000);
                        }
                    }
                });
            }
        });

        /**
         * Update notification after click joinTo or dismiss
         * @param data
         */
        function updateAfterClickJoinOrDismiss(data) {
            $.ajax({
                url: getBaseUrl() + '/sessions/afterClickJoinOrDismiss',
                cache: false,
                type: 'POST',
                data: {
                    notify_id: data.notifyId
                },
                success: function () {
                    socketEvent.countNotifyToRelatedSocket([isGuestId], data);
                }
            });
        }

    },
    /**
     * Click delete notification
     * @return {undefined}
     */
    clickDeleteNotification: function () {
        $(socketEventId.clickDelNotification).click(function () {
            var notifyId = $(this).attr("data-id");
            //console.log(notifyId);
            $.ajax({
                url: getBaseUrl() + '/site/delNotify',
                cache: false,
                type: 'POST',
                data: {id: notifyId},
                success: function (result) {
                    if (isJsonString(result)) {
                        var objData = JSON.parse(result);
                        //console.log(objData);
                        if (objData.status) {
                            $(socketEventId.notificationId + '_' + isGuestId + '_' + objData.notifyId).remove();
                            socket.emit("delNotifyToServer", objData.notifyId);
                        }
                    }
                }
            });
        });
    },
    /**
     * Display the number of notification by related users
     * @param invitedUser
     * @param obj
     * @return {undefined}
     */
    countNotifyToRelatedSocket: function (invitedUser, obj) {
        $.ajax({
            url: getBaseUrl() + '/site/countNotify',
            cache: false,
            type: 'GET',
            success: function (result) {
                //console.log(result);
                var data = {
                    'invitedUser': invitedUser,
                    'countNotify': parseInt(result),
                    'info': obj
                };
                socket.emit("countNotifyToServer", data);
            }
        });
    },
    /**
     * Load Ajax to count all notification
     */
    loadAjaxCountNotify: function () {
        $.ajax({
            url: getBaseUrl() + '/site/countNotify',
            cache: false,
            type: 'GET',
            success: function (result) {
                //console.log(result);
                var num = parseInt(result);
                socketEvent.showTheNumberOfNotification(num);
            }
        });
    },
    /**
     * Show the number of notification
     * @param num
     */
    showTheNumberOfNotification: function (num) {
        $(socketEventId.countAllNotify).remove();
        if (num > 0) {
            var html = '<span id="countAllNotify">' + num + '</span>';
            $(socketEventId.showNumberNotify).append(html);
        }
    },
    /**
     * Click to notification detail
     */
    clickToNotifyDetail: function () {
        $('.clickToNotifyDetail').click(function () {
            var link = $(this).attr("data-link");
            //console.log(link);
            var notifyId = $(this).parent().attr("id").split("_");
            notifyId = notifyId[notifyId.length - 1].trim();

            $.ajax({
                type: 'post',
                url: '/site/setIsRead',
                cache: false,
                data: {
                    id: notifyId
                },
                success: function (result) {
                    var check = parseInt(result);
                    console.log(check);
                    window.location.replace("/" + link);
                }
            });
        });
    },
    /**
     * Push notification to mobile device
     * @param data
     */
    pushNotifyToMobile: function (data) {
        var json = JSON.stringify(data);
        //console.log(data);
        //console.log(json);
        $.ajax({
            type: 'post',
            url: '/device/pushToDevice',
            cache: false,
            data: {
                data: json
            },
            success: function (result) {
                console.log("Push notification to mobile successfully");
                if (isJsonString(result)) {
                    var objectData = JSON.parse(result);
                    console.log(objectData);
                }
            }
        })
    },
    /**
     * Remove joinTo & dismiss Wrapper and insert a message
     * @param data
     */
    removeJoinDismissWrapper: function (data) {
        $(data.selector).parent().append(data.html);
        $(data.selector).remove();
        var invitationPage = $(socketEventId.invitationWrapper + ' ' + data.selector);
        invitationPage.parent().append(data.html);
        invitationPage.remove();
    },
    /**
     * push session data to invitation
     * @param data
     */
    pushSessionJsonToInvitation: function (data) {
        if (data && isJsonString(data)) {
            $.ajax({
                type: 'post',
                url: getBaseUrl() + '/invitations/new',
                data: {json: data},
                success: function (html) {
                    $(socketEventId.invitationWrapper).prepend(html);
                    socketEvent.clickJoinOrDismissInNotification();
                    socketEvent.clickDeleteNotification();
                }
            });
        }
    }
};

/**
 * Get short string by number of characters
 * @param str
 * @param number
 * @return string
 */
var shortString = function (str, number) {
    if (str.length > number) {
        return str.substr(0, number) + '...';
    } else return str;
};

$(document).ready(function () {
    "use strict";
    socketEvent.init();
    socketEvent.clickJoinOrDismissInNotification();
    socketEvent.clickDeleteNotification();
    socketEvent.clickToNotifyDetail();
});
