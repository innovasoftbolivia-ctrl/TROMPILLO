<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    @php
        $pdfRoute = match ($tipo) {
            'reservas' => route('reportes.reservas.pdf', ['desde' => $desde, 'hasta' => $hasta, 'estado' => $estado]),
            'vuelos'   => route('reportes.vuelos.pdf', ['desde' => $desde, 'hasta' => $hasta, 'estado' => $estado]),
            default    => route('reportes.ventas.pdf', ['desde' => $desde, 'hasta' => $hasta, 'estado' => $estado]),
        };
        $tabs = ['ventas' => 'Ventas', 'reservas' => 'Reservas', 'vuelos' => 'Vuelos'];
    @endphp

    {{-- Encabezado --}}
    <div class="sm:flex sm:justify-between sm:items-center mb-6">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Reportes 📊</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ventas, reservas y vuelos por período, con exportación a PDF.</p>
        </div>
        <a href="{{ $pdfRoute }}" target="_blank"
           class="btn bg-red-500 hover:bg-red-600 text-white">
            <svg class="w-4 h-4 fill-current shrink-0 mr-2" viewBox="0 0 16 16"><path d="M8 0a1 1 0 0 1 1 1v6.586l1.293-1.293a1 1 0 1 1 1.414 1.414l-3 3a1 1 0 0 1-1.414 0l-3-3a1 1 0 1 1 1.414-1.414L7 7.586V1a1 1 0 0 1 1-1zM2 13h12a1 1 0 1 1 0 2H2a1 1 0 1 1 0-2z" /></svg>
            Exportar a PDF
        </a>
    </div>

    {{-- Selector de tipo de reporte --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach ($tabs as $key => $label)
            <button wire:click="seleccionar('{{ $key }}')"
                class="btn {{ $tipo === $key ? 'bg-emerald-500 text-white' : 'border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300 hover:border-gray-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl p-4 mb-6">
        <div class="grid grid-cols-12 gap-4 items-end">
            <div class="col-span-6 sm:col-span-3">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Desde</label>
                <input wire:model.live="desde" type="date" class="form-input w-full">
            </div>
            <div class="col-span-6 sm:col-span-3">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Hasta</label>
                <input wire:model.live="hasta" type="date" class="form-input w-full">
            </div>
            <div class="col-span-12 sm:col-span-4">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Estado</label>
                <select wire:model.live="estado" class="form-select w-full">
                    <option value="">Todos</option>
                    @foreach ($estados as $e)
                        <option value="{{ $e }}">{{ ucfirst(str_replace('_', ' ', $e)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Tabla del reporte --}}
    <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800 dark:text-gray-100">{{ $data['titulo'] }}</h2>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $data['periodo'] }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="table-auto w-full dark:text-gray-300">
                <thead class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        @foreach ($data['columnas'] as $i => $c)
                            <th class="px-4 py-3 whitespace-nowrap {{ ($data['align'][$i] ?? 'left') === 'right' ? 'text-right' : 'text-left' }}">{{ $c }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse ($data['filas'] as $fila)
                        <tr>
                            @foreach ($fila as $i => $celda)
                                <td class="px-4 py-3 whitespace-nowrap {{ ($data['align'][$i] ?? 'left') === 'right' ? 'text-right font-medium text-gray-800 dark:text-gray-100' : '' }}">{{ $celda }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($data['columnas']) }}" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">Sin datos en el período seleccionado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Totales --}}
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex flex-wrap justify-end gap-6">
            @foreach ($data['totales'] as $t)
                <div class="text-right">
                    <div class="text-xs text-gray-400 dark:text-gray-500 uppercase">{{ $t['label'] }}</div>
                    <div class="text-lg font-bold text-emerald-600">{{ $t['valor'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
