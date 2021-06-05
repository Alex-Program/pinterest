import {HTTP} from "./classes/HTTP.js";
import {Cookie} from "./classes/Cookie.js";
import {User} from "./classes/User.js";
import {IMG} from "./classes/IMG.js";
import {Time} from "./classes/Time.js";
import {Preloader} from "./classes/Preloader.js";

String.prototype.get = function (from, to = null) {
    if (from < 0) from = this.length + from;
    if (to === null) to = this.length;
    else if (to < 0) to = this.length + to;
    const max = Math.max(from, to);
    from = Math.min(from, to);
    to = max;

    return this.substring(from, to);

}


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
    userName: document.querySelectorAll(".user_name"),
    userAvatar: document.querySelectorAll(".user_avatar"),
    closeModal: document.querySelectorAll(".modal__close"),
    searchInput: document.getElementById("search_input"),
    imageViewModal: document.getElementById("image_view_modal"),
    imageView: document.getElementById("image_view"),
    addComment: document.getElementById("add_comment"),
    commentForm: document.getElementById("comment_form"),
    comments: document.getElementById("comments"),
    imageViewUser: document.getElementById("image_view_user"),
    imageViewAvatar: document.getElementById("image_view_avatar"),
    exit: document.querySelectorAll(".exit")
};

window.renderImage = function (image) {
    const img = image.src ? IMG.MAIN_PHOTOS + "/" + image.src : IMG.NO_IMAGE;
    const date = Time.format(image.time);

    return `<div class="p-2 col-md-2 col-sm-12" data-id="${image.id}">
    <div class="image d-flex flex-column justify-content-between" data-id="${image.id}">
        <img src="${img}" class="w-100">
        <div>
            <div class="fw-bold">${image.name}</div>
            <div>${date}</div>
        </div>
        <div class="overlay">
            <button class="btn color--primary download">Скачать</button>
        </div>
    </div>
</div>`;
}

async function loadUserInfo() {
    const info = await User.info;
    elements.userName.forEach(el => el.innerText = info.name);

    const avatar = info.avatar ? IMG.USER_AVATARS + "/" + info.avatar : IMG.NO_IMAGE;
    elements.userAvatar.forEach(el => el.src = avatar);
}

function renderComment(comment) {
    const avatar = comment.avatar ? IMG.USER_AVATARS + "/" + comment.avatar : IMG.NO_IMAGE;

    return `<div class="comment p-1" data-id="${comment.id}">
                <div class="comment__img img--rounded">
                    <img class="w-100" src="${avatar}">
                </div>
                <div class="comment__text">
                    ${comment.comment}
                </div>
</div>`;
}

window.imageView = async function (imageId) {
    Preloader.open();
    let data = await HTTP.sendRequest("GET", "/api/image/show?id=" + imageId + "&comments=1");
    data = data.data;

    elements.imageViewModal.dataset.id = data.id;
    elements.imageView.src = IMG.MAIN_PHOTOS + "/" + data.src;

    let html = "";
    data.comments.forEach(comment => html += renderComment(comment));
    elements.comments.innerHTML = html;
    elements.imageViewUser.innerText = data.user.name;
    elements.imageViewAvatar.src = data.user.avatar ? IMG.USER_AVATARS + "/" + data.user.avatar : IMG.NO_IMAGE;

    elements.imageViewModal.classList.add("opened");

    Preloader.close();
}

elements.imageView.addEventListener("click", function () {
    if (document.fullscreenElement) document.exitFullscreen();
    else elements.imageView.requestFullscreen();
});

elements.addComment.addEventListener("click", function () {
    if (!elements.commentForm.reportValidity()) return;

    const formData = new FormData(elements.commentForm);
    formData.append("image_id", elements.imageViewModal.dataset.id);
    HTTP.sendRequest("POST", "/api/image/comment/add", formData)
        .then(data => {
            data = data.data;

            const html = renderComment(data);
            elements.comments.insertAdjacentHTML("beforeend", html);
        });
});


HTTP.sendRequest("GET", "/api/user/check")
    .then(data => {
        if (data.result) {
            elements.forGuest.forEach(el => el.style.setProperty("display", "none", "important"));
            loadUserInfo();
            return;
        }

        elements.forAuth.forEach(el => el.style.setProperty("display", "none", "important"));
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

elements.exit.forEach(el => {
    el.addEventListener("click", () => User.exit());
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

elements.closeModal.forEach(el => {
    const modal = el.closest(".modal--custom");
    if (!modal) return;

    el.addEventListener("click", () => modal.classList.remove("opened"));
});

elements.searchInput.addEventListener("keypress", function (event) {
    if (!this.value) return;
    const key = event.key.toLowerCase();
    if (key === "enter") {
        location.href = "/?name=" + encodeURIComponent(this.value);
    }
});
