export const applyDrag = async (arr, dragResult, fecha, task) => {
    const { removedIndex, addedIndex, payload } = dragResult;
    if (removedIndex === null && addedIndex === null) return arr;
    let result = [...arr];
    let itemToAdd = payload;

    if (removedIndex !== null) {
        itemToAdd = result.splice(removedIndex, 1)[0];
    }

    if (addedIndex !== null) {
        let taskResponse;
        await axios.post(route('programming.store'),{task_id:task,employee_id:payload.Num_SAP,fecha:fecha}).then((res) => {
            taskResponse = res.data.task[0]
        })
        result = taskResponse.people;
    }
    return result;
};


