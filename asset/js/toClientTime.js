/**
 * Javascript for convert time (using moment JS)
 * @author UTC.KongLtn
 * Last Update: Dec 31, 2015
 */


var clientTimeId = {
    getArchiveClientTime: ".getArchiveClientTime",
    invitationTime: "#invitationWrapper .notif-time",
    observationToClientTime: ".observationToClientTime"
};


var clientTime = {
    init: function () {
        this.convertInArchive();
        this.convertInInvitation();
    },
    /**
     * Convert to client time in invitation page
     */
    convertInInvitation: function () {
        var invitationArray = $(clientTimeId.invitationTime);
        //var observationDetailArray = $(clientTimeId.observationToClientTime);
        $.ajax({
            type: 'get',
            url: getBaseUrl() + '/site/getTime',
            success: function (data) {
                if (isJsonString(data)) {
                    var obj = JSON.parse(data);
                    $.each(invitationArray, function (index, val) {
                        var str = $(this).text().trim();
                        $(this).text(clientTime.getClientTimeNotifyType(str, obj.time));
                    });

                    //$.each(observationDetailArray, function (index, val) {
                    //    var str2 = $(this).text().trim();
                    //    $(this).text(clientTime.getClientTimeNotifyType(str2, obj.time));
                    //})
                }
            }
        });
    },
    /**
     * Convert to client time in archive page
     */
    convertInArchive: function () {
        var archiveArray = $(clientTimeId.getArchiveClientTime);
        $.each(archiveArray, function (index, val) {
            var str = $(this).text().trim();
            var createDateToUTCSeconds = moment.utc(str).unix();
            $(this).text(clientTime.getArchiveTime(createDateToUTCSeconds));
        })
    },
    /**
     * Get client Time in archive page
     * @param seconds
     * @returns string
     */
    getArchiveTime: function (seconds) {
        var createDate = moment.utc(seconds * 1000);
        var nowDate = moment.utc();
        //var diff = nowDate.diff(createDate, "days");
        var firstText = "";

        var dCD = createDate.get('date'), mCD = createDate.get('month'), yCD = createDate.get('year');
        var delTimeCreateDate = moment([yCD, mCD, dCD]);
        var dNow = nowDate.get('date'), mNow = nowDate.get('month'), yNow = nowDate.get('year');
        var delTimeNowDate = moment([yNow, mNow, dNow]);
        var diff = delTimeNowDate.diff(delTimeCreateDate, "days");

        if (diff == 0) {
            firstText = "Today ";
        } else if (diff == 1) {
            firstText = "Yesterday ";
        } else if (diff > 1) {
            return (createDate.local().format("DD.MM.YYYY HH:mm"));
        }
        return firstText + " " + createDate.local().format("HH:mm");
    },
    /**
     * Get the different past time and current time
     * @param seconds
     * @returns string
     */
    getDiffTime: function (seconds) {
        var createDate = moment.utc(seconds * 1000);
        var nowDate = moment.utc();
        var diff = nowDate.diff(createDate, "seconds");
        diff = (diff == 0 || diff == 1) ? 2 : diff;
        if (2 <= diff && diff <= 45) {
            return diff + " seconds ago";
        } else if (86400 < diff) {
            return createDate.local().format("DD.MM.YYYY HH:mm");
        } else {
            return createDate.fromNow();
        }
    },
    /**
     * Get date by format YY.MM.DD
     * @param seconds
     * @returns string
     */
    getDateYYMMDD: function (seconds) {
        return moment.utc(seconds * 1000).local().format("YY.MM.DD");
    },


    /**
     * Get client time for notification from UTC second
     * @param seconds
     * @param current
     * @returns {string}
     */
    getClientTimeNotifyType: function (seconds, current) {
        var txtTime = "";
        seconds = parseInt(seconds);
        current = parseInt(current);
        var timeMinus = current - seconds;
        timeMinus = (0 < timeMinus && timeMinus < 2) ? 2 : timeMinus;

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
            //txtTime = (dd + '.' + mm + '.' + y + ' ' + hh + ':' + ii);
            txtTime = moment.utc(seconds * 1000).local().format("DD.MM.YYYY HH:mm");
        }
        return txtTime;
    }
};

$(document).ready(function () {
    'use strict';
    if (isGuestId > 0) {
        //console.log("start");
        clientTime.init();
    }
});