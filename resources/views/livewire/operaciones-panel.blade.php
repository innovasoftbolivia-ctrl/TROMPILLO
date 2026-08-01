<div wire:poll.15s>
    @php
        $labels = ['programado' => 'Programado', 'confirmado' => 'Confirmado', 'abordando' => 'Abordando', 'en_vuelo' => 'En vuelo', 'aterrizado' => 'Aterrizado', 'retrasado' => 'Retrasado', 'cancelado' => 'Cancelado'];
        $colores = ['programado' => ['borde' => 'border-sky-500', 'badge' => 'bg-sky-500/15 text-sky-600'], 'confirmado' => ['borde' => 'border-emerald-500', 'badge' => 'bg-emerald-500/15 text-emerald-600'], 'abordando' => ['borde' => 'border-amber-500', 'badge' => 'bg-amber-500/15 text-amber-600'], 'en_vuelo' => ['borde' => 'border-indigo-500', 'badge' => 'bg-indigo-500/15 text-indigo-600'], 'aterrizado' => ['borde' => 'border-teal-500', 'badge' => 'bg-teal-500/15 text-teal-600'], 'retrasado' => ['borde' => 'border-orange-500', 'badge' => 'bg-orange-500/15 text-orange-600'], 'cancelado' => ['borde' => 'border-red-500', 'badge' => 'bg-red-500/15 text-red-600']];
        $hoyObj = \Illuminate\Support\Carbon::today();
    @endphp <!-- Encabezado -->
    <div class="sm:flex sm:justify-between sm:items-center mb-6">
        <div class="mb-4 sm:mb-0">
            <div class="flex items-center gap-2">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">Operaciones del día</h1>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-400"
                    title="Se actualiza automáticamente cada 15 segundos"> <span
                        class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> en vivo </span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 capitalize">
                {{ $fechaObj->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }} @if ($fechaObj->isSameDay($hoyObj))
                    · <span class="text-emerald-600 font-medium">hoy</span>
                    @endif </p>
        </div>
        <div class="flex items-center gap-2"> <a
                href="{{ route('operaciones.reporte', ['desde' => $fechaObj->toDateString(), 'hasta' => $fechaObj->toDateString()]) }}"
                class="btn border-gray-200 /60 text-gray-600 dark:text-gray-300"> <svg
                    class="w-4 h-4 fill-current mr-1.5" viewBox="0 0 20 20">
                    <path
                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7l-5-5H6zm5 1.5L14.5 7H11V3.5zM7 10h6v1H7v-1zm0 3h6v1H7v-1z" />
                </svg> Reporte </a> <button wire:click="irDia(-1)"
                class="btn border-gray-200 /60 text-gray-600 dark:text-gray-300">&larr;</button> <button
                wire:click="hoy" class="btn border-gray-200 /60 text-gray-600 dark:text-gray-300">Hoy</button> <button
                wire:click="irDia(1)" class="btn border-gray-200 /60 text-gray-600 dark:text-gray-300">&rarr;</button>
            <input type="date" wire:model.live="fecha" class="form-input"> </div>
    </div> <!-- Filtros -->
    <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-4 mb-6">
        <div class="grid grid-cols-12 gap-4 items-end">
            <div class="col-span-12 sm:col-span-4"> <label
                    class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Buscar vuelo</label> <input
                    type="text" wire:model.live.debounce.400ms="buscar" class="form-input w-full"
                    placeholder="Número de vuelo"> </div>
            <div class="col-span-6 sm:col-span-4"> <label
                    class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tipo</label> <select
                    wire:model.live="tipo" class="form-select w-full">
                    <option value="">Todos</option>
                    <option value="regular">Regular</option>
                    <option value="charter">Charter</option>
                    <option value="carga">Carga</option>
                    <option value="ambulancia">Ambulancia</option>
                </select> </div>
            <div class="col-span-6 sm:col-span-4"> <label
                    class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Aeronave</label> <select
                    wire:model.live="aeronaveId" class="form-select w-full">
                    <option value="">Todas</option>
                    @foreach ($aeronaves as $a)
                        <option value="{{ $a->id }}">{{ $a->matricula }} · {{ $a->modelo }}</option>
                        @endforeach
                </select> </div>
        </div>
    </div> <!-- Flash -->
    @if ($flashOk)
        <div
            class="mb-6 px-4 py-3 rounded-lg text-sm bg-emerald-500/15 border border-emerald-500/30 text-emerald-700 dark:text-emerald-400">
            {{ $flashOk }}</div>
        @endif @if ($flashError)
            <div
                class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-400">
                {{ $flashError }}</div>
            @endif <!-- KPIs -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                @foreach ([['label' => 'Vuelos del día', 'valor' => $kpis['total']], ['label' => 'En operación', 'valor' => $kpis['en_operacion']], ['label' => 'Aterrizados', 'valor' => $kpis['aterrizados']], ['label' => 'Pasajeros', 'valor' => $kpis['pasajeros']], ['label' => 'Ocupación', 'valor' => $kpis['ocupacion'] . '%']] as $t)
                    <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-4">
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-200 ">{{ $t['valor'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $t['label'] }}</div>
                    </div>
                    @endforeach
            </div> <!-- Alertas de flota -->
            <div class="flex flex-wrap gap-3 mb-6 text-sm"> <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/15 text-emerald-700 dark:text-emerald-400">{{ $flotaActiva }}
                    aeronaves activas</span>
                @if ($flotaMantenimiento > 0)
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/15 text-amber-700 dark:text-amber-400">{{ $flotaMantenimiento }}
                        en mantenimiento</span>
                    @endif
            </div> <!-- Tablero -->
            @if ($columnas->isEmpty())
                <div
                    class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-10 text-center text-gray-500 dark:text-gray-400">
                    No hay vuelos para este día con los filtros aplicados. </div>
            @else
                <div class="flex gap-5 overflow-x-auto pb-4">
                    @foreach ($columnas as $estado => $vuelos)
                        <div class="w-72 shrink-0">
                            <div class="flex items-center justify-between mb-3 px-1">
                                <h3 class="font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $labels[$estado] ?? ucfirst($estado) }}</h3> <span
                                    class="inline-flex text-xs font-medium rounded-full px-2 py-0.5 {{ $colores[$estado]['badge'] ?? 'bg-gray-500/15 text-gray-600' }}">{{ $vuelos->count() }}</span>
                            </div>
                            <div class="space-y-3">
                                @foreach ($vuelos as $v)
                                    @php
                                        $cap = $v->aeronave->capacidad_pasajeros ?? 0;
                                        $ocup = $cap > 0 ? min(100, round(($v->boletos_count / $cap) * 100)) : null;
                                    @endphp <div wire:key="vuelo-{{ $v->id }}"
                                        class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-4 border-l-4 {{ $colores[$estado]['borde'] ?? 'border-gray-300' }}">
                                        <div class="flex items-center justify-between"> <a
                                                href="{{ route('vuelos.show', $v) }}"
                                                class="font-bold text-gray-800 dark:text-gray-200 hover:text-emerald-600">{{ $v->numero_vuelo ?? 'Charter' }}</a>
                                            <span
                                                class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Carbon::parse($v->salida_programada)->format('H:i') }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mt-1.5">
                                            <span class="font-medium">{{ $v->origen->codigo_oaci ?? '?' }}</span> <svg
                                                class="w-4 h-4 mx-1 text-emerald-500 fill-current" viewBox="0 0 16 16">
                                                <path d="M11.4 8L8 4.6 9.4 3.2 15.2 8 9.4 12.8 8 11.4z" />
                                                <path d="M0 7h13v2H0z" />
                                            </svg> <span
                                                class="font-medium">{{ $v->destino->codigo_oaci ?? '?' }}</span> </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $v->aeronave->matricula ?? 'Sin aeronave' }} @if ($v->piloto && $v->piloto->empleado)
                                                · {{ $v->piloto->empleado->apellidos }}
                                                @endif </div>
                                        <div class="mt-3">
                                            <div
                                                class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                <span>{{ $v->boletos_count }} pax @if ($cap)
                                                        / {{ $cap }}
                                                    @endif
                                                </span>
                                                @if ($ocup !== null)
                                                    <span>{{ $ocup }}%</span>
                                                    @endif
                                            </div>
                                            @if ($ocup !== null)
                                                <div
                                                    class="h-1.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $ocup >= 90 ? 'bg-red-500' : 'bg-emerald-500' }}"
                                                        style="width: {{ $ocup }}%"></div>
                                                </div>
                                                @endif
                                        </div>
                                        <div class="mt-3">
                                            @if ($estado === 'aterrizado')
                                                <div class="text-xs text-center text-teal-600 font-medium py-1.5">✓
                                                    Finalizado</div>
                                            @elseif (auth()->user()->can('operaciones.despachar'))
                                                @if (in_array($estado, ['programado', 'confirmado', 'retrasado']))
                                                    <a href="{{ route('vuelos.despachar.form', $v) }}"
                                                        class="btn-sm w-full bg-emerald-500 hover:bg-emerald-600 text-white justify-center">Despachar</a>
                                                @elseif ($estado === 'abordando')
                                                    <button wire:click="cerrar({{ $v->id }})"
                                                        wire:confirm="¿Cerrar y despegar el vuelo? Los boletos sin check-in quedarán como no-show."
                                                        class="btn-sm w-full bg-amber-500 hover:bg-amber-600 text-white justify-center">Cerrar
                                                        (despegar)</button>
                                                @elseif ($estado === 'en_vuelo')
                                                    <button wire:click="aterrizar({{ $v->id }})"
                                                        wire:confirm="¿Registrar el aterrizaje ahora?"
                                                        class="btn-sm w-full bg-indigo-500 hover:bg-indigo-600 text-white justify-center">Aterrizar</button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
</div>
