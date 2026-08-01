<div> @php
    $tipos = ['aeropuerto' => 'Aeropuerto', 'aerodromo' => 'Aeródromo', 'pista' => 'Pista'];
    $superficies = ['asfalto' => 'Asfalto', 'concreto' => 'Concreto', 'tierra' => 'Tierra', 'pasto' => 'Pasto'];
    $esEdicion = $aeropuerto && $aeropuerto->exists;
@endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto"> <!-- Breadcrumb / título -->
        <div class="mb-6"> <a href="{{ route('aeropuertos.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a aeropuertos</a>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold mt-2">
                {{ $esEdicion ? 'Editar aeropuerto · ' . $aeropuerto->codigo_oaci : 'Nuevo aeropuerto' }} </h1>
        </div> <!-- Flash error -->
        @if (session('error'))
            <div
                class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-400">
                {{ session('error') }} </div>
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
                    <div class="grid grid-cols-12 gap-5"> {{-- Código OACI --}} <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium mb-1" for="codigo_oaci">Código OACI <span
                                    class="text-red-500">*</span></label> <input id="codigo_oaci"
                                wire:model.blur="codigo_oaci" type="text" maxlength="4"
                                class="form-input w-full uppercase" placeholder="Ej: SKBO"> @error('codigo_oaci')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div> {{-- Código IATA --}} <div class="col-span-6 sm:col-span-3"> <label
                                class="block text-sm font-medium mb-1" for="codigo_iata">Código IATA</label> <input
                                id="codigo_iata" wire:model.blur="codigo_iata" type="text" maxlength="3"
                                class="form-input w-full uppercase" placeholder="Ej: BOG"> @error('codigo_iata')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div> {{-- Tipo --}} <div class="col-span-12 sm:col-span-6"> <label
                                class="block text-sm font-medium mb-1" for="tipo">Tipo <span
                                    class="text-red-500">*</span></label> <select id="tipo" wire:model="tipo"
                                class="form-select w-full">
                                @foreach ($tipos as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select> </div> {{-- Nombre --}} <div class="col-span-12"> <label
                                class="block text-sm font-medium mb-1" for="nombre">Nombre <span
                                    class="text-red-500">*</span></label> <input id="nombre" wire:model.blur="nombre"
                                type="text" class="form-input w-full"
                                placeholder="Ej: Aeropuerto Internacional El Dorado"> @error('nombre')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div> {{-- Ciudad --}} <div class="col-span-12 sm:col-span-4"> <label
                                class="block text-sm font-medium mb-1" for="ciudad">Ciudad <span
                                    class="text-red-500">*</span></label> <input id="ciudad" wire:model.blur="ciudad"
                                type="text" class="form-input w-full" placeholder="Ej: Santa Cruz"> @error('ciudad')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div> {{-- Departamento --}} <div class="col-span-12 sm:col-span-4"> <label
                                class="block text-sm font-medium mb-1" for="departamento">Departamento</label> <input
                                id="departamento" wire:model.blur="departamento" type="text"
                                class="form-input w-full" placeholder="Ej: Santa Cruz"> </div> {{-- Latitud --}}
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1"
                                for="latitud">Latitud</label> <input id="latitud" wire:model.blur="latitud"
                                type="number" step="any" class="form-input w-full" placeholder="-17.6">
                            @error('latitud')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div> {{-- Longitud --}} <div class="col-span-6 sm:col-span-3"> <label
                                class="block text-sm font-medium mb-1" for="longitud_coord">Longitud</label> <input
                                id="longitud_coord" wire:model.blur="longitud_coord" type="number" step="any"
                                class="form-input w-full" placeholder="-63.1"> @error('longitud_coord')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div> {{-- Elevación --}} <div class="col-span-6 sm:col-span-3"> <label
                                class="block text-sm font-medium mb-1" for="elevacion_pies">Elevación (pies)</label>
                            <input id="elevacion_pies" wire:model.blur="elevacion_pies" type="number"
                                class="form-input w-full" placeholder="1365"> @error('elevacion_pies')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div> {{-- Longitud de pista --}} <div class="col-span-6 sm:col-span-3"> <label
                                class="block text-sm font-medium mb-1" for="longitud_pista_m">Longitud pista
                                (m)</label> <input id="longitud_pista_m" wire:model.blur="longitud_pista_m"
                                type="number" class="form-input w-full" placeholder="3500">
                            @error('longitud_pista_m')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div> {{-- Superficie de pista --}} <div class="col-span-12 sm:col-span-6"> <label
                                class="block text-sm font-medium mb-1" for="superficie_pista">Superficie de
                                pista</label> <select id="superficie_pista" wire:model="superficie_pista"
                                class="form-select w-full">
                                <option value="">— Sin especificar —</option>
                                @foreach ($superficies as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select> </div> {{-- Activo --}} <div class="col-span-12 sm:col-span-6"> <label
                                class="block text-sm font-medium mb-1" for="activo">Activo</label> <select
                                id="activo" wire:model="activo" class="form-select w-full">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select> </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200 /60"> <a
                            href="{{ route('aeropuertos.index') }}"
                            class="btn border-gray-200 /60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-200 dark:text-gray-300">Cancelar</a>
                        <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"> <span
                                wire:loading.remove
                                wire:target="guardar">{{ $esEdicion ? 'Guardar cambios' : 'Crear aeropuerto' }}</span>
                            <span wire:loading wire:target="guardar">Guardando…</span> </button> </div>
                </form>
            </div>
    </div>
</div>
