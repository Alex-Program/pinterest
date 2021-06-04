import {User} from "./classes/User.js";
import {IMG} from "./classes/IMG.js";
import {HTTP} from "./classes/HTTP.js";

const elements = {
    userEmail: document.getElementById("user_email"),
    userImage: document.getElementById("user_image"),
    uploadForm: document.getElementById("upload_form"),
    imageInput: document.getElementById("select_image")
}

User.info.then(info => {
    elements.userEmail.innerText = info.email;
    if (info.avatar) elements.userImage.src = IMG.USER_AVATARS + "/" + info.avatar;
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
        .then(() => elements.userImage.classList.remove("disabled"));

});
