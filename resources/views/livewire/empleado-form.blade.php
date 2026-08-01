<div>
    @php
        $esEdicion = $empleado && $empleado->exists;
        $cargos = ['piloto' => 'Piloto', 'copiloto' => 'Copiloto', 'tecnico' => 'Técnico', 'despachador' => 'Despachador', 'administrativo' => 'Administrativo', 'ventas' => 'Ventas', 'gerente' => 'Gerente'];
    @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('empleados.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a empleados</a>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold mt-2">
                {{ $esEdicion ? 'Editar empleado · ' . $empleado->nombres . ' ' . $empleado->apellidos : 'Nuevo empleado' }}
            </h1>
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
                            <div class="font-medium mb-1">Revisa los errores:</div>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="grid grid-cols-12 gap-5">
                        <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Nombres
                                <span class="text-red-500">*</span></label><input wire:model.blur="nombres"
                                type="text" class="form-input w-full">
                            @error('nombres')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Apellidos
                                <span class="text-red-500">*</span></label><input wire:model.blur="apellidos"
                                type="text" class="form-input w-full">
                            @error('apellidos')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Tipo
                                doc.</label><select wire:model="tipo_documento" class="form-select w-full">
                                <option value="CI">CI</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select></div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Nro.
                                documento <span class="text-red-500">*</span></label><input
                                wire:model.blur="numero_documento" type="text" class="form-input w-full">
                            @error('numero_documento')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3"><label class="block text-sm font-medium mb-1">Cargo <span
                                    class="text-red-500">*</span></label><select wire:model="cargo"
                                class="form-select w-full">
                                @foreach ($cargos as $v => $l)
                                    <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-3"><label
                                class="block text-sm font-medium mb-1">Activo</label><select wire:model="activo"
                                class="form-select w-full">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select></div>
                        <div class="col-span-6 sm:col-span-4"><label
                                class="block text-sm font-medium mb-1">Teléfono</label><input wire:model.blur="telefono"
                                type="text" class="form-input w-full"></div>
                        <div class="col-span-6 sm:col-span-4"><label
                                class="block text-sm font-medium mb-1">Email</label><input wire:model.blur="email"
                                type="email" class="form-input w-full">
                            @error('email')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-4"><label class="block text-sm font-medium mb-1">Fecha
                                nacimiento</label><input wire:model="fecha_nacimiento" type="date"
                                class="form-input w-full"></div>
                        <div class="col-span-6 sm:col-span-4"><label class="block text-sm font-medium mb-1">Fecha
                                contratación</label><input wire:model="fecha_contratacion" type="date"
                                class="form-input w-full"></div>
                        <div class="col-span-6 sm:col-span-4"><label class="block text-sm font-medium mb-1">Salario
                                (Bs)</label><input wire:model.blur="salario" type="number" step="0.01"
                                class="form-input w-full">
                            @error('salario')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200 /60"> <a
                            href="{{ route('empleados.index') }}"
                            class="btn border-gray-200 /60 hover:border-gray-300 text-gray-800 dark:text-gray-200 dark:text-gray-300">Cancelar</a>
                        <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span
                                wire:loading.remove
                                wire:target="guardar">{{ $esEdicion ? 'Guardar cambios' : 'Crear empleado' }}</span><span
                                wire:loading wire:target="guardar">Guardando…</span></button> </div>
                </form>
            </div>
    </div>
</div>
