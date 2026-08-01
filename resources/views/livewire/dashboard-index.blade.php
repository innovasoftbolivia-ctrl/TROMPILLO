<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold">Dashboard 📊</h1>
                <p class="text-sm text-gray-500 mt-1">Resumen general de operaciones y ventas.</p>
            </div>
        </div> <!-- KPIs Operaciones -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5 border border-gray-100 dark:border-slate-700">
                <div class="text-gray-500 text-sm font-medium uppercase mb-1">Vuelos Activos</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['vuelos_activos'] }} <span
                        class="text-sm font-normal text-gray-400">/ {{ $stats['vuelos_total'] }}</span></div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5 border border-gray-100 dark:border-slate-700">
                <div class="text-gray-500 text-sm font-medium uppercase mb-1">Flota Operativa</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['flota_activa'] }} <span
                        class="text-sm font-normal text-gray-400">/ {{ $stats['flota_total'] }}</span></div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5 border border-gray-100 dark:border-slate-700">
                <div class="text-gray-500 text-sm font-medium uppercase mb-1">Pasajeros Históricos</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                    {{ number_format($stats['pasajeros']) }}</div>
            </div>
            <div
                class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5 border border-gray-100 dark:border-slate-700">
                <div class="text-gray-500 text-sm font-medium uppercase mb-1">Total Reservas</div>
                <div class="text-3xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($stats['reservas']) }}
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 gap-6 mb-8"> <!-- Próximos Vuelos -->
            <div
                class="col-span-12 lg:col-span-8 bg-white dark:bg-slate-800 shadow-xs rounded-xl border border-gray-100 dark:border-slate-700">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-200">Próximos Vuelos Programados</h2>
                </div>
                <div class="p-3">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-sm">
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-800/50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Vuelo</th>
                                    <th class="px-4 py-2 text-left">Ruta</th>
                                    <th class="px-4 py-2 text-left">Salida</th>
                                    <th class="px-4 py-2 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @forelse($proximosVuelos as $v)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-indigo-600"><a
                                                href="{{ route('vuelos.show', $v) }}">{{ $v->numero_vuelo ?? 'S/N' }}</a>
                                        </td>
                                        <td class="px-4 py-3">{{ $v->origen->codigo_iata }} →
                                            {{ $v->destino->codigo_iata }}</td>
                                        <td class="px-4 py-3">
                                            {{ \Carbon\Carbon::parse($v->salida_programada)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-center"><span
                                                class="inline-flex text-xs font-medium bg-emerald-100 text-emerald-600 rounded-full px-2 py-0.5">{{ strtoupper($v->estado) }}</span>
                                        </td>
                                </tr> @empty <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">No hay vuelos
                                            próximos programados.</td>
                                    </tr>
                                    @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- KPIs Comerciales -->
            <div
                class="col-span-12 lg:col-span-4 bg-white dark:bg-slate-800 shadow-xs rounded-xl border border-gray-100 dark:border-slate-700 p-5">
                <h2
                    class="font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-100 dark:border-slate-700">
                    Métricas de Ventas</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Ventas
                            Completadas:</span><span class="font-bold">{{ $ventas['pagadas'] }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Ingresos
                            Totales:</span><span class="font-bold text-emerald-600">Bs
                            {{ number_format($ventas['ingresos'], 2) }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Ingresos del
                            Mes:</span><span class="font-bold text-indigo-600">Bs
                            {{ number_format($ventas['mes_monto'], 2) }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Ticket
                            Promedio:</span><span class="font-bold">Bs
                            {{ number_format($ventas['ticket_prom'], 2) }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-gray-500 text-sm">Facturas
                            Emitidas:</span><span class="font-bold">{{ $ventas['facturas'] }}</span></div>
                </div>
            </div>
        </div>
        
        <!-- Gráficas (Chart.js + AlpineJS) -->
        <div class="grid grid-cols-1 gap-6 mb-8">
            <!-- Estado de Vuelos -->
            <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl border border-gray-100 dark:border-slate-700 p-5"
                 x-data="chartVuelos({{ json_encode(array_values($porEstado->toArray())) }}, {{ json_encode(array_keys($porEstado->toArray())) }})">
                <h2 class="font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-100 dark:border-slate-700">Distribución de Vuelos por Estado</h2>
                <div class="flex justify-center h-[250px]">
                    <canvas id="chartVuelosCanvas"></canvas>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-12 gap-6 mb-8"> <!-- Últimas Ventas -->
            <div
                class="col-span-12 bg-white dark:bg-slate-800 shadow-xs rounded-xl border border-gray-100 dark:border-slate-700">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-200">Últimas Ventas Directas</h2> <a
                        href="{{ route('ventas.index') }}" class="text-sm text-indigo-500 hover:underline">Ver
                        todas</a>
                </div>
                <div class="p-3">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-sm">
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-800/50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Número</th>
                                    <th class="px-4 py-2 text-left">Fecha</th>
                                    <th class="px-4 py-2 text-left">Cliente</th>
                                    <th class="px-4 py-2 text-right">Total</th>
                                    <th class="px-4 py-2 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @forelse($ultimasVentas as $v)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-indigo-600"><a
                                                href="{{ route('ventas.show', $v) }}">{{ $v->numero }}</a></td>
                                        <td class="px-4 py-3">
                                            {{ \Carbon\Carbon::parse($v->fecha)->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3">
                                            {{ $v->cliente ? $v->cliente->nombre_completo : 'Casual' }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">Bs
                                            {{ number_format($v->total, 2) }}</td>
                                        <td class="px-4 py-3 text-center"><span
                                                class="inline-flex text-xs font-medium bg-gray-100 text-gray-600 rounded-full px-2 py-0.5">{{ strtoupper($v->estado) }}</span>
                                        </td>
                                </tr> @empty <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No hay ventas
                                            registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart.js initialization logic via Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('chartVuelos', (data, labels) => ({
                init() {
                    new Chart(document.getElementById('chartVuelosCanvas'), {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: [
                                    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#8b5cf6'
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'right' }
                            }
                        }
                    });
                }
            }));
        });
    </script>
</div>
