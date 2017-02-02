/**
 * Javascript for Text Comment
 * @author UTC.HuyTD
 * @author UTC.KongLtn
 * @lastUpdate   Dec 18, 2015
 */

var _commentTextClasses = {
    buttonCommentText: '.buttonCommentText',
    buttonEditCommentText: '.clickEditComment',
    clickDeleteComment: '.clickDeleteComment'
};

var _commentTextIds = {
    commentTextForm: '#commentTextForm',
    commentTextModal: '#commentText',

    idSessionCommentText: '#idSessionCommentText',
    contentCommentText: '#contentCommentText',
    parentIdCommentText: '#parentIdCommentText',
    editCommentTextId: '#editCommentTextId',

    buttonSaveCommentText: '#buttonSaveCommentText'
};

var infoCommentTextEvent = {
    init: function () {
        this.validateCommentTextForm();
        this.buttonSaveCommentTextClick();
        this.buttonAddCommentTextClick();
        this.buttonEditCommentTextClick();
        this.handlerModalCommentTextDissmiss();
        this.clickDeleteComment();
    },

    /**
     * Validate form create/edit comment
     * @returns {undefined}
     */
    validateCommentTextForm: function () {
        $(_commentTextIds.commentTextForm).validate({
            ignore: "",
            rules: {
                contentCommentText: {required: true}
            },
            messages: {
                contentCommentText: {required: "Content is required"}
            }
        });
    },

    /**
     * function handler when modal comment text dismiss
     * @return {undefined}
     */
    handlerModalCommentTextDissmiss: function () {
        $(_commentTextIds.commentTextModal).on('hidden', function () {
            //$(_commentTextIds.contentCommentText).val('');
            $(_commentTextIds.parentIdCommentText).val('');
            $(_commentTextIds.editCommentTextId).val('');
        });
    },

    /**
     * Button create comment, reply comment: click
     * @returns {undefined}
     */
    buttonAddCommentTextClick: function () {
        $(_commentTextClasses.buttonCommentText).unbind("click");
        $(_commentTextClasses.buttonCommentText).on("click", function () {
            var id = $(this).attr("value-id");
            $(_commentTextIds.parentIdCommentText).val(id);
        });
    },

    /**
     * Button edit comment: click
     * @returns {undefined}
     */
    buttonEditCommentTextClick: function () {
        $(_commentTextClasses.buttonEditCommentText).unbind("click");
        $(_commentTextClasses.buttonEditCommentText).on("click", function () {
            var commentId = $(this).attr("data-comment-id");
            infoCommentTextEvent.loadAjaxByCommentId(commentId);
        });
    },

    /**
     * Get comment content to edit
     * @returns {undefined}
     */
    loadAjaxByCommentId: function (commentId) {
        $.ajax({
            type: "post",
            url: getBaseUrl() + '/observation/getContent',
            data: {'commentId': commentId},
            cache: false,
            success: function (data) {
                var dataObj = $.parseJSON(data);
                $(_commentTextIds.contentCommentText).val(dataObj.content);
                $(_commentTextIds.parentIdCommentText).val(dataObj.idCommentParent);
                $(_commentTextIds.editCommentTextId).val(dataObj.idComment);
            }
        });
    },

    /**
     * Button save in create/edit text comment form: click
     * @returns {undefined}
     */
    buttonSaveCommentTextClick: function () {
        $(_commentTextIds.buttonSaveCommentText).on("click", function () {
            if ($(_commentTextIds.commentTextForm).valid()) {
                var idCommentParent = $(_commentTextIds.parentIdCommentText).val();
                var idComment = $(_commentTextIds.editCommentTextId).val();
                var idSesion = $(_commentTextIds.idSessionCommentText).val();
                if (parseInt(idComment) > 0) {
                    $.ajax({
                        url: getBaseUrl() + '/observation/editComment',
                        data: {
                            content: $(_commentTextIds.contentCommentText).val(),
                            id: idSesion,
                            idComment: idComment
                        },
                        async: false,
                        cache: false,
                        type: "post",
                        success: function (data) {
                            if (isJsonString(data)) {
                                var objData = JSON.parse(data);
                                if (objData.status == 'true') {
                                    $(_commentTextIds.commentTextModal).modal('hide');
                                    objData.content = shortString(objData.content, 88);
                                    //console.log(objData);
                                    socketEvent.socketEmitCommentToServer(objData);
                                    socketEvent.pushNotifyToMobile(objData);
                                    $(_commentTextIds.contentCommentText).val('');
                                }
                            }
                        }
                    });
                } else {
                    $.ajax({
                        url: getBaseUrl() + '/observation/createComment',
                        data: {
                            content: $(_commentTextIds.contentCommentText).val(),
                            id: idSesion,
                            parent_id: idCommentParent,
                            idComment: idComment
                        },
                        async: false,
                        cache: false,
                        type: "post",
                        success: function (data) {
                            if (isJsonString(data)) {
                                var objData = JSON.parse(data);
                                if (objData.status == 'true') {
                                    $(_commentTextIds.commentTextModal).modal('hide');
                                    objData.content = shortString(objData.content, 88);
                                    //console.log(objData);
                                    socketEvent.socketEmitCommentToServer(objData);
                                    socketEvent.pushNotifyToMobile(objData);
                                    $(_commentTextIds.contentCommentText).val('');
                                }
                            }
                        }
                    });
                }
            }
        })
    },

    /**
     * Reload text comment
     * @returns {undefined}
     */
    reloadContentCommentText: function (idComment, idCommentParent, typeReload, idSession) {
        $.ajax({
            type: "post",
            url: getBaseUrl() + '/observation/reloadComment',
            data: {'commentId': idComment},
            cache: false,
            success: function (data) {
                if (typeReload == 'add') {
                    if (idCommentParent > 0) {
                        $('#containerCommentReply' + idCommentParent).prepend(data);
                    } else {
                        $('#containerComment' + idSession).prepend(data);
                    }
                } else if (typeReload == 'edit') {
                    $('#commentRow' + idComment).replaceWith(data);
                }
                infoCommentTextEvent.buttonAddCommentTextClick();
                infoCommentTextEvent.buttonEditCommentTextClick();
                infoCommentTextEvent.clickDeleteComment();
                likeObject.clickLike();
                infoFormVideoEvent.showHoverEditMenu();
            }
        });
    },

    /**
     * Button delete comment: click
     * @returns {undefined}
     */
    clickDeleteComment: function () {
        $(_commentTextClasses.clickDeleteComment).unbind("click");
        $(_commentTextClasses.clickDeleteComment).on("click", function () {
            var commentId = $(this).attr("data-delete-id");
            if (confirm('Are you sure you want to delete this comment?')) {
                $.ajax({
                    type: "post",
                    cache: false,
                    url: getBaseUrl() + '/observation/deleteComment',
                    async: false,
                    data: {
                        'commentId': commentId
                    },
                    success: function (data) {
                        if (data == "success") {
                            $('#commentRow' + commentId).slideUp('fast', function () {
                                $(this).remove();
                            });
                            $('#containerCommentReply' + commentId).slideUp('fast', function () {
                                $(this).remove();
                            });
                            socketEvent.socketEmitDeleteComment(commentId);
                        }
                    }
                });
            }
        });
    }
};

$(document).ready(function () {
    "use strict";
    infoCommentTextEvent.init();
});
    