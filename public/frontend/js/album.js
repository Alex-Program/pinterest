import {HTTP} from "./classes/HTTP.js";
import {Time} from "./classes/Time.js";
import {IMG} from "./classes/IMG.js";
import {ScrollLoader} from "./classes/ScrollLoader.js";

const albumId = parseInt(new URL(location.href).searchParams.get("id"), 10);
if (!albumId) throw "Invalid Id";

const elements = {
    albumName: document.getElementById("album_name"),
    albumTime: document.getElementById("album_time"),
    images: document.getElementById("images"),
    openAdd: document.getElementById("open_add"),
    imageModal: document.getElementById("image_modal"),
    addImage: document.getElementById("add_image"),
    selectImage: document.getElementById("select_image"),
    imageInput: document.getElementById("image_input")
};

HTTP.sendRequest("GET", "/api/album/show?id=" + albumId)
    .then(data => {
        data = data.data;

        elements.albumName.innerText = data.name;
        elements.albumTime.innerText = Time.format(data.time);
    });

const scrollLoader = new ScrollLoader(elements.images, document.getElementById("main_container"));
scrollLoader.renderFunction = renderImage;
scrollLoader.url = "/api/images?album_id=" + albumId;
scrollLoader.dataset = "id";
scrollLoader.load();

elements.selectImage.src = IMG.NO_IMAGE;

elements.openAdd.addEventListener("click", () => elements.imageModal.classList.add("opened"));

elements.selectImage.addEventListener("click", () => elements.imageInput.dispatchEvent(new MouseEvent("click")));

IMG.addImagePreview(elements.imageInput, elements.selectImage);

elements.addImage.addEventListener("click", function () {
    if (!elements.imageModal.reportValidity()) return;

    this.disabled = true;

    const formData = new FormData(elements.imageModal);
    formData.append("album_id", String(albumId));

    HTTP.sendRequest("POST", "/api/image/add", formData)
        .then(data => {
            data = data.data;


            const html = renderImage(data);
            elements.images.insertAdjacentHTML("beforeend", html);
            elements.imageModal.reset();
            elements.imageModal.classList.remove("opened");

            this.disabled = false;
        });

});

elements.images.addEventListener("click", function (event) {
    const image = event.target.closest(".image");
    if (!image) return;

    if (event.target.closest(".download")) {
        IMG.download(image.querySelector("img").src);
        return;
    }

    imageView(image.dataset.id);
});
