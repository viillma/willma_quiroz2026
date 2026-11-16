import { computed } from 'vue';

export interface InvoiceLine {
    key: number;
    servicio_id: number | null;
    quantity: number;
    unit_price: number;
}

/**
 * Lógica centralizada para calcular subtotal, descuento, total y saldo
 * de pedidos y cotizaciones. Nunca repetir estos cálculos en las pantallas.
 */
export function useInvoiceCalculator(lines: { value: InvoiceLine[] }) {
    const subtotal = computed(() =>
        lines.value.reduce(
            (sum, item) =>
                sum + (Number(item.quantity) * Number(item.unit_price) || 0),
            0,
        ),
    );

    const totalAfterDiscount = (discount: number | string): number => {
        const desc = Number(discount || 0);

        return Math.max(0, subtotal.value - desc);
    };

    const balance = (total: number, paid: number): number =>
        Math.max(0, total - paid);

    return { subtotal, totalAfterDiscount, balance };
}
