<div>
    @php
        $tipos = ['regular' => 'Regular', 'charter' => 'Charter', 'carga' => 'Carga', 'ambulancia' => 'Ambulancia'];
        $estadosCss = ['programado' => ['label' => 'Programado', 'css' => 'bg-gray-500/15 text-gray-600'], 'confirmado' => ['label' => 'Confirmado', 'css' => 'bg-sky-500/15 text-sky-600'], 'abordando' => ['label' => 'Abordando', 'css' => 'bg-amber-500/15 text-amber-600'], 'en_vuelo' => ['label' => 'En vuelo', 'css' => 'bg-indigo-500/15 text-indigo-600'], 'aterrizado' => ['label' => 'Aterrizado', 'css' => 'bg-emerald-500/15 text-emerald-600'], 'cancelado' => ['label' => 'Cancelado', 'css' => 'bg-red-500/15 text-red-600'], 'retrasado' => ['label' => 'Retrasado', 'css' => 'bg-orange-500/15 text-orange-600']];
    @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">Vuelos 🛫</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestión de programación de vuelos comerciales y
                    privados.</p>
            </div> <a href="{{ route('vuelos.create') }}" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span
                    class="ml-2">Programar vuelo</span></a>
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
                        <div class="col-span-12 sm:col-span-4"><label
                                class="block text-xs font-medium text-gray-500 mb-1">Buscar (vuelo,
                                ciudad)</label><input wire:model.live.debounce.400ms="buscar" type="text"
                                class="form-input w-full"></div>
                        <div class="col-span-6 sm:col-span-3"><label
                                class="block text-xs font-medium text-gray-500 mb-1">Estado</label><select
                                wire:model.live="estado" class="form-select w-full">
                                <option value="">Todos</option>
                                @foreach ($estadosCss as $v => $e)
                                    <option value="{{ $v }}">{{ $e['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-3"><label
                                class="block text-xs font-medium text-gray-500 mb-1">Tipo</label><select
                                wire:model.live="tipo" class="form-select w-full">
                                <option value="">Todos</option>
                                @foreach ($tipos as $v => $l)
                                    <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-2 flex gap-2">
                            @if ($buscar !== '' || $estado !== '' || $tipo !== '')
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
                                    <th class="px-4 py-3 text-left">Nro. Vuelo</th>
                                    <th class="px-4 py-3 text-left">Ruta (Origen → Destino)</th>
                                    <th class="px-4 py-3 text-left">Salida programada</th>
                                    <th class="px-4 py-3 text-left">Aeronave</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                    <th class="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700 /60">
                                @forelse ($vuelos as $v)
                                    <tr wire:key="vuelo-{{ $v->id }}">
                                        <td class="px-4 py-3 font-medium">{{ $v->numero_vuelo ?? 'S/N' }} <span
                                                class="block text-xs font-normal text-gray-500">{{ $tipos[$v->tipo] ?? $v->tipo }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $v->origen->codigo_iata }} →
                                                {{ $v->destino->codigo_iata }}</div>
                                            <div class="text-xs text-gray-500">{{ $v->origen->ciudad }} a
                                                {{ $v->destino->ciudad }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            {{ $v->salida_programada ? \Carbon\Carbon::parse($v->salida_programada)->format('d/m/Y H:i') : '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $v->aeronave ? $v->aeronave->matricula : 'No asignada' }}</td>
                                        <td class="px-4 py-3 text-center"><span
                                                class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosCss[$v->estado]['css'] ?? 'bg-gray-500/20' }}">{{ $estadosCss[$v->estado]['label'] ?? $v->estado }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2"> <a
                                                    href="{{ route('vuelos.show', $v) }}"
                                                    class="text-gray-400 hover:text-emerald-500">Ver</a>
                                                @if (in_array($v->estado, ['programado', 'confirmado']))
                                                    <a href="{{ route('vuelos.edit', $v) }}"
                                                        class="text-gray-400 hover:text-emerald-500">Editar</a>
                                                    @endif @if ($v->estado === 'programado')
                                                        <button wire:click="eliminar({{ $v->id }})"
                                                            wire:confirm="¿Eliminar vuelo?"
                                                            class="text-gray-400 hover:text-red-500">Borrar</button>
                                                    @endif
                                            </div>
                                        </td>
                                </tr> @empty <tr>
                                        <td colspan="6" class="px-4 py-10 text-center text-gray-500">No hay vuelos
                                            programados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">{{ $vuelos->links() }}</div>
    </div>
</div>
