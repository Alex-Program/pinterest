export class Form {

    static addEnterSubmit(input, button) {
        input.addEventListener("keypress", function (event) {
            const key = event.key.toLowerCase();
            if (key === "enter") {
                event.preventDefault();
                button.dispatchEvent(new MouseEvent("click"));
            }
        });
    }

}
