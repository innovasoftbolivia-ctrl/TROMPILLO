<div> @php $estadosCss = ['pendiente'=>['label'=>'Pendiente','css'=>'bg-amber-500/15 text-amber-600'],'pagada'=>['label'=>'Pagada','css'=>'bg-emerald-500/15 text-emerald-600'],'anulada'=>['label'=>'Anulada','css'=>'bg-red-500/15 text-red-600']]; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto">
        <div class="mb-6"><a href="{{ route('ventas.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a ventas</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-bold">Venta {{ $venta->numero }}</h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosCss[$venta->estado]['css'] ?? 'bg-gray-500/20 text-gray-600' }}">{{ $estadosCss[$venta->estado]['label'] ?? $venta->estado }}</span>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0">
                    @if ($venta->factura)
                        <a href="{{ route('facturas.show', $venta->factura->id) }}"
                            class="btn border-emerald-500 text-emerald-600 hover:bg-emerald-50">Ver Factura
                            ({{ $venta->factura->numero_factura }})</a>
                    @else
                        <button wire:click="generarFactura" wire:confirm="¿Emitir factura por esta venta ahora?"
                            class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Emitir Factura</button>
                        @endif
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 text-green-700">{{ session('success') }}</div>
            @endif @if (session('error'))
                <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700">{{ session('error') }}</div>
                @endif <div class="grid grid-cols-12 gap-6 mb-6">
                    <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                        <h3 class="font-semibold mb-4">Detalles Generales</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Fecha</dt>
                                <dd class="font-medium">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Cliente</dt>
                                <dd class="font-medium">
                                    {{ $venta->cliente ? $venta->cliente->nombre_completo : 'Casual' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Vendedor</dt>
                                <dd class="font-medium">{{ $venta->vendedor ? $venta->vendedor->name : 'S/N' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Método de Pago</dt>
                                <dd class="font-medium uppercase">{{ $venta->metodo_pago ?? 'S/N' }}</dd>
                            </div>
                            @if ($venta->reserva)
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Reserva Origen</dt>
                                    <dd class="font-medium"><a href="{{ route('reservas.show', $venta->reserva_id) }}"
                                            class="text-indigo-600 hover:underline">{{ $venta->reserva->codigo }}</a>
                                    </dd>
                                </div>
                                @endif
                        </dl>
                    </div>
                    <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                        <h3 class="font-semibold mb-4">Resumen Económico</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Subtotal</dt>
                                <dd class="font-medium">Bs {{ number_format($venta->subtotal, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Descuento</dt>
                                <dd class="font-medium text-red-500">- Bs {{ number_format($venta->descuento, 2) }}
                                </dd>
                            </div>
                            <div class="border-t border-gray-100 dark:border-slate-700 pt-2 flex justify-between">
                                <dt class="text-gray-700 font-bold">Total Final</dt>
                                <dd class="font-bold text-emerald-600 text-lg">Bs {{ number_format($venta->total, 2) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                        <h2 class="font-semibold">Detalle de Ítems ({{ $venta->detalles->count() }})</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-sm">
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-800/50">
                                <tr>
                                    <th class="px-4 py-3 text-left">Descripción / Producto</th>
                                    <th class="px-4 py-3 text-left">Referencia (Boleto)</th>
                                    <th class="px-4 py-3 text-center">Cantidad</th>
                                    <th class="px-4 py-3 text-right">Precio Unit.</th>
                                    <th class="px-4 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($venta->detalles as $d)
                                    <tr>
                                        <td class="px-4 py-3 font-medium">{{ $d->descripcion }}</td>
                                        <td class="px-4 py-3">
                                            @if($d->boleto)
                                                Boleto {{ $d->boleto->numero_boleto ?? $d->boleto->id }}
                                                <a href="{{ route('boletos.pdf', $d->boleto->id) }}" target="_blank" class="ml-2 text-xs text-indigo-500 hover:underline flex-inline items-center gap-1">
                                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 16 16"><path d="M15 15H1v-3h2v1h10v-1h2v3zM8 11.5l4-4h-3v-6H7v6H4l4 4z"/></svg> PDF
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">{{ $d->cantidad }}</td>
                                        <td class="px-4 py-3 text-right">Bs {{ number_format($d->precio_unitario, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium">Bs
                                            {{ number_format($d->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
    </div>
</div>
