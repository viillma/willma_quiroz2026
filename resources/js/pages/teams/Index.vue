<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Eye, Pencil, Plus } from 'lucide-vue-next';
import CreateTeamModal from '@/components/CreateTeamModal.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { edit, index } from '@/routes/teams';
import type { Team } from '@/types';

type Props = {
    teams: Team[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Teams',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Teams" />

    <h1 class="sr-only">Teams</h1>

    <div class="animate-fade-in p-4 md:p-6 lg:p-8 space-y-6 relative">
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="Teams"
                description="Manage your teams and team memberships"
            />

            <CreateTeamModal>
                <Button data-test="teams-new-team-button" class="btn-gradient-blue btn-shimmer">
                    <Plus /> New team
                </Button>
            </CreateTeamModal>
        </div>

        <div class="space-y-3">
            <div
                v-for="team in teams"
                :key="team.id"
                data-test="team-row"
                class="glass-card rounded-2xl p-4 flex items-center justify-between card-glow transition-all duration-300"
            >
                <div class="flex items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-white">{{ team.name }}</span>
                            <Badge v-if="team.isPersonal" variant="secondary" class="badge-gray">
                                Personal
                            </Badge>
                        </div>
                        <span class="text-sm text-white/60">
                            {{ team.roleLabel }}
                        </span>
                    </div>
                </div>

                <TooltipProvider>
                    <div class="flex items-center gap-2">
                        <Tooltip v-if="team.role === 'member'">
                            <TooltipTrigger as-child>
                                <Button
                                    data-test="team-view-button"
                                    variant="ghost"
                                    size="sm"
                                    as-child
                                    class="btn-gradient-ghost"
                                >
                                    <Link :href="edit(team.slug)">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>View team</p>
                            </TooltipContent>
                        </Tooltip>

                        <Tooltip v-else>
                            <TooltipTrigger as-child>
                                <Button
                                    data-test="team-edit-button"
                                    variant="ghost"
                                    size="sm"
                                    as-child
                                    class="btn-gradient-ghost"
                                >
                                    <Link :href="edit(team.slug)">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <p>Edit team</p>
                            </TooltipContent>
                        </Tooltip>
                    </div>
                </TooltipProvider>
            </div>

            <p
                v-if="teams.length === 0"
                class="py-8 text-center text-white/60"
            >
                You don't belong to any teams yet.
            </p>
        </div>
    </div>
</template>
