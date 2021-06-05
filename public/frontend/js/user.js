import {User} from "./classes/User.js";
import {IMG} from "./classes/IMG.js";
import {HTTP} from "./classes/HTTP.js";
import {Time} from "./classes/Time.js";

const elements = {
    userEmail: document.getElementById("user_email"),
    userImage: document.getElementById("user_image"),
    uploadForm: document.getElementById("upload_form"),
    imageInput: document.getElementById("select_image"),
    openAddAlbum: document.getElementById("open_add"),
    addAlbum: document.getElementById("add_album"),
    albumModal: document.getElementById("album_modal"),
    albums: document.getElementById("albums")
};

User.info.then(info => {
    elements.userEmail.innerText = info.email;
    if (info.avatar) elements.userImage.src = IMG.USER_AVATARS + "/" + info.avatar;
});

function renderAlbum(album) {
    const img = album.avatar ? IMG.ALBUM_PHOTOS + "/" + album.avatar : IMG.NO_IMAGE;
    const date = Time.format(album.time);

    return `<div class="p-2 col-md-2 col-sm-12">
    <div class="album flex-column d-flex justify-content-between rounded h-100" data-id="${album.id}">
        <img src="${img}" class="w-100">
        <div>
            <div class="fw-bold">${album.name}</div>
            <div>${date}</div>
        </div>
    </div>
</div>`;

}

HTTP.sendRequest("GET", "/api/albums")
    .then(data => {
        data = data.data;

        let html = "";
        data.forEach(album => html += renderAlbum(album));
        elements.albums.innerHTML = html;
    });


elements.userImage.addEventListener("click", function () {
    if (this.classList.contains("disabled")) return;

    elements.imageInput.dispatchEvent(new MouseEvent("click"))
});

elements.imageInput.addEventListener("change", function () {
    if (this.files.length === 0) return;

    elements.userImage.classList.add("disabled");

    IMG.readImage(this.files[0]).then(url => elements.userImage.src = url);
    const formData = new FormData(elements.uploadForm);
    HTTP.sendRequest("POST", "/api/user/update", formData)
        .then(() => {
            elements.uploadForm.reset();
            elements.userImage.classList.remove("disabled");
        });

});

elements.openAddAlbum.addEventListener("click", () => elements.albumModal.classList.add("opened"));

elements.addAlbum.addEventListener("click", function () {
    if (!elements.albumModal.reportValidity()) return;

    this.disabled = true;

    const formData = new FormData(elements.albumModal);
    HTTP.sendRequest("POST", "/api/album/add", formData)
        .then(data => {
            data = data.data;

            const html = renderAlbum(data);
            elements.albums.insertAdjacentHTML("beforeend", html);
            elements.albumModal.reset();
            elements.albumModal.classList.remove("opened");

            this.disabled = false;
        });


});

elements.albums.addEventListener("click", function (event) {
    const album = event.target.closest(".album");
    if (!album) return;

    location.href = "/album?id=" + album.dataset.id;
});
