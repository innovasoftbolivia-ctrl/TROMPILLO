<div> @php $estados = ['activa' => ['label' => 'Activa', 'css' => 'bg-emerald-500/15 text-emerald-600'], 'mantenimiento' => ['label' => 'En mantenimiento', 'css' => 'bg-amber-500/15 text-amber-600'], 'inactiva' => ['label' => 'Inactiva', 'css' => 'bg-gray-500/20 text-gray-600'], 'retirada' => ['label' => 'Retirada', 'css' => 'bg-gray-500/20 text-gray-600']]; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"> <a href="{{ route('aeronaves.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a aeronaves</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">
                        {{ $aeronave->matricula }} · {{ $aeronave->modelo }}</h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estados[$aeronave->estado]['css'] ?? 'bg-gray-500/20 text-gray-600' }}">{{ $estados[$aeronave->estado]['label'] ?? $aeronave->estado }}</span>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0"> <a href="{{ route('aeronaves.edit', $aeronave) }}"
                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Editar</a> <button
                        wire:click="eliminar" wire:confirm="¿Eliminar esta aeronave?"
                        class="btn border-gray-200 /60 text-red-600 hover:border-red-300">Eliminar</button> </div>
            </div>
        </div>
        @if (session('error'))
            <div
                class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-400">
                {{ session('error') }}</div>
        @endif
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Identificación</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Matrícula</dt>
                        <dd class="font-medium">{{ $aeronave->matricula }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Modelo</dt>
                        <dd class="font-medium">{{ $aeronave->modelo }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Fabricante</dt>
                        <dd class="font-medium">{{ $aeronave->fabricante }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Año</dt>
                        <dd class="font-medium">{{ $aeronave->ano_fabricacion ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Capacidad y rendimiento</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Pasajeros</dt>
                        <dd class="font-medium">{{ $aeronave->capacidad_pasajeros ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Carga</dt>
                        <dd class="font-medium">
                            {{ $aeronave->capacidad_carga_kg ? number_format($aeronave->capacidad_carga_kg, 0, ',', '.') . ' kg' : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Peso vacío</dt>
                        <dd class="font-medium">
                            {{ $aeronave->peso_vacio_kg ? number_format($aeronave->peso_vacio_kg, 0, ',', '.') . ' kg' : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">MTOW</dt>
                        <dd class="font-medium">
                            {{ $aeronave->peso_maximo_despegue_kg ? number_format($aeronave->peso_maximo_despegue_kg, 0, ',', '.') . ' kg' : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Autonomía</dt>
                        <dd class="font-medium">
                            {{ $aeronave->autonomia_km ? number_format($aeronave->autonomia_km, 0, ',', '.') . ' km' : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Vel. crucero</dt>
                        <dd class="font-medium">
                            {{ $aeronave->velocidad_crucero_kmh ? $aeronave->velocidad_crucero_kmh . ' km/h' : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Mantenimiento</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Horas de vuelo</dt>
                        <dd class="font-medium">{{ number_format($aeronave->horas_vuelo_totales ?? 0, 0, ',', '.') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Última revisión</dt>
                        <dd class="font-medium">{{ $aeronave->fecha_ultima_revision ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Próxima revisión</dt>
                        <dd class="font-medium">{{ $aeronave->fecha_proxima_revision ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
