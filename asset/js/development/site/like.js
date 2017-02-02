/**
 * Javascript for user stories about like action
 * @author UTC.KongLtn
 * Last Update: Nov 12, 2015
 */

/**
 * Variables is using for like actions
 * @type {{
 *      clickLike: string,
 *      countLike: string
 *      }}
 */
var likeId = {
    clickLike: ".clickLike",
    countLike: "#countLike"
};

/**
 * Like Object
 * @type {{
 *      init: Function,
 *      clickLike: Function,
 *      ajaxClickLike: Function
 *      }}
 */
var likeObject = {
    init: function () {
        this.clickLike();
    },
    /**
     * Event: Click Like
     * @return {undefined}
     */
    clickLike: function () {
        // Click edit button
        $(likeId.clickLike).unbind('click');
        $(likeId.clickLike).on('click', function () {
            var cmtId = $(this).attr('cmtId');
            likeObject.ajaxClickLike(cmtId);
        });
    },

    /**
     * Load ajax after click like, change like text, count liked users
     * @param id
     * @return {undefined}
     */
    ajaxClickLike: function (id) {
        $.ajax({
            type: 'post',
            url: getBaseUrl() + '/observation/like',
            data: {'id': id},
            cache: false,
            success: function (data) {
                if (isJsonString(data)) {
                    data = JSON.parse(data);
                    socket.emit("postLikeCount", data);
                    // Push to mobile device
                    socketEvent.pushNotifyToMobile(data);
                }
            }
        });
    }
};

$(document).ready(function () {
    "use strict";
    likeObject.init();
});