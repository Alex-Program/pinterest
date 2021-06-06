import {HTTP} from "./HTTP.js";
import {Preloader} from "./Preloader.js";

export class ScrollLoader {

    constructor(element, elementListen = null) {
        this.element = element;
        this.dataset = "";
        this.renderFunction = null;
        this.searchParams = new URLSearchParams();
        this.pathname = "";
        this.elementListen = elementListen;
        if (!this.elementListen) this.elementListen = element;
        this.isLoad = false;
        this.isLast = false;
        this.needPreloader = true;
        this.order = "asc";

        this.setListeners();
    }

    set url(value) {
        this.searchParams.delete("from_id");
        this.searchParams.delete("to_id");

        this.isLast = false;
        if (!value) this.pathname = "";
        else {
            const url = new URL(location.origin + value);
            this.searchParams = url.searchParams;
            this.pathname = url.pathname;
        }
    }

    get url() {
        if (!this.pathname) return "";
        return this.pathname + "?" + this.searchParams.toString();
    }

    setListeners() {
        this.elementListen.addEventListener("scroll", () => {
            if (!this.renderFunction || this.isLast) return;

            if (this.elementListen.scrollTop + this.elementListen.clientHeight === this.elementListen.scrollHeight) this.load();
        });
    }

    load() {
        if (this.isLoad) return;

        this.isLoad = true;
        if (this.needPreloader) Preloader.open();

        let id = 1;
        const last = this.element.lastElementChild;
        if (last) {
            if (this.order === "asc") id = parseInt(last.dataset[this.dataset]) + 1;
            else id = parseInt(last.dataset[this.dataset]) - 1;
        }

        if (this.order === "asc") this.searchParams.set("from_id", String(id));
        else {
            if(last) this.searchParams.set("to_id", String(id));
            else this.searchParams.delete("to_id");
        }

        return HTTP.sendRequest("GET", this.url)
            .then(data => {
                data = data.data;


                let html = "";
                data.forEach(el => html += this.renderFunction(el));
                this.element.insertAdjacentHTML("beforeend", html);

                if (data.length === 0) this.isLast = true;
                Preloader.close();
                this.isLoad = false;
            });
    }

}
