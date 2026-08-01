<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        {{-- Barra de acciones (no se imprime) --}}
        <div class="mb-6 flex justify-between items-center no-print">
            <a href="{{ route('facturas.index') }}"
                class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">&larr; Volver a facturas</a>
            <div class="flex gap-2">
                @if ($factura->estado === 'emitida')
                    <button wire:click="anular" wire:confirm="¿Estás seguro de anular esta factura?"
                        class="btn border-red-500 text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10">Anular</button>
                @endif
                <button type="button" onclick="window.print()"
                    class="btn border-slate-300 text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">
                    Imprimir
                </button>
                <a href="{{ route('facturas.pdf', $factura->id) }}" target="_blank"
                    class="btn bg-teal-600 hover:bg-teal-700 text-white">Descargar PDF</a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-emerald-500/15 text-emerald-700 no-print">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700 no-print">{{ session('error') }}</div>
        @endif

        {{-- Documento --}}
        <div id="factura-print"
            class="relative bg-white dark:bg-slate-800 shadow-lg rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700">
            {{-- Banda superior --}}
            <div class="h-2 bg-gradient-to-r from-teal-600 to-emerald-500"></div>

            {{-- Marca de agua ANULADA --}}
            @if ($factura->estado === 'anulada')
                <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <span class="text-[7rem] font-black text-red-500/10 rotate-[-25deg] tracking-widest select-none">ANULADA</span>
                </div>
            @endif

            <div class="p-8 relative">
                {{-- Cabecera --}}
                <div class="flex justify-between items-start pb-6 mb-6 border-b border-gray-200 dark:border-slate-700">
                    <div>
                        <p class="text-xl font-bold text-teal-700 dark:text-teal-400 tracking-tight">AEROLÍNEA EL TROMPILLO S.A.</p>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                            NIT: 1029384756<br>
                            Av. Santos Dumont, Santa Cruz de la Sierra — Bolivia<br>
                            Tel: (591) 3-123-4567 · facturacion@trompillo.bo
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-800 dark:text-gray-200 tracking-[.2em]">FACTURA</p>
                        <p class="text-2xl font-extrabold text-teal-700 dark:text-teal-400 mt-1">N.º {{ $factura->numero_factura }}</p>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                            <p><span class="font-medium text-gray-600 dark:text-gray-300">Emisión:</span>
                                {{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y H:i') }}</p>
                            @if ($factura->venta)
                                <p><span class="font-medium text-gray-600 dark:text-gray-300">Venta:</span>
                                    <a href="{{ route('ventas.show', $factura->venta_id) }}" class="text-teal-600 hover:underline">{{ $factura->venta->numero }}</a>
                                </p>
                            @endif
                            @if ($factura->venta && $factura->venta->reserva)
                                <p><span class="font-medium text-gray-600 dark:text-gray-300">Reserva:</span> {{ $factura->venta->reserva->codigo }}</p>
                            @endif
                        </div>
                        <span class="inline-block mt-3 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                            {{ $factura->estado === 'anulada' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $factura->estado === 'anulada' ? 'Anulada' : 'Emitida' }}
                        </span>
                    </div>
                </div>

                {{-- Cliente --}}
                <div class="rounded-lg border border-teal-100 dark:border-teal-500/20 bg-teal-50/60 dark:bg-teal-500/5 border-l-4 border-l-teal-600 p-4 mb-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400">Facturado a</p>
                            <p class="font-bold text-gray-800 dark:text-gray-100 mt-0.5">{{ $factura->razon_social ?: 'S/N' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400">NIT / CI</p>
                            <p class="font-bold text-gray-800 dark:text-gray-100 mt-0.5">{{ $factura->nit ?: '0' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Detalle --}}
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-teal-600 text-white">
                            <th class="py-2.5 px-3 text-xs font-semibold uppercase tracking-wide rounded-l-md">Cant.</th>
                            <th class="py-2.5 px-3 text-xs font-semibold uppercase tracking-wide">Descripción</th>
                            <th class="py-2.5 px-3 text-xs font-semibold uppercase tracking-wide text-right">P. Unitario</th>
                            <th class="py-2.5 px-3 text-xs font-semibold uppercase tracking-wide text-right rounded-r-md">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($factura->venta?->detalles ?? [] as $d)
                            <tr class="border-b border-gray-100 dark:border-slate-700">
                                <td class="py-3 px-3 text-gray-700 dark:text-gray-300">{{ $d->cantidad }}</td>
                                <td class="py-3 px-3 text-gray-700 dark:text-gray-300">
                                    {{ $d->descripcion }}
                                    @if ($d->boleto)
                                        <div class="text-xs text-gray-400 mt-0.5">Boleto: {{ $d->boleto->numero_boleto }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-gray-700 dark:text-gray-300 text-right">Bs {{ number_format($d->precio_unitario, 2) }}</td>
                                <td class="py-3 px-3 text-gray-700 dark:text-gray-300 text-right font-medium">Bs {{ number_format($d->subtotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr class="border-b border-gray-100 dark:border-slate-700">
                                <td class="py-3 px-3 text-gray-700 dark:text-gray-300">1</td>
                                <td class="py-3 px-3 text-gray-700 dark:text-gray-300">Servicios de transporte aéreo — Venta de boletos</td>
                                <td class="py-3 px-3 text-gray-700 dark:text-gray-300 text-right">Bs {{ number_format($factura->subtotal, 2) }}</td>
                                <td class="py-3 px-3 text-gray-700 dark:text-gray-300 text-right font-medium">Bs {{ number_format($factura->subtotal, 2) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Totales --}}
                <div class="flex justify-end mt-6">
                    <div class="w-72 text-sm">
                        @if ($factura->descuento > 0)
                            <div class="flex justify-between py-1">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-700 dark:text-gray-300">Bs {{ number_format($factura->subtotal + $factura->descuento, 2) }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-gray-600 dark:text-gray-400">Descuento</span>
                                <span class="text-red-500">- Bs {{ number_format($factura->descuento, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-1 mt-1 border-t border-gray-100 dark:border-slate-700 pt-2">
                            <span class="text-gray-600 dark:text-gray-400">Importe base crédito fiscal</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Bs {{ number_format($factura->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-gray-600 dark:text-gray-400">IVA (13%)</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">Bs {{ number_format($factura->impuesto_iva, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-3 mt-2 border-t-2 border-teal-600 text-lg font-bold">
                            <span class="text-teal-700 dark:text-teal-400">TOTAL</span>
                            <span class="text-teal-700 dark:text-teal-400">Bs {{ number_format($factura->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Pie --}}
                <div class="mt-10 pt-4 border-t border-dashed border-gray-200 dark:border-slate-700 text-center">
                    <p class="text-[10px] text-gray-400 italic">"ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY"</p>
                    <p class="text-xs text-teal-700 dark:text-teal-400 font-semibold mt-2">Gracias por volar con Aerolínea El Trompillo</p>
                    @if ($factura->venta && $factura->venta->vendedor)
                        <p class="text-[10px] text-gray-400 mt-1">Atendido por: {{ $factura->venta->vendedor->name }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            .no-print { display: none !important; }
            #factura-print, #factura-print * { visibility: visible; }
            #factura-print {
                position: absolute; left: 0; top: 0; width: 100%;
                box-shadow: none; border: none; border-radius: 0;
            }
        }
    </style>
</div>
