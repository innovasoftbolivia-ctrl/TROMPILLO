<div> @php
    $tipos = ['preventivo' => 'Preventivo', 'correctivo' => 'Correctivo', 'inspeccion' => 'Inspección', 'revision_100h' => 'Revisión 100h', 'revision_anual' => 'Revisión anual'];
    $estadosCss = ['programado' => ['label' => 'Programado', 'css' => 'bg-sky-500/15 text-sky-600'], 'en_proceso' => ['label' => 'En proceso', 'css' => 'bg-amber-500/15 text-amber-600'], 'completado' => ['label' => 'Completado', 'css' => 'bg-emerald-500/15 text-emerald-600'], 'cancelado' => ['label' => 'Cancelado', 'css' => 'bg-red-500/15 text-red-600']];
@endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('mantenimientos.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a mantenimientos</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">
                        {{ $mantenimiento->aeronave->matricula ?? '—' }} ·
                        {{ $tipos[$mantenimiento->tipo] ?? $mantenimiento->tipo }}</h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosCss[$mantenimiento->estado]['css'] ?? 'bg-gray-500/20 text-gray-600' }}">{{ $estadosCss[$mantenimiento->estado]['label'] ?? $mantenimiento->estado }}</span>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0"><a href="{{ route('mantenimientos.edit', $mantenimiento) }}"
                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Editar</a><button
                        wire:click="eliminar" wire:confirm="¿Eliminar?"
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
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Detalles</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Aeronave</dt>
                        <dd class="font-medium">{{ $mantenimiento->aeronave->matricula ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Técnico</dt>
                        <dd class="font-medium">
                            {{ $mantenimiento->tecnico ? $mantenimiento->tecnico->nombres . ' ' . $mantenimiento->tecnico->apellidos : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tipo</dt>
                        <dd class="font-medium">{{ $tipos[$mantenimiento->tipo] ?? $mantenimiento->tipo }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Descripción</dt>
                        <dd class="font-medium mt-1">{{ $mantenimiento->descripcion }}</dd>
                    </div>
                </dl>
            </div>
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Programación</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Fecha inicio</dt>
                        <dd class="font-medium">{{ $mantenimiento->fecha_inicio }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Fecha fin</dt>
                        <dd class="font-medium">{{ $mantenimiento->fecha_fin ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Horas vuelo</dt>
                        <dd class="font-medium">{{ $mantenimiento->horas_vuelo_aeronave ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Costo</dt>
                        <dd class="font-medium">Bs {{ number_format($mantenimiento->costo ?? 0, 2, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
