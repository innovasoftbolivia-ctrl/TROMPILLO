<div> @php $tiposFiltro = ['aeropuerto' => 'Aeropuerto', 'aerodromo' => 'Aeródromo', 'pista' => 'Pista']; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto"> <!-- Encabezado -->
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">Aeropuertos 🛬</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestión de aeropuertos, aeródromos y pistas.</p>
            </div> <a href="{{ route('aeropuertos.create') }}" class="btn bg-emerald-500 hover:bg-emerald-600 text-white">
                <svg class="fill-current shrink-0 xs:hidden" width="16" height="16" viewBox="0 0 16 16">
                    <path
                        d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg> <span class="max-xs:sr-only ml-2">Nuevo aeropuerto</span> </a>
        </div> <!-- Flash -->
        @if ($flashOk)
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 border border-green-500/30 text-green-700 dark:text-green-400 flex items-center justify-between"
                x-data="{ show: true }" x-show="show">
                <div class="flex items-center"> <svg class="w-4 h-4 shrink-0 fill-current mr-2" viewBox="0 0 16 16">
                        <path
                            d="M8 0a8 8 0 100 16A8 8 0 008 0zm3.4 5.8l-4 5.333a1 1 0 01-1.516.106l-2-2a1 1 0 011.415-1.414l1.185 1.185L9.8 4.6a1 1 0 111.6 1.2z" />
                    </svg> {{ $flashOk }} </div> <button @click="show = false"
                    class="text-green-700 dark:text-green-400 hover:opacity-70">✕</button>
            </div>
            @endif @if ($flashError)
                <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-400 flex items-center justify-between"
                    x-data="{ show: true }" x-show="show">
                    <div class="flex items-center">{{ $flashError }}</div> <button @click="show = false"
                        class="text-red-700 dark:text-red-400 hover:opacity-70">✕</button>
                </div>
                @endif @if (session('success'))
                    <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 border border-green-500/30 text-green-700 dark:text-green-400 flex items-center justify-between"
                        x-data="{ show: true }" x-show="show">
                        <div class="flex items-center"> <svg class="w-4 h-4 shrink-0 fill-current mr-2"
                                viewBox="0 0 16 16">
                                <path
                                    d="M8 0a8 8 0 100 16A8 8 0 008 0zm3.4 5.8l-4 5.333a1 1 0 01-1.516.106l-2-2a1 1 0 011.415-1.414l1.185 1.185L9.8 4.6a1 1 0 111.6 1.2z" />
                            </svg> {{ session('success') }} </div> <button @click="show = false"
                            class="text-green-700 dark:text-green-400 hover:opacity-70">✕</button>
                    </div>
                    @endif <!-- Filtros -->
                    <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-4 mb-6">
                        <div class="grid grid-cols-12 gap-4 items-end">
                            <div class="col-span-12 sm:col-span-6"> <label
                                    class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1"
                                    for="buscar">Buscar</label> <input id="buscar"
                                    wire:model.live.debounce.400ms="buscar" type="text" class="form-input w-full"
                                    placeholder="OACI, nombre o ciudad"> </div>
                            <div class="col-span-6 sm:col-span-4"> <label
                                    class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1"
                                    for="tipo">Tipo</label> <select id="tipo" wire:model.live="tipo"
                                    class="form-select w-full">
                                    <option value="">Todos</option>
                                    @foreach ($tiposFiltro as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                        @endforeach
                                </select> </div>
                            <div class="col-span-6 sm:col-span-2 flex gap-2">
                                @if ($buscar !== '' || $tipo !== '')
                                    <button wire:click="limpiarFiltros"
                                        class="btn border-gray-200 /60 text-gray-600 dark:text-gray-300 w-full"
                                        title="Limpiar">✕ Limpiar</button>
                                    @endif
                            </div>
                        </div>
                    </div> <!-- Tabla -->
                    <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 /60">
                            <h2 class="font-semibold text-gray-800 dark:text-gray-200 "> Listado <span
                                    class="text-gray-400 dark:text-gray-500 font-medium">({{ $aeropuertos->total() }})</span>
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full dark:text-gray-300">
                                <thead
                                    class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-slate-800/50 ">
                                    <tr>
                                        <th class="px-4 py-3 whitespace-nowrap text-left">OACI</th>
                                        <th class="px-4 py-3 whitespace-nowrap text-left">Nombre</th>
                                        <th class="px-4 py-3 whitespace-nowrap text-left">Ciudad</th>
                                        <th class="px-4 py-3 whitespace-nowrap text-left">Tipo</th>
                                        <th class="px-4 py-3 whitespace-nowrap text-right">Pista (m)</th>
                                        <th class="px-4 py-3 whitespace-nowrap text-center">Activo</th>
                                        <th class="px-4 py-3 whitespace-nowrap text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100 dark:divide-slate-700 /60">
                                    @forelse ($aeropuertos as $aeropuerto)
                                        <tr wire:key="aeropuerto-{{ $aeropuerto->id }}">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-medium text-gray-800 dark:text-gray-200 ">
                                                    {{ $aeropuerto->codigo_oaci }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $aeropuerto->codigo_iata ?? '—' }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-gray-800 dark:text-gray-200 ">
                                                    {{ $aeropuerto->nombre }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="text-gray-800 dark:text-gray-200 ">
                                                    {{ $aeropuerto->ciudad }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $aeropuerto->departamento ?? $aeropuerto->pais }}</div>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap capitalize">{{ $aeropuerto->tipo }}
                                            </td>
                                            <td
                                                class="px-4 py-3 whitespace-nowrap text-right text-gray-800 dark:text-gray-200 ">
                                                {{ $aeropuerto->longitud_pista_m ? number_format($aeropuerto->longitud_pista_m, 0, ',', '.') : '—' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                                @if ($aeropuerto->activo)
                                                    <span
                                                        class="inline-flex text-xs font-medium rounded-full text-center px-2.5 py-1 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">Activo</span>
                                                @else
                                                    <span
                                                        class="inline-flex text-xs font-medium rounded-full text-center px-2.5 py-1 bg-gray-500/20 text-gray-600 dark:text-gray-400">Inactivo</span>
                                                    @endif
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-2"> <a
                                                        href="{{ route('aeropuertos.show', $aeropuerto) }}"
                                                        class="text-gray-400 hover:text-emerald-500" title="Ver">
                                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                            <path
                                                                d="M10 4C4.5 4 1 10 1 10s3.5 6 9 6 9-6 9-6-3.5-6-9-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z" />
                                                        </svg> </a> <a
                                                        href="{{ route('aeropuertos.edit', $aeropuerto) }}"
                                                        class="text-gray-400 hover:text-emerald-500" title="Editar">
                                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                            <path
                                                                d="M13.7 3.3a1 1 0 011.4 0l1.6 1.6a1 1 0 010 1.4l-1 1-3-3 1-1zM3 13l7.3-7.3 3 3L6 16H3v-3z" />
                                                        </svg> </a> <button
                                                        wire:click="eliminar({{ $aeropuerto->id }})"
                                                        wire:confirm="¿Eliminar el aeropuerto {{ $aeropuerto->codigo_oaci }}? Esta acción no se puede deshacer."
                                                        class="text-gray-400 hover:text-red-500" title="Eliminar"> <svg
                                                            class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                            <path
                                                                d="M7 4V3a1 1 0 011-1h4a1 1 0 011 1v1h4a1 1 0 110 2h-1v11a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 010-2h4zm2 3a1 1 0 012 0v7a1 1 0 11-2 0V7z" />
                                                        </svg> </button> </div>
                                            </td>
                                    </tr> @empty <tr>
                                            <td colspan="7"
                                                class="px-4 py-10 text-center text-gray-500 dark:text-gray-400"> No hay
                                                aeropuertos que coincidan. <a href="{{ route('aeropuertos.create') }}"
                                                    class="text-emerald-500 hover:text-emerald-600 font-medium">Crea el
                                                    primero</a>. </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- Paginación -->
                    <div class="mt-6"> {{ $aeropuertos->links() }} </div>
    </div>
</div>
