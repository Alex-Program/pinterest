import {HTTP} from "./HTTP.js";

let info = null;

export class User {
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
}
