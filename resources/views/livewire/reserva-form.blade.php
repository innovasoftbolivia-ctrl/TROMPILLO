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
                        <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6 mb-6">
                            <h2 class="font-bold text-lg mb-4 border-b border-gray-100 dark:border-slate-700 pb-2">
                                Información de Pago (Opcional)</h2>
                            <div class="grid grid-cols-12 gap-6">
                                <div class="col-span-6 sm:col-span-3"><label
                                        class="block text-sm font-medium mb-1">Monto (Bs)</label><input
                                        wire:model.blur="pago_monto" type="number" step="0.01"
                                        class="form-input w-full" placeholder="Total..."></div>
                                <div class="col-span-6 sm:col-span-3"><label
                                        class="block text-sm font-medium mb-1">Método</label><select
                                        wire:model="pago_metodo" class="form-select w-full">
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta_credito">Tarj. Crédito</option>
                                        <option value="tarjeta_debito">Tarj. Débito</option>
                                        <option value="transferencia">Transferencia</option>
                                        <option value="pse">PSE</option>
                                        <option value="nequi">Nequi / QR</option>
                                    </select></div>
                                <div class="col-span-6 sm:col-span-3"><label
                                        class="block text-sm font-medium mb-1">Estado Pago</label><select
                                        wire:model="pago_estado" class="form-select w-full">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="pagado">Pagado</option>
                                    </select></div>
                                <div class="col-span-6 sm:col-span-3"><label
                                        class="block text-sm font-medium mb-1">Ref/Comprobante</label><input
                                        wire:model.blur="pago_referencia" type="text" class="form-input w-full">
                                </div>
                            </div>
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
