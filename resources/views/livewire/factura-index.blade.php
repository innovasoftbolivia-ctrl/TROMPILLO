<div>
    @php $estadosCss = ['emitida'=>['label'=>'Emitida','css'=>'bg-emerald-500/15 text-emerald-600'],'anulada'=>['label'=>'Anulada','css'=>'bg-red-500/15 text-red-600']]; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold">Facturas 🧾</h1>
                <p class="text-sm text-gray-500 mt-1">Historial de facturación de la aerolínea.</p>
            </div>
        </div>
        @if ($flashOk)
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 text-green-700 flex justify-between"
                x-data="{ show: true }" x-show="show">
                <div>{{ $flashOk }}</div><button @click="show = false">✕</button>
            </div>
            @endif @if ($flashError)
                <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700 flex justify-between"
                    x-data="{ show: true }" x-show="show">
                    <div>{{ $flashError }}</div><button @click="show = false">✕</button>
                </div>
            @endif
            <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-4 mb-6">
                <div class="grid grid-cols-12 gap-4 items-end">
                    <div class="col-span-12 sm:col-span-6"><label
                            class="block text-xs font-medium text-gray-500 mb-1">Buscar (Nro, NIT, Razón
                            Social)</label><input wire:model.live.debounce.400ms="buscar" type="text"
                            class="form-input w-full"></div>
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
                                <th class="px-4 py-3 text-left">Factura</th>
                                <th class="px-4 py-3 text-left">Datos Cliente</th>
                                <th class="px-4 py-3 text-left">Venta Asociada</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                            @forelse ($facturas as $f)
                                <tr wire:key="factura-{{ $f->id }}">
                                    <td class="px-4 py-3 font-medium">{{ $f->numero_factura }} <span
                                            class="block text-xs text-gray-500">{{ \Carbon\Carbon::parse($f->fecha_emision)->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $f->razon_social }}</div>
                                        <div class="text-xs text-gray-500">NIT: {{ $f->nit }}</div>
                                    </td>
                                    <td class="px-4 py-3"><a href="{{ route('ventas.show', $f->venta_id) }}"
                                            class="text-indigo-600 hover:underline">{{ $f->venta->numero }}</a></td>
                                    <td class="px-4 py-3 text-right font-medium">Bs {{ number_format($f->total, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center"><span
                                            class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosCss[$f->estado]['css'] ?? 'bg-gray-500/20' }}">{{ $estadosCss[$f->estado]['label'] ?? $f->estado }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2"> <a
                                                href="{{ route('facturas.show', $f) }}"
                                                class="text-gray-400 hover:text-emerald-500">Ver</a>
                                            @if ($f->estado === 'emitida')
                                                <button wire:click="anular({{ $f->id }})"
                                                    wire:confirm="¿Anular esta factura?"
                                                    class="text-gray-400 hover:text-red-500">Anular</button>
                                            @endif
                                        </div>
                                    </td>
                            </tr> @empty <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">No hay facturas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-6">{{ $facturas->links() }}</div>
    </div>
</div>
