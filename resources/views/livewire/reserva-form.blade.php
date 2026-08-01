<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
        <div class="mb-6"><a href="{{ route('reservas.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a reservas</a>
            <h1 class="text-2xl md:text-3xl font-bold mt-2">{{ $isEdit ? 'Editar Reserva ' . $codigo : 'Nueva Reserva' }}
            </h1>
        </div>
        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700">
                {{ session('error') }}</div>
            @endif <form wire:submit="guardar">
                @if ($reservaBloqueada)
                    <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500 text-white font-semibold shadow-sm">
                        {{ $mensajeBloqueo }} </div>
                    @endif @if ($errors->any())
                        <div
                            class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6 mb-6">
                        <h2 class="font-bold text-lg mb-4 border-b border-gray-100 dark:border-slate-700 pb-2">Datos
                            Principales</h2>
                        <div class="grid grid-cols-12 gap-6">
                            <div class="col-span-12 sm:col-span-3"><label class="block text-sm font-medium mb-1">Código
                                    PNR <span class="text-red-500">*</span></label><input wire:model.blur="codigo"
                                    type="text" class="form-input w-full uppercase" {{ $isEdit ? 'readonly' : '' }}>
                            </div>
                            <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Vuelo
                                    <span class="text-red-500">*</span></label><select wire:model.live="vuelo_id"
                                    class="form-select w-full">
                                    <option value="">— Seleccionar Vuelo —</option>
                                    @foreach ($vuelos as $v)
                                        <option value="{{ $v->id }}">{{ $v->numero_vuelo ?? 'S/N' }}
                                            ({{ $v->origen->codigo_iata }} → {{ $v->destino->codigo_iata }}) -
                                            {{ $v->salida_programada }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-12 sm:col-span-3"><label class="block text-sm font-medium mb-1">Estado
                                    <span class="text-red-500">*</span></label><select wire:model="estado"
                                    class="form-select w-full">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="cancelada">Cancelada</option>
                                    <option value="completada">Completada</option>
                                </select></div>
                            <div class="col-span-12 sm:col-span-4"><label class="block text-sm font-medium mb-1">Fecha
                                    Reserva</label><input wire:model="fecha_reserva" type="datetime-local"
                                    class="form-input w-full"></div>
                            <div class="col-span-12 sm:col-span-8"><label
                                    class="block text-sm font-medium mb-1">Notas</label>
                                <textarea wire:model.blur="notas" class="form-textarea w-full" rows="1"></textarea>
                            </div>
                        </div>
                    </div>
                    @if (!$isEdit)
                        <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6 mb-6">
                            <div
                                class="flex items-center justify-between mb-4 border-b border-gray-100 dark:border-slate-700 pb-2">
                                <h2 class="font-bold text-lg">Boletos (Personas)</h2>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="abrirPersona"
                                        class="btn-sm border-emerald-500 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">+ Nueva persona</button>
                                    <button type="button" wire:click="agregarBoleto"
                                        class="btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">✚ Agregar</button>
                                </div>
                            </div>

                            {{-- Mapa de asientos (horizontal, estilo aerolínea premium) --}}
                            @if ($mapa)
                                @php
                                    $totalCeldas = collect($mapa['filas'])->flatMap(fn ($f) => $f['celdas']);
                                    $nSel = $totalCeldas->where('estado', 'seleccionado')->count();
                                    $nOcu = $totalCeldas->where('estado', 'ocupado')->count();
                                    $nLib = $totalCeldas->where('estado', 'libre')->count();
                                @endphp
                                <div class="mb-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/70 bg-gradient-to-b from-white to-slate-50/70 dark:from-slate-800/80 dark:to-slate-900/60 shadow-sm overflow-hidden">
                                    {{-- Encabezado --}}
                                    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-b border-slate-100 dark:border-slate-700/60 bg-white/60 dark:bg-slate-800/40">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md shadow-emerald-500/20">
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M22 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5L22 16z"/></svg>
                                            </span>
                                            <div>
                                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-tight tracking-tight">Selección de asientos</p>
                                                <p class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                                                    <span class="font-mono uppercase tracking-wider">{{ $mapa['matricula'] }}</span>
                                                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                                                    {{ $mapa['capacidad'] }} asientos
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2.5">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $nSel ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400 ring-1 ring-sky-500/20' : 'bg-slate-100 dark:bg-slate-700/60 text-slate-400 dark:text-slate-500' }}">
                                                <svg class="w-3 h-3 fill-current" viewBox="0 0 16 16"><path d="M6 12 2 8l1.4-1.4L6 9.2l6.6-6.6L14 4z"/></svg>
                                                {{ $nSel }} elegido{{ $nSel === 1 ? '' : 's' }}
                                            </span>
                                            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                                {{ $nLib }} libre{{ $nLib === 1 ? '' : 's' }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Fuselaje / cabina --}}
                                    <div class="overflow-x-auto px-4 py-5">
                                        <div class="inline-flex items-stretch min-w-full">
                                            {{-- Nariz / cabina --}}
                                            <div class="shrink-0 w-20 rounded-l-[3rem] bg-gradient-to-br from-slate-200 via-slate-100 to-white dark:from-slate-700 dark:via-slate-700 dark:to-slate-800 ring-1 ring-inset ring-white/60 dark:ring-slate-600/40 border border-r-0 border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center gap-1 relative">
                                                <span class="absolute inset-y-3 right-0 w-px bg-slate-200 dark:bg-slate-700"></span>
                                                <svg class="w-7 h-7 text-slate-400/80 dark:text-slate-500 fill-current" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 4a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm-5 9c0-1.7 2.2-2.5 5-2.5s5 .8 5 2.5v1H7v-1z"/></svg>
                                                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Cabina</span>
                                            </div>

                                            {{-- Piso de cabina --}}
                                            <div class="flex items-stretch gap-2.5 px-5 py-4 border-y border-slate-200 dark:border-slate-700 bg-[repeating-linear-gradient(90deg,transparent,transparent_46px,rgba(148,163,184,.12)_46px,rgba(148,163,184,.12)_48px)] dark:bg-[repeating-linear-gradient(90deg,transparent,transparent_46px,rgba(148,163,184,.08)_46px,rgba(148,163,184,.08)_48px)]">
                                                @foreach ($mapa['filas'] as $fila)
                                                    <div class="flex flex-col items-center gap-2">
                                                        <div class="px-1.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700/70 text-[9px] font-bold text-slate-400 dark:text-slate-400 tabular-nums">{{ $fila['num'] }}</div>
                                                        @foreach ($fila['celdas'] as $ci => $c)
                                                            @php
                                                                $st = $c['estado'];
                                                                $cushion = $st === 'ocupado'
                                                                    ? 'bg-gradient-to-b from-rose-100 to-rose-200 dark:from-rose-500/25 dark:to-rose-600/40'
                                                                    : ($st === 'seleccionado'
                                                                        ? 'bg-gradient-to-b from-sky-400 to-blue-600'
                                                                        : 'bg-gradient-to-b from-emerald-50 to-emerald-200 dark:from-emerald-500/25 dark:to-emerald-600/40');
                                                                $frame = $st === 'ocupado'
                                                                    ? 'ring-1 ring-rose-300/70 dark:ring-rose-600'
                                                                    : ($st === 'seleccionado'
                                                                        ? 'ring-2 ring-sky-500/60 shadow-lg shadow-sky-500/30'
                                                                        : 'ring-1 ring-emerald-300/60 dark:ring-emerald-500/40 shadow-sm');
                                                                $rest = $st === 'ocupado' ? 'bg-rose-300 dark:bg-rose-600/60' : ($st === 'seleccionado' ? 'bg-blue-700/70' : 'bg-emerald-300/80 dark:bg-emerald-500/50');
                                                                $txt  = $st === 'ocupado' ? 'text-rose-400 dark:text-rose-300' : ($st === 'seleccionado' ? 'text-white' : 'text-emerald-700 dark:text-emerald-100');
                                                                $fx   = $st === 'ocupado' ? 'cursor-not-allowed' : 'cursor-pointer hover:-translate-y-1 hover:brightness-105';
                                                            @endphp
                                                            <button type="button"
                                                                @if ($st === 'ocupado') disabled @else wire:click="elegirAsiento('{{ $c['codigo'] }}')" @endif
                                                                title="Asiento {{ $c['codigo'] }} — {{ ucfirst($st) }}"
                                                                class="group relative w-10 h-11 transition-all duration-200 ease-out {{ $fx }}">
                                                                {{-- respaldo --}}
                                                                <span class="absolute top-0 left-1/2 -translate-x-1/2 w-7 h-2.5 rounded-t-lg {{ $rest }}"></span>
                                                                {{-- apoyabrazos --}}
                                                                <span class="absolute bottom-1 left-0 w-1.5 h-5 rounded-full {{ $rest }}"></span>
                                                                <span class="absolute bottom-1 right-0 w-1.5 h-5 rounded-full {{ $rest }}"></span>
                                                                {{-- cojín --}}
                                                                <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-8 h-8 rounded-xl {{ $cushion }} {{ $frame }} flex items-center justify-center transition-all">
                                                                    @if ($st === 'seleccionado')
                                                                        <svg class="w-4 h-4 text-white fill-current drop-shadow" viewBox="0 0 16 16"><path d="M6 12 2 8l1.4-1.4L6 9.2l6.6-6.6L14 4z"/></svg>
                                                                    @elseif ($st === 'ocupado')
                                                                        <svg class="w-3 h-3 text-rose-500 dark:text-rose-300 fill-current" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 1.5c-3 0-5 1.5-5 3.5V15h10v-2c0-2-2-3.5-5-3.5z"/></svg>
                                                                    @else
                                                                        <span class="text-[11px] font-extrabold {{ $txt }} group-hover:scale-110 transition-transform">{{ $c['letra'] }}</span>
                                                                    @endif
                                                                </span>
                                                            </button>
                                                            @if ($ci === 1 && count($fila['celdas']) > 2)
                                                                <div class="h-3 flex items-center"><span class="w-px h-2 bg-slate-300/60 dark:bg-slate-600/60"></span></div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Cola --}}
                                            <div class="shrink-0 w-14 rounded-r-[3.5rem] bg-gradient-to-bl from-slate-200 via-slate-100 to-white dark:from-slate-700 dark:via-slate-700 dark:to-slate-800 ring-1 ring-inset ring-white/60 dark:ring-slate-600/40 border border-l-0 border-slate-200 dark:border-slate-700"></div>
                                        </div>
                                    </div>

                                    {{-- Pie: leyenda + ayuda --}}
                                    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-t border-slate-100 dark:border-slate-700/60 bg-white/50 dark:bg-slate-800/30">
                                        <div class="flex items-center gap-3.5 text-[11px] text-slate-500 dark:text-slate-400">
                                            <span class="inline-flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-gradient-to-b from-emerald-50 to-emerald-200 dark:from-emerald-500/25 dark:to-emerald-600/40 ring-1 ring-emerald-300/60"></span> Libre</span>
                                            <span class="inline-flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-gradient-to-b from-sky-400 to-blue-600 ring-1 ring-sky-500/50"></span> Elegido</span>
                                            <span class="inline-flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-md bg-gradient-to-b from-rose-100 to-rose-200 dark:from-rose-500/25 dark:to-rose-600/40 ring-1 ring-rose-300/70 dark:ring-rose-600"></span> Ocupado</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Tocá un asiento <span class="text-emerald-600 dark:text-emerald-400 font-medium">libre</span> para asignarlo · tocá el <span class="text-sky-600 dark:text-sky-400 font-medium">elegido</span> para liberarlo</p>
                                    </div>
                                </div>
                            @else
                                <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 text-sm">
                                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zM7 4h2v5H7V4zm0 6h2v2H7v-2z"/></svg>
                                    Elegí un vuelo para ver el mapa de asientos.
                                </div>
                            @endif

                            @forelse($boletos as $index => $boleto)
                                <div class="grid grid-cols-12 gap-4 items-end mb-4 p-4 border border-gray-100 dark:border-slate-700 rounded-lg bg-gray-50 dark:bg-slate-800/50"
                                    wire:key="boleto-{{ $index }}">
                                    <div class="col-span-12 sm:col-span-5"><label
                                            class="block text-sm font-medium mb-1">Persona {{ $index + 1 }} — Carnet / CI <span
                                                class="text-red-500">*</span></label>
                                        <input wire:model.live.debounce.400ms="boletos.{{ $index }}.carnet" type="text"
                                            class="form-input w-full" placeholder="Escribí el carnet…" autocomplete="off">
                                        @if (!empty($boleto['nombre']))
                                            <input type="text" value="✓ {{ $boleto['nombre'] }}" readonly
                                                class="form-input w-full mt-1 bg-emerald-50 dark:bg-emerald-500/10 border-emerald-300 dark:border-emerald-500/40 text-emerald-700 dark:text-emerald-300 font-semibold">
                                        @elseif (!empty($boleto['carnet']))
                                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">No encontrado — usá "+ Nueva persona" para registrarlo</p>
                                        @endif
                                    </div>
                                    <div class="col-span-4 sm:col-span-2"><label
                                            class="block text-sm font-medium mb-1">Asiento</label><input
                                            wire:model.blur="boletos.{{ $index }}.asiento" type="text"
                                            class="form-input w-full uppercase" placeholder="Ej: 12A"></div>
                                    <div class="col-span-4 sm:col-span-2"><label
                                            class="block text-sm font-medium mb-1">Precio (Bs) <span
                                                class="text-red-500">*</span></label><input
                                            wire:model.blur="boletos.{{ $index }}.precio" type="number"
                                            step="0.01" class="form-input w-full"></div>
                                    <div class="col-span-4 sm:col-span-2"><label
                                            class="block text-sm font-medium mb-1">Eqp (kg)</label><input
                                            wire:model.blur="boletos.{{ $index }}.equipaje_kg" type="number"
                                            step="0.1" class="form-input w-full"></div>
                                    <div class="col-span-12 sm:col-span-1 text-right">
                                        @if (count($boletos) > 1)
                                            <button type="button" wire:click="removerBoleto({{ $index }})"
                                                class="text-red-500 hover:text-red-700">Eliminar</button>
                                        @endif
                                    </div>
                            </div> @empty <div class="text-red-500 text-sm">Debes agregar al menos un pasajero.
                                </div>
                            @endforelse
                        </div>
                    @endif
                    <div class="flex items-center justify-end gap-3 pt-5"> <a href="{{ route('reservas.index') }}"
                            class="btn border-gray-200 text-gray-800 dark:text-gray-200">Cancelar</a> <button
                            type="submit"
                            class="btn bg-indigo-500 hover:bg-indigo-600 text-white disabled:opacity-50 disabled:cursor-not-allowed"
                            @if ($reservaBloqueada) disabled @endif> <span wire:loading.remove
                                wire:target="guardar">{{ $isEdit ? 'Guardar Cambios' : 'Crear Reserva Completa' }}</span>
                            <span wire:loading wire:target="guardar">Procesando…</span> </button> </div>
            </form>

            {{-- Modal: alta rápida de persona/pasajero (sin salir de la reserva) --}}
            @if ($mostrarPersona)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:key="modal-persona">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100">Nueva persona</h3>
                            <button type="button" wire:click="$set('mostrarPersona', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Registrá una persona nueva; se agregará a la reserva automáticamente.</p>
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-sm font-medium mb-1">Nombres <span class="text-red-500">*</span></label>
                                <input wire:model.blur="np_nombres" wire:key="np-nombres" type="text" class="form-input w-full">
                            </div>
                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-sm font-medium mb-1">Apellidos <span class="text-red-500">*</span></label>
                                <input wire:model.blur="np_apellidos" wire:key="np-apellidos" type="text" class="form-input w-full">
                            </div>
                            <div class="col-span-4">
                                <label class="block text-sm font-medium mb-1">Tipo doc.</label>
                                <input wire:model.blur="np_tipo_documento" wire:key="np-tipodoc" type="text" class="form-input w-full">
                            </div>
                            <div class="col-span-8">
                                <label class="block text-sm font-medium mb-1">Nro. de carnet <span class="text-red-500">*</span></label>
                                <input wire:model.blur="np_numero_documento" wire:key="np-numdoc" type="text" class="form-input w-full" placeholder="Ej: 8802345">
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium mb-1">Nacionalidad</label>
                                <input wire:model.blur="np_nacionalidad" wire:key="np-nac" type="text" class="form-input w-full">
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium mb-1">Peso (kg)</label>
                                <input wire:model.blur="np_peso_kg" wire:key="np-peso" type="number" step="0.1" class="form-input w-full">
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium mb-1">Teléfono</label>
                                <input wire:model.blur="np_telefono" wire:key="np-tel" type="text" class="form-input w-full">
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-sm font-medium mb-1">Email</label>
                                <input wire:model.blur="np_email" wire:key="np-email" type="email" class="form-input w-full">
                            </div>
                        </div>
                        @error('np_nombres') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                        @error('np_apellidos') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                        @error('np_numero_documento') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" wire:click="$set('mostrarPersona', false)" class="btn border-gray-200 text-gray-600">Cancelar</button>
                            <button type="button" wire:click="guardarPersona" class="btn bg-emerald-500 hover:bg-emerald-600 text-white">
                                <span wire:loading.remove wire:target="guardarPersona">Registrar y agregar</span>
                                <span wire:loading wire:target="guardarPersona">Guardando…</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
    </div>
</div>
