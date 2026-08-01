<div>
    @php $esEdicion = $persona && $persona->exists; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"> <a href="{{ route('personas.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a personas</a>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold mt-2">
                {{ $esEdicion ? 'Editar persona' : 'Nueva persona' }}</h1>
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
                        <div class="col-span-12 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Tipo
                                persona <span class="text-red-500">*</span></label> <select
                                wire:model.live="tipo_persona" wire:key="pf-tipo-persona" class="form-select w-full">
                                <option value="natural">Natural</option>
                                <option value="juridica">Jurídica</option>
                            </select> </div>
                        <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Tipo doc.
                                <span class="text-red-500">*</span></label> <select wire:model="tipo_documento" wire:key="pf-tipo-doc"
                                class="form-select w-full">
                                <option value="CI">CI</option>
                                <option value="NIT">NIT</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="CE">CE</option>
                            </select> </div>
                        <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Nro.
                                documento <span class="text-red-500">*</span></label> <input
                                wire:model.blur="numero_documento" wire:key="pf-num-doc" type="text" class="form-input w-full">
                            @error('numero_documento')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @if ($tipo_persona === 'natural')
                            <div class="col-span-12 sm:col-span-6"> <label
                                    class="block text-sm font-medium mb-1">Nombres <span
                                        class="text-red-500">*</span></label> <input wire:model.blur="nombres" wire:key="pf-nombres"
                                    type="text" class="form-input w-full"> @error('nombres')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-12 sm:col-span-6"> <label
                                    class="block text-sm font-medium mb-1">Apellidos <span
                                        class="text-red-500">*</span></label> <input wire:model.blur="apellidos" wire:key="pf-apellidos"
                                    type="text" class="form-input w-full"> @error('apellidos')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Fecha
                                    nacimiento</label> <input wire:model="fecha_nacimiento" wire:key="pf-fecha-nac" type="date"
                                    class="form-input w-full"> </div>
                            <div class="col-span-6 sm:col-span-4"> <label
                                    class="block text-sm font-medium mb-1">Sexo</label> <select wire:model="sexo" wire:key="pf-sexo"
                                    class="form-select w-full">
                                    <option value="">—</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select> </div>
                            <div class="col-span-12 mt-1">
                                <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500">Datos de pasajero <span class="font-normal normal-case">(opcional, para cuando viaje)</span></p>
                            </div>
                            <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Nacionalidad</label>
                                <input wire:model.blur="nacionalidad" wire:key="pf-nacionalidad" type="text" class="form-input w-full"> </div>
                            <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Peso (kg)</label>
                                <input wire:model.blur="peso_kg" wire:key="pf-peso" type="number" step="0.1" class="form-input w-full"> </div>
                            <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Contacto emergencia</label>
                                <input wire:model.blur="contacto_emergencia" wire:key="pf-contacto-emg" type="text" class="form-input w-full"> </div>
                            <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Tel. emergencia</label>
                                <input wire:model.blur="telefono_emergencia" wire:key="pf-tel-emg" type="text" class="form-input w-full"> </div>
                        @else
                            <div class="col-span-12 sm:col-span-6"> <label class="block text-sm font-medium mb-1">Razón
                                    social <span class="text-red-500">*</span></label> <input
                                    wire:model.blur="razon_social" wire:key="pf-razon-social" type="text" class="form-input w-full">
                                @error('razon_social')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-6 sm:col-span-3"> <label
                                    class="block text-sm font-medium mb-1">NIT</label> <input wire:model.blur="nit" wire:key="pf-nit"
                                    type="text" class="form-input w-full"> </div>
                            <div class="col-span-12 sm:col-span-6"> <label
                                    class="block text-sm font-medium mb-1">Representante legal</label> <input
                                    wire:model.blur="representante_legal" wire:key="pf-rep-legal" type="text" class="form-input w-full">
                            </div>
                            @endif <div class="col-span-12 sm:col-span-4"> <label
                                    class="block text-sm font-medium mb-1">País</label> <select wire:model="pais_id" wire:key="pf-pais"
                                    class="form-select w-full">
                                    <option value="">— Sin especificar —</option>
                                    @foreach ($paises as $pais)
                                        <option value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                                    @endforeach
                                </select> </div>
                            <div class="col-span-6 sm:col-span-4"> <label
                                    class="block text-sm font-medium mb-1">Teléfono</label> <input
                                    wire:model.blur="telefono" wire:key="pf-telefono" type="text" class="form-input w-full"> </div>
                            <div class="col-span-6 sm:col-span-4"> <label
                                    class="block text-sm font-medium mb-1">Email</label> <input wire:model.blur="email" wire:key="pf-email"
                                    type="email" class="form-input w-full"> @error('email')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-12"> <label class="block text-sm font-medium mb-1">Dirección</label>
                                <input wire:model.blur="direccion" wire:key="pf-direccion" type="text" class="form-input w-full"> </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200 /60"> <a
                            href="{{ route('personas.index') }}"
                            class="btn border-gray-200 /60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-200 dark:text-gray-300">Cancelar</a>
                        <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"> <span
                                wire:loading.remove
                                wire:target="guardar">{{ $esEdicion ? 'Guardar cambios' : 'Crear persona' }}</span>
                            <span wire:loading wire:target="guardar">Guardando…</span> </button> </div>
                </form>
            </div>
    </div>
</div>
