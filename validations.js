//validates version number field
function isNumberKey(event) {
    // Allow numbers and decimal character 
    var charCode = (event.which) ? event.which : event.keyCode;
    if (charCode != 46 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

//Set a cookie
function setCookie(cookieName, cookieValue) {
    var today = new Date();
    var expire = new Date();
    expire.setTime(today.getTime() + 3600000*24*365);
    document.cookie = cookieName+"="+escape(cookieValue) + ";expires="+expire.toGMTString();
}

//Read cookie by name
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}