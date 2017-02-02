function createObjectURLMentor(file) {
    if (window.webkitURL) {
        return window.webkitURL.createObjectURL(file);
    } else if (window.URL && window.URL.createObjectURL) {
        return window.URL.createObjectURL(file);
    } else {
        return null;
    }
}
function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
var isMobile = {
    Android: function () {
        return /Android/i.test(navigator.userAgent);
    },
    BlackBerry: function () {
        return /BlackBerry/i.test(navigator.userAgent);
    },
    iOS: function () {
        return /iPhone|iPad|iPod/i.test(navigator.userAgent);
    },
    Windows: function () {
        return /IEMobile/i.test(navigator.userAgent);
    },
    any: function () {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows());
    },
    iOSAndAndroid: function () {
        return (isMobile.Android() || isMobile.iOS());
    },
    webViewIOS: function () {
        return /(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(navigator.userAgent);
    },
    webViewAndroid: function () {
        return /Mentor-Android/i.test(navigator.userAgent);
    },
    webView: function () {
        return (isMobile.webViewIOS() || isMobile.webViewAndroid());
    }
};

var _idsCommon = {
    uploadVideoButtonApp: '#uploadVideoButtonApp',
    uploadVideoButton: '#uploadVideoButton',
    uploadImageButtonApp: '#buttonSaveCommentImageApp',
    uploadImageButton: '#buttonSaveCommentImage',
    dropZoneVideoProgress: '#drop_zone_video_progress'
};

var commonEvent = {
    init: function () {
        this.showButtonSaveUploadVideoInMobile();
    },

    showButtonSaveUploadVideoInMobile: function () {
        var checkDevide = isMobile.iOSAndAndroid();
        if (checkDevide) {
            $(_idsCommon.uploadVideoButton).removeClass('active');
            $(_idsCommon.uploadVideoButtonApp).addClass('active');
            $(_idsCommon.dropZoneVideoProgress).hide();
        } else {
            $(_idsCommon.uploadVideoButton).addClass('active');
            $(_idsCommon.uploadVideoButtonApp).removeClass('active');
        }
        /*
         var checkAndroid = isMobile.Android();
         if(checkAndroid){
         $(_idsCommon.uploadImageButton).removeClass('active');
         $(_idsCommon.uploadImageButtonApp).addClass('active');
         }else{
         $(_idsCommon.uploadImageButton).addClass('active');
         $(_idsCommon.uploadImageButtonApp).removeClass('active');
         }
         */
    }
};

$(document).ready(function () {
    "use strict";
    commonEvent.init();
    //console.log(isMobile.webView());
});

function getIdSessionFromAndroid() {
    var idSessionCommentVideo = $('#idSessionCommentVideo').val();
    window.MentorApp.receiveIdSession(idSessionCommentVideo);
}

function getCommentIdFromAndroid() {
    var videoEditCommentId = $('#videoEditCommentId').val();
    window.MentorApp.receiveCommentId(videoEditCommentId);
}

function getIdUserFromAndroid() {
    var idUserComment = $('#idUserComment').val();
    window.MentorApp.receiveIdUser(idUserComment);
}

function getlimitDurationFromAndroid() {
    var limitDurationVideo = $('#limitDurationVideo').val();
    window.MentorApp.receiveLimitDuration(limitDurationVideo);
}

function getIdUserLoginedFromAndroid() {
    var idUserLogined = $('#isGuest').val();
    window.MentorApp.receiveIdUserLogined(idUserLogined);
}
function getBadgeNumberFromAndroid() {
    var badgeNumber = $('#countAllNotify').html();
    window.MentorApp.receiveBadgeNumber(badgeNumber);
}


function getArchiveNameFromAndroid() {
    var archiveName = $('#newFileArchiveForm .archiveName').val();
    window.MentorApp.receiveArchiveName(archiveName);
}

function getArchiveIdFromAndroid() {
    var archiveId = $('#inputArchiveId').val();
    window.MentorApp.receiveArchiveId(archiveId);
}