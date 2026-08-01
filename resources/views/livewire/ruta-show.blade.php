<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"> <a href="{{ route('rutas.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a rutas</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">
                        {{ $ruta->origen->ciudad ?? '?' }} → {{ $ruta->destino->ciudad ?? '?' }} </h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $ruta->activa ? 'bg-emerald-500/15 text-emerald-600' : 'bg-gray-500/20 text-gray-600' }}">{{ $ruta->activa ? 'Activa' : 'Inactiva' }}</span>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0"> <a href="{{ route('rutas.edit', $ruta) }}"
                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Editar</a> <button
                        wire:click="eliminar" wire:confirm="¿Eliminar esta ruta?"
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
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Trayecto</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Origen</dt>
                        <dd class="font-medium">{{ $ruta->origen->ciudad ?? '?' }}
                            ({{ $ruta->origen->codigo_oaci ?? '' }})</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Destino</dt>
                        <dd class="font-medium">{{ $ruta->destino->ciudad ?? '?' }}
                            ({{ $ruta->destino->codigo_oaci ?? '' }})</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Distancia</dt>
                        <dd class="font-medium">
                            {{ $ruta->distancia_km ? number_format($ruta->distancia_km, 0, ',', '.') . ' km' : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Duración</dt>
                        <dd class="font-medium">
                            {{ $ruta->duracion_estimada_min ? $ruta->duracion_estimada_min . ' min' : '—' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Comercial</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Precio base</dt>
                        <dd class="font-medium">Bs {{ number_format($ruta->precio_base, 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Activa</dt>
                        <dd class="font-medium">{{ $ruta->activa ? 'Sí' : 'No' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
