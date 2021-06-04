export class IMG {

    static get USER_AVATARS() {
        return "/storage/images/users";
    }

    static readImage(file) {
        return new Promise(resolve => {
            const fileReader = new FileReader();
            fileReader.onload = () => resolve(fileReader.result);
            fileReader.onerror = () => resolve(null);
            fileReader.readAsDataURL(file);
        });
    }

    static getUrl(url, defaultUrl = "frontend/images/no_image.webp") {
        return new Promise(resolve => {
            const image = new Image();
            image.onerror = () => resolve(defaultUrl);
            image.onload = () => resolve(url);
            image.src = url;
        });
    }

}
