<div>
    @php $esEdicion = $pasajero && $pasajero->exists; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"> <a href="{{ route('pasajeros.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a pasajeros</a>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold mt-2">
                {{ $esEdicion ? 'Editar pasajero · ' . $pasajero->nombre_completo : 'Nuevo pasajero' }}</h1>
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
                            <div class="font-medium mb-1">Revisa los siguientes errores:</div>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="grid grid-cols-12 gap-5">
                        <div class="col-span-12 sm:col-span-6"> <label class="block text-sm font-medium mb-1">Nombres
                                <span class="text-red-500">*</span></label> <input wire:model.blur="nombres"
                                type="text" class="form-input w-full" placeholder="Juan Carlos"> @error('nombres')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-12 sm:col-span-6"> <label class="block text-sm font-medium mb-1">Apellidos
                                <span class="text-red-500">*</span></label> <input wire:model.blur="apellidos"
                                type="text" class="form-input w-full" placeholder="Pérez López"> @error('apellidos')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Tipo doc.
                                <span class="text-red-500">*</span></label> <select wire:model="tipo_documento"
                                class="form-select w-full">
                                <option value="CI">CI</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="CE">CE</option>
                            </select> </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Nro.
                                documento <span class="text-red-500">*</span></label> <input
                                wire:model.blur="numero_documento" type="text" class="form-input w-full"
                                placeholder="12345678"> @error('numero_documento')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Fecha
                                nacimiento</label> <input wire:model="fecha_nacimiento" type="date"
                                class="form-input w-full"> </div>
                        <div class="col-span-6 sm:col-span-3"> <label
                                class="block text-sm font-medium mb-1">Nacionalidad <span
                                    class="text-red-500">*</span></label> <input wire:model.blur="nacionalidad"
                                type="text" class="form-input w-full" placeholder="Boliviana"> @error('nacionalidad')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-4"> <label
                                class="block text-sm font-medium mb-1">Teléfono</label> <input
                                wire:model.blur="telefono" type="text" class="form-input w-full"
                                placeholder="+591 7XXXXXXX"> </div>
                        <div class="col-span-6 sm:col-span-4"> <label
                                class="block text-sm font-medium mb-1">Email</label> <input wire:model.blur="email"
                                type="email" class="form-input w-full" placeholder="email@ejemplo.com">
                            @error('email')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Peso
                                (kg)</label> <input wire:model.blur="peso_kg" type="number" step="0.1"
                                class="form-input w-full" placeholder="75"> @error('peso_kg')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-12 sm:col-span-6"> <label class="block text-sm font-medium mb-1">Contacto
                                emergencia</label> <input wire:model.blur="contacto_emergencia" type="text"
                                class="form-input w-full" placeholder="Nombre del contacto"> </div>
                        <div class="col-span-12 sm:col-span-6"> <label class="block text-sm font-medium mb-1">Teléfono
                                emergencia</label> <input wire:model.blur="telefono_emergencia" type="text"
                                class="form-input w-full" placeholder="+591 7XXXXXXX"> </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200 /60"> <a
                            href="{{ route('pasajeros.index') }}"
                            class="btn border-gray-200 /60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-200 dark:text-gray-300">Cancelar</a>
                        <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"> <span
                                wire:loading.remove
                                wire:target="guardar">{{ $esEdicion ? 'Guardar cambios' : 'Crear pasajero' }}</span>
                            <span wire:loading wire:target="guardar">Guardando…</span> </button> </div>
                </form>
            </div>
    </div>
</div>
