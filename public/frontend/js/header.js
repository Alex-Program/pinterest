import {HTTP} from "./classes/HTTP.js";
import {Cookie} from "./classes/Cookie.js";
import {User} from "./classes/User.js";
import {IMG} from "./classes/IMG.js";
import {Time} from "./classes/Time.js";
import {Preloader} from "./classes/Preloader.js";
import {ScrollLoader} from "./classes/ScrollLoader.js";
import {Form} from "./classes/Form.js";

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
    imageViewLink: document.getElementById("image_view_user_link"),
    imageViewName: document.getElementById("image_view_name"),
    imageViewDescription: document.getElementById("image_view_description"),
    editImage: document.getElementById("edit_image"),
    exit: document.querySelectorAll(".exit"),
    editImageModal: document.getElementById("image_edit_modal"),
    editImageForm: document.getElementById("edit_image_form"),
    editImageFile: document.getElementById("edit_image_file"),
    editImagePreview: document.getElementById("edit_image_preview"),
    saveImage: document.getElementById("save_image"),
    like: document.getElementById("like"),
    imageViewTags: document.getElementById("image_view_tags")
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
                <div class="comment__img img--rounded avatar--preview">
                    <img class="w-100" src="${avatar}">
                </div>
                <div class="comment__text">
                    ${comment.comment}
                </div>
</div>`;
}

const commentScroll = new ScrollLoader(elements.comments);
commentScroll.renderFunction = renderComment;
commentScroll.dataset = "id";
commentScroll.order = "desc";

window.imageView = async function (imageId) {
    Preloader.open();

    elements.comments.innerHTML = "";
    const fields = JSON.stringify(["tags"]);

    commentScroll.url = `/api/image/comments?image_id=${imageId}&order=desc`;
    await Promise.all([
        HTTP.sendRequest("GET", "/api/image/show?id=" + imageId + "&is_liked=1&fields=" + fields)
            .then(data => {
                data = data.data;

                if (data.is_liked) elements.like.classList.add("liked");
                else elements.like.classList.remove("liked");

                elements.imageViewModal.dataset.id = data.id;
                elements.imageView.src = IMG.MAIN_PHOTOS + "/" + data.src;
                elements.imageViewName.innerText = data.name;
                elements.imageViewDescription.innerText = data.description;
                elements.imageViewUser.innerText = data.user.name;
                elements.imageViewLink.href = "/user?id=" + data.user_id;
                elements.imageViewAvatar.src = data.user.avatar ? IMG.USER_AVATARS + "/" + data.user.avatar : IMG.NO_IMAGE;

                const tags = data.tags.split(" ");
                let html = "";
                tags.forEach(tag => {
                    tag = tag.trim();
                    if (tag[0] !== "#") tag = "#" + tag;
                    html += `<span class="px-1 tag text--primary pointer" data-tag="${tag.substring(1)}">${tag}</span>`;
                });
                elements.imageViewTags.innerHTML = html;

                if (+data.user_id !== User.ID) {
                    elements.editImage.style.display = "none";
                    if (User.ID > 0) elements.like.style.display = "";

                } else {
                    elements.editImage.style.display = "";
                    elements.like.style.display = "none";
                }

            }),
        commentScroll.load()
    ]);


    elements.imageViewModal.classList.add("opened");

    Preloader.close();
}

elements.imageViewTags.addEventListener("click", function (event) {
    const tag = event.target.closest(".tag");
    if (!tag) return;

    location.href = "/?tags=" + encodeURIComponent(JSON.stringify([tag.dataset.tag]));
});

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
            elements.comments.insertAdjacentHTML("afterbegin", html);
            elements.commentForm.reset();
            elements.comments.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
});

Form.addEnterSubmit(elements.commentForm, elements.addComment);

HTTP.sendRequest("GET", "/api/user/check")
    .then(data => {
        if (data.result) {
            elements.forGuest.forEach(el => el.style.setProperty("display", "none", "important"));
            loadUserInfo();
            return;
        }

        User.exit();
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
    el.addEventListener("click", () => {
        User.exit();
        location.reload();
    });
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
    const modal = el.closest(".modal--custom, .modal--right, .modal--right__full");
    if (!modal) return;

    el.addEventListener("click", () => modal.classList.remove("opened"));
});

elements.searchInput.addEventListener("keypress", function (event) {
    if (!this.value) return;
    const key = event.key.toLowerCase();
    if (key === "enter") {
        const tags = this.value.trim().split(" ");
        location.href = "/?tags=" + encodeURIComponent(JSON.stringify(tags));
    }
});

window.editImage = async function (imageId) {

    Preloader.open();

    elements.editImageModal.dataset.id = imageId;

    let data = await HTTP.sendRequest("GET", "/api/image/show?id=" + imageId);
    data = data.data;

    const formElements = elements.editImageForm.elements;

    elements.editImagePreview.src = IMG.MAIN_PHOTOS + "/" + data.src;
    formElements.namedItem("name").value = data.name;
    formElements.namedItem("tags").value = data.tags;
    formElements.namedItem("description").value = data.description;


    elements.editImageModal.classList.add("opened");
    Preloader.close();
};

elements.editImage.addEventListener("click", () => editImage(elements.imageViewModal.dataset.id));

elements.editImagePreview.src = IMG.NO_IMAGE;
IMG.addImagePreview(elements.editImageFile, elements.editImagePreview);
elements.editImagePreview.addEventListener("click", () => elements.editImageFile.dispatchEvent(new MouseEvent("click")));

elements.saveImage.addEventListener("click", function () {
    if (!elements.editImageForm.reportValidity()) return;

    this.disabled = true;
    Preloader.open();

    const formElements = elements.editImageForm.elements;

    const formData = new FormData(elements.editImageForm);
    formData.append("id", elements.editImageModal.dataset.id);
    if (formElements.namedItem("image").files.length === 0) formData.delete("image");
    if (!formData.get("description")) formData.set("description", "");

    HTTP.sendRequest("POST", "/api/image/save", formData)
        .then(() => {

            this.disabled = false;
            elements.editImageModal.classList.remove("opened");
            Preloader.close();
        });

});

elements.like.addEventListener("click", function () {
    if (this.classList.contains("disabled")) return;

    this.classList.add("disabled");
    Preloader.open();

    const url = this.classList.contains("liked") ? "/api/image/dislike" : "/api/image/like";
    const formData = new FormData();
    formData.append("image_id", elements.imageViewModal.dataset.id);

    HTTP.sendRequest("POST", url, formData)
        .then(() => {

            this.classList.toggle("liked");
            this.classList.remove("disabled");
            Preloader.close();

        });

});
