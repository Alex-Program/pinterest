import {HTTP} from "./classes/HTTP.js";
import {ScrollLoader} from "./classes/ScrollLoader.js";
import {IMG} from "./classes/IMG.js";

const searchParams = new URL(location.href).searchParams;

const element = {
    images: document.getElementById("images")
};


let searchURL = "/api/images?order=desc";
if (searchParams.get("name")) searchURL += "&name=" + encodeURIComponent(searchParams.get("name"));

const scrollLoader = new ScrollLoader(element.images, document.querySelector("#main_container"));
scrollLoader.renderFunction = renderImage;
scrollLoader.url = searchURL;
scrollLoader.order = "desc";
scrollLoader.dataset = "id";
scrollLoader.load();

element.images.addEventListener("click", function (event) {
    const image = event.target.closest(".image");
    if (!image) return;

    if (event.target.closest(".download")) {
        IMG.download(image.querySelector("img").src);
        return;
    }

    imageView(image.dataset.id);
});
