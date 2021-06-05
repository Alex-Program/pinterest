export class Time {

    static format(time, showSeconds = false) {
        time *= 1000;

        const date = new Date();
        date.setTime(time);

        const hours = ("0" + date.getHours()).get(-2);
        const minutes = ("0" + date.getMinutes()).get(-2);
        const seconds = ("0" + date.getSeconds()).get(-2);
        const day = ("0" + date.getDate()).get(-2);
        const year = date.getFullYear();
        const month = ("0" + (date.getMonth() + 1)).get(-2);

        let str = `${day}.${month}.${year} ${hours}:${minutes}`;
        if (showSeconds) str += `:${seconds}`;

        return str;
    }

}
