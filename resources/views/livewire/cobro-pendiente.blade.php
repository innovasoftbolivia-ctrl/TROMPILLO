<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('ventas.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">&larr; Volver a ventas</a>
            <h1 class="text-2xl md:text-3xl font-bold mt-2">Cobrar reserva pendiente</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buscá al cliente por su carnet para cobrar una reserva sin pagar, emitir la factura y el boleto.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-emerald-500/15 text-emerald-700">{{ session('success') }}</div>
        @endif
        @if ($flashError)
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700">{{ $flashError }}</div>
        @endif

        {{-- Buscador por carnet --}}
        <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6 mb-6">
            <form wire:submit="buscar" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium mb-1">Carnet del cliente (CI)</label>
                    <input wire:model="carnet" type="text" class="form-input w-full" placeholder="Ej: 7654321" autofocus>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Método de pago</label>
                    <select wire:model="metodo" class="form-select">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="qr">QR</option>
                    </select>
                </div>
                <button type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white">
                    <span wire:loading.remove wire:target="buscar">Buscar</span>
                    <span wire:loading wire:target="buscar">Buscando…</span>
                </button>
            </form>
        </div>

        {{-- Resultados --}}
        @if ($busco)
            @if ($reservas->isEmpty())
                <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-8 text-center">
                    <p class="text-gray-500 dark:text-gray-400">No se encontraron reservas con cobro pendiente para el carnet
                        <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $carnet }}</span>.</p>
                    <p class="text-xs text-gray-400 mt-1">Verificá el número o revisá si la reserva ya fue pagada.</p>
                </div>
            @else
                <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl overflow-hidden">
                    <div class="px-6 py-3 border-b border-gray-100 dark:border-slate-700">
                        <h2 class="font-bold">Cobros pendientes ({{ $reservas->count() }})</h2>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-slate-700">
                        @foreach ($reservas as $r)
                            <div class="p-5 flex flex-wrap items-center justify-between gap-4">
                                <div class="min-w-[240px]">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-gray-800 dark:text-gray-100">{{ $r->codigo }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-500/15 text-amber-600">
                                            {{ ucfirst($r->estado) }} · sin pagar
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ $r->titular?->nombre_completo ?? 'Pasajero' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        @if ($r->vuelo)
                                            {{ $r->vuelo->numero_vuelo }} · {{ $r->vuelo->origen?->codigo_iata }} → {{ $r->vuelo->destino?->codigo_iata }}
                                            · {{ \Carbon\Carbon::parse($r->vuelo->salida_programada)->format('d/m/Y H:i') }}
                                        @endif
                                        · {{ $r->boletos->count() ?: 1 }} boleto(s)
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-emerald-600">Bs {{ number_format($r->monto_cobrar, 2) }}</p>
                                    <button type="button"
                                        wire:click="cobrarYFacturar({{ $r->id }})"
                                        wire:confirm="¿Cobrar Bs {{ number_format($r->monto_cobrar, 2) }} y emitir factura y boleto?"
                                        wire:loading.attr="disabled"
                                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white mt-1">
                                        <span wire:loading.remove wire:target="cobrarYFacturar({{ $r->id }})">Cobrar y facturar</span>
                                        <span wire:loading wire:target="cobrarYFacturar({{ $r->id }})">Procesando…</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
