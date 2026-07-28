<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, FolderGit2, LayoutGrid, Users, Briefcase, Package, Palette, DollarSign, BarChart3, Shield, ShieldCheck, Settings, Image as ImageIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TeamSwitcher from '@/components/TeamSwitcher.vue';
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
import { dashboard } from '@/routes';
import servicios from '@/routes/servicios';
import usuarios from '@/routes/usuarios';
import clientes from '@/routes/clientes';
import pedidos from '@/routes/pedidos';
import reportes from '@/routes/reportes';
import roles from '@/routes/roles';
import { usePermissions } from '@/composables/usePermissions';
import type { NavItem } from '@/types';

const page = usePage();
const { can } = usePermissions();

const teamSlug = computed(() => {
    const team = page.props.currentTeam as { slug: string } | null;
    return team?.slug ?? '';
});

const dashboardUrl = computed(() =>
    teamSlug.value ? dashboard(teamSlug.value).url : '/',
);

const catalogUrl = computed(() =>
    teamSlug.value ? `/${teamSlug.value}/catalogo` : '#',
);

const allNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboardUrl.value,
        icon: LayoutGrid,
        permission: 'panel.ver',
    },
    {
        title: 'Servicios',
        href: teamSlug.value ? servicios.index(teamSlug.value).url : '#',
        icon: Briefcase,
        permission: 'servicios.ver',
    },
    {
        title: 'Clientes',
        href: teamSlug.value ? clientes.index(teamSlug.value).url : '#',
        icon: Users,
        permission: 'clientes.ver',
    },
    {
        title: 'Pedidos',
        href: teamSlug.value ? pedidos.index(teamSlug.value).url : '#',
        icon: Package,
        permission: 'pedidos.ver',
    },
    {
        title: 'Catálogo',
        href: catalogUrl.value,
        icon: ImageIcon,
        permission: 'panel.ver',
    },
    {
        title: 'Diseños',
        href: '#',
        icon: Palette,
        permission: 'diseños.ver',
    },
    {
        title: 'Pagos',
        href: '#',
        icon: DollarSign,
        permission: 'pagos.ver',
    },
    {
        title: 'Reportes',
        href: teamSlug.value ? reportes.index(teamSlug.value).url : '#',
        icon: BarChart3,
        permission: 'reportes.ver',
    },
    {
        title: 'Usuarios',
        href: teamSlug.value ? usuarios.index(teamSlug.value).url : '#',
        icon: Shield,
        permission: 'usuarios.gestionar',
    },
    {
        title: 'Roles',
        href: teamSlug.value ? roles.index(teamSlug.value).url : '#',
        icon: ShieldCheck,
        permission: 'usuarios.gestionar',
    },
    {
        title: 'Configuración',
        href: '#',
        icon: Settings,
        permission: 'configuracion.ver',
    },
]);

const groupMap: Record<string, string[]> = {
    Principal: ['Dashboard'],
    Gestión: ['Servicios', 'Clientes', 'Pedidos', 'Catálogo', 'Diseños', 'Pagos', 'Reportes'],
    Configuración: ['Usuarios', 'Roles', 'Configuración'],
};

const navGroups = computed(() => {
    const visible = allNavItems.value.filter(
        (item) => !item.permission || can(item.permission),
    );
    return Object.entries(groupMap)
        .map(([label, titles]) => ({
            label,
            items: visible.filter((item) => titles.includes(item.title)),
        }))
        .filter((group) => group.items.length > 0);
});

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
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
            <SidebarMenu>
                <SidebarMenuItem>
                    <TeamSwitcher />
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
