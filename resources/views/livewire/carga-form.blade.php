<div>
    @php $esEdicion = $envio && $envio->exists; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('carga.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a envíos</a>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold mt-2">
                {{ $esEdicion ? 'Editar envío · ' . $envio->guia : 'Nuevo envío de carga' }}</h1>
        </div>
        @if (session('error'))
            <div
                class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-400">
                {{ session('error') }}</div>
            @endif <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5 sm:p-8">
                <form wire:submit="guardar">
                    @if ($errors->any())
                        <div
                            class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-400">
                            <div class="font-medium mb-1">Errores:</div>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="grid grid-cols-12 gap-5">
                        <div class="col-span-6 sm:col-span-4"><label class="block text-sm font-medium mb-1">Guía <span
                                    class="text-red-500">*</span></label><input wire:model.blur="guia" type="text"
                                class="form-input w-full">
                            @error('guia')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-12 sm:col-span-4"><label
                                class="block text-sm font-medium mb-1">Vuelo</label><select wire:model="vuelo_id"
                                class="form-select w-full">
                                <option value="">— Sin asignar —</option>
                                @foreach ($vuelos as $v)
                                    <option value="{{ $v->id }}">{{ $v->origen->ciudad ?? '?' }} →
                                        {{ $v->destino->ciudad ?? '?' }} ({{ $v->salida_programada ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-4"><label class="block text-sm font-medium mb-1">Estado <span
                                    class="text-red-500">*</span></label><select wire:model="estado"
                                class="form-select w-full">
                                <option value="registrado">Recibido</option>
                                <option value="en_transito">En tránsito</option>
                                <option value="entregado">Entregado</option>
                                <option value="devuelto">Devuelto</option>
                            </select></div>
                        <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Remitente
                                <span class="text-red-500">*</span></label><input wire:model.blur="remitente"
                                type="text" class="form-input w-full">
                            @error('remitente')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-medium">Doc. remitente (CI/NIT)</label>
                                <button type="button" wire:click="abrirRemitente" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">+ Nuevo remitente</button>
                            </div>
                            <input wire:model.live.debounce.400ms="remitente_documento" type="text" class="form-input w-full" placeholder="Escribí el documento…" autocomplete="off">
                            <p class="text-xs text-gray-400 mt-1">Si ya existe, se completa el nombre del remitente solo.</p>
                        </div>
                        <div class="col-span-12 sm:col-span-6"><label
                                class="block text-sm font-medium mb-1">Destinatario <span
                                    class="text-red-500">*</span></label><input wire:model.blur="destinatario"
                                type="text" class="form-input w-full">
                            @error('destinatario')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Doc.
                                destinatario</label><input wire:model.blur="destinatario_documento" type="text"
                                class="form-input w-full"></div>
                        <div class="col-span-12"><label class="block text-sm font-medium mb-1">Descripción</label>
                            <textarea wire:model.blur="descripcion" class="form-textarea w-full" rows="2"></textarea>
                        </div>
                        <div class="col-span-4"><label class="block text-sm font-medium mb-1">Peso (kg) <span
                                    class="text-red-500">*</span></label><input wire:model.blur="peso_kg" type="number"
                                step="0.1" class="form-input w-full">
                            @error('peso_kg')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-4"><label class="block text-sm font-medium mb-1">Valor declarado <span
                                    class="text-red-500">*</span></label><input wire:model.blur="valor_declarado"
                                type="number" step="0.01" class="form-input w-full">
                            @error('valor_declarado')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-4"><label class="block text-sm font-medium mb-1">Costo envío <span
                                    class="text-red-500">*</span></label><input wire:model.blur="costo_envio"
                                type="number" step="0.01" class="form-input w-full">
                            @error('costo_envio')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200 /60"> <a
                            href="{{ route('carga.index') }}"
                            class="btn border-gray-200 /60 text-gray-800 dark:text-gray-200 dark:text-gray-300">Cancelar</a>
                        <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span
                                wire:loading.remove
                                wire:target="guardar">{{ $esEdicion ? 'Guardar' : 'Crear' }}</span><span wire:loading
                                wire:target="guardar">Guardando…</span></button> </div>
                </form>

                {{-- Modal: alta rápida de remitente --}}
                @if ($mostrarRemitente)
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:key="modal-remitente">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100">Nuevo remitente</h3>
                                <button type="button" wire:click="$set('mostrarRemitente', false)" class="text-gray-400 hover:text-gray-600">✕</button>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Registrá al remitente; se cargará en el envío (y su venta/factura saldrá a su nombre).</p>
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 sm:col-span-6">
                                    <label class="block text-sm font-medium mb-1">Tipo</label>
                                    <select wire:model.live="nr_tipo_persona" wire:key="nr-tipo" class="form-select w-full">
                                        <option value="juridica">Jurídica (empresa)</option>
                                        <option value="natural">Natural</option>
                                    </select>
                                </div>
                                <div class="col-span-4"><label class="block text-sm font-medium mb-1">Tipo doc.</label>
                                    <input wire:model.blur="nr_tipo_documento" wire:key="nr-tipodoc" type="text" class="form-input w-full"></div>
                                <div class="col-span-8"><label class="block text-sm font-medium mb-1">Nro. de documento <span class="text-red-500">*</span></label>
                                    <input wire:model.blur="nr_numero_documento" wire:key="nr-numdoc" type="text" class="form-input w-full" placeholder="CI o NIT"></div>
                                @if ($nr_tipo_persona === 'natural')
                                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Nombres <span class="text-red-500">*</span></label>
                                        <input wire:model.blur="nr_nombres" wire:key="nr-nombres" type="text" class="form-input w-full"></div>
                                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Apellidos <span class="text-red-500">*</span></label>
                                        <input wire:model.blur="nr_apellidos" wire:key="nr-apellidos" type="text" class="form-input w-full"></div>
                                @else
                                    <div class="col-span-12"><label class="block text-sm font-medium mb-1">Razón social <span class="text-red-500">*</span></label>
                                        <input wire:model.blur="nr_razon_social" wire:key="nr-razon" type="text" class="form-input w-full"></div>
                                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">NIT</label>
                                        <input wire:model.blur="nr_nit" wire:key="nr-nit" type="text" class="form-input w-full"></div>
                                @endif
                                <div class="col-span-6"><label class="block text-sm font-medium mb-1">Teléfono</label>
                                    <input wire:model.blur="nr_telefono" wire:key="nr-tel" type="text" class="form-input w-full"></div>
                                <div class="col-span-6"><label class="block text-sm font-medium mb-1">Email</label>
                                    <input wire:model.blur="nr_email" wire:key="nr-email" type="email" class="form-input w-full"></div>
                            </div>
                            @error('nr_razon_social') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                            @error('nr_nombres') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                            @error('nr_numero_documento') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" wire:click="$set('mostrarRemitente', false)" class="btn border-gray-200 text-gray-600">Cancelar</button>
                                <button type="button" wire:click="guardarRemitente" class="btn bg-emerald-500 hover:bg-emerald-600 text-white">
                                    <span wire:loading.remove wire:target="guardarRemitente">Registrar remitente</span>
                                    <span wire:loading wire:target="guardarRemitente">Guardando…</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
    </div>
</div>
