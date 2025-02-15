import Swal from "sweetalert2/dist/sweetalert2";
import { router } from "@inertiajs/vue3";
// import "sweetalert2/src/sweetalert2.scss";
import { singularize, camelize } from "inflected";

export function useSweetalert() {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,

        timer: 3000,
        timerProgressBar: true,
        confirmButtonColor: "#e7515a",
        customClass: { container: "toast" },
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });
    const confirmDelete = (id, title, url) =>
        Swal.fire({
            title: "¿Está seguro de eliminar este " + title + "?",
            text: "Esta acción no podrá ser reestablecida 😕",
            showCancelButton: true,
            confirmButtonText: "Sí, Eliminar",
            icon: "warning",
            confirmButtonColor: "#e7515a",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                router.delete(route(url + ".destroy", id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        Toast.fire({
                            icon: "success",
                            title: title + " Eliminado",
                        });
                    },
                    onError: (error) => {
                        console.log(error)
                        Toast.fire({
                            icon: "error",
                            title: error.message,
                        });
                    }
                });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        });

    const toast = (msg, severity) =>
        Toast.fire({ icon: severity, title: camelize(singularize(msg, "es")) });

    return { toast, confirmDelete };
}
