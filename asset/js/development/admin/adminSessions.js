/**
 * Javascript for user stories in Sessions section of admin page
 * @author UTC.KongLtn
 * Last Update: 5/11/2015
 */


$(document).ready(function () {
    /**
     * Click Show/Hide comments
     * @return undefined
     */
    $('.adminClickShowComment').click(function () {
        var sesId = $(this).attr("data-id");
        var commentObj = $(".cmt_session_" + sesId);
        var iTagObj = $(this).find("i");
        var className = iTagObj.attr('class');
        var isPlusIcon = (className.indexOf("fa-plus-circle") != -1);
        if (isPlusIcon) {
            iTagObj.removeClass("fa-plus-circle");
            iTagObj.addClass("fa-minus-circle");
            commentObj.removeClass("hidden");
        } else {
            iTagObj.removeClass("fa-minus-circle");
            iTagObj.addClass("fa-plus-circle");
            commentObj.addClass("hidden");
        }
    });

    /**
     * Remove Comment by Ajax
     * @param id
     * @return undefined
     */
    function loadAjaxRemoveCommentInAdmin(id) {
        $.ajax({
            'type': 'post',
            'url': '/admin/default/removeComment',
            'cache': false,
            'data': {
                'id': id
            },
            success: function (data) {
                $('#commentIdAdmin_' + id).remove();
                var searchTxt = 'tr[data-parentid=' + id + ']';
                $('.tBodyGetCmt').find(searchTxt).remove();
            }
        });
    }

    /**
     *  Click remove comment of Session
     * @return undefined
     */
    $('.clickRemoveCommentAdmin').click(function () {
        var cmtId = $(this).attr("data-id");
        if (confirm('Delete comment?')) {
            loadAjaxRemoveCommentInAdmin(cmtId);
        }
    });
    /**
     * Click remove sesison
     */
    $('.clickRemoveSession').click(function () {
        var sessionId = $(this).attr('data-id');
        if (confirm('Delete session ?')) {
            window.location.replace(getBaseUrl() + '/admin/default/removeSession/id/' + sessionId);
        }
    });
    /**
     * Click remove document
     */
    $('.clickDelDocument').click(function () {
        var docId = $(this).attr('data-id');
        if (confirm('Delete document ?')) {
            window.location.replace(getBaseUrl() + '/admin/default/delDocument/id/' + docId);
        }
    });
    //
});
