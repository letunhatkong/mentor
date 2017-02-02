/**
 * Javascript for user stories in archive page
 * @author UTC.KongLtn
 * Last Update: Dec 22, 2015
 */

var _archivesId = {
    clickNewLink: '.btn-add.btn-link',
    clickNewFile: '.btn-add.btn-file',
    clickEditArchive: '.clickEditArchive',
    editArchiveModal: '#editArchiveModal',
    clickDelArchive: '.clickDelArchive',
    plusButton: '#plusButtonInArchive',
    xButton: '#xButtonInArchive'
};

var newFileArchiveForm = {
    id: '#newFileArchiveForm',
    modal: '#newFileArchiveModal',
    text: "#tit-modal-image",

    name: '#newFileArchiveForm input[name="archiveName"]',
    file: '#newFileArchiveForm input[name="archivePath"]',
    link: '#archiveLinkId',
    fileType: '#archiveFileGroup',
    linkType: '#archiveLinkGroup',

    archiveId: "#inputArchiveId",
    archiveType: "#typeOfArchive",
    actionType: "#typeOfAction",

    submit: '#buttonOkNewFileArchive',
    progress: "#progressArchiveFile"
};

var archivesFormEvent = {
    init: function () {
        this.validateForm();
        this.clickIconToEditArchive();
    },

    /**
     * Validate create archive form, edit archive form
     * @return undefined
     */
    validateForm: function () {
        // Create file Archive Form validate
        $(newFileArchiveForm.id).validate({
            rules: {
                archiveName: {required: true},
                archivePath: {required: true}
            },
            messages: {
                archiveName: {required: "File Name is required."},
                archivePath: {required: "File is required."}
            }
        });
        // Click submit new archive file
        $(newFileArchiveForm.submit).on("click", function () {
            if ($(newFileArchiveForm.id).valid()) {
                // $(newFileArchiveForm.id).submit();

                //// test
                //$(newFileArchiveForm.id).ajaxSubmit({
                //    type: "post",
                //    url: getBaseUrl() + "/api/serviceArchive/upload",
                //    success: function (data) {
                //        console.log(data);
                //    }
                //});
                //// # tets

                var bar = $(newFileArchiveForm.progress);
                $(newFileArchiveForm.id).ajaxSubmit({
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
                    type: "post",
                    url: getBaseUrl() + "/archives/create",
                    success: function () {
                        location.reload();
                    }
                });
            }
        });

        // Click new archive + Reset Form
        $(_archivesId.clickNewFile).click(function () {
            $(newFileArchiveForm.text).text('New archive');
            $(newFileArchiveForm.actionType).val('add');
            $(newFileArchiveForm.archiveType).val('file');
            archivesFormEvent.resetForm();
        });
        $(_archivesId.clickNewLink).click(function () {
            $(newFileArchiveForm.text).text('New archive');
            $(newFileArchiveForm.actionType).val('add');
            $(newFileArchiveForm.archiveType).val('link');
            archivesFormEvent.resetForm();
        });

        // Click delete archive by ajax
        $(_archivesId.clickDelArchive).click(function () {
            if (confirm("Delete archive?")) {
                var arId = $(this).attr('data-id');
                archivesFormEvent.ajaxDeleteArchive(arId);
            }
        })
    },

    /**
     * Click icon to edit archive
     */
    clickIconToEditArchive: function () {
        $(_archivesId.clickEditArchive).click(function () {
            $(newFileArchiveForm.text).text('Edit Archive');
            var archiveId = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            var archiveLink = $(this).attr('data-link');
            var archiveType = $(this).attr('data-type');
            $(newFileArchiveForm.archiveType).val(archiveType);
            $(newFileArchiveForm.actionType).val('edit');
            archivesFormEvent.resetForm();

            // Check webview
            var checkDevice = isMobile.webView();
            if (checkDevice) {
                $('#uploadArchiveInput').addClass('display-none');
                $('#chosenArchiveFileApp').removeClass('display-none');
                $('#buttonOkNewFileArchive').addClass('display-none');
            } else {
                $('#uploadArchiveInput').removeClass('display-none');
                $('#chosenArchiveFileApp').addClass('display-none');
                $('#buttonOkNewFileArchive').removeClass('display-none');
            }

            var objData = {
                id: archiveId,
                name: name,
                archiveType: archiveType,
                actionType: 'edit',
                archiveLink: archiveLink
            };
            console.log(objData);
            archivesFormEvent.pushArchiveDataToEditForm(objData);
            $("#newFileArchiveModal").modal({
                toggle: "modal",
                backdrop: "static",
                keyboard: false
            });
        });
    },

    /**
     * Push archive data to edit form
     * @param data
     */
    pushArchiveDataToEditForm: function (data) {
        if (data && data !== undefined && data !== null) {
            $(newFileArchiveForm.archiveId).val(data.id);
            $(newFileArchiveForm.name).val(data.name);
            $(newFileArchiveForm.archiveType).val(data.archiveType);
            $(newFileArchiveForm.link).val(data.archiveLink);
        }
    },

    /**
     * Reset form
     */
    resetForm: function () {
        $(newFileArchiveForm.archiveId).val(0);
        $(newFileArchiveForm.name).val('');
        $(newFileArchiveForm.file).val('');
        $(newFileArchiveForm.link).val('');
        var archiveType = $(newFileArchiveForm.archiveType).val();
        var actionType = $(newFileArchiveForm.actionType).val();

        console.log(archiveType + ' ' + actionType);

        if (archiveType == "file") {
            $(newFileArchiveForm.linkType).addClass('display-none');
            $(newFileArchiveForm.fileType).removeClass('display-none');
        } else if (archiveType == "link") {
            $(newFileArchiveForm.linkType).removeClass('display-none');
            $(newFileArchiveForm.fileType).addClass('display-none');
        }
    },

    /**
     * Load Ajax delete archive by archive id
     * @param arId : archive id
     */
    ajaxDeleteArchive: function (arId) {
        $.ajax({
            type: 'post',
            url: getBaseUrl() + '/archives/delDocument',
            data: {'id': arId},
            cache: false,
            success: function () {
                $(location).attr('href', '/archives');
            }
        });
    },

    /**
     * Handle click button events
     */
    clickControllerButtons: function () {
        $(_archivesId.plusButton).click(function () {
            $(this).addClass('display-none');
            $(_archivesId.xButton).removeClass('display-none');
            $(_archivesId.clickNewFile).removeClass('display-none');
            $(_archivesId.clickNewLink).removeClass('display-none');

        });
        $(_archivesId.xButton).click(function () {
            $(this).addClass('display-none');
            $(_archivesId.plusButton).removeClass('display-none');
            $(_archivesId.clickNewFile).addClass('display-none');
            $(_archivesId.clickNewLink).addClass('display-none');
        });
    }
};

function closeArchiveModalPopup() {
    // close modal popup
    $("#newFileArchiveModal").modal('hide');
}

$(document).ready(function () {
    "use strict";
    archivesFormEvent.init();
    archivesFormEvent.clickControllerButtons();
});