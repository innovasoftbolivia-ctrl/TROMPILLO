<div> @php $estadosCss = ['programado'=>['label'=>'Programado','css'=>'bg-gray-500/15 text-gray-600'],'confirmado'=>['label'=>'Confirmado','css'=>'bg-sky-500/15 text-sky-600'],'abordando'=>['label'=>'Abordando','css'=>'bg-amber-500/15 text-amber-600'],'en_vuelo'=>['label'=>'En vuelo','css'=>'bg-indigo-500/15 text-indigo-600'],'aterrizado'=>['label'=>'Aterrizado','css'=>'bg-emerald-500/15 text-emerald-600'],'cancelado'=>['label'=>'Cancelado','css'=>'bg-red-500/15 text-red-600'],'retrasado'=>['label'=>'Retrasado','css'=>'bg-orange-500/15 text-orange-600']]; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
        <div class="mb-6"><a href="{{ route('vuelos.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a vuelos</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-bold">Vuelo {{ $vuelo->numero_vuelo ?? 'S/N' }}</h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosCss[$vuelo->estado]['css'] ?? 'bg-gray-500/20 text-gray-600' }}">{{ $estadosCss[$vuelo->estado]['label'] ?? $vuelo->estado }}</span>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0">
                    @if (in_array($vuelo->estado, ['programado', 'confirmado']))
                        <a href="{{ route('vuelos.despachar.form', $vuelo) }}"
                            class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Despachar vuelo</a> <a
                            href="{{ route('vuelos.edit', $vuelo) }}"
                            class="btn border-gray-200 text-gray-600 hover:border-gray-300">Editar</a>
                        @endif @if ($vuelo->estado === 'abordando')
                            <button wire:click="cerrar"
                                wire:confirm="¿Cerrar vuelo y marcar salida real ahora? Esto cerrará el abordaje."
                                class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Cerrar vuelo
                                (Despegar)</button>
                            @endif @if ($vuelo->estado === 'en_vuelo')
                                <button wire:click="aterrizar" wire:confirm="¿Registrar aterrizaje ahora?"
                                    class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Registrar
                                    aterrizaje</button>
                                @endif
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 border border-green-500/30 text-green-700">
                {{ session('success') }}</div>
            @endif @if (session('error'))
                <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700">
                    {{ session('error') }}</div>
            @endif
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                    <h3 class="font-semibold mb-4">Itinerario</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Origen</dt>
                            <dd class="font-medium">{{ $vuelo->origen->ciudad }} ({{ $vuelo->origen->codigo_iata }})
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Destino</dt>
                            <dd class="font-medium">{{ $vuelo->destino->ciudad }} ({{ $vuelo->destino->codigo_iata }})
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Salida Prog.</dt>
                            <dd class="font-medium">{{ $vuelo->salida_programada }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Llegada Prog.</dt>
                            <dd class="font-medium">{{ $vuelo->llegada_programada ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Salida Real</dt>
                            <dd class="font-medium">{{ $vuelo->salida_real ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Llegada Real</dt>
                            <dd class="font-medium">{{ $vuelo->llegada_real ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
                <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                    <h3 class="font-semibold mb-4">Operación</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Aeronave</dt>
                            <dd class="font-medium">{{ $vuelo->aeronave ? $vuelo->aeronave->matricula : 'Pendiente' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Piloto</dt>
                            <dd class="font-medium">
                                {{ $vuelo->piloto ? $vuelo->piloto->empleado->nombres . ' ' . $vuelo->piloto->empleado->apellidos : 'Pendiente' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Copiloto</dt>
                            <dd class="font-medium">
                                {{ $vuelo->copiloto ? $vuelo->copiloto->empleado->nombres . ' ' . $vuelo->copiloto->empleado->apellidos : 'Pendiente' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Tipo</dt>
                            <dd class="font-medium">{{ ucfirst($vuelo->tipo) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Asientos</dt>
                            <dd class="font-medium">{{ $vuelo->asientos_disponibles }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Precio</dt>
                            <dd class="font-medium">Bs {{ number_format($vuelo->precio, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
    </div>
</div>
