<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('vuelos.show', $vuelo) }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a vuelo</a>
            <h1 class="text-2xl md:text-3xl font-bold mt-2">Despachar Vuelo {{ $vuelo->numero_vuelo }}</h1>
        </div>
        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700">
                {{ session('error') }}</div>
        @endif
        <div class="grid grid-cols-12 gap-6 mb-6">
            <div class="col-span-12 sm:col-span-4 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5 text-center">
                <div class="text-sm text-gray-500 mb-1">Pax. Confirmados</div>
                <div class="text-3xl font-bold text-indigo-600">{{ $numPax }}</div>
                <div class="text-xs text-gray-400 mt-1">/ {{ $vuelo->asientos_disponibles }} disponibles</div>
            </div>
            <div class="col-span-12 sm:col-span-4 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5 text-center">
                <div class="text-sm text-gray-500 mb-1">Payload Total</div>
                <div class="text-3xl font-bold text-amber-500">{{ number_format($payload, 0) }} <span
                        class="text-sm font-normal">kg</span></div>
                <div class="text-xs text-gray-400 mt-1">Pax: {{ number_format($pesoPax, 0) }} | Eq:
                    {{ number_format($pesoEquipaje, 0) }} | Cga: {{ number_format($pesoCarga, 0) }}</div>
            </div>
            <div class="col-span-12 sm:col-span-4 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5 text-center">
                <div class="text-sm text-gray-500 mb-1">Ruta</div>
                <div class="text-2xl font-bold mt-1">{{ $vuelo->origen->codigo_iata }} &rarr;
                    {{ $vuelo->destino->codigo_iata }}</div>
            </div>
        </div>
        <form wire:submit="despachar" class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6">
            <h2 class="text-xl font-bold mb-4">Asignación de recursos</h2>
            <div class="grid grid-cols-12 gap-5">
                <div class="col-span-12"><label class="block text-sm font-medium mb-1">Aeronave <span
                            class="text-red-500">*</span></label><select wire:model="aeronave_id"
                        class="form-select w-full">
                        <option value="">— Seleccionar Aeronave —</option>
                        @foreach ($aeronaves as $a)
                            <option value="{{ $a->id }}">{{ $a->matricula }} ({{ $a->modelo }}) - MTOW:
                                {{ $a->peso_maximo_despegue_kg }} kg</option>
                        @endforeach
                    </select>
                    @error('aeronave_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Piloto (Comandante)
                        <span class="text-red-500">*</span></label><select wire:model="piloto_id"
                        class="form-select w-full">
                        <option value="">— Seleccionar —</option>
                        @foreach ($pilotos as $p)
                            <option value="{{ $p->id }}">{{ $p->empleado->nombres }}
                                {{ $p->empleado->apellidos }}</option>
                        @endforeach
                    </select>
                    @error('piloto_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Copiloto (Primer
                        Oficial)</label><select wire:model="copiloto_id" class="form-select w-full">
                        <option value="">— Ninguno —</option>
                        @foreach ($pilotos as $p)
                            <option value="{{ $p->id }}">{{ $p->empleado->nombres }}
                                {{ $p->empleado->apellidos }}</option>
                        @endforeach
                    </select>
                    @error('copiloto_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-200"> <a
                    href="{{ route('vuelos.show', $vuelo) }}"
                    class="btn border-gray-200 text-gray-800 dark:text-gray-200">Cancelar</a> <button type="submit"
                    class="btn bg-indigo-500 hover:bg-indigo-600 text-white"><span wire:loading.remove
                        wire:target="despachar">Confirmar Despacho</span><span wire:loading
                        wire:target="despachar">Procesando…</span></button> </div>
        </form>
    </div>
</div>
