/**
 * Javascript for user stories in profile user page
 * @author Samuel Kong
 * Last Update: Dec 22, 2015
 */

/**
 * Variables is using for like actions
 */
var _profileId = {
    clickUploadAva: "#clickUploadAva",
    uploadAvatarId: "#fileUploadAva",
    tmpAvaImage: "#tmpAvaImage",
    clickEditPass: "#clickEditPass",
    changePassWrapper: "#changePassWrapper",
    changePassForm: "#changePassForm",
    editPassSubmitButtonId: "#editPassSubmitButtonId",
    currPassId: "#currPassId",
    firstPass: "#firstPassword",
    secondPass: "#secondPassword",
    passInDb: "#dbPass",
    txtErrOfPass: "#txtErrOfPass",
    cancelButtonEditPassProfile: "#cancelButtonEditPassProfile",
    successProfileId: "#successProfileId",
    clickCloseSuccessProfile: "#successProfileId .close",
    successPasswordId: "#successPasswordId",
    clickCloseSuccessPass: "#successPasswordId .close",
    editProfileForm: "#editProfileForm",
    clickEditProfileSubmit: "#clickEditProfileSubmit",
    firstName: 'input[name=firstName]',
    lastName: 'input[name=lastName]',
    avatarPath: 'input[name=avatarPath]',
    gender: 'input[name=gender]',
    phone: 'input[name=phone]'

};

/**
 *
 * @type {{init: Function, getTempAvatar: Function, validateChangePassForm: Function, editPass: Function}}
 */
var profileFormEvent = {
    init: function () {
        this.getTempAvatar();
        this.validateChangePassForm();
        this.validateEditForm();
        this.editPass();
        this.closeSuccessMessage();
    },
    /**
     * Get temp avatar image
     * @return undefined
     */
    getTempAvatar: function () {
        // Get uploaded Avatar
        $(_profileId.clickUploadAva).click(function () {
            $(_profileId.uploadAvatarId).trigger('click');
        });
        $(_profileId.uploadAvatarId).change(function (e) {
            //$(_profileId.tmpAvaImage).attr("src",srcAva);
            if (e.target.files[0].name.match(/\.(jpg|jpeg|png|gif)$/)) {
                var srcAva = createObjectURLMentor(e.target.files[0]);
                $(_profileId.tmpAvaImage).attr("src", srcAva);
            } else {
                $(this).val("");
            }
        });
    },
    /**
     * Validate change password form
     * @return undefined
     */
    validateEditForm: function () {
        // Validate form
        $(_profileId.editProfileForm).validate({
            rules: {
                firstName: {required: true},
                lastName: {required: true},
                phone: {required: true, number: true}
            },
            messages: {
                firstName: {required: "First name is required."},
                lastName: {required: "Re-enter password is required."},
                phone: {
                    required: "Phone is required.",
                    number: "Not number."
                }
            }
        });

        // Submit
        $(_profileId.clickEditProfileSubmit).click(function () {
            if ($(_profileId.editProfileForm).valid()) {
                $(_profileId.editProfileForm).ajaxSubmit({
                    type: "post",
                    url: getBaseUrl() + '/auth/profile/editProfile',
                    success: function (result) {
                        //console.log(result);
                        $('#successProfileId').prepend(result);
                        profileFormEvent.closeSuccessMessage();
                    }
                });
            }
        })
    },
    /**
     * Validate change password form
     * @return undefined
     */
    validateChangePassForm: function () {
        $(_profileId.changePassForm).validate({
            rules: {
                currPass: {required: true, equalTo: _profileId.passInDb},
                newPass: {required: true},
                reNewPass: {required: true, equalTo: '#firstPassword'}
            },
            messages: {
                currPass: {
                    required: "Current password is required.",
                    equalTo: "Current password is not valid."
                },
                newPass: {required: "New password is required."},
                reNewPass: {
                    required: "Re-enter password is required.",
                    equalTo: "Password doesn't map."
                }
            }
        });
    },
    /**
     * Edit Password events
     * @return undefined
     */
    editPass: function () {
        $(_profileId.cancelButtonEditPassProfile).click(function () {
            $(_profileId.changePassWrapper).toggle();
        });
        $(_profileId.clickEditPass).click(function () {
            $(_profileId.changePassWrapper).toggle();
        });
        $(_profileId.editPassSubmitButtonId).click(function () {
            if ($(_profileId.changePassForm).valid()) {
                var formData = {
                    'newPass': $('#firstPassword').val()
                };
                $.ajax({
                    type: 'post',
                    url: getBaseUrl() + '/auth/profile/editPass',
                    data: formData,
                    success: function (data) {
                        //console.log(data);
                        if (isJsonString(data)) {
                            $(_profileId.successPasswordId).removeClass('display-none');
                            profileFormEvent.resetPassForm();
                            var obj = JSON.parse(data);
                            //console.log(obj);
                            $(_profileId.passInDb).val(obj);
                        }
                    }
                })
            }
        });
    },

    closeSuccessMessage: function () {
        $(_profileId.clickCloseSuccessPass).click(function () {
            $(this).parent().addClass("display-none");
        });
        $(_profileId.clickCloseSuccessProfile).click(function () {
            $(this).parent().remove();
        })
    },

    resetPassForm: function () {
        $(_profileId.currPassId).val("");
        $(_profileId.firstPass).val("");
        $(_profileId.secondPass).val("");
    }
};

$(document).ready(function () {
    "use strict";
    profileFormEvent.init();
});