/**
 * Javascript for count time life of a activated session
 * @author UTC.KongLtn
 * Last Update: Dev 09, 2015
 */

/**
 * countdownEvent
 * @type {{init: Function, onLoadPastTime: Function}}
 */
var countdownEvent = {
    init: function () {
        this.onLoadPastTime();
    },
    onLoadPastTime: function () {
        var pastPoint = $('#getTimeLifeOfSession').attr('data-time');
        pastPoint = parseInt(pastPoint);

        if (pastPoint > 0) {
            $.ajax({
                url: '/site/getTime',
                cache: false,
                success: function (data) {
                    if (isJsonString(data)) {
                        data = JSON.parse(data);
                        repeatCount(pastPoint,data.time);
                    }
                }
            });
        }
    }
};

/**
 * Insert 0 before a number
 * @param number
 * @param max
 * @returns {*}
 */
function pad(number, max) {
    number = number.toString();
    return number.length < max ? pad("0" + number, max) : number;
}

/**
 * Repeat count up
 * @param past
 * @param now
 */
function repeatCount(past, now) {
    past2 = past;
    now2 = now;
    var day = 0, hour = 0, min = 0, sec = 0;

    var diff = now - past;
    if (diff > 0) {
        day = Math.floor(diff / 86400);
        hour = Math.floor((diff - day * 86400) / 3600);
        min = Math.floor((diff - day * 86400 - hour * 3600) / 60);
        sec = Math.floor(diff - day * 86400 - hour * 3600 - min * 60);
    }
    $('#pastTimeDay').text(pad(day, 2));
    $('#pastTimeHour').text(pad(hour, 2));
    $('#pastTimeMin').text(pad(min, 2));
    $('#pastTimeSec').text(pad(sec, 2));
    setTimeout("repeatCount(past2,now2+1)", 1000);
}

$(document).ready(function () {
    "use strict";
    countdownEvent.init();
});