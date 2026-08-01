<div>
    @php
        $esEdicion = $piloto && $piloto->exists;
        $tipos = ['PPL' => 'PPL - Privado', 'CPL' => 'CPL - Comercial', 'ATPL' => 'ATPL - Transporte', 'PCA' => 'PCA - Aviación Comercial'];
    @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('pilotos.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a pilotos</a>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold mt-2">
                {{ $esEdicion ? 'Editar piloto · ' . $piloto->licencia_numero : 'Nuevo piloto' }}</h1>
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
                        <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Empleado
                                <span class="text-red-500">*</span></label><select wire:model="empleado_id"
                                class="form-select w-full">
                                <option value="">— Seleccionar —</option>
                                @foreach ($empleados as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombres }} {{ $e->apellidos }}</option>
                                @endforeach
                            </select>
                            @error('empleado_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Nro.
                                licencia <span class="text-red-500">*</span></label><input
                                wire:model.blur="licencia_numero" type="text" class="form-input w-full">
                            @error('licencia_numero')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Tipo <span
                                    class="text-red-500">*</span></label><select wire:model="tipo_licencia"
                                class="form-select w-full">
                                @foreach ($tipos as $v => $l)
                                    <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-4"><label class="block text-sm font-medium mb-1">Horas de
                                vuelo <span class="text-red-500">*</span></label><input wire:model.blur="horas_vuelo"
                                type="number" class="form-input w-full">
                            @error('horas_vuelo')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-4"><label class="block text-sm font-medium mb-1">Vencimiento
                                licencia</label><input wire:model="vencimiento_licencia" type="date"
                                class="form-input w-full"></div>
                        <div class="col-span-6 sm:col-span-4"><label class="block text-sm font-medium mb-1">Vencimiento
                                médico</label><input wire:model="vencimiento_medico" type="date"
                                class="form-input w-full"></div>
                        <div class="col-span-12"><label
                                class="block text-sm font-medium mb-1">Habilitaciones</label><input
                                wire:model.blur="habilitaciones" type="text" class="form-input w-full"
                                placeholder="Ej: IFR, VFR nocturno, vuelo montaña"></div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200 /60"> <a
                            href="{{ route('pilotos.index') }}"
                            class="btn border-gray-200 /60 hover:border-gray-300 text-gray-800 dark:text-gray-200 dark:text-gray-300">Cancelar</a>
                        <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span
                                wire:loading.remove
                                wire:target="guardar">{{ $esEdicion ? 'Guardar cambios' : 'Crear piloto' }}</span><span
                                wire:loading wire:target="guardar">Guardando…</span></button> </div>
                </form>
            </div>
    </div>
</div>
