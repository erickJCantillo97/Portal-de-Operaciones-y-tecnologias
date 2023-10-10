export const applyDrag = async (arr, dragResult, fecha, task) => {
    let statusConsult = false
    const { removedIndex, addedIndex, payload } = dragResult;
    if (removedIndex === null && addedIndex === null) return arr;
    const result = [...arr];
    let itemToAdd = payload;

    if (removedIndex !== null) {
        itemToAdd = result.splice(removedIndex, 1)[0];
    }

    if (addedIndex !== null) {
        result.splice(addedIndex, 0, itemToAdd);
        axios.post(route('programming.store'),{task_id:task,employee_id:payload.Num_SAP,fecha:fecha}).then((res) => {
            statusConsult = res.data.status
        })
        if(statusConsult){
            console.log("Consulta Terminada")
            // result.splice(addedIndex, 0, itemToAdd);
            console.log(result);
        }
    }

    return result;
};


