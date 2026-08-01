<div> @php
    $estadosCss = ['pendiente' => ['label' => 'Pendiente', 'css' => 'bg-amber-500/15 text-amber-600'], 'confirmada' => ['label' => 'Confirmada', 'css' => 'bg-emerald-500/15 text-emerald-600'], 'cancelada' => ['label' => 'Cancelada', 'css' => 'bg-red-500/15 text-red-600'], 'completada' => ['label' => 'Completada', 'css' => 'bg-indigo-500/15 text-indigo-600']];
    $estadosBoleto = ['reservado' => 'bg-gray-200 text-gray-700', 'emitido' => 'bg-blue-200 text-blue-700', 'usado' => 'bg-green-200 text-green-700', 'cancelado' => 'bg-red-200 text-red-700', 'no_show' => 'bg-orange-200 text-orange-700'];
@endphp <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto">
        <div class="mb-6"><a href="{{ route('reservas.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a reservas</a>
            <div class="sm:flex sm:items-center sm:justify-between mt-2">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl md:text-3xl font-bold">Reserva {{ $reserva->codigo }}</h1> <span
                        class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosCss[$reserva->estado]['css'] ?? 'bg-gray-500/20 text-gray-600' }}">{{ $estadosCss[$reserva->estado]['label'] ?? $reserva->estado }}</span>
                </div>
                <div class="flex flex-wrap items-end gap-2 mt-4 sm:mt-0"> <a href="{{ route('reservas.edit', $reserva) }}"
                        class="btn border-gray-200 text-gray-600 hover:border-gray-300">Editar</a>
                    @if (!$reserva->venta && $reserva->estado !== 'cancelada')
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Monto (Bs)</label>
                            <input wire:model="cobroMonto" type="number" step="0.01" class="form-input w-28">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Método</label>
                            <select wire:model="cobroMetodo" class="form-select">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="tarjeta_credito">Tarjeta crédito</option>
                                <option value="tarjeta_debito">Tarjeta débito</option>
                            </select>
                        </div>
                        <button wire:click="cobrar" wire:confirm="¿Registrar el cobro y generar la venta?"
                            class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Cobrar y generar venta</button>
                    @endif
                    @if ($reserva->venta)
                        <a href="{{ route('ventas.show', $reserva->venta->id) }}"
                            class="btn bg-emerald-500 hover:bg-emerald-600 text-white">Ver Venta</a>
                    @endif
                </div>
            </div>
        </div>
        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 border border-green-500/30 text-green-700">
                {{ session('success') }}</div>
            @endif @if (session('error'))
                <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 border border-red-500/30 text-red-700">
                    {{ session('error') }}</div>
                @endif <div class="grid grid-cols-12 gap-6 mb-6">
                    <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                        <h3 class="font-semibold mb-4">Detalles de Reserva</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Fecha</dt>
                                <dd class="font-medium">
                                    {{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Titular</dt>
                                <dd class="font-medium">
                                    {{ $reserva->titular ? $reserva->titular->nombres . ' ' . $reserva->titular->apellidos : '—' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Total Reservado</dt>
                                <dd class="font-medium text-emerald-600">Bs {{ number_format($reserva->total, 2) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Notas</dt>
                                <dd class="font-medium mt-1">{{ $reserva->notas ?? 'Sin notas.' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div class="col-span-12 sm:col-span-6 bg-white dark:bg-slate-800 shadow-xs rounded-xl p-5">
                        <h3 class="font-semibold mb-4">Vuelo Asociado</h3>
                        @if ($reserva->vuelo)
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Nro Vuelo</dt>
                                    <dd class="font-medium"><a href="{{ route('vuelos.show', $reserva->vuelo_id) }}"
                                            class="text-indigo-600 hover:underline">{{ $reserva->vuelo->numero_vuelo ?? 'S/N' }}</a>
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Ruta</dt>
                                    <dd class="font-medium">{{ $reserva->vuelo->origen->codigo_iata }} →
                                        {{ $reserva->vuelo->destino->codigo_iata }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Salida</dt>
                                    <dd class="font-medium">
                                        {{ \Carbon\Carbon::parse($reserva->vuelo->salida_programada)->format('d/m/Y H:i') }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Estado Vuelo</dt>
                                    <dd class="font-medium uppercase">{{ $reserva->vuelo->estado }}</dd>
                                </div>
                            </dl>
                        @else
                            <div class="text-gray-500">Vuelo no disponible.</div>
                            @endif
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                        <h2 class="font-semibold">Boletos ({{ $reserva->boletos->count() }})</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-sm">
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-800/50">
                                <tr>
                                    <th class="px-4 py-3 text-left">Nro. Boleto</th>
                                    <th class="px-4 py-3 text-left">Persona</th>
                                    <th class="px-4 py-3 text-center">Asiento</th>
                                    <th class="px-4 py-3 text-center">Equipaje (kg)</th>
                                    <th class="px-4 py-3 text-right">Precio</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                    <th class="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach ($reserva->boletos as $b)
                                    <tr>
                                        <td class="px-4 py-3 font-medium">{{ $b->numero_boleto }}</td>
                                        <td class="px-4 py-3">{{ $b->pasajero->nombres }}
                                            {{ $b->pasajero->apellidos }} <span
                                                class="block text-xs text-gray-500">{{ $b->pasajero->documento_identidad }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center font-mono">{{ $b->asiento ?? 'S/A' }}</td>
                                        <td class="px-4 py-3 text-center">{{ number_format($b->equipaje_kg, 1) }} kg
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium">Bs
                                            {{ number_format($b->precio, 2) }}</td>
                                        <td class="px-4 py-3 text-center"><span
                                                class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 {{ $estadosBoleto[$b->estado] ?? 'bg-gray-100' }}">{{ strtoupper($b->estado) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if (in_array($b->estado, ['emitido', 'reservado']) &&
                                                    $reserva->vuelo &&
                                                    in_array($reserva->vuelo->estado, ['confirmado', 'abordando']))
                                                <button wire:click="checkinBoleto({{ $b->id }})"
                                                    wire:confirm="¿Marcar check-in (usado) para este pasajero?"
                                                    class="text-xs btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">Check-in</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
    </div>
</div>
