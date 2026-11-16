import { usePage } from '@inertiajs/vue3';

export const DEFAULT_CURRENCY_SYMBOL = 'Bs';

/**
 * Función centralizada para formatear montos en Bolivianos (Bs).
 * Ejemplo: 80 -> "Bs 80,00" | 1000 -> "Bs 1.000,00"
 */
export function formatMoney(
    value: number | string | null | undefined,
    symbol?: string,
): string {
    const currentSymbol =
        symbol ??
        (usePage().props.settings as Record<string, unknown> | undefined)?.[
            'currency_symbol'
        ] ??
        DEFAULT_CURRENCY_SYMBOL;

    const num = Number(value ?? 0);

    return `${currentSymbol} ${num.toLocaleString('es-BO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
}

/**
 * Formatea un número sin símbolo de moneda. Ej.: 1000 -> "1.000,00"
 */
export function formatNumber(
    value: number | string | null | undefined,
): string {
    const num = Number(value ?? 0);

    return num.toLocaleString('es-BO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

/**
 * Formatea una fecha a formato local (d/m/Y).
 */
export function formatDate(value: string | Date | null | undefined): string {
    if (!value) {
        return '—';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '—';
    }

    return date.toLocaleDateString('es-BO', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

/**
 * Devuelve una fecha "hace X" en español.
 */
export function timeAgo(value: string | Date | null | undefined): string {
    if (!value) {
        return '—';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '—';
    }

    const seconds = Math.floor((Date.now() - date.getTime()) / 1000);
    const intervals: [number, string][] = [
        [31536000, 'año'],
        [2592000, 'mes'],
        [86400, 'día'],
        [3600, 'hora'],
        [60, 'minuto'],
    ];

    for (const [secondsPer, label] of intervals) {
        const count = Math.floor(seconds / secondsPer);

        if (count >= 1) {
            return count === 1 ? `hace 1 ${label}` : `hace ${count} ${label}s`;
        }
    }

    return 'hace un momento';
}
