<div>
    @php $estadosCss = ['pendiente'=>['label'=>'Pendiente','css'=>'bg-amber-500/15 text-amber-600'],'confirmada'=>['label'=>'Confirmada','css'=>'bg-emerald-500/15 text-emerald-600'],'cancelada'=>['label'=>'Cancelada','css'=>'bg-red-500/15 text-red-600'],'completada'=>['label'=>'Completada','css'=>'bg-indigo-500/15 text-indigo-600']]; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">Reservas 🎟️</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestión de reservas de vuelos.</p>
            </div> <a href="{{ route('reservas.create') }}"
                class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span class="ml-2">Nueva reserva</span></a>
        </div>
        @if ($flashOk)
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 border border-green-500/30 text-green-700 flex items-center justify-between"
                x-data="{ show: true }" x-show="show">
                <div>{{ $flashOk }}</div><button @click="show = false">✕</button>
            </div>
            @endif @if ($flashError)
                <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700 flex items-center justify-between"
                    x-data="{ show: true }" x-show="show">
                    <div>{{ $flashError }}</div><button @click="show = false">✕</button>
                </div>
                @endif @if (session('success'))
                    <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 border border-green-500/30 text-green-700 flex items-center justify-between"
                        x-data="{ show: true }" x-show="show">
                        <div>{{ session('success') }}</div><button @click="show = false">✕</button>
                    </div>
                @endif
                <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-4 mb-6">
                    <div class="grid grid-cols-12 gap-4 items-end">
                        <div class="col-span-12 sm:col-span-6"><label
                                class="block text-xs font-medium text-gray-500 mb-1">Buscar (Código)</label><input
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
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-800/50 ">
                                <tr>
                                    <th class="px-4 py-3 text-left">Código</th>
                                    <th class="px-4 py-3 text-left">Vuelo (Ruta)</th>
                                    <th class="px-4 py-3 text-left">Titular</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                    <th class="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700 /60">
                                @forelse ($reservas as $r)
                                    <tr wire:key="reserva-{{ $r->id }}">
                                        <td class="px-4 py-3 font-medium">{{ $r->codigo }} <span
                                                class="block text-xs font-normal text-gray-500">{{ \Carbon\Carbon::parse($r->fecha_reserva)->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $r->vuelo->numero_vuelo ?? 'S/N' }}</div>
                                            <div class="text-xs text-gray-500">{{ $r->vuelo->origen->codigo_iata }} →
                                                {{ $r->vuelo->destino->codigo_iata }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $r->titular ? $r->titular->nombres . ' ' . $r->titular->apellidos : 'Sin titular' }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium">Bs
                                            {{ number_format($r->total, 2) }}</td>
                                        <td class="px-4 py-3 text-center"><span
                                                class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosCss[$r->estado]['css'] ?? 'bg-gray-500/20' }}">{{ $estadosCss[$r->estado]['label'] ?? $r->estado }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2"> <a
                                                    href="{{ route('reservas.show', $r) }}"
                                                    class="text-gray-400 hover:text-emerald-500">Ver</a> <a
                                                    href="{{ route('reservas.edit', $r) }}"
                                                    class="text-gray-400 hover:text-emerald-500">Editar</a>
                                                @if ($r->estado === 'pendiente' || $r->estado === 'cancelada')
                                                    <button wire:click="eliminar({{ $r->id }})"
                                                        wire:confirm="¿Eliminar reserva?"
                                                        class="text-gray-400 hover:text-red-500">Borrar</button>
                                                @endif
                                            </div>
                                        </td>
                                </tr> @empty <tr>
                                        <td colspan="6" class="px-4 py-10 text-center text-gray-500">No hay reservas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">{{ $reservas->links() }}</div>
    </div>
</div>
