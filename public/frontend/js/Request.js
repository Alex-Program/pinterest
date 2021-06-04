export class Request {

    static sendRequest(method, url, data = null) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.onload = () => {
                try {
                    const data = JSON.parse(xhr.responseText);
                    resolve(data);
                } catch {
                    resolve(xhr.responseText);
                }
            }
            xhr.onerror = () => reject(xhr.status);
            xhr.open(method, url);
            xhr.send(data);

        });
    }

}
