/**
 * Javascript for Picture comment
 * @date        2015/10/28
 */

var _commentPictureClasses={
    buttonEditCommentImage:'.clickEditImageComment'
};

var _commentPictureIds={  
    commentImageModal:'#myCamera',    
    
    idSessionCommentImage:'#idSessionCommentImage',
    tmpImage:'#tmpImage',
    fileUploadImage:'#fileUploadImage',
    
    buttonUploadImage:'#buttonUploadImage',    
    buttonSaveCommentImage:'#buttonSaveCommentImage',
    
    idCommentImage:'#idCommentImage',
    
    selectImageButtonApp:'#selectImageButtonApp',
    uploadPictureMobile: '#uploadPictureMobile'
};
var formDataImage = new FormData();
var fileExits = false;
var infoCommentPictureEvent = {
    init: function(){
        this.handlerModalImageDismiss();
        this.clickEditImageComment();
        this.showTempImage();
        this.clickUploadImageButton();
        //this.selectImageButtonAppClick();
        this.clickOrHolderUploadImageInMobile();
    },
    
    /**
     * function handler when modal comment picture dissmiss
     * @return {undefined}
     */
    handlerModalImageDismiss: function(){
        $(_commentPictureIds.commentImageModal).on('hidden', function () {
            $(_commentPictureIds.tmpImage).attr('src',getBaseUrl()+'/images/choose_an_image.jpg');
            $(_commentPictureIds.idCommentImage).val('');
        });
    },
    
    /**
     * Button edit comment: click
     * @returns {undefined}
     */
    clickEditImageComment: function(){
        $(_commentPictureClasses.buttonEditCommentImage).unbind('click');
        $(_commentPictureClasses.buttonEditCommentImage).on('click',function(){
            var imageCommentId = $(this).attr("data-image-comment-id");
            $(_commentPictureIds.idCommentImage).val(imageCommentId);
            infoCommentPictureEvent.loadAjaxByImageCommentId(imageCommentId);
        });
    },
    
    /**
     * Get picture src to edit
     * @returns {undefined}
     */
    loadAjaxByImageCommentId:function(imageCommentId){
        $.ajax({
            type: "post",
            url: getBaseUrl() +'/observation/getContentMediaPath',
            data: { 'imageCommentId' : imageCommentId },
            cache: false,
            success: function(dataImage){
                var dataObj = $.parseJSON(dataImage);
                var imgSrc =  '/upload/images/'+dataObj.contentMediaPath;
                $(_commentPictureIds.tmpImage).attr("src",imgSrc);
            }
        });
    },
    
    showTempImage:function(){
        $(_commentPictureIds.buttonUploadImage).click(function(){
            //var checkAndroid = isMobile.Android();
            //if(checkAndroid){
            //    $(_commentPictureIds.selectImageButtonApp).trigger('click');
            //    return false;
            //}else{
                $(_commentPictureIds.fileUploadImage).trigger('click');
                return false;
            //}            
        });
        
        $(_commentPictureIds.tmpImage).click(function(){
            //var checkAndroid = isMobile.Android();
            //if(checkAndroid){
            //    $(_commentPictureIds.selectImageButtonApp).trigger('click');
            //    return false;
            //}else{
                $(_commentPictureIds.fileUploadImage).trigger('click');
                return false;
            //}            
        });
        
        $(_commentPictureIds.fileUploadImage).change(function(e){
            var files = e.target.files;
            for (var i = 0; i < files.length; i++) {
                var f = files[i];
                var idSessionCommentImage = $(_commentPictureIds.idSessionCommentImage).val();                
                if (e.target.files[0].name.match(/\.(jpg|jpeg|png|gif)$/)) {
                    fileExits = true;
                    formDataImage = new FormData();
                    formDataImage.append("imagePath", f);
                    formDataImage.append("idSessionCommentImage", idSessionCommentImage);
                    var srcImg = createObjectURLMentor(f);
                    $(_commentPictureIds.tmpImage).attr("src",srcImg);
                } else {
                    fileExits = false;
                    $(this).val("");
                }
            }
        });
    },
    /*
    selectImageButtonAppClick: function () { 
        $(_commentPictureIds.selectImageButtonApp).unbind('click');
        $(_commentPictureIds.selectImageButtonApp).on('click',function(){
            window.location.href=$(_commentPictureIds.selectImageButtonApp).attr('href');
        });
    },
    */
    /**
     * Button upload in create/edit picture comment form: click
     * @returns {undefined}
     */
    clickUploadImageButton: function () {        
        $(_commentPictureIds.buttonSaveCommentImage).on('click', function () {
            console.log('click');
            var idCommentImage = $(_commentPictureIds.idCommentImage).val();
            if(fileExits == true){
                if(parseInt(idCommentImage) >0){
                    formDataImage.append("idCommentImage", idCommentImage);
                    //edit
                    $.ajax({                        
                        url: getBaseUrl() + '/observation/editImageComment',
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        data: formDataImage,
                        success: function (data) {
                            if(isJsonString(data)){
                                var objData =JSON.parse(data);
                                if(objData.status == 'true'){
                                    $(_commentPictureIds.commentImageModal).modal('hide');
                                    //console.log(objData);
                                    socketEvent.socketEmitCommentToServer(objData);
                                    socketEvent.pushNotifyToMobile(objData);
                                    //infoCommentPictureEvent.reloadContentCommentImage(objData.idComment,'edit',objData.idSesion);
                                }                               
                            }
                        }
                    });
                }else{
                    //add
                    formDataImage.append("idCommentImage", 0);
                    $.ajax({                        
                        url: getBaseUrl() + '/observation/createImageComment',
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        data: formDataImage,
                        success: function (data) {
                            if(isJsonString(data)){
                                var objData =JSON.parse(data);
                                if(objData.status == 'true'){
                                    $(_commentPictureIds.commentImageModal).modal('hide');
                                    //console.log(objData);
                                    socketEvent.socketEmitCommentToServer(objData);
                                    socketEvent.pushNotifyToMobile(objData);
                                    //infoCommentPictureEvent.reloadContentCommentImage(objData.idComment,'add',objData.idSesion);
                                }                               
                            }
                        }
                    });
                }
            }
        });
    },    
    
    /**
     * Reload picture comment
     * @returns {undefined}
     */
    reloadContentCommentImage: function(idComment,typeReload,idSession){
        $.ajax({
            type: "post",
            url: getBaseUrl() +'/observation/reloadComment',
            data: { 'commentId' : idComment },
            cache: false,
            success: function(data){
                if(typeReload == 'add'){
                    $('#containerComment'+idSession).prepend(data); 
                }else if(typeReload == 'edit'){
                   $('#commentRow'+idComment).replaceWith(data);
                }
                infoCommentPictureEvent.clickEditImageComment();
                infoCommentTextEvent.buttonAddCommentTextClick();
                infoCommentTextEvent.clickDeleteComment();
                likeObject.clickLike();
                infoFormVideoEvent.showHoverEditMenu();
            }
        });
    },
    /**
     * Click or clicke and holder upload image icon in mobile
     */
    clickOrHolderUploadImageInMobile : function() {
        $(_commentPictureIds.uploadPictureMobile).on("taphold", function () {
            window.location.href = "myapp://selectPhoto";
        });
        $(_commentPictureIds.uploadPictureMobile).click(function () {
            window.location.href = "myapp://takePhoto";
        });
    },

    testImg : function(){
        $('#takePictureField').change(function(url){
            console.log(url);
            // Read in file
            var file = url.target.files[0];

            // Ensure it's an image
            if(file.type.match(/image.*/)) {
                console.log('An image has been loaded');

                // Load the image
                var reader = new FileReader();
                reader.onload = function (readerEvent) {
                    var image = new Image();
                    image.onload = function (imageEvent) {
                        // Resize the image
                        var canvas = document.createElement('canvas'),
                            max_size = 500,
                            width = image.width,
                            height = image.height;
                        if (width > height) {
                            if (width > max_size) {
                                height *= max_size / width;
                                width = max_size;
                            }
                        } else {
                            if (height > max_size) {
                                width *= max_size / height;
                                height = max_size;
                            }
                        }
                        canvas.width = width;
                        canvas.height = height;
                        canvas.getContext('2d').drawImage(image, 0, 0, width, height);
                        var dataUrl = canvas.toDataURL('image/jpeg');
                        var resizedImage = dataURLToBlob(dataUrl);
                        console.log(resizedImage);
                        $.event.trigger({
                            type: "imageResized",
                            blob: resizedImage,
                            url: url,
                            imgBase64: readerEvent.target.result
                        });
                    };
                    image.src = readerEvent.target.result;
                    console.log("image");
                };
                reader.readAsDataURL(file);
            }
        });

        $(document).on("imageResized", function (event) {
            var data = new FormData($("form[id='uploadImageForm']")[0]);
            console.log(event.blob);
            var url = URL.createObjectURL(event.blob);
            console.log(event.imgBase64);

            var reader = new window.FileReader();
            reader.readAsDataURL(event.blob);
            reader.onloadend = function() {
                var base64data = reader.result;
                console.log("data64");
                console.log(base64data );
                $("#uploadImageForm").ajaxSubmit({
                    type: "post",
                    url: getBaseUrl() + '/observation/testUploadImage',
                    data: {
                        content:base64data
                    },
                    success: function (data) {
                        console.log(data);
                    }
                });
            };
        });
    }
};

$(document).ready(function () {
    "use strict";
    infoCommentPictureEvent.init();
    infoCommentPictureEvent.testImg();
});
    