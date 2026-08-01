<div> @php $estadosCss = ['emitida'=>['label'=>'Emitida','css'=>'bg-emerald-500/15 text-emerald-600'],'anulada'=>['label'=>'Anulada','css'=>'bg-red-500/15 text-red-600']]; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6 flex justify-between items-center"> <a href="{{ route('facturas.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a facturas</a>
            <div class="flex gap-2">
                @if ($factura->estado === 'emitida')
                    <button wire:click="anular" wire:confirm="¿Estás seguro de anular esta factura?"
                        class="btn border-red-500 text-red-600 hover:bg-red-50">Anular Factura</button>
                    @endif 
                    <a href="{{ route('facturas.pdf', $factura->id) }}" target="_blank"
                        class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Descargar PDF</a>
            </div>
        </div>
        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 text-green-700">{{ session('success') }}</div>
            @endif @if (session('error'))
                <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700">{{ session('error') }}</div>
                @endif <div id="factura-print"
                    class="bg-white dark:bg-slate-800 shadow-lg rounded-xl p-8 max-w-3xl mx-auto border border-gray-100 dark:border-slate-700">
                    <!-- Cabecera -->
                    <div class="flex justify-between items-start border-b border-gray-200 pb-6 mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 tracking-tight">FACTURA</h1>
                            <div class="text-gray-500 mt-1">Nro. {{ $factura->numero_factura }}</div>
                            <div class="mt-4 text-sm text-gray-600">
                                <p class="font-bold text-gray-800 dark:text-gray-200">AEROLÍNEA EL TROMPILLO S.A.</p>
                                <p>NIT: 1029384756</p>
                                <p>Av. Santos Dumont, Santa Cruz, Bolivia</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if ($factura->estado === 'anulada')
                                <div
                                    class="inline-block border-2 border-red-500 text-red-500 font-bold text-xl px-4 py-1 rounded mb-4 rotate-12 opacity-80">
                                    ANULADA</div>
                                @endif <div class="text-sm text-gray-600">
                                    <p><span class="font-medium">Emisión:</span>
                                        {{ \Carbon\Carbon::parse($factura->fecha_emision)->format('d/m/Y H:i') }}</p>
                                    <p class="mt-2 text-indigo-600 font-medium">Venta: <a
                                            href="{{ route('ventas.show', $factura->venta_id) }}">{{ $factura->venta->numero }}</a>
                                    </p>
                                </div>
                        </div>
                    </div> <!-- Datos Cliente -->
                    <div class="bg-gray-50 dark:bg-slate-800/50 rounded-lg p-4 mb-8">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Facturado a:</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div><span class="text-gray-500 text-sm">Señor(es):</span> <span
                                    class="font-bold text-gray-800 dark:text-gray-200">{{ $factura->razon_social }}</span>
                            </div>
                            <div><span class="text-gray-500 text-sm">NIT/CI:</span> <span
                                    class="font-bold text-gray-800 dark:text-gray-200">{{ $factura->nit }}</span></div>
                        </div>
                    </div> <!-- Detalle Venta -->
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-200">
                                <th class="py-3 px-2 text-sm font-semibold text-gray-600">Cant.</th>
                                <th class="py-3 px-2 text-sm font-semibold text-gray-600">Detalle</th>
                                <th class="py-3 px-2 text-sm font-semibold text-gray-600 text-right">P. Unitario</th>
                                <th class="py-3 px-2 text-sm font-semibold text-gray-600 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach ($factura->venta->detalles as $d)
                                <tr class="border-b border-gray-100 dark:border-slate-700">
                                    <td class="py-3 px-2 text-gray-700">{{ $d->cantidad }}</td>
                                    <td class="py-3 px-2 text-gray-700"> {{ $d->descripcion }} @if ($d->boleto)
                                            <div class="text-xs text-gray-400 mt-0.5">Boleto:
                                                {{ $d->boleto->numero_boleto }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2 text-gray-700 text-right">Bs
                                        {{ number_format($d->precio_unitario, 2) }}</td>
                                    <td class="py-3 px-2 text-gray-700 text-right font-medium">Bs
                                        {{ number_format($d->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table> <!-- Totales -->
                    <div class="flex justify-end mt-6">
                        <div class="w-64 text-sm">
                            @if ($factura->venta->descuento > 0)
                                <div class="flex justify-between py-1"><span class="text-gray-600">Subtotal
                                        Ventas:</span> <span>Bs
                                        {{ number_format($factura->venta->subtotal, 2) }}</span></div>
                                <div class="flex justify-between py-1"><span class="text-gray-600">Descuento:</span>
                                    <span class="text-red-500">-Bs
                                        {{ number_format($factura->venta->descuento, 2) }}</span></div>
                                @endif <div
                                    class="flex justify-between py-1 mt-2 border-t border-gray-100 dark:border-slate-700 pt-2">
                                    <span class="text-gray-600">Importe Base Crédito Fiscal:</span> <span
                                        class="font-medium">Bs {{ number_format($factura->subtotal, 2) }}</span></div>
                                <div class="flex justify-between py-1"><span class="text-gray-600">IVA (13%):</span>
                                    <span class="font-medium">Bs {{ number_format($factura->impuesto_iva, 2) }}</span>
                                </div>
                                <div
                                    class="flex justify-between py-3 mt-2 border-t-2 border-gray-800 text-lg font-bold">
                                    <span class="text-gray-800 dark:text-gray-200">TOTAL FACTURA:</span> <span
                                        class="text-gray-800 dark:text-gray-200">Bs
                                        {{ number_format($factura->total, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="mt-12 text-center text-xs text-gray-400">"ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL
                        PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY"</div>
                </div>
    </div>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #factura-print,
            #factura-print * {
                visibility: visible;
            }

            #factura-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                border: none;
                padding: 0;
            }
        }
    </style>
</div>
