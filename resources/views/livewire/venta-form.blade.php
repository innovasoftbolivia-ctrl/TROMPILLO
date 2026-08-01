<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
        <div class="mb-6"><a href="{{ route('ventas.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a ventas</a>
            <h1 class="text-2xl md:text-3xl font-bold mt-2">Venta de servicios extra</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Solo para servicios sueltos (exceso de equipaje, cambios, penalidades). Los boletos y los fletes de carga se facturan automáticamente desde Reservas y Carga.</p>
        </div>
        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700">{{ session('error') }}</div>
            @endif <form wire:submit="guardar">
                @if ($ventaBloqueada)
                    <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500 text-white font-semibold">
                        {{ $mensajeBloqueo }} </div>
                    @endif @if ($errors->any())
                        <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700">
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
                            <div class="col-span-12 sm:col-span-6">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-sm font-medium">Cliente — Carnet / NIT <span class="text-xs font-normal text-gray-400">(vacío = casual)</span></label>
                                    <button type="button" wire:click="abrirCliente" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">+ Nuevo cliente</button>
                                </div>
                                <input wire:model.live.debounce.400ms="cliente_carnet" type="text" class="form-input w-full" placeholder="Escribí el documento…" autocomplete="off">
                                @if (!empty($cliente_nombre))
                                    <input type="text" value="✓ {{ $cliente_nombre }}" readonly
                                        class="form-input w-full mt-1 bg-emerald-50 dark:bg-emerald-500/10 border-emerald-300 dark:border-emerald-500/40 text-emerald-700 dark:text-emerald-300 font-semibold">
                                @elseif (!empty($cliente_carnet))
                                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">No encontrado — usá "+ Nuevo cliente" para registrarlo</p>
                                @endif
                            </div>
                            <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Método
                                    de Pago</label><select wire:model="metodo_pago" class="form-select w-full">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="otro">Otro</option>
                                </select></div>
                            <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Estado
                                    de la Venta</label><select wire:model="estado" class="form-select w-full">
                                    <option value="pagada">Pagada</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="anulada">Anulada</option>
                                </select></div>
                            <div class="col-span-12 sm:col-span-6"><label
                                    class="block text-sm font-medium mb-1">Descuento Global (Bs)</label><input
                                    wire:model.blur="descuento" type="number" step="0.01" class="form-input w-full">
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6 mb-6">
                        <div
                            class="flex items-center justify-between mb-4 border-b border-gray-100 dark:border-slate-700 pb-2">
                            <h2 class="font-bold text-lg">Líneas de Venta (Detalle)</h2> <button type="button"
                                wire:click="agregarDetalle"
                                class="btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">✚ Agregar Ítem</button>
                        </div>
                        @forelse($detalles as $index => $detalle)
                            <div class="grid grid-cols-12 gap-4 items-end mb-4 p-4 border border-gray-100 dark:border-slate-700 rounded-lg bg-gray-50 dark:bg-slate-800/50"
                                wire:key="detalle-{{ $index }}">
                                <div class="col-span-12 sm:col-span-6"><label
                                        class="block text-sm font-medium mb-1">Descripción / Servicio <span
                                            class="text-red-500">*</span></label><input
                                        wire:model.blur="detalles.{{ $index }}.descripcion" type="text"
                                        class="form-input w-full" placeholder="Ej: Exceso de equipaje, cambio de fecha, penalidad..."></div>
                                <div class="col-span-4 sm:col-span-2"><label
                                        class="block text-sm font-medium mb-1">Cantidad <span
                                            class="text-red-500">*</span></label><input
                                        wire:model.blur="detalles.{{ $index }}.cantidad" type="number"
                                        class="form-input w-full"></div>
                                <div class="col-span-4 sm:col-span-3"><label
                                        class="block text-sm font-medium mb-1">Precio Unitario (Bs) <span
                                            class="text-red-500">*</span></label><input
                                        wire:model.blur="detalles.{{ $index }}.precio_unitario" type="number"
                                        step="0.01" class="form-input w-full"></div>
                                <div class="col-span-12 sm:col-span-1 text-right">
                                    @if (count($detalles) > 1)
                                        <button type="button" wire:click="removerDetalle({{ $index }})"
                                            class="text-red-500 hover:text-red-700">Eliminar</button>
                                    @endif
                                </div>
                        </div> @empty <div class="text-red-500 text-sm">Debes agregar al menos un ítem a la venta.
                            </div>
                        @endforelse
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-5"> <a href="{{ route('ventas.index') }}"
                            class="btn border-gray-200 text-gray-800 dark:text-gray-200">Cancelar</a> <button
                            type="submit"
                            class="btn bg-emerald-500 hover:bg-emerald-600 text-white disabled:opacity-50 disabled:cursor-not-allowed"
                            @if ($ventaBloqueada) disabled @endif> <span wire:loading.remove
                                wire:target="guardar">Completar Venta</span> <span wire:loading
                                wire:target="guardar">Guardando…</span> </button> </div>
            </form>

            {{-- Modal: alta rápida de cliente (sin salir de la venta) --}}
            @if ($mostrarCliente)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:key="modal-cliente">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100">Nuevo cliente</h3>
                            <button type="button" wire:click="$set('mostrarCliente', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Registrá un cliente nuevo; quedará seleccionado en la venta.</p>
                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-12 sm:col-span-6">
                                <label class="block text-sm font-medium mb-1">Tipo</label>
                                <select wire:model.live="nc_tipo_persona" wire:key="nc-tipo" class="form-select w-full">
                                    <option value="natural">Natural</option>
                                    <option value="juridica">Jurídica</option>
                                </select>
                            </div>
                            <div class="col-span-4"><label class="block text-sm font-medium mb-1">Tipo doc.</label>
                                <input wire:model.blur="nc_tipo_documento" wire:key="nc-tipodoc" type="text" class="form-input w-full"></div>
                            <div class="col-span-8"><label class="block text-sm font-medium mb-1">Nro. de documento <span class="text-red-500">*</span></label>
                                <input wire:model.blur="nc_numero_documento" wire:key="nc-numdoc" type="text" class="form-input w-full" placeholder="CI o NIT"></div>
                            @if ($nc_tipo_persona === 'natural')
                                <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Nombres <span class="text-red-500">*</span></label>
                                    <input wire:model.blur="nc_nombres" wire:key="nc-nombres" type="text" class="form-input w-full"></div>
                                <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Apellidos <span class="text-red-500">*</span></label>
                                    <input wire:model.blur="nc_apellidos" wire:key="nc-apellidos" type="text" class="form-input w-full"></div>
                            @else
                                <div class="col-span-12"><label class="block text-sm font-medium mb-1">Razón social <span class="text-red-500">*</span></label>
                                    <input wire:model.blur="nc_razon_social" wire:key="nc-razon" type="text" class="form-input w-full"></div>
                                <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">NIT</label>
                                    <input wire:model.blur="nc_nit" wire:key="nc-nit" type="text" class="form-input w-full"></div>
                            @endif
                            <div class="col-span-6"><label class="block text-sm font-medium mb-1">Teléfono</label>
                                <input wire:model.blur="nc_telefono" wire:key="nc-tel" type="text" class="form-input w-full"></div>
                            <div class="col-span-6"><label class="block text-sm font-medium mb-1">Email</label>
                                <input wire:model.blur="nc_email" wire:key="nc-email" type="email" class="form-input w-full"></div>
                        </div>
                        @error('nc_nombres') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                        @error('nc_razon_social') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                        @error('nc_numero_documento') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" wire:click="$set('mostrarCliente', false)" class="btn border-gray-200 text-gray-600">Cancelar</button>
                            <button type="button" wire:click="guardarCliente" class="btn bg-emerald-500 hover:bg-emerald-600 text-white">
                                <span wire:loading.remove wire:target="guardarCliente">Registrar cliente</span>
                                <span wire:loading wire:target="guardarCliente">Guardando…</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
    </div>
</div>
