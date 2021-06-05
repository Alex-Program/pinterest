const preloader = document.getElementById("preloader");

export class Preloader {

    static open() {
        preloader.classList.add("opened");
    }

    static close() {
        preloader.classList.remove("opened");
    }

}
