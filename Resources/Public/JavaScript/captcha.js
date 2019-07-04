!function(d,w,m){
    w.hasReCaptchaAPI||(w.hasReCaptchaAPI=true,m=d.createElement('script'),m.async=1,m.defer=1,m.src='https://www.google.com/recaptcha/api.js',d.body.appendChild(m));
    var g=d.querySelectorAll(".g-recaptcha")[0],a=g.closest("form");
    a.addEventListener("submit",function(t){
        d.getElementById(g.getAttribute('data-form-elementid')).value=g.querySelector(".g-recaptcha-response").value
    },!1);

    w.addEventListener('Onedrop.AjaxForm:after', function (elem) {
        rebuildCaptcha()
    }, true);

}(document,window);

function recaptchaCallback(token) {
    let captchas = document.querySelectorAll(".g-recaptcha");
    for(let i=0; i<captchas.length; i++) {
        let inputId = captchas[i].getAttribute('data-form-elementid');
        document.getElementById(inputId).value=token;
    }
}

function rebuildCaptcha() {
    let captchas = document.querySelectorAll(".g-recaptcha");
    for(let i=0; i<captchas.length; i++) {
        try {
            grecaptcha.render(captchas[i]);
        } catch (e) {
            // do nothing
        }
    }
}

window.recaptchaCallback = recaptchaCallback;

if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector ||
        Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        var el = this;
        if (!document.documentElement.contains(el)) return null;
        do {
            if (el.matches(s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}
