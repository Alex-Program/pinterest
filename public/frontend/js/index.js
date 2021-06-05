import {HTTP} from "./classes/HTTP.js";
import {ScrollLoader} from "./classes/ScrollLoader.js";

const searchParams = new URL(location.href).searchParams;

const element = {
    images: document.getElementById("images")
};


let searchURL = "/api/images";
if (searchParams.get("name")) searchURL += "?name=" + encodeURIComponent(searchParams.get("name"));

const scrollLoader = new ScrollLoader(element.images, document.querySelector("#main_container"));
scrollLoader.renderFunction = renderImage;
scrollLoader.url = searchURL;
scrollLoader.dataset = "id";
scrollLoader.load();
