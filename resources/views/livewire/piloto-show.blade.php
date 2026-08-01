<div> @php $tipos = ['PPL'=>'PPL','CPL'=>'CPL','ATPL'=>'ATPL','PCA'=>'PCA']; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('pilotos.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a pilotos</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">
                        {{ $piloto->licencia_numero }}</h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 bg-violet-500/15 text-violet-600">{{ $piloto->tipo_licencia }}</span>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0"><a href="{{ route('pilotos.edit', $piloto) }}"
                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Editar</a><button
                        wire:click="eliminar" wire:confirm="¿Eliminar este piloto?"
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
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Empleado</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Nombre</dt>
                        <dd class="font-medium">
                            {{ $piloto->empleado ? $piloto->empleado->nombres . ' ' . $piloto->empleado->apellidos : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Licencia</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Número</dt>
                        <dd class="font-medium">{{ $piloto->licencia_numero }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Tipo</dt>
                        <dd class="font-medium">{{ $piloto->tipo_licencia }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Horas vuelo</dt>
                        <dd class="font-medium">{{ number_format($piloto->horas_vuelo ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Vencimiento lic.</dt>
                        <dd class="font-medium">{{ $piloto->vencimiento_licencia ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Vencimiento médico</dt>
                        <dd class="font-medium">{{ $piloto->vencimiento_medico ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Habilitaciones</dt>
                        <dd class="font-medium">{{ $piloto->habilitaciones ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
