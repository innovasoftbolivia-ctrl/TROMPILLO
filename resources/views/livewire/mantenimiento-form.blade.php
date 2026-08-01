<div>
    @php $esEdicion = $mantenimiento && $mantenimiento->exists; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('mantenimientos.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a mantenimientos</a>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold mt-2">
                {{ $esEdicion ? 'Editar mantenimiento' : 'Nuevo mantenimiento' }}</h1>
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
                        <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Aeronave
                                <span class="text-red-500">*</span></label><select wire:model="aeronave_id"
                                class="form-select w-full">
                                <option value="">— Seleccionar —</option>
                                @foreach ($aeronaves as $a)
                                    <option value="{{ $a->id }}">{{ $a->matricula }} - {{ $a->modelo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('aeronave_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-12 sm:col-span-6"><label
                                class="block text-sm font-medium mb-1">Técnico</label><select wire:model="tecnico_id"
                                class="form-select w-full">
                                <option value="">— Sin asignar —</option>
                                @foreach ($empleados as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombres }} {{ $e->apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Tipo <span
                                    class="text-red-500">*</span></label><select wire:model="tipo"
                                class="form-select w-full">
                                <option value="preventivo">Preventivo</option>
                                <option value="correctivo">Correctivo</option>
                                <option value="inspeccion">Inspección</option>
                                <option value="revision_100h">Revisión 100h</option>
                                <option value="revision_anual">Revisión anual</option>
                            </select></div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Estado <span
                                    class="text-red-500">*</span></label><select wire:model="estado"
                                class="form-select w-full">
                                <option value="programado">Programado</option>
                                <option value="en_proceso">En proceso</option>
                                <option value="completado">Completado</option>
                                <option value="cancelado">Cancelado</option>
                            </select></div>
                        <div class="col-span-12"><label class="block text-sm font-medium mb-1">Descripción <span
                                    class="text-red-500">*</span></label>
                            <textarea wire:model.blur="descripcion" class="form-textarea w-full" rows="3"></textarea>
                            @error('descripcion')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Fecha inicio
                                <span class="text-red-500">*</span></label><input wire:model="fecha_inicio"
                                type="date" class="form-input w-full">
                            @error('fecha_inicio')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Fecha
                                fin</label><input wire:model="fecha_fin" type="date" class="form-input w-full"></div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Horas vuelo
                                aeronave</label><input wire:model.blur="horas_vuelo_aeronave" type="number"
                                class="form-input w-full"></div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Costo (Bs)
                                <span class="text-red-500">*</span></label><input wire:model.blur="costo" type="number"
                                step="0.01" class="form-input w-full">
                            @error('costo')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200 /60"> <a
                            href="{{ route('mantenimientos.index') }}"
                            class="btn border-gray-200 /60 text-gray-800 dark:text-gray-200 dark:text-gray-300">Cancelar</a>
                        <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span
                                wire:loading.remove
                                wire:target="guardar">{{ $esEdicion ? 'Guardar' : 'Crear' }}</span><span wire:loading
                                wire:target="guardar">Guardando…</span></button> </div>
                </form>
            </div>
    </div>
</div>
