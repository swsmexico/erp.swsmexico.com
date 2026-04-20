import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useFlash() {
    const page = usePage()

    const success = computed(() => page.props.flash?.success)
    const error   = computed(() => page.props.flash?.error)
    const warning = computed(() => page.props.flash?.warning)

    return { success, error, warning }
}
