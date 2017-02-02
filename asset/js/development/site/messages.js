/**
 * Javascript for Screen Message Information
 * @author      UTC.HuyTD
 * @date        2015/10/20
 */
var _objMessages = {
    messagesContent:'textarea[name=messagesContent]'
};

var _classesMessages = {
    replyMessage:'.replyMessage',
    replyAllMessage:'.replyAllMessage'
};
var _idsMessages = {
    createMessagesForm:'#createMessagesForm',
    createMessagesButton:'#createMessagesButton',
    idMessageReply:'#idMessageReply',
    createMessagesModal:'#CreateMessages',
    containerMessage:'#containerMessage'
};

var infoMessagesFormEvent = {
    init: function () {
        this.validateCreateMessagesForm();
        this.buttonOkCreateMessagesFormClick();
        this.handlerModalCreateMessagesDissmiss();
        this.buttonReplyInMessageClick();
        this.buttonReplyAllInMessageClick();
    },
    
    /**
     * Validate form create messages
     * @returns {undefined}
     */
    validateCreateMessagesForm: function(){
        $(_idsMessages.createMessagesForm).validate({ 
            ignore: "",
            rules: {
                messagesContent: {
                    required: true,
                    maxlength:200
                }                
            },
            messages: {
                messagesContent: {
                    required: "Message content is required",
                    maxlength: "message content must be less than 200 characters"
                }
            }
        });
    },
    
    /**
     * Button ok in create messages form: click
     * @returns {undefined}
     */
    buttonOkCreateMessagesFormClick: function (){
        $(_idsMessages.createMessagesButton).on("click", function(){
            if ($(_idsMessages.createMessagesForm).valid()) {
                var data = $(_idsMessages.createMessagesForm).serialize();
                $.ajax({                
                    url: getBaseUrl() + '/messages/create',
                    cache: false,
                    async: false,
                    type: 'POST',
                    data: data,                
                    success: function (result) {
                        $(_idsMessages.createMessagesModal).modal('hide');
                        if(isJsonString(result)){
                            var objData =JSON.parse(result);
                            objData.invitedUser.push(isGuestId);
                            //console.log(objData);
                            socketEvent.socketEmitMessage(objData);
                        }
                    }
                }); 
                //$(_idsMessages.createMessagesForm).submit();
            }
	});
    }, 
    
    /**
     * Function push content message by ajax
     * @param idMessage
     */
    pushContentMessage: function(idMessage){
        $.ajax({
            type: "post",
            url: getBaseUrl() +'/messages/getMessage',
            data: { 'idMessage' : idMessage },
            cache: false,
            success: function(data){
                $(_idsMessages.containerMessage).prepend(data);
                infoMessagesFormEvent.buttonReplyAllInMessageClick();
                infoMessagesFormEvent.buttonReplyInMessageClick();
            }
        });
    },
    
    /**
     * function handler when modal create message dismiss
     */
    handlerModalCreateMessagesDissmiss: function(){
        $(_idsMessages.createMessagesModal).on('hidden', function () {
            var elements = document.getElementById("usersInvited").options;
            for(var i = 0; i < elements.length; i++){
              elements[i].selected = false;
            }
            $("#usersInvited").trigger("liszt:updated");
            $(_idsMessages.idMessageReply).val('');
            $(_objMessages.messagesContent).text('');
            $(_idsMessages.createMessagesForm).validate().resetForm();            
        });
    },
    
    /**
     * Button reply in create messages form: click
     * @returns {undefined}
     */
    buttonReplyInMessageClick: function(){
        $(_classesMessages.replyMessage).unbind('click');
        $(_classesMessages.replyMessage).on('click',function(){
            var idMessageReply = $(this).attr('data-id-reply');
            $(_idsMessages.idMessageReply).val(idMessageReply);
            $.ajax({                
                url: getBaseUrl() + '/messages/getUserReplyMessage',
                cache: false,
                async: false,
                type: 'POST',
                data: {
                    idMessageReply:idMessageReply
                },                
                success: function (result) {
                    if(result!==0){
                        var replyData = $.parseJSON(result);
                        //replyData.userReply;
                        $("#usersInvited option[value='" + replyData.userReply + "']").prop("selected", true);
                        $("#usersInvited").trigger("liszt:updated");
                    }
                }
            }); 
        });
    },
    
    /**
     * Button reply all in create messages form: click
     * @returns {undefined}
     */
    buttonReplyAllInMessageClick: function(){
        $(_classesMessages.replyAllMessage).unbind('click');
        $(_classesMessages.replyAllMessage).on('click',function(){
            var idMessageReply = $(this).attr('data-id-reply');
            $(_idsMessages.idMessageReply).val(idMessageReply);
            $.ajax({                
                url: getBaseUrl() + '/messages/getUserReplyAllMessage',
                cache: false,
                async: false,
                type: 'POST',
                data: {
                    idMessageReply:idMessageReply
                },                
                success: function (result) {
                    if(result!==0){                        
                        var replyData = $.parseJSON(result);
                        for (var i=0; i < replyData.length; i++) {
                            $("#usersInvited option[value='" + replyData[i].userReply + "']").prop("selected", true);
                        }
                        $("#usersInvited").trigger("liszt:updated");
                    }
                }
            });            
        });
    }    
};

$(document).ready(function () {
    "use strict";
    infoMessagesFormEvent.init();
});