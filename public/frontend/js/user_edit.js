import {IMG} from "./classes/IMG.js";
import {User} from "./classes/User.js";
import {Preloader} from "./classes/Preloader.js";
import {HTTP} from "./classes/HTTP.js";

const elements = {
    editForm: document.getElementById("edit_form"),
    saveUser: document.getElementById("save_user"),
    editImage: document.getElementById("edit_user_image")
};

const formElements = elements.editForm.elements;

IMG.addImagePreview(formElements.namedItem("image"), elements.editImage);
elements.editImage.addEventListener("click", () => formElements.namedItem("image").dispatchEvent(new MouseEvent("click")));

Preloader.open();

User.info.then(info => {
    formElements.namedItem("name").value = info.name;

    elements.editImage.src = info.avatar ? IMG.USER_AVATARS + "/" + info.avatar : IMG.NO_IMAGE;

    Preloader.close();
});

elements.saveUser.addEventListener("click", function () {
    if (!elements.editForm.reportValidity()) return;

    this.disabled = true;
    Preloader.open();

    const formData = new FormData(elements.editForm);
    if (!formData.get("password")) formData.delete("password");
    if (formElements.namedItem("image").files.length === 0) formData.delete("image");

    HTTP.sendRequest("POST", "/api/user/update", formData)
        .then(() => {
            this.disabled = false;
            Preloader.close();

        });
});
