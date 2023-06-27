let allow_hide = true;
let countdown_timer_settings = 60;
let countdown_timer;

$(document).ready(() =>
{
    // checkCookieTimer();
    // setCountDownTimer();
    // modalHandler();
    // addSubmitEvent();
})
async function popupConfirmWinPost(url, target, width, height, info, id)
{
    let check_password = prompt("Please enter secondary password.");
    let check_confirm = await checkPasswordAuth(check_password);

    if(check_confirm)
    {
        popupWinPost(url, target, width, height, info, id);
    }  
    else
    {
        alert("Invalid Password");
    }
}
function checkCookieTimer()
{
    let countdown_timer_cookie = getCookie("countdown_timer")
    console.log("cookie", countdown_timer_cookie)
    if(countdown_timer_cookie)
    {
        countdown_timer = parseInt(countdown_timer_cookie);
    }
    else
    {
        countdown_timer = countdown_timer_settings;
    }
}
function setCountDownTimer()
{
    setInterval(() =>
    {
        console.log(countdown_timer, allow_hide);
        setCookie("countdown_timer", countdown_timer, 999999);
        countdown_timer = countdown_timer - 1;

        if(countdown_timer < 0 && allow_hide == true)
        {
            showModal(); 
        }
    }, 1000)

}
function modalHandler()
{
    $("#passwordAuthModal").on('hide.bs.modal', function ()
    {
        return allow_hide
    });
}
function showModal()
{
    allow_hide = false;
    $("#passwordAuthModal").modal('show');
    $("#passwordAuthForm").find("input").val("");
}
function hideModal()
{
    countdown_timer = countdown_timer_settings;
    allow_hide = true;
    $("#passwordAuthModal").modal('hide');  
}
function addSubmitEvent()
{
    $("#passwordAuthForm").submit((e) =>
    {
        disable_button();
        submitAjaxRequest()
        e.preventDefault();
    });
}
function enable_button()
{
    $(".submit-second-password-button").prop("disabled", false);
    $(".submit-second-password-button").html("Submit Password");
}
function disable_button()
{
    $(".submit-second-password-button").prop("disabled", true);
    $(".submit-second-password-button").html("Submitting");
}
async function submitAjaxRequest()
{
    let form_data           = $("#passwordAuthForm").serializeArray();
    let password_auth       = form_data[0].value;

    let response = await checkPasswordAuth(password_auth);
    enable_button();

    if(response == 0)
    {
        alert("Invalid Password Entered");
        window.location.href = "/login_w/logout.php";    
    }
    else
    {
        hideModal();
    }
}

function checkPasswordAuth(password)
{
    return new Promise((resolve, reject) =>
    {  
        $.ajax(
        {
            type: 'post',
            dataType: 'json',
            url: '/login_w/_password_auth.php',
            data: { password_auth: password  },
            success: function (result)
            {
                resolve(parseInt(result));
            },
            error: function (request, status, error)
            {    
                reject(error);
            }
        });
    });
}

function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function eraseCookie(name) {   
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}