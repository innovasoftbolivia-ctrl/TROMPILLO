<div>
    @php $esEdicion = $ruta && $ruta->exists; @endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"> <a href="{{ route('rutas.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a rutas</a>
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-200 font-bold mt-2">
                {{ $esEdicion ? 'Editar ruta' : 'Nueva ruta' }}</h1>
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
                        </div> @endif <div class="grid grid-cols-12 gap-5">
                            <div class="col-span-12 sm:col-span-6"> <label class="block text-sm font-medium mb-1">Origen
                                    <span class="text-red-500">*</span></label> <select wire:model="origen_id"
                                    class="form-select w-full">
                                    <option value="">— Seleccionar —</option>
                                    @foreach ($aeropuertos as $a)
                                        <option value="{{ $a->id }}">{{ $a->ciudad }} ({{ $a->codigo_oaci }})
                                        </option>
                                    @endforeach
                                </select> @error('origen_id')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-12 sm:col-span-6"> <label
                                    class="block text-sm font-medium mb-1">Destino <span
                                        class="text-red-500">*</span></label> <select wire:model="destino_id"
                                    class="form-select w-full">
                                    <option value="">— Seleccionar —</option>
                                    @foreach ($aeropuertos as $a)
                                        <option value="{{ $a->id }}">{{ $a->ciudad }} ({{ $a->codigo_oaci }})
                                        </option>
                                    @endforeach
                                </select> @error('destino_id')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-6 sm:col-span-4"> <label
                                    class="block text-sm font-medium mb-1">Distancia (km)</label> <input
                                    wire:model.blur="distancia_km" type="number" class="form-input w-full"
                                    placeholder="450"> @error('distancia_km')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-6 sm:col-span-4"> <label
                                    class="block text-sm font-medium mb-1">Duración (min)</label> <input
                                    wire:model.blur="duracion_estimada_min" type="number" class="form-input w-full"
                                    placeholder="90"> @error('duracion_estimada_min')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-6 sm:col-span-4"> <label class="block text-sm font-medium mb-1">Precio
                                    base (Bs) <span class="text-red-500">*</span></label> <input
                                    wire:model.blur="precio_base" type="number" step="0.01"
                                    class="form-input w-full" placeholder="350"> @error('precio_base')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-6 sm:col-span-4"> <label
                                    class="block text-sm font-medium mb-1">Activa</label> <select wire:model="activa"
                                    class="form-select w-full">
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select> </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200 /60"> <a
                                href="{{ route('rutas.index') }}"
                                class="btn border-gray-200 /60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-200 dark:text-gray-300">Cancelar</a>
                            <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"> <span
                                    wire:loading.remove
                                    wire:target="guardar">{{ $esEdicion ? 'Guardar cambios' : 'Crear ruta' }}</span>
                                <span wire:loading wire:target="guardar">Guardando…</span> </button> </div>
                </form>
            </div>
    </div>
</div>
