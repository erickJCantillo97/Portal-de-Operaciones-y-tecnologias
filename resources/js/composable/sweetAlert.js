import Swal from 'sweetalert2/dist/sweetalert2.js'
import { router, useForm } from '@inertiajs/vue3';
import 'sweetalert2/src/sweetalert2.scss'
import {singularize, camelize}  from 'inflected';

export function useSweetalert(){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    const confirmDelete = (id, title) => Swal.fire({
        title: 'Estas Seguro que desea eliminar '+title,
        showCancelButton: true,
        confirmButtonText: 'Si, Eliminar',
        icon: 'warning',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            router.delete(route(title.toLowerCase()+'.destroy', id), {
                onSuccess: () => {
                    Toast.fire({ icon: 'success', title: title +' Elimnado'})
                }
            })
        },
            allowOutsideClick: () => !Swal.isLoading()
        })

    const toast = (msg, severity) => Toast.fire({ icon: severity, title: camelize(singularize(msg, 'es'))})

    return {toast, confirmDelete}
}
