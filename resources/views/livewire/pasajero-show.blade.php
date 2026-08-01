<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"> <a href="{{ route('pasajeros.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a pasajeros</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">
                    {{ $pasajero->nombre_completo }}</h1>
                <div class="flex gap-2 mt-4 sm:mt-0"> <a href="{{ route('pasajeros.edit', $pasajero) }}"
                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Editar</a> <button
                        wire:click="eliminar" wire:confirm="¿Eliminar a este pasajero?"
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
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Datos personales</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Nombres</dt>
                        <dd class="font-medium">{{ $pasajero->nombres }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Apellidos</dt>
                        <dd class="font-medium">{{ $pasajero->apellidos }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Documento</dt>
                        <dd class="font-medium">{{ $pasajero->tipo_documento }} {{ $pasajero->numero_documento }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Fecha nacimiento</dt>
                        <dd class="font-medium">{{ $pasajero->fecha_nacimiento ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Nacionalidad</dt>
                        <dd class="font-medium">{{ $pasajero->nacionalidad }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Peso</dt>
                        <dd class="font-medium">{{ $pasajero->peso_kg ? $pasajero->peso_kg . ' kg' : '—' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Contacto</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Teléfono</dt>
                        <dd class="font-medium">{{ $pasajero->telefono ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="font-medium">{{ $pasajero->email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Emergencia</dt>
                        <dd class="font-medium">{{ $pasajero->contacto_emergencia ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Tel. emergencia</dt>
                        <dd class="font-medium">{{ $pasajero->telefono_emergencia ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
