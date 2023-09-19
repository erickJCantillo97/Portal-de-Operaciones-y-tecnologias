import { Toolbar, Toast, DateHelper, CSSHelper } from "@bryntum/gantt";

/**
 * @module GanttToolbar
 */

/**
 * @extends Core/widget/Toolbar
 */
export default class GanttToolbar extends Toolbar {
    // Factoryable type name
    static get type() {
        return "gantttoolbar";
    }

    static get $name() {
        return "GanttToolbar";
    }

    // Called when toolbar is added to the Gantt panel
    set parent(parent) {
        super.parent = parent;

        const me = this;

        me.gantt = parent;

        parent.project.on({
            load: "updateStartDateField",
            refresh: "updateStartDateField",
            thisObj: me
        });

        me.styleNode = document.createElement("style");
        document.head.appendChild(me.styleNode);
    }

    get parent() {
        return super.parent;
    }

    static get configurable() {
        return {
            items: [
                {
                    type: "buttonGroup",
                    items: [
                        {
                            color: "b-green",
                            ref: "addTaskButton",
                            icon: "b-fa b-fa-plus",
                            text: "",
                            tooltip: "Crear una nueva tarea",
                            onAction: "up.onAddTaskClick"
                        }
                    ]
                },
                {
                    type: "buttonGroup",
                    items: [
                        {
                            ref: "editTaskButton",
                            icon: "b-fa b-fa-pen",
                            text: "",
                            tooltip: "Editar celda seleccionada",
                            onAction: "up.onEditTaskClick"
                        },
                        {
                            ref: "undoRedo",
                            type: "undoredo",
                            items: {
                                transactionsCombo: null
                            }
                        }
                    ]
                },
                {
                    type: "buttonGroup",
                    items: [
                        {
                            ref: "expandAllButton",
                            icon: "b-fa b-fa-angle-double-down",
                            tooltip: "Expandir todo",
                            onAction: "up.onExpandAllClick"
                        },
                        {
                            ref: "collapseAllButton",
                            icon: "b-fa b-fa-angle-double-up",
                            tooltip: "Colapsar todo",
                            onAction: "up.onCollapseAllClick"
                        }
                    ]
                },

                {
                    type: "buttonGroup",
                    items: [
                        {
                            type: "button",
                            ref: "settingsButton",
                            tooltip: "Ajustes",
                            toggleable: true,
                            icon: 'b-fa-gear',
                            menu: {
                                type: "popup",
                                anchor: true,
                                cls: "settings-menu",
                                layoutStyle: {
                                    flexDirection: "column"
                                },
                                onBeforeShow: "up.onSettingsShow",

                                items: [
                                    {
                                        type: "slider",
                                        ref: "rowHeight",
                                        text: "Altura de celdas",
                                        width: "12em",
                                        showValue: true,
                                        min: 10,
                                        max: 70,
                                        onInput: "up.onSettingsRowHeightChange"
                                    },
                                    {
                                        type: "slider",
                                        ref: "barMargin",
                                        text: "Altura barras",
                                        width: "12em",
                                        showValue: true,
                                        min: 0,
                                        max: 10,
                                        onInput: "up.onSettingsMarginChange"
                                    },
                                    {
                                        type: "slider",
                                        ref: "duration",
                                        text: "Duracion animaciones ",
                                        width: "12em",
                                        min: 0,
                                        max: 2000,
                                        step: 100,
                                        showValue: true,
                                        onInput: "up.onSettingsDurationChange"
                                    }
                                ]
                            }
                        },
                        // {
                        //     type: "button",
                        //     color: "b-red",
                        //     ref: "criticalPathsButton",
                        //     icon: "b-fa b-fa-fire",
                        //     text: "Critical paths",
                        //     tooltip: "Highlight critical paths",
                        //     toggleable: true,
                        //     onAction: "up.onCriticalPathsClick"
                        // }
                    ]
                },
                {
                    type: "datefield",
                    ref: "startDateField",
                    placeholder: "Fecha de Inicio",
                    show: false,
                    required  : 'done',
                    flex: "0 0 17em",
                    listeners: {
                        change: "up.onStartDateChange"
                    }
                },
                {
                    type: "textfield",
                    ref: "filterByName",
                    cls: "filter-by-name",
                    flex: "0 0 12.5em",
                    // Placeholder for others
                    placeholder: "Buscar Actividad",
                    clearable: true,
                    keyStrokeChangeDelay: 100,
                    triggers: {
                        filter: {
                            align: "end",

                        }
                    },
                    onChange: "up.onFilterChange"
                },
                {
                    type : 'button',
                    text : 'Exportar',
                    ref  : 'mspExportBtn',
                    icon : 'b-fa-file-export',
                    onAction: "up.onExport"
                }
            ]
        };
    }
    onExport() {
        // give a filename based on task name
        const filename = this.gantt.project.taskStore.first && `${this.gantt.project.taskStore.first.name}.xml`;

        // call the export to download the XML file
        this.gantt.features.mspExport.export({
            filename
        });
    }

    setAnimationDuration(value) {
        const me = this,
            cssText = `.b-animating .b-gantt-task-wrap { transition-duration: ${value /
                1000}s !important; }`;

        me.gantt.transitionDuration = value;

        if (me.transitionRule) {
            me.transitionRule.cssText = cssText;
        } else {
            me.transitionRule = CSSHelper.insertRule(cssText);
        }
    }

    updateStartDateField() {
        const { startDateField } = this.widgetMap;

        startDateField.value = this.gantt.project.startDate;

        // This handler is called on project.load/propagationComplete, so now we have the
        // initial start date. Prior to this time, the empty (default) value would be
        // flagged as invalid.
        startDateField.required = true;
    }

    // region controller methods

    async onAddTaskClick() {
        const { gantt } = this,
            added = gantt.taskStore.rootNode.appendChild({
                name: "New task",
                duration: 1
            });

        // wait for immediate commit to calculate new task fields
        await gantt.project.commitAsync();

        // scroll to the added task
        await gantt.scrollRowIntoView(added);

        gantt.features.cellEdit.startEditing({
            record: added,
            field: "name"
        });
    }

    onEditTaskClick() {
        const { gantt } = this;

        if (gantt.selectedRecord) {
            gantt.editTask(gantt.selectedRecord);
        } else {
            Toast.show("First select the task you want to edit");
        }
    }

    onExpandAllClick() {
        this.gantt.expandAll();
    }

    onCollapseAllClick() {
        this.gantt.collapseAll();
    }

    onZoomInClick() {
        this.gantt.zoomIn();
    }

    onZoomOutClick() {
        this.gantt.zoomOut();
    }

    onZoomToFitClick() {
        this.gantt.zoomToFit({
            leftMargin: 50,
            rightMargin: 50
        });
    }

    onShiftPreviousClick() {
        this.gantt.shiftPrevious();
    }

    onShiftNextClick() {
        this.gantt.shiftNext();
    }

    onStartDateChange({ value, oldValue }) {
        if (!oldValue) {
            // ignore initial set
            return;
        }

        this.gantt.startDate = DateHelper.add(value, -1, "week");

        this.gantt.project.setStartDate(value);
    }

    onFilterChange({ value }) {
        if (value === "") {
            this.gantt.taskStore.clearFilters();
        } else {
            value = value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

            this.gantt.taskStore.filter({
                filters: task =>
                    task.name && task.name.match(new RegExp(value, "i")),
                replace: true
            });
        }
    }

    onFeaturesClick({ source: item }) {
        const { gantt } = this;

        if (item.feature) {
            const feature = gantt.features[item.feature];
            feature.disabled = !feature.disabled;
        } else if (item.subGrid) {
            const subGrid = gantt.subGrids[item.subGrid];
            subGrid.collapsed = !subGrid.collapsed;
        }
    }

    onFeaturesShow({ source: menu }) {
        const { gantt } = this;

        menu.items.map(item => {
            const { feature } = item;

            if (feature) {
                // a feature might be not presented in the gantt
                // (the code is shared between "advanced" and "php" demos which use a bit different set of features)
                if (gantt.features[feature]) {
                    item.checked = !gantt.features[feature].disabled;
                }
                // hide not existing features
                else {
                    item.hide();
                }
            } else {
                item.checked = gantt.subGrids[item.subGrid].collapsed;
            }
        });
    }

    onSettingsShow({ source: menu }) {
        const { gantt } = this,
            { rowHeight, barMargin, duration } = menu.widgetMap;

        rowHeight.value = gantt.rowHeight;
        barMargin.value = gantt.barMargin;
        barMargin.max = gantt.rowHeight / 2 - 5;
        duration.value = gantt.transitionDuration;
    }

    onSettingsRowHeightChange({ value }) {
        this.gantt.rowHeight = value;
        this.widgetMap.settingsButton.menu.widgetMap.barMargin.max =
            value / 2 - 5;
    }

    onSettingsMarginChange({ value }) {
        this.gantt.barMargin = value;
    }

    onSettingsDurationChange({ value }) {
        this.gantt.transitionDuration = value;
        this.styleNode.innerHTML = `.b-animating .b-gantt-task-wrap { transition-duration: ${value /
            1000}s !important; }`;
    }

    onCriticalPathsClick({ source }) {
        this.gantt.features.criticalPaths.disabled = !source.pressed;
    }

    // endregion
}

// Register this widget type with its Factory
GanttToolbar.initClass();
