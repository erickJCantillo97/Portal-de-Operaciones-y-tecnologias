export const applyDrag = async (arr, dragResult, fecha, task) => {
    const { removedIndex, addedIndex, payload } = dragResult;
    if (removedIndex === null && addedIndex === null) return arr;
    const result = [...arr];
    let itemToAdd = payload;

    if (removedIndex !== null) {
        itemToAdd = result.splice(removedIndex, 1)[0];
    }

    if (addedIndex !== null) {
        await axios.post(route('programming.store'), { task_id: task, employee_id: payload.Num_SAP, fecha: fecha }).then((res) => {
            result.splice(addedIndex, 0, itemToAdd);
        })
    }
    return result;
};


