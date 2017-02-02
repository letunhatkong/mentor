/**
 * Javascript for Screen Planning
 * @author UTC.KongLtn
 * Last update Dec, 11, 2015
 */

var _planId = {
    clickSetActive: ".clickSetActive",
    clickDeleteSession: ".delSessionIcon"
};

/**
 * Planning Event
 * @type {{init: Function, clickSetActivePlanning: Function, setActiveSessionByAjax: Function, clickDeleteSession: Function}}
 */
var planningEvent = {
    init: function () {
        this.clickSetActivePlanning();
        this.clickDeleteSession();
    },

    /**
     * Button set active in planning screen: click
     * @returns {undefined}
     */
    clickSetActivePlanning: function () {
        $(_planId.clickSetActive).click(function () {
            console.log($(this).attr("data-id"));
            var sessionId = parseInt($(this).attr("data-id"));
            planningEvent.setActiveSessionByAjax(sessionId);
        });
    },

    /**
     * Set active or deactive when click button set active
     * @returns {undefined}
     */
    setActiveSessionByAjax: function (idSession) {
        $.ajax({
            url: getBaseUrl() + '/planning/setActive',
            type: 'post',
            data: {
                'idSession': idSession
            },
            cache: false,
            async: false,
            success: function (data) {
                console.log(data);
                return data;
            }
        });
    },
    /**
     * Click delete a session
     */
    clickDeleteSession: function () {
        ///sessions/delete/id
        $(_planId.clickDeleteSession).click(function () {
            var sessionId = $(this).attr("data-id");
            if (confirm('Are you sure you want to delete this session?')) {
                $.ajax({
                    type: 'post',
                    url: '/sessions/delete',
                    data: {id: sessionId},
                    cache: false,
                    success: function (data) {
                        window.location.replace("/planning");
                    }
                })
            }
        })
    }
};

$(document).ready(function () {
    "use strict";
    planningEvent.init();
});