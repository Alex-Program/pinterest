export class IMG {

    static get USER_AVATARS() {
        return "/storage/images/users";
    }

    static get MAIN_PHOTOS() {
        return "/storage/images/main";
    }

    static get ALBUM_PHOTOS() {
        return "/storage/images/albums";
    }

    static get NO_IMAGE() {
        return "/frontend/images/no_image.webp";
    }

    static readImage(file) {
        return new Promise(resolve => {
            const fileReader = new FileReader();
            fileReader.onload = () => resolve(fileReader.result);
            fileReader.onerror = () => resolve(null);
            fileReader.readAsDataURL(file);
        });
    }

    static getName(src) {
        const split = src.split("/");
        return split[split.length - 1];
    }

    static getUrl(url, defaultUrl = "frontend/images/no_image.webp") {
        return new Promise(resolve => {
            const image = new Image();
            image.onerror = () => resolve(defaultUrl);
            image.onload = () => resolve(url);
            image.src = url;
        });
    }

    static addImagePreview(input, image) {
        input.addEventListener("change", function () {
            if (this.files.length === 0) return;

            IMG.readImage(this.files[0]).then(src => image.src = src);
        });
    }

}
