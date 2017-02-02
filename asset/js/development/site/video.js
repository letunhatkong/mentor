/**
 * Javascript for Screen Observation Infomation
 * @author      UTC.HuyTD
 * @date        2015/10/21
 */
var _constVideo = {
    clickEditVideoComment: 'a.clickEditVideoComment'
};

var _classedVideo = {
    barProgress: '.bar'
};

var _idsVideo = {
    dropZonePlayer: '#drop_zone_video_player',
    videoUploadError: '#videoupload-error',
    videoBrowser: '#videoBrowser',
    dropZoneArea: '#drop_zone_video_upload',
    uploadVideoButton: '#uploadVideoButton',
    containerVideoUploadForm: '#containerVideoUploadForm',
    idSessionTemp: '#idSessionTemp',

    errorSplitVideo: '#errorSplitVideo',
    minRangeSelect: '#minRangeSelect',
    durationRangeSelect: '#durationRangeSelect',
    idCommentTemp: '#idCommentTemp',
    formEdit: '#formEdit',
    videoModal: '#myVideo',
    videoEditCommentId: '#videoEditCommentId',

    selectVideoButtonApp: '#selectVideoButtonApp',
    uploadVideoMobile: '#uploadVideoMobile'
};

var dropZone = document.getElementById("drop_zone_video_upload");
var formDataVideo = new FormData();
var fileExits = false;

var infoFormVideoEvent = {
    init: function () {
        this.dropZoneListener();
        this.browserFileListener();
        this.clickUploadVideoButton();
        this.handlerModalVideoDissmiss();
        this.clickEditVideoComment();
        this.selectVideoButtonAppClick();
        this.clickOrHolderUploadVideoInMobile();
    },

    /**
     * Defined drop video zone listerner: drop,dragover,click
     * @returns {undefined}
     */
    dropZoneListener: function () {
        if (dropZone) {
            dropZone.addEventListener("dragover", function (evt) {
                infoFormVideoEvent.handleDragOverVideoFile(evt);
            }, false);
            dropZone.addEventListener('drop', function (evt) {
                var files = evt.dataTransfer.files;
                infoFormVideoEvent.handleFileSelectVideoFile(evt, files);
            }, false);
        }
        $(_idsVideo.dropZoneArea).click(function () {
            var checkDevide = isMobile.iOSAndAndroid();
            if (checkDevide) {
                $(_idsVideo.selectVideoButtonApp).trigger('click');
                return false;
            } else {
                $(_idsVideo.videoBrowser).trigger('click');
                return false;
            }
        });
    },
    selectVideoButtonAppClick: function () {
        $(_idsVideo.selectVideoButtonApp).unbind('click');
        $(_idsVideo.selectVideoButtonApp).on('click', function () {
            window.location.href = $(_idsVideo.selectVideoButtonApp).attr('href');
        });
    },
    /**
     * Function drop video zone listerner: dragover
     * @returns {undefined}
     */
    handleDragOverVideoFile: function (evt) {
        evt.stopPropagation();
        evt.preventDefault();
        evt.dataTransfer.dropEffect = 'copy';
    },

    /**
     * Function drop video zone listerner: drop
     * @returns {undefined}
     */
    handleFileSelectVideoFile: function (evt, files) {
        evt.stopPropagation();
        evt.preventDefault();
        var sumSize = 0;
        for (var i = 0; i < files.length; i++) {
            var f = files[i];
            var filename = f.name;
            $(_idsVideo.dropZoneArea).text(filename);
            var typeFile = f.type;
            var typeMPEG = typeFile.substring(0, typeFile.indexOf('/'));
            var extFile = filename.split('.').pop().toLowerCase();

            sumSize += f.size;
            var src = createObjectURLMentor(f);

            if (typeMPEG == 'video') {
                if (extFile == "mp4" || extFile == "mov" || extFile == "avi" || extFile == "m4v" || extFile == "wmv") {
                    formDataVideo = new FormData();
                    fileExits = true;
                    formDataVideo.append("videoBrowserValid", f);
                    formDataVideo.append("idSession", idSession);
                    formDataVideo.append("idCommentEdit", $(_idsVideo.videoEditCommentId).val());
                    $(_idsVideo.videoUploadError).html('');
                    $(_idsVideo.dropZonePlayer).html('<video id="player-' + i + '" src="' + src + '" class="col-11" controls></video>');

                    $("#player-" + i).on("canplay", i, function (e) {
                        var dur = this.duration;
                        if (limitDur) {
                            if (limitDur > 0 && dur >= limitDur) {
                                $(_idsVideo.videoUploadError).html('Video duration exceeds the allowed limit.');
                            } else {
                                $(_idsVideo.videoUploadError).html('');
                            }
                        }
                        return false;
                    });
                } else {
                    fileExits = false;
                    $(_idsVideo.videoUploadError).html('Unsupported video to play.</br>Notes: Good support web video formats: MP4,MOV,AVI,M4V,WMV.');
                }
            } else {
                fileExits = false;
                $(_idsVideo.dropZonePlayer).html('<video id="player-' + i + '" class="col-11" controls autoplay><source src="" type="video/mp4">Your browser does not support HTML5 video.</video>');
                $(_idsVideo.videoUploadError).html('Not the format video.Please choose a different video.</br>Notes: Good support web video formats: MP4,MOV,AVI,M4V,WMV');
            }
        }
    },

    /**
     * Defined browser File listerner: change
     * @returns {undefined}
     */
    browserFileListener: function () {
        $(_idsVideo.videoBrowser).on('change', function (evt) {
            var files = evt.target.files;
            infoFormVideoEvent.handleFileSelectVideoFile(evt, files);
        });
    },

    /**
     * Upload button in form upload video: click
     * @return {undefined}
     */
    clickUploadVideoButton: function () {
        $(_idsVideo.uploadVideoButton).on('click', function () {
            var bar = $(_classedVideo.barProgress);
            var isFormEdit = $(_idsVideo.formEdit).val();
            if (typeof isFormEdit === 'undefined') {
                if (fileExits === true) {
                    $.ajax({
                        xhr: function () {
                            var xhr = new window.XMLHttpRequest();

                            xhr.upload.addEventListener("progress", function (evt) {
                                if (evt.lengthComputable) {
                                    var percentComplete = evt.loaded / evt.total;
                                    percentComplete = parseInt(percentComplete * 100);
                                    var percentVal = percentComplete + '%';
                                    bar.width(percentVal);

                                    if (percentComplete === 100) {
                                        percentVal = '100%';
                                        bar.width(percentVal);
                                    }
                                }
                            }, false);

                            return xhr;
                        },
                        url: getBaseUrl() + '/video/edit',
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        data: formDataVideo,
                        beforeSend: function () {
                            var percentVal = '0%';
                            bar.width(percentVal);
                        },
                        success: function (result) {
                            var percentVal = '100%';
                            bar.width(percentVal);
                            if (isJsonString(result)) {
                                var objData = JSON.parse(result);
                                $(_idsVideo.videoModal).modal('hide');
                                //console.log(objData);
                                socketEvent.socketEmitCommentToServer(objData);
                                socketEvent.pushNotifyToMobile(objData);
                            } else {
                                $(_idsVideo.containerVideoUploadForm).html(result);
                                infoFormVideoEvent.dropZoneListener();
                                infoFormVideoEvent.browserFileListener();
                                infoFormVideoEvent.selectVideoButtonAppClick();
                            }
                        }
                    });
                } else {
                    $(_idsVideo.videoUploadError).html('The format is not supported.Please choose a different video.</br>Notes: Good support web video formats: MP4,MOV,AVI,M4V,WMV.');
                }
            } else if (isFormEdit == 1) {
                var limitFieldMin = $(_idsVideo.minRangeSelect).text();
                var limitDuration = $(_idsVideo.durationRangeSelect).text();
                var idComment = $(_idsVideo.idCommentTemp).val();
                $.ajax({
                    url: getBaseUrl() + '/video/splitVideo',
                    cache: false,
                    async: false,
                    type: 'POST',
                    data: {
                        startTimeSplit: limitFieldMin,
                        durationSplit: limitDuration,
                        idComment: idComment
                    },
                    success: function (result) {
                        if (isJsonString(result)) {
                            var objData = JSON.parse(result);
                            $(_idsVideo.videoModal).modal('hide');
                            //console.log(objData);
                            socketEvent.socketEmitCommentToServer(objData);
                            socketEvent.pushNotifyToMobile(objData);
                        } else {
                            $(_idsVideo.errorSplitVideo).html(result);
                        }
                    }
                });
            }
        });
    },

    /**
     * function handler when modal video upload dismiss
     * @return {undefined}
     */
    handlerModalVideoDissmiss: function () {
        $(_idsVideo.videoModal).on('hidden', function () {
            $(_idsVideo.videoEditCommentId).val('');
            $.ajax({
                url: getBaseUrl() + '/video/upload',
                cache: false,
                async: false,
                type: 'POST',
                data: {
                    idSession: idSession
                },
                success: function (result) {
                    $(_idsVideo.containerVideoUploadForm).html(result);
                    infoFormVideoEvent.dropZoneListener();
                    infoFormVideoEvent.browserFileListener();
                    infoFormVideoEvent.selectVideoButtonAppClick();
                }
            });
            var checkDevide = isMobile.iOS();
            if (checkDevide) {
                window.location = 'myapp://webCancelUploadVideo';
            }
        });
    },

    /**
     * function handler when click edit video in comment: click
     * @return {undefined}
     */
    clickEditVideoComment: function () {
        $(_constVideo.clickEditVideoComment).unbind('click');
        $(_constVideo.clickEditVideoComment).on('click', function () {
            $(_idsVideo.videoEditCommentId).val($(this).attr('data-comment-id'));
        });
    },

    /**
     * Function reload content comment by ajax
     * @param idComment
     * @param typeReload
     * @param idSession
     */
    reloadContentCommentVideo: function (idComment, typeReload, idSession) {
        $.ajax({
            type: "post",
            url: getBaseUrl() + '/observation/reloadComment',
            data: {'commentId': idComment},
            cache: false,
            success: function (data) {
                if (typeReload == 'add') {
                    $('#containerComment' + idSession).prepend(data);
                } else if (typeReload == 'edit') {
                    $('#commentRow' + idComment).replaceWith(data);
                }
                infoFormVideoEvent.clickEditVideoComment();
                infoCommentTextEvent.buttonAddCommentTextClick();
                infoCommentTextEvent.clickDeleteComment();
                likeObject.clickLike();
                infoFormVideoEvent.showHoverEditMenu();
            }
        });
    },
    /**
     * Show hover edit menu
     * @return {undefined}
     */
    showHoverEditMenu: function () {
        $(".drop-ic").unbind('mouseenter mouseleave');
        $(".drop-ic").hover(function () {
                if ($(this).is('.show-op')) {
                    $(this).removeClass('show-op');
                } else {
                    $(this).addClass('show-op');
                }
            }, function () {
                if ($(this).is('.show-op')) {
                    $(this).removeClass('show-op');
                } else {
                    $(this).addClass('show-op');
                }
            }
        );
    },
    /**
     * Click or Click and holder upload video icon in mobile
     */
    clickOrHolderUploadVideoInMobile: function () {
        $(_idsVideo.uploadVideoMobile).on("taphold", function () {
            //console.log("hold");
            window.location.href = "myapp://selectVideo";
        });
        $(_idsVideo.uploadVideoMobile).click(function () {
            //console.log("click");
            window.location.href = "myapp://takeVideo";
        });
    },

    /**
     * Show uploading video
     */
    showUploadingVideo: function () {
        $.ajax({
            type: "get",
            url: getBaseUrl() + '/site/getData',
            cache: false,
			async: false,
            success: function (data) {
                if (isJsonString(data)) {
                    var obj = $.parseJSON(data);
                    var html = '<div class="row-fluid row-fluid-top">';
                    html += '<div class="class="timeline-messages">';
                    html += '   <div class="col-12 msg-time-chat">';
                    html += '       <div class="col-6">';
                    html += '           <i class="fa fa-title float-left"></i>';
                    html += '           <a class="img-avartar">';
                    html += '               <img alt="" src="'+obj.avaPath+'">';
                    html += '               <span>'+obj.fullName+'</span>';
                    html += '           </a>';
                    html += '       </div>';
                    html += '       <div class="col-6 text-align-right count-time">';
                    html += '           <span>'+obj.date+'</span>';
                    html += '       </div>';
                    html += '       <div class="message-body">';
                    html += '           <p>';
                    html += '               <img src="/images/uploading.jpg" alt="Uploading ..." class="video">';
                    html += '           </p>';
                    html += '           <div class="clearfix"></div>';
                    html += '       </div>';
                    html += '   </div>';
                    html += '</div>';
                    html += '</div>';

                    $('.containerComment').prepend(html);
                    $('.btn-add.btn-close').trigger("click");
                }
            }
        });
    }
};

$(document).ready(function () {
    "use strict";
    infoFormVideoEvent.init();
});
