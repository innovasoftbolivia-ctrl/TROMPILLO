<div> @php $estadosCss = ['registrado'=>['label'=>'Recibido','css'=>'bg-sky-500/15 text-sky-600'],'en_transito'=>['label'=>'En tránsito','css'=>'bg-amber-500/15 text-amber-600'],'entregado'=>['label'=>'Entregado','css'=>'bg-emerald-500/15 text-emerald-600'],'devuelto'=>['label'=>'Devuelto','css'=>'bg-red-500/15 text-red-600']]; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('carga.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a envíos</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold">Guía {{ $envio->guia }}
                    </h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosCss[$envio->estado]['css'] ?? 'bg-gray-500/20 text-gray-600' }}">{{ $estadosCss[$envio->estado]['label'] ?? $envio->estado }}</span>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0"><a href="{{ route('carga.edit', $envio) }}"
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
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Partes</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Remitente</dt>
                        <dd class="font-medium">{{ $envio->remitente }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Doc. remitente</dt>
                        <dd class="font-medium">{{ $envio->remitente_documento ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Destinatario</dt>
                        <dd class="font-medium">{{ $envio->destinatario }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Doc. destinatario</dt>
                        <dd class="font-medium">{{ $envio->destinatario_documento ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
            <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Detalles</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Descripción</dt>
                        <dd class="font-medium">{{ $envio->descripcion ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Peso</dt>
                        <dd class="font-medium">{{ number_format($envio->peso_kg, 1, ',', '.') }} kg</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Valor declarado</dt>
                        <dd class="font-medium">Bs {{ number_format($envio->valor_declarado, 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Costo envío</dt>
                        <dd class="font-medium">Bs {{ number_format($envio->costo_envio, 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Vuelo</dt>
                        <dd class="font-medium">
                            {{ $envio->vuelo ? ($envio->vuelo->origen->ciudad ?? '?') . ' → ' . ($envio->vuelo->destino->ciudad ?? '?') : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
