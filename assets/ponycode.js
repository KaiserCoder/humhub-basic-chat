function smiley(name) {
    var chatText = $('#chatText');
    chatText.val(chatText.val() + name);
}

document.getElementById ("imgButton").addEventListener (
    "click", imgButtonEvent, false
);
document.getElementById ("urlButton").addEventListener (
    "click", urlButtonEvent, false
);
document.getElementById ("videoButton").addEventListener (
    "click", videoButtonEvent, false
);
document.getElementById ("rainbowButton").addEventListener (
    "click", rainbowButtonEvent, false
);
document.getElementById ("bButton").addEventListener (
    "click", bButtonEvent, false
);
document.getElementById ("iButton").addEventListener (
    "click", iButtonEvent, false
);
document.getElementById ("uButton").addEventListener (
    "click", uButtonEvent, false
);
document.getElementById ("spoilerButton").addEventListener (
    "click", spoilerButtonEvent, false
);

function imgButtonEvent () {
    $("#imgText").remove();
    $("#buttonInput").empty();
    $("#sendImgButton").remove();

    $("#buttonInput").html
    (
        '<input id="imgText" name="imgText" placeholder="Url de l\'image" class="form-control" autocomplete="off" maxlength="510" type="text">'
    );
    $("#imgText").after
    (
        '<button id="sendImgButton" class="btn btn-default" type="button">Envoyer</button>'
    );
    document.getElementById ("sendImgButton").addEventListener (
        "click", sendImgButtonEvent, false
    );
}

function urlButtonEvent () {
    $("#urlText").remove();
    $("#buttonInput").empty();
    $("#sendUrlButton").remove();

    $("#buttonInput").html
    (
        '<input id="urlText" name="urlText" placeholder="Url" class="form-control" autocomplete="off" maxlength="510" type="text">'
    );
    $("#urlText").after
    (
        '<button id="sendUrlButton" class="btn btn-default" type="button">Envoyer</button>'
    );
    document.getElementById ("sendUrlButton").addEventListener (
        "click", sendUrlButtonEvent, false
    );
}

function videoButtonEvent () {
    $("#videoText").remove();
    $("#buttonInput").empty();
    $("#sendVideoButton").remove();

    $("#buttonInput").html
    (
        '<input id="videoText" name="videoText" placeholder="Url de la video" class="form-control" autocomplete="off" maxlength="510" type="text">'
    );
    $("#videoText").after
    (
        '<button id="sendVideoButton" class="btn btn-default" type="button">Envoyer</button>'
    );
    document.getElementById ("sendVideoButton").addEventListener (
        "click", sendVideoButtonEvent, false
    );
}

function rainbowButtonEvent () {
    $("#buttonInput").empty();
    var chatText =$('#chatText');
    chatText.val(chatText.val()+'[rainbow][/rainbow]');
}

function bButtonEvent () {
    $("#buttonInput").empty();
    var chatText =$('#chatText');
    chatText.val(chatText.val()+'[b][/b]');
}
function iButtonEvent () {
    $("#buttonInput").empty();
    var chatText =$('#chatText');
    chatText.val(chatText.val()+'[i][/i]');
}
function uButtonEvent () {
    $("#buttonInput").empty();
    var chatText =$('#chatText');
    chatText.val(chatText.val()+'[u][/u]');
}
function spoilerButtonEvent () {
    $("#buttonInput").empty();
    var chatText =$('#chatText');
    chatText.val(chatText.val()+'[spoiler][/spoiler]');
}
function sendImgButtonEvent () {
    var chatText =$('#chatText');
    var imgText =$('#imgText');
    if (imgText.val() !== '') {
        chatText.val(chatText.val()+'[img]'+imgText.val()+'[/img]');
    }
    $("#imgText").remove();
    $("#sendImgButton").remove();
}


function sendUrlButtonEvent () {
    var chatText =$('#chatText');
    var urlText =$('#urlText');
    if(urlText.val() !== ''){
        chatText.val(chatText.val()+'[url]'+urlText.val()+'[/url]');
    }
    $("#urlText").remove();
    $("#sendUrlButton").remove();
}

function sendVideoButtonEvent () {
    var chatText =$('#chatText');
    var videoText =$('#videoText');
    if(videoText.val() !== '') {
        chatText.val(chatText.val()+'[video]'+videoText.val()+'[/video]');
    }
    $("#videoText").remove();
    $("#sendVideoButton").remove();
}