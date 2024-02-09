import { usePage } from "@inertiajs/vue3"

export function usePermissions() {
    const hasRole = (name) => {

        return usePage().props.auth.user.roles.includes(name);
    }
    const hasPermission = (name) => {
        if (Array.isArray(name)) {
            return name.some(permiso => usePage().props.auth.user.permissions.includes(permiso));
        }
        return usePage().props.auth.user.permissions.includes(name);
    }

    return { hasRole, hasPermission }
}
