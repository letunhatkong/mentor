/**
 * Javascript for Screen Session
 * @author UTC.HuyTD
 * @author Kong.Ltn
 * Last Update - Dec 11, 2015
 */

var _sessionIds = {
    sessionsForm: '#sessions-form',
    buttonCancel: '#buttonCancel',
    buttonOK: '#buttonOK',
    // Input form data
    title: 'input[name=title]',
    date: 'input[name=date]',
    content: 'textarea[name=content]',
    topic: 'select.SlectBox'
};

/**
 * Session events
 * @type {{init: Function, validateSessionForm: Function, buttonCancelClick: Function, buttonOkClick: Function}}
 */
var infoSessionsFormEvent = {
    init: function () {
        this.validateSessionForm();
        this.buttonCancelClick();
        this.buttonOkClick();
    },

    /**
     * Validate form create/edit session
     * @returns {undefined}
     */
    validateSessionForm: function () {
        $(_sessionIds.sessionsForm).validate({
            ignore: ":hidden:not(select)",
            rules: {
                "userInvited[]": {required: true},
                title: {required: true},
                date: {required: true},
                content: {required: true},
                topic: {
                    number: true,
                    required: true
                }
            },
            messages: {
                "userInvited[]": {
                    required: "Please choose a user"
                },
                title: {required: "Title is required"},
                date: {required: "Date is required"},
                content: {required: "Description is required"},
                topic: {
                    number: "Please choose a topic",
                    required: "Please choose a topic"
                }
            },
            errorPlacement: function (error, element) {
                if (element.hasClass("SlectBox")) {
                    $(element.parent()).append(error);
                } else if (element.hasClass("atc")) {
                    $(element.parent()).append(error);
                } else{
                    error.insertAfter(element);
                }
            }
        });
    },

    /**
     * Button cancel in create/edit session form: click
     * @returns {undefined}
     */
    buttonCancelClick: function () {
        $(_sessionIds.buttonCancel).on("click", function () {
            window.history.back();
        });
    },

    /**
     * Button ok in create/edit session form: click
     * @returns {undefined}
     */
    buttonOkClick: function () {
        $(_sessionIds.buttonOK).on("click", function () {
            if ($(_sessionIds.sessionsForm).valid()) {
                var data = $(_sessionIds.sessionsForm).serialize();
                //console.log(data);
                $.ajax({
                    url: getBaseUrl() + '/sessions/saveSession',
                    cache: false,
                    async: false,
                    type: 'POST',
                    data: data,
                    success: function (result) {
                        if (isJsonString(result)) {
                            var objData = JSON.parse(result);
                            objData.invitedUser.push(isGuestId);
                            console.log(objData);
                            if (objData.status == "true") {
                                socketEvent.socketEmitSessionToServer(objData);
                                socketEvent.pushNotifyToMobile(objData);
                                window.location.href = getBaseUrl() + "/planning";
                            }
                        }
                    }
                });
            }
        });
    }
};

$(document).ready(function () {
    "use strict";
    infoSessionsFormEvent.init();
});