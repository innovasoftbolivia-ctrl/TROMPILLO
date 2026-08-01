<div> @php $cargos = ['piloto'=>'Piloto','copiloto'=>'Copiloto','tecnico'=>'Técnico','despachador'=>'Despachador','administrativo'=>'Administrativo','ventas'=>'Ventas','gerente'=>'Gerente']; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('empleados.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a empleados</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">{{ $empleado->nombres }}
                        {{ $empleado->apellidos }}</h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 bg-sky-500/15 text-sky-600">{{ $cargos[$empleado->cargo] ?? $empleado->cargo }}</span>
                    <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $empleado->activo ? 'bg-emerald-500/15 text-emerald-600' : 'bg-gray-500/20 text-gray-600' }}">{{ $empleado->activo ? 'Activo' : 'Inactivo' }}</span>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0"><a href="{{ route('empleados.edit', $empleado) }}"
                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Editar</a><button
                        wire:click="eliminar" wire:confirm="¿Eliminar este empleado?"
                        class="btn border-gray-200 /60 text-red-600 hover:border-red-300">Eliminar</button></div>
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
                        <dt class="text-gray-500 dark:text-gray-400">Documento</dt>
                        <dd class="font-medium">{{ $empleado->tipo_documento }} {{ $empleado->numero_documento }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Fecha nacimiento</dt>
                        <dd class="font-medium">{{ $empleado->fecha_nacimiento ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Teléfono</dt>
                        <dd class="font-medium">{{ $empleado->telefono ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="font-medium">{{ $empleado->email ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Laboral</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Cargo</dt>
                        <dd class="font-medium">{{ $cargos[$empleado->cargo] ?? $empleado->cargo }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Contratación</dt>
                        <dd class="font-medium">{{ $empleado->fecha_contratacion ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Salario</dt>
                        <dd class="font-medium">
                            {{ $empleado->salario ? 'Bs ' . number_format($empleado->salario, 2, ',', '.') : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
