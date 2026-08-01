<div>
    @php $esEdicion = $aeronave && $aeronave->exists; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"> <a href="{{ route('aeronaves.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a aeronaves</a>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold mt-2">
                {{ $esEdicion ? 'Editar aeronave · ' . $aeronave->matricula : 'Nueva aeronave' }}</h1>
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
                        <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Matrícula
                                <span class="text-red-500">*</span></label> <input wire:model.blur="matricula"
                                type="text" class="form-input w-full uppercase" placeholder="CP-XXXX">
                            @error('matricula')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Modelo
                                <span class="text-red-500">*</span></label> <input wire:model.blur="modelo"
                                type="text" class="form-input w-full" placeholder="Cessna 208"> @error('modelo')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Fabricante
                                <span class="text-red-500">*</span></label> <input wire:model.blur="fabricante"
                                type="text" class="form-input w-full" placeholder="Cessna"> @error('fabricante')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Año
                                fabricación</label> <input wire:model.blur="ano_fabricacion" type="number"
                                class="form-input w-full" placeholder="2020"> </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Capacidad
                                pax</label> <input wire:model.blur="capacidad_pasajeros" type="number"
                                class="form-input w-full" placeholder="14"> </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Carga (kg)
                                <span class="text-red-500">*</span></label> <input wire:model.blur="capacidad_carga_kg"
                                type="number" class="form-input w-full" placeholder="1400"> </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Peso vacío
                                (kg)</label> <input wire:model.blur="peso_vacio_kg" type="number"
                                class="form-input w-full"> </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">MTOW
                                (kg)</label> <input wire:model.blur="peso_maximo_despegue_kg" type="number"
                                class="form-input w-full"> </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Autonomía
                                (km)</label> <input wire:model.blur="autonomia_km" type="number"
                                class="form-input w-full"> </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Vel.
                                crucero (km/h)</label> <input wire:model.blur="velocidad_crucero_kmh" type="number"
                                class="form-input w-full"> </div>
                        <div class="col-span-6 sm:col-span-3"> <label class="block text-sm font-medium mb-1">Horas vuelo
                                <span class="text-red-500">*</span></label> <input wire:model.blur="horas_vuelo_totales"
                                type="number" class="form-input w-full" placeholder="0"> </div>
                        <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Última
                                revisión</label> <input wire:model="fecha_ultima_revision" type="date"
                                class="form-input w-full"> </div>
                        <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Próxima
                                revisión</label> <input wire:model="fecha_proxima_revision" type="date"
                                class="form-input w-full"> </div>
                        <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Estado
                                <span class="text-red-500">*</span></label> <select wire:model="estado"
                                class="form-select w-full">
                                <option value="activa">Activa</option>
                                <option value="mantenimiento">En mantenimiento</option>
                                <option value="inactiva">Inactiva</option>
                                <option value="retirada">Retirada</option>
                            </select> </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200 /60"> <a
                            href="{{ route('aeronaves.index') }}"
                            class="btn border-gray-200 /60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-200 dark:text-gray-300">Cancelar</a>
                        <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"> <span
                                wire:loading.remove
                                wire:target="guardar">{{ $esEdicion ? 'Guardar cambios' : 'Crear aeronave' }}</span>
                            <span wire:loading wire:target="guardar">Guardando…</span> </button> </div>
                </form>
            </div>
    </div>
</div>
