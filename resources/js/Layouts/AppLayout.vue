<template>
    <div class="flex h-screen bg-gray-100 overflow-hidden" style="background: #F2F2F2;">

        <!-- Sidebar -->
        <aside
            :class="[
                'flex flex-col bg-white border-r border-gray-200 transition-all duration-300 z-30',
                sidebarOpen ? 'w-56' : 'w-16'
            ]"
        >
            <!-- Logo -->
            <div class="flex items-center h-16 px-4 border-b border-gray-200 shrink-0">
                <img src="/images/logo.png" alt="SWS" class="h-8 w-8 shrink-0" />
                <span
                    v-if="sidebarOpen"
                    class="ml-3 font-bold text-sm transition-opacity duration-200"
                    style="color: #0D0D0D;"
                >SWS Mexico</span>
            </div>

            <!-- Nav items -->
            <nav class="flex-1 py-4 overflow-y-auto overflow-x-hidden">
                <NavItem
                    v-for="item in navItems"
                    :key="item.route"
                    :item="item"
                    :collapsed="!sidebarOpen"
                />
            </nav>

            <!-- Toggle button -->
            <button
                @click="sidebarOpen = !sidebarOpen"
                class="flex items-center justify-center h-12 border-t border-gray-200 hover:bg-gray-50 transition-colors"
                style="color: #595959;"
            >
                <ChevronLeftIcon v-if="sidebarOpen" class="w-5 h-5" />
                <ChevronRightIcon v-else class="w-5 h-5" />
            </button>
        </aside>

        <!-- Main -->
        <div class="flex flex-col flex-1 min-w-0">

            <!-- Header -->
            <header class="flex items-center h-16 px-6 bg-white border-b border-gray-200 gap-4 shrink-0">

                <!-- Búsqueda global -->
                <div class="flex-1 max-w-lg relative">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#595959;" />
                    <input
                        v-model="searchQuery"
                        @input="onSearch"
                        @focus="searchOpen = true"
                        @blur="closeSearch"
                        type="text"
                        placeholder="Buscar clientes, prefacturas, proyectos..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:border-transparent"
                        style="--tw-ring-color: #5CC8F2;"
                    />
                    <!-- Resultados -->
                    <div
                        v-if="searchOpen && searchResults.length"
                        class="absolute top-full mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden"
                    >
                        <Link
                            v-for="result in searchResults"
                            :key="result.url"
                            :href="result.url"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 text-sm border-b border-gray-100 last:border-0"
                        >
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" :style="result.badgeStyle">
                                {{ result.tipo }}
                            </span>
                            <span style="color:#0D0D0D;">{{ result.nombre }}</span>
                        </Link>
                    </div>
                </div>

                <div class="flex items-center gap-3 ml-auto">
                    <!-- Notificaciones -->
                    <div class="relative">
                        <button
                            @click="notifOpen = !notifOpen"
                            class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors"
                            style="color:#595959;"
                        >
                            <BellIcon class="w-5 h-5" />
                            <span
                                v-if="notifCount > 0"
                                class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white rounded-full"
                                style="background:#5CC8F2;"
                            >{{ notifCount > 99 ? '99+' : notifCount }}</span>
                        </button>

                        <!-- Panel notificaciones -->
                        <div
                            v-if="notifOpen"
                            class="absolute right-0 top-full mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden"
                        >
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                <span class="font-semibold text-sm" style="color:#0D0D0D;">Notificaciones</span>
                                <button
                                    v-if="notifCount > 0"
                                    @click="marcarTodasLeidas"
                                    class="text-xs hover:underline"
                                    style="color:#5CC8F2;"
                                >Marcar todas leídas</button>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <NotifItem
                                    v-for="notif in notificaciones"
                                    :key="notif.id"
                                    :notif="notif"
                                    @click="irANotif(notif)"
                                />
                                <div v-if="!notificaciones.length" class="px-4 py-8 text-center text-sm" style="color:#595959;">
                                    Sin notificaciones pendientes
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usuario -->
                    <div class="relative">
                        <button
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors"
                        >
                            <div
                                class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                                style="background:#5CC8F2;"
                            >{{ userInitials }}</div>
                            <span class="text-sm hidden sm:block" style="color:#0D0D0D;">{{ $page.props.auth.user.name }}</span>
                        </button>

                        <div
                            v-if="userMenuOpen"
                            class="absolute right-0 top-full mt-2 w-44 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden"
                        >
                            <Link
                                href="/profile"
                                class="flex items-center gap-2 px-4 py-3 text-sm hover:bg-gray-50"
                                style="color:#0D0D0D;"
                            >
                                <UserIcon class="w-4 h-4" style="color:#595959;" />
                                Mi perfil
                            </Link>
                            <hr class="border-gray-100" />
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="flex items-center gap-2 w-full px-4 py-3 text-sm hover:bg-gray-50 text-left"
                                style="color:#0D0D0D;"
                            >
                                <ArrowRightOnRectangleIcon class="w-4 h-4" style="color:#595959;" />
                                Cerrar sesión
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>

        <!-- Overlay para cerrar menús en móvil -->
        <div
            v-if="notifOpen || userMenuOpen || searchOpen"
            class="fixed inset-0 z-20"
            @click="closeAll"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import {
    ChevronLeftIcon, ChevronRightIcon, MagnifyingGlassIcon,
    BellIcon, UserIcon, ArrowRightOnRectangleIcon,
    HomeIcon, UserGroupIcon, FolderIcon, CreditCardIcon,
    BanknotesIcon, DocumentTextIcon, ClipboardDocumentListIcon,
    Cog6ToothIcon, ChartBarIcon,
} from '@heroicons/vue/24/outline'
import NavItem from '@/Components/Layout/NavItem.vue'
import NotifItem from '@/Components/Layout/NotifItem.vue'

const page = usePage()
const sidebarOpen = ref(true)
const notifOpen = ref(false)
const userMenuOpen = ref(false)
const searchOpen = ref(false)
const searchQuery = ref('')
const searchResults = ref([])
const notificaciones = ref([])

const notifCount = computed(() => notificaciones.value.filter(n => !n.leida_at).length)

const userInitials = computed(() => {
    const name = page.props.auth.user?.name ?? ''
    return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase()
})

const can = (perm) => page.props.auth.permissions?.includes(perm)

const navItems = computed(() => [
    { label: 'Dashboard',        route: 'dashboard',      icon: ChartBarIcon,              show: can('dashboard.ver') },
    { label: 'Prospectos',       route: 'prospectos.index', icon: UserGroupIcon,            show: can('prospectos.ver') },
    { label: 'Proyectos',        route: 'proyectos.index', icon: FolderIcon,                show: can('proyectos.ver') },
    { label: 'Cobranza',         route: 'cobranza.index',  icon: CreditCardIcon,            show: can('cobranza.ver') },
    { label: 'Estados de Cuenta',route: 'estados.index',   icon: BanknotesIcon,             show: can('estados_cuenta.ver') },
    { label: 'Pagos',            route: 'pagos.index',     icon: DocumentTextIcon,          show: can('pagos.ver') },
    { label: 'Nóminas',          route: 'nominas.index',   icon: ClipboardDocumentListIcon, show: can('nominas.ver') },
    { label: 'Reporte Diario',   route: 'reporte.index',   icon: HomeIcon,                  show: can('reporte_diario.ver') },
    { label: 'Configuración',    route: 'config.index',    icon: Cog6ToothIcon,             show: can('configuracion.ver') },
].filter(i => i.show))

let searchTimeout = null
const onSearch = () => {
    clearTimeout(searchTimeout)
    if (!searchQuery.value.trim()) { searchResults.value = []; return }
    searchTimeout = setTimeout(async () => {
        const res = await axios.get('/api/buscar', { params: { q: searchQuery.value } })
        searchResults.value = res.data
    }, 300)
}

const closeSearch = () => setTimeout(() => { searchOpen.value = false }, 200)
const closeAll = () => { notifOpen.value = false; userMenuOpen.value = false; searchOpen.value = false }

const marcarTodasLeidas = async () => {
    await axios.post('/api/notificaciones/leer-todas')
    notificaciones.value.forEach(n => n.leida_at = new Date().toISOString())
}

const irANotif = (notif) => {
    notifOpen.value = false
    if (!notif.leida_at) axios.patch(`/api/notificaciones/${notif.id}/leer`)
    if (notif.url) router.visit(notif.url)
}

onMounted(async () => {
    try {
        const res = await axios.get('/api/notificaciones')
        notificaciones.value = res.data
    } catch {}
})
</script>
