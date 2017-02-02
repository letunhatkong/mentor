/**
 * Javascript for user stories at note page
 * @author UTC.KongLtn
 * Last Update: 5/11/2015
 */

/**
 * Variables is using for form actions in note page
 * @type {{addFormId: string, editFormId: string, submitAddArchiveForm: string, submitEditArchiveForm: string, clickDelete: string, clickCancel: string, bottomBarNotes: string, clickEditNotes: string, textAreaNote: string, tmpNoteContent: string}}
 * @private
 */
var _notesId = {
    addFormId: "#create-archives-form",
    editFormId: "#edit-archives-form",
    submitAddArchiveForm: "#submitAddArchiveForm",
    submitEditArchiveForm: "#submitEditArchiveForm",
    clickDelete : "#clickDeleteEditArchiveForm",

    clickCancel: "#clickCancelNotesForm",
    bottomBarNotes: "#bottomBarNotes",
    clickEditNotes: "#clickEditNotes",
    textAreaNote: "#textAreaNote",
    tmpNoteContent: '#tmpNoteContent'
};

/**
 * Note page events
 * @type {{init: Function, validateForm: Function}}
 */
var notesEvent = {
    init: function () {
        this.validateForm();
    },
    /**
     * Detect click cancel button & edit button
     */
    validateForm: function() {
        $(_notesId.submitAddArchiveForm).on("click", function(){

        });

        // Click cancel button
        $(_notesId.clickCancel).click(function(){
            var t = $(_notesId.textAreaNote);
            t.prop("readonly", true);
            $(_notesId.textAreaNote).val($(_notesId.tmpNoteContent).val());
            $(_notesId.bottomBarNotes).toggle();
        });
        // Click edit button
        $(_notesId.clickEditNotes).click(function(){
            var t = $(_notesId.textAreaNote);
            var check = t.attr("readonly");
            (check) ? t.prop("readonly", false) : t.prop("readonly", true);

            $(_notesId.bottomBarNotes).toggle();
        })
    }
    //ajaxEditNote: function(){
    //    $.ajax({
    //        type: 'post',
    //        url: getBaseUrl() + '/notes',
    //        cache: false,
    //        success : function(data){
    //            $(location).attr('href', '/notes');
    //    }
    //    });
    //}
};

$(document).ready(function () {
    "use strict";
    notesEvent.init();
});