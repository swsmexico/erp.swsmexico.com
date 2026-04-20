import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function usePermissions() {
    const page = usePage()

    const permissions = computed(() => page.props.auth?.permissions ?? [])
    const roles       = computed(() => page.props.auth?.user?.roles ?? [])

    const can  = (permission) => permissions.value.includes(permission)
    const hasRole = (role)    => roles.value.includes(role)
    const isAdmin = computed(() => hasRole('administrador'))

    return { can, hasRole, isAdmin, permissions, roles }
}
