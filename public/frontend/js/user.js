import {User} from "./classes/User.js";
import {IMG} from "./classes/IMG.js";
import {HTTP} from "./classes/HTTP.js";
import {Time} from "./classes/Time.js";
import {Preloader} from "./classes/Preloader.js";

let userId = parseInt(new URL(location.href).searchParams.get("id"));
if (!userId) {
    if (!User.ID) throw "Invalid User";
    else userId = User.ID;
}

const elements = {
    userEmail: document.getElementById("user_email"),
    userImage: document.getElementById("user_image"),
    uploadForm: document.getElementById("upload_form"),
    imageInput: document.getElementById("select_image"),
    openAddAlbum: document.getElementById("open_add"),
    addAlbum: document.getElementById("add_album"),
    albumModal: document.getElementById("album_modal"),
    albums: document.getElementById("albums"),
    forUser: document.querySelectorAll(".for_user"),
    forOther: document.querySelectorAll(".for_other"),
    follow: document.getElementById("follow"),
    unfollow: document.getElementById("unfollow"),
    followersCount: document.getElementById("followers_count"),
    subscribesCount: document.getElementById("subscribes_count"),
    followersModal: document.getElementById("follower_modal"),
    followersTitle: document.getElementById("followers_title"),
    followersList: document.getElementById("followers_list"),
    followers: document.querySelectorAll(".followers")
};

Preloader.open();

let userInfoPromise;
let albumsUrl = "/api/albums";

if (userId === User.ID) {
    userInfoPromise = User.info;
    elements.forOther.forEach(el => el.style.display = "none");
} else {
    userInfoPromise = HTTP.sendRequest("GET", `/api/user/info?id=${userId}&is_follow=1`).then(data => {
        data = data.data;

        if (User.ID) {
            if (!data.is_follow) elements.unfollow.style.display = "none";
            else elements.follow.style.display = "none";
        }

        return data;
    });
    albumsUrl += "?user_id=" + userId;
    elements.forUser.forEach(el => el.style.display = "none");
}

function renderFollowers(title, list) {
    elements.followersTitle.innerText = title;

    let html = "";
    list.forEach(user => {
        const img = user.avatar ? IMG.USER_AVATARS + "/" + user.avatar : IMG.NO_IMAGE;
        html += `<a class="d-flex flex-row py-1" href="/user?id=${user.id}">
                    <div class="img--rounded">
                        <img class="w-100" src="${img}">
                    </div>
                    <div class="px-2 fw-bold">${user.name}</div>
</a>`
    });
    elements.followersList.innerHTML = html;
    elements.followersModal.classList.add("opened");

}

Promise.all([
    userInfoPromise.then(info => {
        elements.userEmail.innerText = info.email;
        if (info.avatar) elements.userImage.src = IMG.USER_AVATARS + "/" + info.avatar;
    }),
    HTTP.sendRequest("GET", albumsUrl)
        .then(data => {
            data = data.data;

            let html = "";
            data.forEach(album => html += renderAlbum(album));
            elements.albums.innerHTML = html;
        }),
    HTTP.sendRequest("GET", `/api/user/followers?user_id=${userId}&count=1`)
        .then(data => {
            data = data.data;

            elements.followersCount.innerText = data.count;
        }),
    HTTP.sendRequest("GET", `/api/user/followers?follower_id=${userId}&count=1`)
        .then(data => {
            data = data.data;

            elements.subscribesCount.innerText = data.count;
        })
])
    .then(() => Preloader.close());

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


elements.userImage.addEventListener("click", function () {
    if (this.classList.contains("disabled")) return;

    if (userId === User.ID) {
        elements.imageInput.dispatchEvent(new MouseEvent("click"))
    } else if (document.fullscreenElement) document.exitFullscreen();
    else this.requestFullscreen();
});

elements.imageInput.addEventListener("change", function () {
    if (this.files.length === 0) return;

    Preloader.open();
    elements.userImage.classList.add("disabled");

    IMG.readImage(this.files[0]).then(url => elements.userImage.src = url);
    const formData = new FormData(elements.uploadForm);
    HTTP.sendRequest("POST", "/api/user/update", formData)
        .then(() => {
            elements.uploadForm.reset();
            elements.userImage.classList.remove("disabled");

            Preloader.close();
        });

});

elements.openAddAlbum.addEventListener("click", () => elements.albumModal.classList.add("opened"));

elements.addAlbum.addEventListener("click", function () {
    if (!elements.albumModal.reportValidity()) return;

    this.disabled = true;
    Preloader.open();

    const formData = new FormData(elements.albumModal);
    HTTP.sendRequest("POST", "/api/album/add", formData)
        .then(data => {
            data = data.data;

            const html = renderAlbum(data);
            elements.albums.insertAdjacentHTML("beforeend", html);
            elements.albumModal.reset();
            elements.albumModal.classList.remove("opened");

            this.disabled = false;
            Preloader.close();
        });


});

elements.albums.addEventListener("click", function (event) {
    const album = event.target.closest(".album");
    if (!album) return;

    location.href = "/album?id=" + album.dataset.id;
});

elements.follow.addEventListener("click", function () {

    this.disabled = true;
    Preloader.open();

    const formData = new FormData();
    formData.append("user_id", String(userId));
    HTTP.sendRequest("POST", "/api/user/follower/add", formData)
        .then(() => {
            elements.follow.style.display = "none";
            elements.unfollow.style.display = "";
            this.disabled = false;
            Preloader.close();
        });

});

elements.unfollow.addEventListener("click", function () {
    this.disabled = true;
    Preloader.open();

    const formData = new FormData();
    formData.append("user_id", String(userId));
    HTTP.sendRequest("POST", "/api/user/follower/delete", formData)
        .then(() => {
            elements.follow.style.display = "";
            elements.unfollow.style.display = "none";
            this.disabled = false;
            Preloader.close();
        });

});

elements.followers.forEach(el => {
    const type = el.dataset.type;
    const title = type === "followers" ? "Подписчики" : "Подписки";
    const field = type === "followers" ? "user_id" : "follower_id";
    const url = `/api/user/followers?${field}=${userId}&user=1`;

    el.addEventListener("click", async function () {
        Preloader.open();
        let data = await HTTP.sendRequest("GET", url);
        data = data.data;
        renderFollowers(title, data);

        Preloader.close();
    });

});
