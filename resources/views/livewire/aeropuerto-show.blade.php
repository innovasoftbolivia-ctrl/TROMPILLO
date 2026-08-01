<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto"> <!-- Encabezado -->
        <div class="mb-6"> <a href="{{ route('aeropuertos.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a aeropuertos</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">
                        {{ $aeropuerto->codigo_oaci }} · {{ $aeropuerto->nombre }} </h1>
                    @if ($aeropuerto->activo)
                        <span
                            class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">Activo</span>
                    @else
                        <span
                            class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 bg-gray-500/20 text-gray-600 dark:text-gray-400">Inactivo</span>
                        @endif
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0"> <a href="{{ route('aeropuertos.edit', $aeropuerto) }}"
                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Editar</a> <button
                        wire:click="eliminar" wire:confirm="¿Eliminar este aeropuerto?"
                        class="btn border-gray-200 /60 text-red-600 hover:border-red-300">Eliminar</button> </div>
            </div>
        </div>
        @if (session('error'))
            <div
                class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-400">
                {{ session('error') }}</div>
        @endif <!-- Detalles -->
        <div class="grid grid-cols-12 gap-6"> <!-- Identificación -->
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Identificación</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Código OACI</dt>
                        <dd class="font-medium">{{ $aeropuerto->codigo_oaci }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Código IATA</dt>
                        <dd class="font-medium">{{ $aeropuerto->codigo_iata ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Nombre</dt>
                        <dd class="font-medium">{{ $aeropuerto->nombre }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Tipo</dt>
                        <dd class="font-medium capitalize">{{ $aeropuerto->tipo }}</dd>
                    </div>
                </dl>
            </div> <!-- Ubicación -->
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Ubicación</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Ciudad</dt>
                        <dd class="font-medium">{{ $aeropuerto->ciudad }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Departamento</dt>
                        <dd class="font-medium">{{ $aeropuerto->departamento ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">País</dt>
                        <dd class="font-medium">{{ $aeropuerto->pais }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Latitud</dt>
                        <dd class="font-medium">{{ $aeropuerto->latitud ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Longitud</dt>
                        <dd class="font-medium">{{ $aeropuerto->longitud ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Elevación</dt>
                        <dd class="font-medium">
                            {{ $aeropuerto->elevacion_pies ? number_format($aeropuerto->elevacion_pies, 0, ',', '.') . ' pies' : '—' }}
                        </dd>
                    </div>
                </dl>
            </div> <!-- Pista -->
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Pista</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Longitud</dt>
                        <dd class="font-medium">
                            {{ $aeropuerto->longitud_pista_m ? number_format($aeropuerto->longitud_pista_m, 0, ',', '.') . ' m' : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Superficie</dt>
                        <dd class="font-medium capitalize">{{ $aeropuerto->superficie_pista ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
