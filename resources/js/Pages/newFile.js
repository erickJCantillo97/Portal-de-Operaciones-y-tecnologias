import { ref, onMounted } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid';
import Button from '@/Components/Button.vue';
import PieChart from './PieChart.vue';

export default (() => {
    const __VLS_setup = async () => {
        onMounted(() => {
            initFilters();
            getContracts();
        });

        //Selecciona por defecto 5 contratos para graficar
        const loadInitialSelectedContracts = () => {
            selectedContracts.value = contracts.value.length > 5 ? contracts.value.slice(0, 5) : contracts.value;
            contractsList();
        };

        const suma = ref(0);
        let loading = true;

        //Obtener Contratos por API Routes
        const getContracts = () => {
            try {
                axios.get(route('getContracts')).then((res) => {
                    contracts.value = res.data.contracts;
                    loadInitialSelectedContracts();
                    suma.value = res.data.contracts.reduce((total, obj) => total + parseInt(obj.cost), 0); // Cálculo del Porcentaje de Contratos
                    loading = false;
                });
            } catch (error) {
                console.error('Error al obtener contratos:', error);
            }
        };

        const filters = ref({
            global: { value: null, matchMode: FilterMatchMode.CONTAINS }
        });

        const initFilters = () => {
            filters.value = {
                global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            };
        };

        const clearFilter = () => {
            initFilters();
        };

        const onRowSelect = (event) => {
            datos.value = [];
            series.value = [];

            // Agregar el contrato seleccionado a selectedContracts si no está presente
            if (!selectedContracts.value.find((contract) => contract.id === event.data.id)) {
                selectedContracts.value.push(event.data);
            }
            contractsList();
        };

        const onRowUnselect = (event) => {
            datos.value = [];
            series.value = [];
            selectedContracts.value = selectedContracts.value.filter((contract) => contract.id !== event.data.id);
            contractsList();
        };

        const formatCurrency = (value) => {
            return parseFloat(value).toLocaleString('es-CO', {
                style: 'currency',
                currency: 'COP',
            });
        };

        const title = ref({
            text: 'Contratos',
            subtext: '',
            left: 'center'
        });

        //Variables Reactivas
        const contracts = ref();
        const selectedContracts = ref([]);

        //Carga datos
        const showGraph = ref(0); //Permite Re-renderizar el componente hijo (PieChart) sin necesidad de recargar página 😎
        const datos = ref([]);
        const series = ref([]);
        // const legends = ref([])
        const contractsList = () => {
            showGraph.value++; //Permite Re-renderizar el componente hijo (PieChart) sin necesidad de recargar página 😎

            selectedContracts.value.forEach((contract) => {
                let chartItemsRender = {
                    value: contract.cost / 1000000,
                    name: contract.name,
                    label: {
                        formatter: '{b|{b}}{abg|}\n{hr|}\n  ${c}M  {per|{d}%}  ',
                        backgroundColor: '#F6F8FC',
                        borderColor: '#8C8D8E',
                        borderWidth: 1,
                        borderRadius: 4,
                        rich: {
                            a: {
                                color: '#6E7079',
                                lineHeight: 22,
                                align: 'center'
                            },
                            hr: {
                                borderColor: '#8C8D8E',
                                width: '100%',
                                borderWidth: 1,
                                height: 0
                            },
                            b: {
                                align: 'center',
                                color: '#6E7079',
                                fontSize: 10,
                                fontWeight: 'bold',
                                lineHeight: 15
                            },
                            c: {
                                fontWeight: 'bold',
                            },
                            per: {
                                color: '#fff',
                                backgroundColor: '#4C5058',
                                padding: [3, 4],
                                borderRadius: 4
                            }
                        }
                    }
                };
                datos.value.push(chartItemsRender);
                // legends.value.push(chartItemsRender.name)
            });

            series.value.push({
                type: 'pie',
                radius: '70%',
                center: ['50%', '55%'],
                selectedMode: 'single',
                is3D: true,
                data: datos.value
            });
        };
        const __VLS_publicComponent = (await import('vue')).defineComponent({
            setup() {
                return {};
            },
        });

        const __VLS_componentsOption = {};

        let __VLS_name!: 'DataChart';
        function __VLS_template() {
            let __VLS_ctx!: InstanceType<import('./__VLS_types.js').PickNotAny<typeof __VLS_publicComponent, new () => {}>> & InstanceType<import('./__VLS_types.js').PickNotAny<typeof __VLS_internalComponent, new () => {}>> & {};
            /* Components */
            let __VLS_localComponents!: NonNullable<typeof __VLS_internalComponent extends { components: infer C; } ? C : {}> & typeof __VLS_componentsOption & typeof __VLS_ctx;
            let __VLS_otherComponents!: typeof __VLS_localComponents & import('./__VLS_types.js').GlobalComponents;
            let __VLS_own!: import('./__VLS_types.js').SelfComponent<typeof __VLS_name, typeof __VLS_internalComponent & typeof __VLS_publicComponent & (new () => { $slots: typeof __VLS_slots; }) >;
            let __VLS_components!: typeof __VLS_otherComponents & Omit<typeof __VLS_own, keyof typeof __VLS_otherComponents>;
            /* Style Scoped */
            type __VLS_StyleScopedClasses = {};
            let __VLS_styleScopedClasses!: __VLS_StyleScopedClasses | keyof __VLS_StyleScopedClasses | (keyof __VLS_StyleScopedClasses)[];
            /* CSS variable injection */
            /* CSS variable injection end */
            let __VLS_templateComponents!: {} &
                import('./__VLS_types.js').WithComponent<'DataTable', typeof __VLS_components, 'DataTable'> &
                import('./__VLS_types.js').WithComponent<'Button', typeof __VLS_components, 'Button'> &
                import('./__VLS_types.js').WithComponent<'MagnifyingGlassIcon', typeof __VLS_components, 'MagnifyingGlassIcon'> &
                import('./__VLS_types.js').WithComponent<'Column', typeof __VLS_components, 'Column'> &
                import('./__VLS_types.js').WithComponent<'PieChart', typeof __VLS_components, 'PieChart'>;
            __VLS_components.DataTable; __VLS_components.DataTable;
            // @ts-ignore
            [DataTable, DataTable,];
            __VLS_components.Button; __VLS_components.Button;
            // @ts-ignore
            [Button, Button,];
            __VLS_components.MagnifyingGlassIcon;
            // @ts-ignore
            [MagnifyingGlassIcon,];
            __VLS_components.Column; __VLS_components.Column; __VLS_components.Column; __VLS_components.Column; __VLS_components.Column; __VLS_components.Column; __VLS_components.Column; __VLS_components.Column; __VLS_components.Column; __VLS_components.Column;
            // @ts-ignore
            [Column, Column, Column, Column, Column, Column, Column, Column, Column, Column,];
            __VLS_components.PieChart; __VLS_components.PieChart;
            // @ts-ignore
            [PieChart, PieChart,];
            {
                ({} as JSX.IntrinsicElements).div;
                ({} as JSX.IntrinsicElements).div;
                (__VLS_x as JSX.IntrinsicElements)['div'] = { class: ("grid grid-cols-1 p-3 m-1 sm:grid-cols-1 md:grid-cols-2 rounded-xl"), };
                {
                    __VLS_templateComponents.DataTable;
                    (__VLS_x as import('./__VLS_types.js').ComponentProps<typeof __VLS_templateComponents.DataTable>) = { id: ("tabla"), stripedRows: (true), class: ("p-datatable-sm"), value: ((__VLS_ctx.contracts)), selection: ((__VLS_ctx.selectedContracts)), filters: ((__VLS_ctx.filters)), dataKey: ("id"), filterDisplay: ("menu"), loading: ((__VLS_ctx.loading)), selectAll: ((false)), globalFilterFields: ((['name', 'gerencia', 'start_date', 'end_date', 'hoursPerDay', 'daysPerWeek', 'daysPerMonth'])), currentPageReportTemplate: (" {first} al {last} de {totalRecords}"), paginatorTemplate: ("FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"), paginator: ((true)), rows: ((5)), rowsPerPageOptions: (([5, 10, 15, 50])), };
                    const __VLS_0 = new __VLS_templateComponents.DataTable({ id: ("tabla"), stripedRows: (true), class: ("p-datatable-sm"), value: ((__VLS_ctx.contracts)), selection: ((__VLS_ctx.selectedContracts)), filters: ((__VLS_ctx.filters)), dataKey: ("id"), filterDisplay: ("menu"), loading: ((__VLS_ctx.loading)), selectAll: ((false)), globalFilterFields: ((['name', 'gerencia', 'start_date', 'end_date', 'hoursPerDay', 'daysPerWeek', 'daysPerMonth'])), currentPageReportTemplate: (" {first} al {last} de {totalRecords}"), paginatorTemplate: ("FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"), paginator: ((true)), rows: ((5)), rowsPerPageOptions: (([5, 10, 15, 50])), });
                    const __VLS_1 = __VLS_templateComponents.DataTable({ id: ("tabla"), stripedRows: (true), class: ("p-datatable-sm"), value: ((__VLS_ctx.contracts)), selection: ((__VLS_ctx.selectedContracts)), filters: ((__VLS_ctx.filters)), dataKey: ("id"), filterDisplay: ("menu"), loading: ((__VLS_ctx.loading)), selectAll: ((false)), globalFilterFields: ((['name', 'gerencia', 'start_date', 'end_date', 'hoursPerDay', 'daysPerWeek', 'daysPerMonth'])), currentPageReportTemplate: (" {first} al {last} de {totalRecords}"), paginatorTemplate: ("FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"), paginator: ((true)), rows: ((5)), rowsPerPageOptions: (([5, 10, 15, 50])), });
                    let __VLS_2!: import('./__VLS_types.js').PickNotAny<typeof __VLS_0, typeof __VLS_1>;
                    type __VLS_3 = import('./__VLS_types.js').InstanceProps<typeof __VLS_2, typeof __VLS_templateComponents.DataTable>;
                    const __VLS_4: import('./__VLS_types.js').EventObject<typeof __VLS_2, 'rowUnselect', typeof __VLS_templateComponents.DataTable, __VLS_3['onRowUnselect']> = {
                        rowUnselect: (__VLS_ctx.onRowUnselect)
                    };
                    // @ts-ignore
                    [contracts, selectedContracts, filters, loading, contracts, selectedContracts, filters, loading, contracts, selectedContracts, filters, loading, onRowUnselect,];
                    type __VLS_5 = import('./__VLS_types.js').InstanceProps<typeof __VLS_2, typeof __VLS_templateComponents.DataTable>;
                    const __VLS_6: import('./__VLS_types.js').EventObject<typeof __VLS_2, 'rowSelect', typeof __VLS_templateComponents.DataTable, __VLS_5['onRowSelect']> = {
                        rowSelect: (__VLS_ctx.onRowSelect)
                    };
                    // @ts-ignore
                    [onRowSelect,];
                    {
                        ({} as JSX.IntrinsicElements).template;
                        ({} as JSX.IntrinsicElements).template;
                        (__VLS_x as JSX.IntrinsicElements)['template'] = {};
                        const __VLS_7 = new __VLS_templateComponents.DataTable({ id: ("tabla"), stripedRows: (true), class: ("p-datatable-sm"), value: ((__VLS_ctx.contracts)), selection: ((__VLS_ctx.selectedContracts)), filters: ((__VLS_ctx.filters)), dataKey: ("id"), filterDisplay: ("menu"), loading: ((__VLS_ctx.loading)), selectAll: ((false)), globalFilterFields: ((['name', 'gerencia', 'start_date', 'end_date', 'hoursPerDay', 'daysPerWeek', 'daysPerMonth'])), currentPageReportTemplate: (" {first} al {last} de {totalRecords}"), paginatorTemplate: ("FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"), paginator: ((true)), rows: ((5)), rowsPerPageOptions: (([5, 10, 15, 50])), });
                        const __VLS_8 = __VLS_templateComponents.DataTable({ id: ("tabla"), stripedRows: (true), class: ("p-datatable-sm"), value: ((__VLS_ctx.contracts)), selection: ((__VLS_ctx.selectedContracts)), filters: ((__VLS_ctx.filters)), dataKey: ("id"), filterDisplay: ("menu"), loading: ((__VLS_ctx.loading)), selectAll: ((false)), globalFilterFields: ((['name', 'gerencia', 'start_date', 'end_date', 'hoursPerDay', 'daysPerWeek', 'daysPerMonth'])), currentPageReportTemplate: (" {first} al {last} de {totalRecords}"), paginatorTemplate: ("FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"), paginator: ((true)), rows: ((5)), rowsPerPageOptions: (([5, 10, 15, 50])), });
                        // @ts-ignore
                        [contracts, selectedContracts, filters, loading, contracts, selectedContracts, filters, loading,];
                        let __VLS_9!: import('./__VLS_types.js').ExtractComponentSlots<import('./__VLS_types.js').PickNotAny<typeof __VLS_7, typeof __VLS_8>>;
                        __VLS_9.header;
                        {
                            ({} as JSX.IntrinsicElements).div;
                            ({} as JSX.IntrinsicElements).div;
                            (__VLS_x as JSX.IntrinsicElements)['div'] = { class: ("flex justify-between w-full h-8 mb-2 "), };
                            {
                                ({} as JSX.IntrinsicElements).div;
                                ({} as JSX.IntrinsicElements).div;
                                (__VLS_x as JSX.IntrinsicElements)['div'] = { class: ("flex space-x-4"), };
                                {
                                    ({} as JSX.IntrinsicElements).div;
                                    ({} as JSX.IntrinsicElements).div;
                                    (__VLS_x as JSX.IntrinsicElements)['div'] = { class: ("w-8"), title: ("Filtrar Contratos"), };
                                    {
                                        __VLS_templateComponents.Button;
                                        (__VLS_x as import('./__VLS_types.js').ComponentProps<typeof __VLS_templateComponents.Button>) = { type: ("button"), severity: ("primary"), class: ("hover:bg-primary "), };
                                        const __VLS_10 = new __VLS_templateComponents.Button({ type: ("button"), severity: ("primary"), class: ("hover:bg-primary "), });
                                        const __VLS_11 = __VLS_templateComponents.Button({ type: ("button"), severity: ("primary"), class: ("hover:bg-primary "), });
                                        let __VLS_12!: import('./__VLS_types.js').PickNotAny<typeof __VLS_10, typeof __VLS_11>;
                                        type __VLS_13 = import('./__VLS_types.js').InstanceProps<typeof __VLS_12, typeof __VLS_templateComponents.Button>;
                                        const __VLS_14: import('./__VLS_types.js').EventObject<typeof __VLS_12, 'click', typeof __VLS_templateComponents.Button, __VLS_13['onClick']> = {
                                            click: $event => {
                                                __VLS_ctx.clearFilter();
                                            }
                                        };
                                        // @ts-ignore
                                        [clearFilter,];
                                        {
                                            ({} as JSX.IntrinsicElements).i;
                                            ({} as JSX.IntrinsicElements).i;
                                            (__VLS_x as JSX.IntrinsicElements)['i'] = { class: ("pi pi-filter-slash"), style: ({}), };
                                        }
                                    }
                                }
                                {
                                    ({} as JSX.IntrinsicElements).div;
                                    ({} as JSX.IntrinsicElements).div;
                                    (__VLS_x as JSX.IntrinsicElements)['div'] = { class: ("relative flex rounded-md shadow-sm"), };
                                    {
                                        ({} as JSX.IntrinsicElements).div;
                                        ({} as JSX.IntrinsicElements).div;
                                        (__VLS_x as JSX.IntrinsicElements)['div'] = { class: ("absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"), };
                                        {
                                            (__VLS_x as import('./__VLS_types.js').ComponentProps<typeof __VLS_templateComponents.MagnifyingGlassIcon>) = { class: ("w-5 h-4 text-gray-400"), 'aria-hidden': ("true"), };
                                        }
                                    }
                                    {
                                        ({} as JSX.IntrinsicElements).input;
                                        (__VLS_x as JSX.IntrinsicElements)['input'] = { type: ("search"), title: ("Buscar Contrato"), class: ("block w-10/12 py-4 pl-10 text-gray-900 border-0 rounded-md ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"), placeholder: ("Buscar..."), };
                                        (__VLS_ctx.filters.global.value);
                                        // @ts-ignore
                                        [filters,];
                                    }
                                }
                            }
                        }
                    }
                    {
                        __VLS_templateComponents.Column;
                        (__VLS_x as import('./__VLS_types.js').ComponentProps<typeof __VLS_templateComponents.Column>) = { selectionMode: ("multiple"), headerStyle: ("width: 3rem"), };
                    }
                    {
                        __VLS_templateComponents.Column;
                        (__VLS_x as import('./__VLS_types.js').ComponentProps<typeof __VLS_templateComponents.Column>) = { field: ("contract_id"), header: ("Contrato"), };
                    }
                    {
                        __VLS_templateComponents.Column;
                        (__VLS_x as import('./__VLS_types.js').ComponentProps<typeof __VLS_templateComponents.Column>) = { header: ("Porcentaje"), };
                        {
                            ({} as JSX.IntrinsicElements).template;
                            ({} as JSX.IntrinsicElements).template;
                            (__VLS_x as JSX.IntrinsicElements)['template'] = {};
                            const __VLS_15 = new __VLS_templateComponents.Column({ header: ("Porcentaje"), });
                            const __VLS_16 = __VLS_templateComponents.Column({ header: ("Porcentaje"), });
                            let __VLS_17!: import('./__VLS_types.js').ExtractComponentSlots<import('./__VLS_types.js').PickNotAny<typeof __VLS_15, typeof __VLS_16>>;
                            const slotProps = __VLS_17.body;
                            (((slotProps.data.cost / __VLS_ctx.suma) * 100).toFixed(2));
                            // @ts-ignore
                            [suma,];
                        }
                    }
                    {
                        __VLS_templateComponents.Column;
                        (__VLS_x as import('./__VLS_types.js').ComponentProps<typeof __VLS_templateComponents.Column>) = { field: ("cost"), header: ("Costo"), };
                        {
                            ({} as JSX.IntrinsicElements).template;
                            ({} as JSX.IntrinsicElements).template;
                            (__VLS_x as JSX.IntrinsicElements)['template'] = {};
                            const __VLS_18 = new __VLS_templateComponents.Column({ field: ("cost"), header: ("Costo"), });
                            const __VLS_19 = __VLS_templateComponents.Column({ field: ("cost"), header: ("Costo"), });
                            let __VLS_20!: import('./__VLS_types.js').ExtractComponentSlots<import('./__VLS_types.js').PickNotAny<typeof __VLS_18, typeof __VLS_19>>;
                            const slotProps = __VLS_20.body;
                            (__VLS_ctx.formatCurrency(slotProps.data.cost));
                            // @ts-ignore
                            [formatCurrency,];
                        }
                    }
                    {
                        __VLS_templateComponents.Column;
                        (__VLS_x as import('./__VLS_types.js').ComponentProps<typeof __VLS_templateComponents.Column>) = { field: ("end_date"), header: ("Fecha Finalización"), };
                    }
                }
                {
                    ({} as JSX.IntrinsicElements).div;
                    ({} as JSX.IntrinsicElements).div;
                    (__VLS_x as JSX.IntrinsicElements)['div'] = { class: ("ml-1"), };
                    {
                        ({} as JSX.IntrinsicElements).div;
                        ({} as JSX.IntrinsicElements).div;
                        (__VLS_x as JSX.IntrinsicElements)['div'] = { class: ("max-w-full p-3 md:max-w-full"), };
                        {
                            __VLS_templateComponents.PieChart;
                            (__VLS_x as import('./__VLS_types.js').ComponentProps<typeof __VLS_templateComponents.PieChart>) = { title: ((__VLS_ctx.title)), series: ((__VLS_ctx.series)), key: ((__VLS_ctx.showGraph)), };
                            // @ts-ignore
                            [title, series, showGraph,];
                        }
                    }
                }
            }
            if (typeof __VLS_styleScopedClasses === 'object' && !Array.isArray(__VLS_styleScopedClasses)) {
            }
            declare var __VLS_slots: {};
            return __VLS_slots;
        }
        const __VLS_internalComponent = (await import('vue')).defineComponent({
            setup() {
                return {
                    DataTable: DataTable,
                    Column: Column,
                    MagnifyingGlassIcon: MagnifyingGlassIcon,
                    Button: Button,
                    PieChart: PieChart,
                    suma: suma,
                    loading: loading,
                    filters: filters,
                    clearFilter: clearFilter,
                    onRowSelect: onRowSelect,
                    onRowUnselect: onRowUnselect,
                    formatCurrency: formatCurrency,
                    title: title,
                    contracts: contracts,
                    selectedContracts: selectedContracts,
                    showGraph: showGraph,
                    series: series,
                };
            },
        });
        return {} as typeof __VLS_publicComponent;
    };
    return {} as typeof __VLS_setup extends () => Promise<infer T> ? T : never;
})({} as any);
