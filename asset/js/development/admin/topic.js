/**
 * Javascript for topic section of admin page
 * @author UTC.KongLtn
 * Last Update: Dec 14, 2015
 */

var topicIdAdmin = {
    clickNewTopic: '#clickNewTopic',
    clickTopicStatus: ".clickTopicStatus",
    clickEditTopic: ".clickEditTopic",
    clickDelTopic: ".clickDelTopic"
};


var addTopicForm = {
    formId: '#add_topic_admin_form',
    submitButton: '#newTopicButtonOK',
    name: '#add_topic_admin_form input[name="name"]'
};

var editTopicForm = {
    formId: '#edit_topic_admin_form',
    submitButton: '#editTopicButtonOK',
    name: '#edit_topic_admin_form input[name="name"]',
    status: '#edit_topic_admin_form input[name="status"]',
    id: '#edit_topic_admin_form input[name="id"]',
    activated: '#edit_topic_admin_form input[name=status][value=1]',
    pending: '#edit_topic_admin_form input[name=status][value=0]'
};

/**
 * Topic form in admin
 * @type {{init: Function, validateTopicFormInAdminPage: Function, clickSaveButtonTopicForm: Function, clickTopicStatus: Function, clickDelTopic: Function, clickEditTopic: Function, pushDataToEditForm: Function, resetAddTopicForm: Function}}
 */
var topicFormAdmin = {
    init: function () {
        this.validateTopicFormInAdminPage();
        this.clickSaveButtonTopicForm();
        this.clickTopicStatus();
        this.clickEditTopic();
        this.clickDelTopic();
    },
    /**
     * Validate add topic form in admin page
     * @return undefined
     */
    validateTopicFormInAdminPage: function () {
        // Add topic form
        $(addTopicForm.formId).validate({
            rules: {
                name: {required: true},
                status: {required: true}
            },
            messages: {
                name: {required: "Topic name is required."},
                status: {required: "Topic status is required."}
            }
        });
        $(topicIdAdmin.clickNewTopic).click(function () {
            topicFormAdmin.resetAddTopicForm();
        });

        // Edit topic form
        $(editTopicForm.formId).validate({
            rules: {
                name: {required: true},
                status: {required: true}
            },
            messages: {
                name: {required: "Topic name is required."},
                status: {required: "Topic status is required."}
            }
        });
    },

    /**
     * Click save button in add topic form
     * @return undefined
     */
    clickSaveButtonTopicForm: function () {
        // Add a topic
        $(addTopicForm.submitButton).on("click", function () {
            if ($(addTopicForm.formId).valid()) {
                $(addTopicForm.formId).submit();
            }
        });

        // Edit a topic
        $(editTopicForm.submitButton).on("click", function () {
            if ($(editTopicForm.formId).valid()) {
                $(editTopicForm.formId).submit();
            }
        });
    },

    /**
     * Change topic status
     */
    clickTopicStatus: function () {
        $(topicIdAdmin.clickTopicStatus).click(function () {
            var topicId = $(this).parent().attr('data-id');
            var thisTarget = $(this);
            $.ajax({
                type: 'post',
                cache: false,
                data: {id: topicId},
                url: getBaseUrl() + '/admin/default/changeTopicStatus',
                success: function (data) {
                    data = parseInt(data);
                    var text = false;
                    if (data == 1) text = "Activated";
                    if (data == 0) text = "Pending";
                    if (text) thisTarget.text(text);
                }
            });
        });
    },

    /**
     * Click delete a topic
     */
    clickDelTopic: function () {
        $(topicIdAdmin.clickDelTopic).click(function () {
            if (confirm('Are you sure to delete this topic ?')) {
                var topicId = $(this).parent().attr('data-id');
                var thisTarget = $(this);
                $.ajax({
                    type: 'post',
                    cache: false,
                    data: {id: topicId},
                    url: getBaseUrl() + '/admin/default/delTopic',
                    success: function (data) {
                        data = parseInt(data);
                        if (data > 0) {
                            thisTarget.parent().remove();
                        }
                    }
                });
            }
        });
    },

    /**
     * Click delete a topic
     */
    clickEditTopic: function () {
        $(topicIdAdmin.clickEditTopic).click(function () {
            var topicId = $(this).parent().attr('data-id');
            //var thisTarget = $(this);
            $.ajax({
                type: 'get',
                cache: false,
                data: {id: topicId},
                url: getBaseUrl() + '/admin/default/getTopic',
                success: function (data) {
                    if (isJsonString(data)) {
                        var objData = $.parseJSON(data);
                        console.log(objData);
                        topicFormAdmin.pushDataToEditForm(objData);
                    }
                }
            });

        });
    },

    /**
     * Push topic data to edit form
     * @param data
     */
    pushDataToEditForm: function(data){
        if (data && data !== undefined) {
            $(editTopicForm.name).val(data.name);
            $(editTopicForm.id).val(data.idTopic);
            if (data.active === "1") {
                $(editTopicForm.pending).removeAttr("checked");
                $(editTopicForm.activated).attr("checked","checked");
            } else {
                $(editTopicForm.activated).removeAttr("checked");
                $(editTopicForm.pending).attr("checked","checked");
            }
            var validator = $(editTopicForm.formId).validate();
            validator.resetForm();
        }
    },

    /**
     * Reset data of add topic form
     * @return undefined
     */
    resetAddTopicForm: function () {
        $(addTopicForm.name).val("");
        var validator = $(addTopicForm.formId).validate();
        validator.resetForm();
    }
};

$(document).ready(function () {
    "use strict";
    topicFormAdmin.init();
});