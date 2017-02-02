/**
 * Created by kongltn on 12/25/2015.
 */

$(document).ready(function () {
    $("#backButtonInheaderBar").click(function () {
        if (sessionStorage.backURL !== undefined) {
            sessionStorage.backIsClick = 1;
            var curArray = JSON.parse(sessionStorage.backURL);
            if (curArray.length > 1) curArray.pop();
            sessionStorage.backURL = JSON.stringify(curArray);
            window.location.replace(curArray[curArray.length - 1]);
        }
    });

    if (!sessionStorage.backURL || sessionStorage.backURL === undefined) {
        sessionStorage.backURL = JSON.stringify([window.location.origin]);
    }

    var curUrlHref = window.location.href;
    if (sessionStorage.backIsClick && sessionStorage.backIsClick === "1") {
        sessionStorage.backIsClick = 0;
    } else {
        var urlArray = JSON.parse(sessionStorage.backURL);
        if (curUrlHref != urlArray[urlArray.length - 1]) {
            urlArray.push(curUrlHref);
        }
        sessionStorage.backURL = JSON.stringify(urlArray);
    }

    //console.log(sessionStorage.backURL);
    //console.log(sessionStorage.backIsClick);
});

/* Utility function to convert a canvas to a BLOB */
var dataURLToBlob = function(dataURL) {
    var BASE64_MARKER = ';base64,';
    var parts, contentType, raw;
    if (dataURL.indexOf(BASE64_MARKER) == -1) {
        parts = dataURL.split(',');
        contentType = parts[0].split(':')[1];
        raw = parts[1];

        return new Blob([raw], {type: contentType});
    }

    parts = dataURL.split(BASE64_MARKER);
    contentType = parts[0].split(':')[1];
    raw = window.atob(parts[1]);
    var rawLength = raw.length;

    var uInt8Array = new Uint8Array(rawLength);

    for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
    }

    return new Blob([uInt8Array], {type: contentType});
};
/* End Utility function to convert a canvas to a BLOB      */

function getBase64Image(img) {
    // Create an empty canvas element
    var canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;

    // Copy the image contents to the canvas
    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0);

    // Get the data-URL formatted image
    // Firefox supports PNG and JPEG. You could check img.src to
    // guess the original format, but be aware the using "image/jpg"
    // will re-encode the image.
    var dataURL = canvas.toDataURL("image/png");

    return dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
}
