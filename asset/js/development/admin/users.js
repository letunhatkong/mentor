/**
 * Javascript for user stories in User section of admin page
 * @author UTC.KongLtn
 * Last Update: 5/11/2015
 */

/**
 * Variables are used for user section in admin page
 * @type {{formAddUserInAdminPage: string, saveAddUserInAdminPageButton: string, checkExistsUser: string, uploadAvatarId: string, tmpAvaImage: string, clickAddUserButton: string}}
 * @private
 */
var _ids = {
    formAddUserInAdminPage: '#add_user_admin_form',
    saveAddUserInAdminPageButton: "#addUserSubmitButton",
    checkExistsUser: "#checkExistsUser",
    uploadAvatarId: "#uploadAvatarId",
    tmpAvaImage: "#tmpAvaImage",
    clickAddUserButton: "#clickAddUserButton"
};

/**
 * Variables ar used in edit user form
 * @type {{editFormId: string, clickUserName: string, firstName: string, lastName: string, username: string, email: string, phone: string, female: string, male: string, clickUploadButton: string, uploadAvatarId: string, tmpAvaImage: string, submitButton: string}}
 */
var editForm = {
    editFormId: '#edit_user_admin_form',
    clickUserName: ".clickUserName",
    firstName: 'input[name=firstNameEdit]',
    lastName: 'input[name=lastNameEdit]',
    username: 'input[name=usernameEdit]',
    email: 'input[name=emailEdit]',
    phone: 'input[name=phoneEdit]',
    female: 'input[name=genderEdit][data-gender=0]',
    male: 'input[name=genderEdit][data-gender=1]',
    clickUploadButton: "#clickUploadAvaEditForm",
    uploadAvatarId: "#uploadAvatarEdit",
    tmpAvaImage: "#tmpAvaImageEdit",
    submitButton: "#editUserSubmitButton"
};

/**
 * Variables ar used in add user form
 * @type {{firstName: string, lastName: string, username: string, password: string, re_password: string, email: string, phone: string, clickUploadButton: string, uploadAvatarId: string, tmpAvaImage: string}}
 */
var addForm = {
    firstName: 'input[name=firstName]',
    lastName: 'input[name=lastName]',
    username: 'input[name=username]',
    password: 'input[name=password]',
    re_password: 'input[name=re_password]',
    email: 'input[name=email]',
    phone: 'input[name=phone]',
    clickUploadButton: "#clickUploadAvaAddUserForm",
    uploadAvatarId: "#uploadAvatar",
    tmpAvaImage: "#tmpAvaImage"
};

/**
 *
 * @type {{init: Function, validationFormAddUserInAdminPage: Function, validationFormEditUserInAdminPage: Function, saveAddUserInAdminPageButtonClick: Function, clickSaveButtonEditUserAdminPage: Function, checkExistsUser: Function, setExistsUser: Function, clickEditUserName: Function, loadAjaxByUserId: Function, pushUserToEditForm: Function, resetAddUserForm: Function}}
 */
var infoFormEvent = {
    init: function () {
        this.validationFormAddUserInAdminPage();
        this.saveAddUserInAdminPageButtonClick();
        this.clickEditUserName();
        this.clickDelUserName();
        this.validationFormEditUserInAdminPage();
        this.clickSaveButtonEditUserAdminPage();
    },
    /**
     * Validate add user form in admin page
     * @return undefined
     */
    validationFormAddUserInAdminPage: function () {
        $(_ids.formAddUserInAdminPage).validate({
            rules: {
                firstName: {required: true},
                lastName: {required: true},
                username: {required: true},
                email: {required: true, email: true},
                password: {required: true},
                re_password: {required: true, equalTo: '#firstPassword'},
                phone: {required: true, number: true},
                existsUser: {equalTo: '#valueExistsUser'},
                uploadAvatar: {required: true}
            },
            messages: {
                firstName: {required: "First name is required."},
                lastName: {required: "Last name is required."},
                username: {required: "First name is required."},
                password: {required: "Password is required."},
                re_password: {required: "Re-password is required."},
                phone: {required: "Phone is required.", number: "Not number"},
                email: {required: "Email is required."},
                existsUser: {equalTo: "Username or Email is exists."},
                uploadAvatar: {required: "Avatar is required."}
            }
        });
    },
    /**
     * Validate edit user form in admin page
     * @return undefined
     */
    validationFormEditUserInAdminPage: function () {
        $(editForm.editFormId).validate({
            rules: {
                firstNameEdit: {required: true},
                lastNameEdit: {required: true},
                usernameEdit: {required: true},
                emailEdit: {required: true, email: true},
                re_passwordEdit: {equalTo: '#firstPasswordEdit'},
                phoneEdit: {required: true, number: true},
                existsUserEdit: {equalTo: '#valueExistsUser'}
            },
            messages: {
                firstNameEdit: {required: "First name is required."},
                lastNameEdit: {required: "Last name is required."},
                usernameEdit: {required: "First name is required."},
                phoneEdit: {required: "Phone is required.", number: "Not number"},
                emailEdit: {required: "Email is required."},
                existsUserEdit: {equalTo: "Username or Email is exists."}
            }
        });
    },
    /**
     * Click save button in add user form
     * @return undefined
     */
    saveAddUserInAdminPageButtonClick: function () {
        // Username input is changed (add user form)
        $(addForm.username).change(function () {
            var username = $(addForm.username).val();
            var email = $(addForm.email).val();
            infoFormEvent.checkExistsUser(username, email);
        });
        // Email input is changed (add user form)
        $(addForm.email).change(function () {
            var username = $(addForm.username).val();
            var email = $(addForm.email).val();
            infoFormEvent.checkExistsUser(username, email);
        });
        // Click upload Avatar button (add user form)
        $(addForm.clickUploadButton).click(function () {
            $(addForm.uploadAvatarId).trigger("click");
        });
        // Get uploaded Avatar (add user form)
        $(addForm.uploadAvatarId).change(function (e) {
            if (e.target.files[0].name.match(/\.(jpg|jpeg|png|gif)$/)) {
                var srcAva = createObjectURLMentor(e.target.files[0]);
                $(addForm.tmpAvaImage).attr("src", srcAva);
            } else {
                $(addForm.tmpAvaImage).attr("src", "/images/defaultUser.png");
                $(this).val("");
            }
        });
        // Click add User button (admin page)
        $(_ids.clickAddUserButton).click(function () {
            infoFormEvent.resetAddUserForm();
        });
        // Click Submit Button (add user form)
        $(_ids.saveAddUserInAdminPageButton).on("click", function () {
            if ($(_ids.formAddUserInAdminPage).valid()) {
                $(_ids.formAddUserInAdminPage).submit();
            }
        });
    },
    /**
     * Click save button in edit user form
     * @return undefined
     */
    clickSaveButtonEditUserAdminPage: function () {
        // Click upload Avatar button (Edit user form)
        $(editForm.clickUploadButton).click(function () {
            $(editForm.uploadAvatarId).trigger("click");
        });
        // Get uploaded Avatar (edit user form)
        $(editForm.uploadAvatarId).change(function (e) {
            if (e.target.files[0].name.match(/\.(jpg|jpeg|png|gif)$/)) {
                var srcAva = createObjectURLMentor(e.target.files[0]);
                $(editForm.tmpAvaImage).attr("src", srcAva);
            } else {
                $(this).val("");
            }
        });
        // Click Submit Button (edit user form)
        $(editForm.submitButton).on("click", function () {
            if ($(editForm.editFormId).valid()) {
                $(editForm.editFormId).submit();
            }
        });
    },
    /**
     * Check user is exists in database
     * @param username
     * @param email
     * @return string data: "true" or "false"
     */
    checkExistsUser: function (username, email) {
        $.ajax({
            url: getBaseUrl() + '/admin/default/existsUser',
            type: 'post',
            data: {
                'username': username,
                'email': email
            },
            cache: false,
            success: function (data) {
                var checkExists = (data === "true");
                infoFormEvent.setExistsUser(checkExists);
                return data;
            }
        });
    },
    /**
     * Set user data if user is exists, and validate form
     * @param check
     * @return undefined
     */
    setExistsUser: function (check) {
        $(_ids.checkExistsUser).val(check);
        $(_ids.formAddUserInAdminPage).valid();
    },
    /**
     * Click edit username
     * @return undefined
     */
    clickEditUserName: function () {
        // Click username link
        $(editForm.clickUserName).click(function () {
            // Remove value of Upload Avatar Button
            $(editForm.uploadAvatarId).val("");
            // Get User Id
            var userId = $(this).attr('data-user-id');
            // Load AJAX User Data
            infoFormEvent.loadAjaxByUserId(userId);
        });
    },
    /**
     * Load Ajax get user data by user id
     * @param uId
     * @return undefined
     */
    loadAjaxByUserId: function (uId) {
        $.ajax({
            type: "post",
            url: getBaseUrl() + '/admin/default/getUser',
            data: {'id': uId},
            cache: false,
            success: function (data) {
                var dataObj = $.parseJSON(data);
                infoFormEvent.pushUserToEditForm(dataObj);
            }
        });
    },
    /**
     * Push user data to edit form
     * @param data
     * @return undefined
     */
    pushUserToEditForm: function (data) {
        try {
            $(editForm.firstName).val(data.firstName);
            $(editForm.lastName).val(data.lastName);
            $(editForm.username).val(data.username);
            $(editForm.email).val(data.email);
            $(editForm.phone).val(data.phone);

            if (data.gender === "1") {
                $(editForm.male).attr("checked", "checked");
                $(editForm.female).removeAttr("checked");
            } else {
                $(editForm.male).removeAttr("checked");
                $(editForm.female).attr("checked", "checked");
            }

            var avaSrc = (data.avatarPath && data.avatarPath != "") ? '/upload/avatars/' + data.avatarPath : "/images/defaultUser.png";
            $(editForm.tmpAvaImage).attr("src", avaSrc);
        } catch (err) {
            console.log(err);
        }
    },
    /**
     * Reset data of add user form
     * @return undefined
     */
    resetAddUserForm: function () {
        $(addForm.firstName).val("");
        $(addForm.lastName).val("");
        $(addForm.username).val("");
        $(addForm.password).val("");
        $(addForm.re_password).val("");
        $(addForm.email).val("");
        $(addForm.phone).val("");
        $(addForm.tmpAvaImage).attr("src", "/images/defaultUser.png");
        var validator = $(_ids.formAddUserInAdminPage).validate();
        validator.resetForm();
    },
    /**
     * Click Del User
     */
    clickDelUserName: function () {
        $('.clickDelUser').click(function () {
            var userId = $(this).attr('data-id');
            if (confirm('Delete user ?')) {
                window.location.replace(getBaseUrl() + '/admin/default/removeUser/id/' + userId);
            }
        })
    }
};

$(document).ready(function () {
    "use strict";
    infoFormEvent.init();
});