<div>
    @php $estadosCss = ['pendiente'=>['label'=>'Pendiente','css'=>'bg-amber-500/15 text-amber-600'],'pagada'=>['label'=>'Pagada','css'=>'bg-emerald-500/15 text-emerald-600'],'anulada'=>['label'=>'Anulada','css'=>'bg-red-500/15 text-red-600']]; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold">Ventas 💰</h1>
                <p class="text-sm text-gray-500 mt-1">Registro de ventas y facturación.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('ventas.cobrar') }}" class="btn border-emerald-500 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                    <span class="ml-1">Cobrar reserva pendiente</span>
                </a>
                <a href="{{ route('ventas.create') }}" class="btn bg-emerald-500 hover:bg-emerald-600 text-white">
                    <span class="ml-1">Nueva Venta Directa</span>
                </a>
            </div>
        </div>
        @if ($flashOk)
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 text-green-700 flex justify-between"
                x-data="{ show: true }" x-show="show">
                <div>{{ $flashOk }}</div><button @click="show = false">✕</button>
            </div>
            @endif @if (session('success'))
                <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 text-green-700 flex justify-between"
                    x-data="{ show: true }" x-show="show">
                    <div>{{ session('success') }}</div><button @click="show = false">✕</button>
                </div>
            @endif
            <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-4 mb-6">
                <div class="grid grid-cols-12 gap-4 items-end">
                    <div class="col-span-12 sm:col-span-6"><label
                            class="block text-xs font-medium text-gray-500 mb-1">Buscar (Nro Venta)</label><input
                            wire:model.live.debounce.400ms="buscar" type="text" class="form-input w-full"></div>
                    <div class="col-span-6 sm:col-span-4"><label
                            class="block text-xs font-medium text-gray-500 mb-1">Estado</label><select
                            wire:model.live="estado" class="form-select w-full">
                            <option value="">Todos</option>
                            @foreach ($estadosCss as $v => $e)
                                <option value="{{ $v }}">{{ $e['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-6 sm:col-span-2 flex gap-2">
                        @if ($buscar !== '' || $estado !== '')
                            <button wire:click="limpiarFiltros" class="btn border-gray-200 text-gray-600 w-full">✕
                                Limpiar</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-auto w-full text-sm">
                        <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left">Nro Venta</th>
                                <th class="px-4 py-3 text-left">Cliente</th>
                                <th class="px-4 py-3 text-center">Items</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-center">Factura</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse ($ventas as $v)
                                <tr wire:key="venta-{{ $v->id }}">
                                    <td class="px-4 py-3 font-medium">{{ $v->numero }} <span
                                            class="block text-xs text-gray-500">{{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $v->cliente ? $v->cliente->nombre_completo : 'Cliente Casual' }}</td>
                                    <td class="px-4 py-3 text-center">{{ $v->detalles_count }}</td>
                                    <td class="px-4 py-3 text-right font-medium">Bs {{ number_format($v->total, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($v->factura)
                                            <a href="{{ route('facturas.show', $v->factura->id) }}"
                                            class="text-xs text-emerald-600 font-medium hover:underline">{{ $v->factura->numero_factura }}</a>@else<span
                                                class="text-xs text-gray-400">Sin Emitir</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center"><span
                                            class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosCss[$v->estado]['css'] ?? 'bg-gray-500/20' }}">{{ $estadosCss[$v->estado]['label'] ?? $v->estado }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2"> <a
                                                href="{{ route('ventas.show', $v) }}"
                                                class="text-gray-400 hover:text-emerald-500">Ver</a>
                                            @if (!$v->factura && $v->estado !== 'pagada')
                                                <button wire:click="eliminar({{ $v->id }})"
                                                    wire:confirm="¿Eliminar venta?"
                                                    class="text-gray-400 hover:text-red-500">Borrar</button>
                                            @endif
                                        </div>
                                    </td>
                            </tr> @empty <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">No hay ventas
                                        registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-6">{{ $ventas->links() }}</div>
    </div>
</div>
