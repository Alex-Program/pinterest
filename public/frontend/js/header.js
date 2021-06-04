import {HTTP} from "./classes/HTTP.js";
import {Cookie} from "./classes/Cookie.js";
import {User} from "./classes/User.js";

const elements = {
    authModal: document.getElementById("auth_modal"),
    openAuth: document.getElementById("open_auth"),
    authToggles: document.querySelectorAll("#auth_modal .toggle"),
    registration: document.getElementById("registration"),
    registrationForm: document.getElementById("registration_form"),
    loginForm: document.getElementById("login_form"),
    login: document.getElementById("login"),
    forGuest: document.querySelectorAll(".for_guest"),
    forAuth: document.querySelectorAll(".for_auth"),
    userName: document.querySelectorAll(".user_name")
};

async function loadUserInfo() {
    const info = await User.info;
    elements.userName.forEach(el => el.innerText = info.name);
}

HTTP.sendRequest("GET", "/api/user/check")
    .then(data => {
        if (data.result) {
            elements.forGuest.forEach(el => el.style.display = "none");
            elements.forAuth.forEach(el => el.style.display = "");
            loadUserInfo();
            return;
        }

        elements.forGuest.forEach(el => el.style.display = "");
        elements.forAuth.forEach(el => el.style.display = "none");
    });

elements.openAuth.addEventListener("click", () => {
    elements.authModal.classList.remove("second");
    elements.authModal.classList.toggle("opened");
});

elements.authToggles.forEach(el => {
    const type = el.dataset.type;

    el.addEventListener("click", function () {
        if (type === "second") elements.authModal.classList.add("second");
        else elements.authModal.classList.remove("second");
    });
});

document.body.addEventListener("click", function (event) {
    if (event.target.closest("#auth_modal") || event.target.closest("#open_auth")) return;

    elements.authModal.classList.remove("opened", "second");
});

elements.registration.addEventListener("click", function () {
    if (!elements.registrationForm.reportValidity()) return;


    this.disabled = true;

    const formData = new FormData(elements.registrationForm);

    HTTP.sendRequest("POST", "/api/registration", formData)
        .then(data => {
            this.disabled = false;

            if (!data.result) {
                if (data.data === "exists") alert("Данный email зарегистрирован");
                return;
            }

            data = data.data;

            Cookie.setCookie("Id", data.id, {"max-age": Cookie.defaultTime});
            Cookie.setCookie("Token", data.token, {"max-age": Cookie.defaultTime});
            location.reload();
        });

});

elements.login.addEventListener("click", function () {
    if (!elements.loginForm.reportValidity()) return;

    this.disabled = true;

    const formData = new FormData(elements.loginForm);

    HTTP.sendRequest("POST", "/api/login", formData)
        .then(data => {
            this.disabled = false;

            if (!data.result) {
                if (data.data === "invalid") alert("Неверные логин или пароль");
                return;
            }

            data = data.data;
            Cookie.setCookie("Id", data.id, {"max-age": Cookie.defaultTime});
            Cookie.setCookie("Token", data.token, {"max-age": Cookie.defaultTime});
            location.reload();
        });
});
