<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"> <a href="{{ route('personas.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a personas</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">
                        {{ $persona->nombre_completo }}</h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $persona->tipo_persona === 'natural' ? 'bg-sky-500/15 text-sky-600' : 'bg-violet-500/15 text-violet-600' }}">{{ ucfirst($persona->tipo_persona) }}</span>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0"> <a href="{{ route('personas.edit', $persona) }}"
                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Editar</a> <button
                        wire:click="eliminar" wire:confirm="¿Eliminar esta persona?"
                        class="btn border-gray-200 /60 text-red-600 hover:border-red-300">Eliminar</button> </div>
            </div>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Identificación</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Documento</dt>
                        <dd class="font-medium">{{ $persona->tipo_documento }} {{ $persona->numero_documento }}</dd>
                    </div>
                    @if ($persona->tipo_persona === 'natural' && $persona->natural)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Nombres</dt>
                            <dd class="font-medium">{{ $persona->natural->nombres }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Apellidos</dt>
                            <dd class="font-medium">{{ $persona->natural->apellidos }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Fecha nacimiento</dt>
                            <dd class="font-medium">{{ $persona->natural->fecha_nacimiento ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Sexo</dt>
                            <dd class="font-medium">{{ $persona->natural->sexo ?? '—' }}</dd>
                        </div>
                    @elseif ($persona->juridica)
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Razón social</dt>
                            <dd class="font-medium">{{ $persona->juridica->razon_social }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">NIT</dt>
                            <dd class="font-medium">{{ $persona->juridica->nit ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Representante</dt>
                            <dd class="font-medium">{{ $persona->juridica->representante_legal ?? '—' }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Contacto</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Teléfono</dt>
                        <dd class="font-medium">{{ $persona->telefono ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="font-medium">{{ $persona->email ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Dirección</dt>
                        <dd class="font-medium">{{ $persona->direccion ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">País</dt>
                        <dd class="font-medium">{{ $persona->pais->nombre ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
