import "./bootstrap";
import "../css/app.css";
import "../css/tailwind.css";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";
import PrimeVue from "primevue/config";
import ConfirmationService from "primevue/confirmationservice";
import ToastService from "primevue/toastservice";
import "primevue/resources/themes/mdc-light-indigo/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";
import Tooltip from 'primevue/tooltip';
import VueApexCharts from "vue3-apexcharts";
const appName = import.meta.env.VITE_APP_NAME || "Portal de Operaciones Tecnológicas";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(ConfirmationService)
            .use(ToastService)
            .use(PrimeVue, {
                ripple: true,
                locale: optionsPrimeVUE
            })
            .use(VueApexCharts)
            .directive('tooltip', Tooltip)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});

const optionsPrimeVUE = {
    startsWith: 'Starts with',
    contains: 'Contains',
    notContains: 'Not contains',
    endsWith: 'Ends with',
    equals: 'Equals',
    notEquals: 'Not equals',
    noFilter: 'No Filter',
    lt: 'Less than',
    lte: 'Less than or equal to',
    gt: 'Greater than',
    gte: 'Greater than or equal to',
    dateIs: 'Date is',
    dateIsNot: 'Date is not',
    dateBefore: 'Date is before',
    dateAfter: 'Date is after',
    clear: 'Limpiar',
    apply: 'Aplicar',
    matchAll: 'Match All',
    matchAny: 'Match Any',
    addRule: 'Add Rule',
    removeRule: 'Remove Rule',
    accept: 'Si',
    reject: 'No',
    choose: 'Elegir',
    upload: 'Subir',
    cancel: 'Cancelar',
    completed: 'Completado',
    pending: 'Pendiente',
    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
    dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
    dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octuber', 'Noviembre', 'Dicembre'],
    monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    chooseYear: 'Choose Year',
    chooseMonth: 'Choose Month',
    chooseDate: 'Choose Date',
    prevDecade: 'Previous Decade',
    nextDecade: 'Next Decade',
    prevYear: 'Previous Year',
    nextYear: 'Next Year',
    prevMonth: 'Previous Month',
    nextMonth: 'Next Month',
    prevHour: 'Previous Hour',
    nextHour: 'Next Hour',
    prevMinute: 'Previous Minute',
    nextMinute: 'Next Minute',
    prevSecond: 'Previous Second',
    nextSecond: 'Next Second',
    am: 'am',
    pm: 'pm',
    today: 'Hoy',
    weekHeader: 'Wk',
    firstDayOfWeek: 0,
    dateFormat: 'mm/dd/yy',
    weak: 'Semana',
    medium: 'Medium',
    strong: 'Strong',
    passwordPrompt: 'Enter a password',
    emptyFilterMessage: 'No results found', // @deprecated Use 'emptySearchMessage' option instead.
    searchMessage: '{0} results are available',
    selectionMessage: '{0} items selected',
    emptySelectionMessage: 'No selected item',
    emptySearchMessage: 'Sin resultados',
    emptyMessage: 'No hay opciones Disponibles',
    aria: {
        trueLabel: 'True',
        falseLabel: 'False',
        nullLabel: 'Not Selected',
        star: '1 star',
        stars: '{star} stars',
        selectAll: 'All items selected',
        unselectAll: 'All items unselected',
        close: 'Close',
        previous: 'Previous',
        next: 'Next',
        navigation: 'Navigation',
        scrollTop: 'Scroll Top',
        moveTop: 'Move Top',
        moveUp: 'Move Up',
        moveDown: 'Move Down',
        moveBottom: 'Move Bottom',
        moveToTarget: 'Move to Target',
        moveToSource: 'Move to Source',
        moveAllToTarget: 'Move All to Target',
        moveAllToSource: 'Move All to Source',
        pageLabel: '{page}',
        firstPageLabel: 'First Page',
        lastPageLabel: 'Last Page',
        nextPageLabel: 'Next Page',
        prevPageLabel: 'Previous Page',
        rowsPerPageLabel: 'Rows per page',
        jumpToPageDropdownLabel: 'Jump to Page Dropdown',
        jumpToPageInputLabel: 'Jump to Page Input',
        selectRow: 'Row Selected',
        unselectRow: 'Row Unselected',
        expandRow: 'Row Expanded',
        collapseRow: 'Row Collapsed',
        showFilterMenu: 'Show Filter Menu',
        hideFilterMenu: 'Hide Filter Menu',
        filterOperator: 'Filter Operator',
        filterConstraint: 'Filter Constraint',
        editRow: 'Row Edit',
        saveEdit: 'Save Edit',
        cancelEdit: 'Cancel Edit',
        listView: 'List View',
        gridView: 'Grid View',
        slide: 'Slide',
        slideNumber: '{slideNumber}',
        zoomImage: 'Zoom Image',
        zoomIn: 'Zoom In',
        zoomOut: 'Zoom Out',
        rotateRight: 'Rotate Right',
        rotateLeft: 'Rotate Left'
    }
}

