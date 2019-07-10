/* global grecaptcha */

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

/*
 * helpers
 */

function getCaptchas() {
    return document.querySelectorAll('.g-recaptcha');
}

function getSubmitButtons() {
    return document.querySelectorAll('form button[type="submit"]');
}

function setValuesForRecaptchas(token) {
    getCaptchas().forEach((captcha) => {
        let inputId = captcha.getAttribute('data-form-elementid');
        document.getElementById(inputId).value=token;
    });
}

/*
 * code for regular recaptchas
 */

/**
 * callback for recaptcha
 *
 * @param token
 */
function recaptchaCallback(token) {
    setValuesForRecaptchas(token);
}

/**
 * rebuild all captchas
 */
function rebuildCaptcha() {
    getCaptchas().forEach((captcha) => {
        try {
            grecaptcha.render(captcha);
        } catch (e) {
            // do nothing
        }
    });
}

/*
 * code for invisible recaptchas
 */

let activeButton = null;

function submitForm() {
    let formId = activeButton.name.match('((\\w|\\d)+-)+(\\w|\\d)+')[0];
    let form = document.querySelector('form#' + formId);
    let hiddenField = document.createElement('input');
    hiddenField.name = activeButton.name;
    hiddenField.setAttribute('value', activeButton.value);
    hiddenField.hidden = true;
    form.appendChild(hiddenField);
    form.submit();
}

/**
 * add token to inputs and submit form
 *
 * @param token
 */
function invisibleCallback(token) {
    if(activeButton) {
        setValuesForRecaptchas(token);
        submitForm();
        activeButton = null;
    }
}

/**
 * check if recaptchas are invisible, if so add listener
 * to stop form submission and validate recaptcha
 */
function initSubmitButtons() {
    if (getCaptchas()[0].getAttribute('data-size') === 'invisible') {
        getSubmitButtons().forEach((button) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                activeButton = button;
                grecaptcha.execute();
                return false;
            });
        });
    }
}

/*
 * initialization
 */

if (getCaptchas()) {
    window.recaptchaCallback = recaptchaCallback;
    window.invisibleCallback = invisibleCallback;

    initSubmitButtons();
}

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
