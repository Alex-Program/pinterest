import {HTTP} from "./HTTP.js";
import {Cookie} from "./Cookie.js";

let info = null;

export class User {

    static get ID() {
        return parseInt(Cookie.getCookie("Id"));
    }

    static get info() {
        if (info !== null) return info;

        info = new Promise(resolve => {
            HTTP.sendRequest("GET", "/api/user/info")
                .then(data => {
                    resolve(data.data);
                });
        });

        return info;
    }

    static exit() {
        Cookie.deleteCookie("Id");
        Cookie.deleteCookie("Token");
    }
}
