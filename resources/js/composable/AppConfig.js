export const useGanttConfig = () => {
    return {

        columns : [{ type : 'name', width : 160 }],

        startDate : new Date(2022, 0, 1),
        endDate   : new Date(2022, 0, 10)
    };
};

export const useProjectConfig = () => {
    return {
        startDate : new Date(2022, 0, 1)
    };
};
