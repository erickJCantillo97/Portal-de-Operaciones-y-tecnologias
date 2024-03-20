import html2canvas from "html2canvas";
import Moment from "moment";

/**
 * This function exports common utilities in JavaScript.
 */
export function useCommonUtilities() {
    /**
     * The function `byteSizeFormatter` converts a given number of bytes into a human-readable format
     * with appropriate units (B, KB, MB, GB).
     * @param bytes - The `bytes` parameter in the `byteSizeFormatter` function represents the size of
     * a file or data in bytes that you want to format into a human-readable format (e.g., KB, MB, GB).
     * @returns The function `byteSizeFormatter` returns a formatted string representing the input
     * `bytes` in a human-readable format, with the size type (bytes, KB, MB, GB) based on the
     * magnitude of the input.
     */
    const byteSizeFormatter = (bytes) => {
        const k = 1024;
        const dm = 1;
        const sizeType = ["B", "KB", "MB", "GB"];

        if (bytes === 0) {
            return `0 byte`;
        }

        const i = Math.floor(Math.log(bytes) / Math.log(k));
        const formattedSize = parseFloat((bytes / Math.pow(k, i)).toFixed(dm));

        return `${formattedSize} ${sizeType[i]}`;
    };

    /**
     * The function `calculatePercentage` calculates the percentage of a given data value in relation
     * to a total value, ensuring the result is between 0 and 100.
     * @param data - The `data` parameter represents the value for which you want to calculate the
     * percentage. It is the part of the total for which you want to find the percentage.
     * @param total - The `total` parameter in the `calculatePercentage` function represents the total
     * value or total amount that you want to calculate the percentage of.
     * @returns The function `calculatePercentage` returns the calculated percentage value as a whole
     * number (rounded down) between 0 and 100. If the calculated percentage is greater than 100, it
     * returns 100. If the calculated percentage is less than 0, it returns 0.
     */
    const calculatePercentage = (data, total) => {
        let percentage = (data / total) * 100;

        if (percentage > 100) {
            percentage = 100;
        }

        if (percentage < 0) {
            percentage = 0;
        }

        return percentage.toFixed(0) > 100 ? 0 : percentage.toFixed(0);
    };

    /**
     * The `currencyFormat` function formats a numerical value as currency in Colombian Pesos (COP)
     * with no decimal places.
     * @param value - The `value` parameter represents the numerical value that you want to format as a
     * currency.
     * @param currency - The `currency` parameter in the `currencyFormat` function represents the currency
     * code that specifies the currency to be used for formatting the value. If the `currency` parameter
     * is not provided or is `null`, the default currency used is "COP" (Colombian Peso).
     * @returns The `currencyFormat` function returns a formatted currency string based on the input
     * `value` and `currency` parameters. If `value` is undefined or null, it returns "Sin definir".
     * Otherwise, it formats the `value` as a currency value using the `toLocaleString` method with the
     * specified options, including the currency symbol based on the `currency` parameter (defaulting
     */
    const currencyFormat = (value, currency = "COP") => {
        if (value == null) {
            return "Sin definir";
        }
        const formattedValue = new Intl.NumberFormat("es-CO", {
            style: "currency",
            currency,
            maximumFractionDigits: 0,
        }).format(parseInt(value));
        return formattedValue.replace(/\s/g, " "); // Elimina los espacios en blanco
    };

    /**
     * The `excelExport` function takes an HTML table, adds a timestamp to the data, and saves it as an
     * XLSB file with the specified file name.
     * @param fileName - The `fileName` parameter in the `excelExport` function is a string that
     * represents the name of the file that will be generated when exporting the data to an Excel file.
     * This parameter is used to specify the name of the output file with a `.xlsb` extension.
     */
    const excelExport = (fileName) => {
        // Acquire Data (reference to the HTML table)
        let table_elt = document.getElementById("tabla");

        let workbook = XLSX.utils.table_to_book(table_elt);

        let ws = workbook.Sheets["Sheet1"];
        XLSX.utils.sheet_add_aoa(ws, [["Creado " + new Date().toISOString()]], {
            origin: -1,
        });

        // Package and Release Data (`writeFile` tries to write and save an XLSB file)
        XLSX.writeFile(workbook, fileName + ".xlsb");
    };

    /**
     * The function `formatDate` takes a date string in the format "yyyymmdd" and returns it in the
     * format "dd/mm/yyyy", unless the day is "00" in which case it returns "Indefinido".
     * @param date - The `formatDate` function takes a date string in the format "YYYYMMDD" and returns
     * a formatted date string in the format "DD/MM/YYYY". If the day part of the date is "00", it
     * returns "Indefinido" instead.
     * @returns The `formatDate` function takes a date string in the format "yyyymmdd" and returns a
     * formatted date string in the format "dd/mm/yyyy". If the day part of the date is "00", it
     * returns "Indefinido".
     */
    function formatDate(date) {
        // Extraer año, mes y día
        var year = date.slice(0, 4);
        var month = date.slice(4, 6);
        var day = date.slice(6, 8);

        // Formato de salida: dd/mm/aaaa
        return day === "00" ? "Indefinido" : `${day}/${month}/${year}`;
    }

    /**
     * The function `formatDateTime24h` converts a given date and time to a 24-hour format in the
     * Spanish (Colombia) locale.
     * @param dateTime - The `dateTime` parameter in the `formatDateTime24h` function should be a valid
     * date and time string that can be parsed by the `Date` constructor in JavaScript. This can be in
     * various formats such as ISO 8601 format (e.g., "2022-10-31T
     * @returns The `formatDateTime24h` function takes a `dateTime` parameter, converts it to a Date
     * object, and then returns the date and time in a specific format using the `toLocaleString`
     * method with the locale set to "es-CO" (Spanish - Colombia). The returned value includes the
     * year, month, day, hour (in 24-hour format), and minute in a 2
     */
    function formatDateTime24h(dateTime) {
        return new Date(dateTime).toLocaleString("es-CO", {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            hour12: false,
        });
    }

    /**
     * The `getDays` function calculates the number of days between two given dates.
     * @param startDate - The `startDate` parameter in the `getDays` function represents the starting
     * date of a period for which you want to calculate the number of days. It should be provided in a
     * format that can be parsed by the `Date` constructor, such as a string in a valid date format or
     * a timestamp
     * @param endDate - The `endDate` parameter in the `getDays` function represents the date that
     * marks the end of a time period for which you want to calculate the number of days.
     * @returns The function `getDays` takes two date parameters, `startDate` and `endDate`, calculates
     * the difference in days between them, and returns the number of days as an integer. If the
     * calculated number of days is negative, it returns 0.
     */
    const getDays = (startDate, endDate) => {
        let date_1 = new Date(startDate);
        let date_2 = new Date(endDate);

        // Calcular la diferencia en milisegundos
        let diffMiliseconds = date_2 - date_1;

        // Calcular la diferencia en días
        let milisecondsPerDay = 24 * 60 * 60 * 1000; // Número de milisegundos en un día
        return Math.round(diffMiliseconds / milisecondsPerDay) < 0
            ? 0
            : Math.round(diffMiliseconds / milisecondsPerDay);
    };

    /**
     * The function `html2canvasDownload` captures the content of an HTML element as an image and
     * downloads it as a PNG file.
     */
    const html2canvasDownload = async () => {
        showLineChart.value++;
        const contentToCapture = document.getElementById("contentToCapture");
        await html2canvas(contentToCapture, {
            windowWidth: 1280,
            windowHeight: 720,
        }).then((canvas) => {
            const imageUrl = canvas.toDataURL("image/png");
            const link = document.createElement("a");
            link.href = imageUrl;
            link.download = "captura_de_pantalla.png";
            link.click();
        });
    };

    /**
     * The `truncateString` function takes a string and a maximum length as parameters, and returns a
     * truncated version of the string with "..." appended if it exceeds the maximum length.
     * @param string - The `string` parameter is the input string that you want to truncate if its
     * length exceeds a certain `maxLength`.
     * @param maxLength - The `maxLength` parameter in the `truncateString` function represents the
     * maximum length that the input `string` should be truncated to. If the length of the input
     * `string` is greater than `maxLength`, the function will truncate the `string` to `maxLength`
     * characters and append "..." at
     * @returns The `truncateString` function returns a truncated version of the input `string` if its
     * length exceeds the `maxLength`. If the `string` is longer than `maxLength`, it will return the
     * first `maxLength` characters followed by "...". If the `string` is equal to or shorter than
     * `maxLength`, it will return the original `string` unchanged.
     */
    const truncateString = (string, maxLength) => {
        let truncatedString =
            string.length > maxLength
                ? string.substring(0, maxLength) + "..."
                : string;
        return truncatedString;
    };

    /* The `return` statement in the `commonUtilities` function is creating an object that contains
    references to all the utility functions defined within the `commonUtilities` function. Each key
    in the object corresponds to a utility function, and the value associated with each key is the
    function itself. */
    return {
        byteSizeFormatter,
        calculatePercentage,
        currencyFormat,
        excelExport,
        formatDate,
        formatDateTime24h,
        getDays,
        html2canvasDownload,
        truncateString,
    };
}
