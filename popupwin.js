function popupwindow(url, title, w, h) {
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    top = top - 40;
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
} 

function popup_login() {
    const win = popupwindow(
        "page_login.php",
        "Login",
        500, 500
    );
}

function popup_thread() {
    const win = popupwindow(
        "popup_create_thread.php",
        "Create Thread",
        500, 500
    );
}
