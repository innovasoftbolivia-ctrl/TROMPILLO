<div>
    @php
        $labels = ['programado' => 'Programado', 'confirmado' => 'Confirmado', 'abordando' => 'Abordando', 'en_vuelo' => 'En vuelo', 'aterrizado' => 'Aterrizado', 'retrasado' => 'Retrasado', 'cancelado' => 'Cancelado'];
        $porDia = $vuelos->groupBy(fn($v) => \Illuminate\Support\Carbon::parse($v->salida_programada)->toDateString());
    @endphp <style>
        @media print {

            #sidebar,
            header,
            .no-print {
                display: none !important;
            }

            body,
            main {
                background: #fff !important;
            }

            .reporte-card {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
            }

            a {
                color: inherit !important;
                text-decoration: none !important;
            }

            @page {
                margin: 1.4cm;
            }
        }
    </style>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
        <div class="no-print mb-6"> <a href="{{ route('operaciones') }}"
                class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">&larr; Volver a operaciones</a>
            <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-4 mt-3">
                <div class="grid grid-cols-12 gap-4 items-end">
                    <div class="col-span-6 sm:col-span-3"><label
                            class="block text-xs font-medium text-gray-500 mb-1">Desde</label><input type="date"
                            wire:model.live="desde" class="form-input w-full"></div>
                    <div class="col-span-6 sm:col-span-3"><label
                            class="block text-xs font-medium text-gray-500 mb-1">Hasta</label><input type="date"
                            wire:model.live="hasta" class="form-input w-full"></div>
                    <div class="col-span-6 sm:col-span-2"><label
                            class="block text-xs font-medium text-gray-500 mb-1">Estado</label><select
                            wire:model.live="estado" class="form-select w-full">
                            <option value="">Todos</option>
                            @foreach ($labels as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-6 sm:col-span-2"><label
                            class="block text-xs font-medium text-gray-500 mb-1">Tipo</label><select
                            wire:model.live="tipo" class="form-select w-full">
                            <option value="">Todos</option>
                            @foreach (['regular' => 'Regular', 'charter' => 'Charter', 'carga' => 'Carga', 'ambulancia' => 'Ambulancia'] as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-2"><button type="button" onclick="window.print()"
                            class="btn bg-emerald-500 hover:bg-emerald-600 text-white w-full">Imprimir</button></div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6 sm:p-8 reporte-card">
            <div class="flex items-start justify-between border-b border-gray-200 pb-5 mb-5">
                <div class="flex items-center gap-3"> <img src="{{ asset('images/emblema-trompillo.svg') }}"
                        alt="El Trompillo" class="w-10 h-12">
                    <div>
                        <div class="text-lg font-bold text-gray-800 dark:text-gray-200">Aerolínea El Trompillo</div>
                        <div class="text-sm text-gray-500">Reporte de operaciones</div>
                    </div>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <div>Rango: <span
                            class="font-medium text-gray-700">{{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }}
                            – {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}</span></div>
                    <div>Generado: {{ now()->format('d/m/Y H:i') }}</div>
                    @if ($estado || $tipo)
                        <div class="mt-1">Filtros: @if ($estado)
                                <span class="capitalize">{{ $labels[$estado] ?? $estado }}</span>
                                @endif @if ($tipo)
                                    <span class="capitalize">{{ $tipo }}</span>
                                @endif
                        </div>@endif
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $resumen['vuelos'] }}</div>
                    <div class="text-xs text-gray-500">Vuelos</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $resumen['pasajeros'] }}</div>
                    <div class="text-xs text-gray-500">Pasajeros</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">Bs
                        {{ number_format($resumen['ingresos'], 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-500">Ingresos (boletos)</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $resumen['ocupacion'] }}%</div>
                    <div class="text-xs text-gray-500">Ocupación prom.</div>
                </div>
            </div>
            @forelse ($porDia as $dia => $vuelosDia)
                <div class="mb-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2 capitalize">
                        {{ \Illuminate\Support\Carbon::parse($dia)->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }}
                        <span class="text-gray-400 font-normal">({{ $vuelosDia->count() }})</span></h3>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-sm">
                            <thead class="text-xs uppercase text-gray-400 border-b border-gray-200">
                                <tr>
                                    <th class="py-2 pr-3 text-left">Hora</th>
                                    <th class="py-2 px-3 text-left">Vuelo</th>
                                    <th class="py-2 px-3 text-left">Ruta</th>
                                    <th class="py-2 px-3 text-left">Aeronave</th>
                                    <th class="py-2 px-3 text-left">Piloto</th>
                                    <th class="py-2 px-3 text-left">Estado</th>
                                    <th class="py-2 px-3 text-right">Pax</th>
                                    <th class="py-2 pl-3 text-right">Ocup.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($vuelosDia as $v)
                                    @php
                                        $cap = $v->aeronave->capacidad_pasajeros ?? 0;
                                        $ocup = $cap > 0 ? round(($v->boletos_count / $cap) * 100) : null;
                                    @endphp <tr>
                                        <td class="py-2 pr-3 whitespace-nowrap">
                                            {{ \Illuminate\Support\Carbon::parse($v->salida_programada)->format('H:i') }}
                                        </td>
                                        <td
                                            class="py-2 px-3 whitespace-nowrap font-medium text-gray-800 dark:text-gray-200">
                                            {{ $v->numero_vuelo ?? 'Charter' }}</td>
                                        <td class="py-2 px-3 whitespace-nowrap">{{ $v->origen->codigo_oaci ?? '?' }} →
                                            {{ $v->destino->codigo_oaci ?? '?' }}</td>
                                        <td class="py-2 px-3 whitespace-nowrap">{{ $v->aeronave->matricula ?? '—' }}
                                        </td>
                                        <td class="py-2 px-3 whitespace-nowrap">
                                            {{ optional($v->piloto?->empleado)->apellidos ?? '—' }}</td>
                                        <td class="py-2 px-3 whitespace-nowrap capitalize">
                                            {{ $labels[$v->estado] ?? $v->estado }}</td>
                                        <td class="py-2 px-3 whitespace-nowrap text-right">{{ $v->boletos_count }}</td>
                                        <td class="py-2 pl-3 whitespace-nowrap text-right">
                                            {{ $ocup !== null ? $ocup . '%' : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            </div> @empty <p class="text-center text-gray-500 py-8">No hay vuelos en el rango y filtros
                    seleccionados.</p>
            @endforelse
        </div>
    </div>
</div>
