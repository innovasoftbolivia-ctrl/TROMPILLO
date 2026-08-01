<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
        <div class="mb-6"><a href="{{ route('vuelos.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a vuelos</a>
            <h1 class="text-2xl md:text-3xl font-bold mt-2">
                {{ $vuelo && $vuelo->exists ? 'Editar Vuelo' : 'Programar Vuelo' }}</h1>
        </div>
        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700">
                {{ session('error') }}</div>
            @endif <form wire:submit="guardar" class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6">
                @if ($errors->any())
                    <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="grid grid-cols-12 gap-6"> <!-- Info principal -->
                    <div class="col-span-12 sm:col-span-3"><label class="block text-sm font-medium mb-1">Nro.
                            Vuelo</label><input wire:model.blur="numero_vuelo" type="text" class="form-input w-full"
                            placeholder="OPCIONAL"></div>
                    <div class="col-span-12 sm:col-span-3"><label class="block text-sm font-medium mb-1">Tipo <span
                                class="text-red-500">*</span></label><select wire:model="tipo"
                            class="form-select w-full">
                            <option value="regular">Regular</option>
                            <option value="charter">Charter</option>
                            <option value="carga">Carga</option>
                            <option value="ambulancia">Ambulancia</option>
                        </select></div>
                    <div class="col-span-12 sm:col-span-3"><label class="block text-sm font-medium mb-1">Estado <span
                                class="text-red-500">*</span></label><select wire:model="estado"
                            class="form-select w-full">
                            <option value="programado">Programado</option>
                            <option value="confirmado">Confirmado</option>
                            <option value="abordando">Abordando</option>
                            <option value="en_vuelo">En vuelo</option>
                            <option value="aterrizado">Aterrizado</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="retrasado">Retrasado</option>
                        </select></div>
                    <div class="col-span-12 sm:col-span-3"><label
                            class="block text-sm font-medium mb-1">Ruta</label><select wire:model="ruta_id"
                            class="form-select w-full">
                            <option value="">— Ninguna —</option>
                            @foreach ($rutas as $r)
                                <option value="{{ $r->id }}">{{ $r->origen->codigo_iata }} →
                                    {{ $r->destino->codigo_iata }}</option>
                            @endforeach
                        </select>
                    </div> <!-- Origen/Destino -->
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Origen <span
                                class="text-red-500">*</span></label><select wire:model="origen_id"
                            class="form-select w-full">
                            <option value="">— Seleccionar —</option>
                            @foreach ($aeropuertos as $a)
                                <option value="{{ $a->id }}">{{ $a->ciudad }} ({{ $a->codigo_iata }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Destino <span
                                class="text-red-500">*</span></label><select wire:model="destino_id"
                            class="form-select w-full">
                            <option value="">— Seleccionar —</option>
                            @foreach ($aeropuertos as $a)
                                <option value="{{ $a->id }}">{{ $a->ciudad }} ({{ $a->codigo_iata }})
                                </option>
                            @endforeach
                        </select>
                    </div> <!-- Fechas Programadas -->
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Salida
                            programada <span class="text-red-500">*</span></label><input wire:model="salida_programada"
                            type="datetime-local" class="form-input w-full"></div>
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Llegada
                            programada</label><input wire:model="llegada_programada" type="datetime-local"
                            class="form-input w-full"></div> <!-- Fechas Reales -->
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Salida
                            real</label><input wire:model="salida_real" type="datetime-local" class="form-input w-full">
                    </div>
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Llegada
                            real</label><input wire:model="llegada_real" type="datetime-local"
                            class="form-input w-full"></div> <!-- Comercial -->
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Asientos
                            disponibles <span class="text-red-500">*</span></label><input
                            wire:model.blur="asientos_disponibles" type="number" class="form-input w-full"></div>
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Precio boleto
                            (Bs) <span class="text-red-500">*</span></label><input wire:model.blur="precio"
                            type="number" step="0.01" class="form-input w-full"></div>
                    <!-- Tripulación & Aeronave preasignada -->
                    <div class="col-span-12 sm:col-span-4"><label class="block text-sm font-medium mb-1">Aeronave
                            (opcional)</label><select wire:model="aeronave_id" class="form-select w-full">
                            <option value="">— Sin preasignar —</option>
                            @foreach ($aeronaves as $a)
                                <option value="{{ $a->id }}">{{ $a->matricula }} ({{ $a->modelo }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-4"><label class="block text-sm font-medium mb-1">Piloto
                            (opcional)</label><select wire:model="piloto_id" class="form-select w-full">
                            <option value="">— Sin preasignar —</option>
                            @foreach ($pilotos as $p)
                                <option value="{{ $p->id }}">{{ $p->empleado->nombres }}
                                    {{ $p->empleado->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-4"><label class="block text-sm font-medium mb-1">Copiloto
                            (opcional)</label><select wire:model="copiloto_id" class="form-select w-full">
                            <option value="">— Sin preasignar —</option>
                            @foreach ($pilotos as $p)
                                <option value="{{ $p->id }}">{{ $p->empleado->nombres }}
                                    {{ $p->empleado->apellidos }}</option>
                            @endforeach
                        </select></div>
                    <div class="col-span-12"><label class="block text-sm font-medium mb-1">Observaciones</label>
                        <textarea wire:model.blur="observaciones" class="form-textarea w-full" rows="3"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200"> <a
                        href="{{ route('vuelos.index') }}"
                        class="btn border-gray-200 text-gray-800 dark:text-gray-200">Cancelar</a> <button
                        type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span
                            wire:loading.remove
                            wire:target="guardar">{{ $vuelo && $vuelo->exists ? 'Guardar' : 'Crear' }}</span><span
                            wire:loading wire:target="guardar">Guardando…</span></button> </div>
            </form>
    </div>
</div>
