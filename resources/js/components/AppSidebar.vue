<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Mail,
    Globe,
    LayoutGrid,
    Users,
    Briefcase,
    Package,
    FileText,
    DollarSign,
    BarChart3,
    Shield,
    ShieldCheck,
    Settings,
    Image as ImageIcon,
    Layers,
    SlidersHorizontal,
    FolderOpen,
    FolderKanban,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarSeparator,
} from '@/components/ui/sidebar';
import { usePermissions } from '@/composables/usePermissions';
import { dashboard } from '@/routes';
import adminWeb from '@/routes/admin-web';
import catalogo from '@/routes/catalogo';
import clientes from '@/routes/clientes';
import configuracion from '@/routes/configuracion';
import cotizaciones from '@/routes/cotizaciones';
import media from '@/routes/media';
import pagos from '@/routes/pagos';
import pedidos from '@/routes/pedidos';
import portafolio from '@/routes/portafolio';
import reportes from '@/routes/reportes';
import roles from '@/routes/roles';
import servicios from '@/routes/servicios';
import usuarios from '@/routes/usuarios';
import type { NavItem } from '@/types';

const { can } = usePermissions();

const dashboardUrl = dashboard().url;

const catalogUrl = catalogo.index().url;

const webMenuItems: NavItem[] = [
    {
        title: 'Carrusel',
        href: adminWeb.carousel.index().url,
        icon: Layers,
        permission: 'web.ver',
    },
    {
        title: 'Galería',
        href: media.index().url,
        icon: FolderOpen,
        permission: 'web.ver',
    },
    {
        title: 'Configuración',
        href: adminWeb.settings.index().url,
        icon: SlidersHorizontal,
        permission: 'web.ver',
    },
];

const allNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboardUrl,
        icon: LayoutGrid,
        permission: 'panel.ver',
    },
    {
        title: 'Servicios',
        href: servicios.index().url,
        icon: Briefcase,
        permission: 'servicios.ver',
    },
    {
        title: 'Clientes',
        href: clientes.index().url,
        icon: Users,
        permission: 'clientes.ver',
    },
    {
        title: 'Pedidos',
        href: pedidos.index().url,
        icon: Package,
        permission: 'pedidos.ver',
    },
    {
        title: 'Cotizaciones',
        href: cotizaciones.index().url,
        icon: FileText,
        permission: 'cotizaciones.ver',
    },
    {
        title: 'Catálogo',
        href: catalogUrl,
        icon: ImageIcon,
        permission: 'catalogo.ver',
    },
    {
        title: 'Diseños',
        href: portafolio.index().url,
        icon: FolderKanban,
        permission: 'diseños.ver',
    },
    {
        title: 'Pagos',
        href: pagos.index().url,
        icon: DollarSign,
        permission: 'pagos.ver',
    },
    {
        title: 'Reportes',
        href: reportes.index().url,
        icon: BarChart3,
        permission: 'reportes.ver',
    },
    {
        title: 'Sitio web',
        href: '#',
        icon: Globe,
        items: webMenuItems,
    },
    {
        title: 'Usuarios',
        href: usuarios.index().url,
        icon: Shield,
        permission: 'usuarios.gestionar',
    },
    {
        title: 'Roles',
        href: roles.index().url,
        icon: ShieldCheck,
        permission: 'usuarios.gestionar',
    },
    {
        title: 'Configuración',
        href: configuracion.index().url,
        icon: Settings,
        permission: 'configuracion.ver',
    },
]);

const groupMap: Record<string, string[]> = {
    Principal: ['Dashboard'],
    Gestión: [
        'Servicios',
        'Clientes',
        'Pedidos',
        'Cotizaciones',
        'Catálogo',
        'Diseños',
        'Reportes',
    ],
    Finanzas: ['Pagos'],
    'Sitio Web': ['Sitio web'],
    Configuración: ['Usuarios', 'Roles', 'Configuración'],
};

function isItemVisible(item: NavItem): boolean {
    if (!item.permission || can(item.permission)) {
        return true;
    }

    return false;
}

const navGroups = computed(() => {
    return Object.entries(groupMap)
        .map(([label, titles]) => ({
            label,
            items: allNavItems.value
                .filter((item) => titles.includes(item.title))
                .map((item) => {
                    if (item.items && item.items.length) {
                        const children = item.items.filter(isItemVisible);

                        return children.length
                            ? { ...item, items: children }
                            : null;
                    }

                    return isItemVisible(item) ? item : null;
                })
                .filter((item): item is NavItem => item !== null),
        }))
        .filter((group) => group.items.length > 0);
});

const footerNavItems: NavItem[] = [
    {
        title: 'hola@creapix.com',
        href: 'mailto:hola@creapix.com',
        icon: Mail,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardUrl">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <template v-for="(group, index) in navGroups" :key="group.label">
                <SidebarSeparator v-if="index > 0" class="mx-3 my-1" />
                <NavMain :groups="[group]" />
            </template>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
