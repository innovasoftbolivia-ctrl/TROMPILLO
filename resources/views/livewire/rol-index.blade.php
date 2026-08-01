<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold">Roles y Permisos 🛡️</h1>
                <p class="text-sm text-gray-500 mt-1">Configuración de Control de Acceso Basado en Roles (RBAC).</p>
            </div> <a href="{{ route('roles.create') }}" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span
                    class="ml-2">Nuevo Rol</span></a>
        </div>
        @if ($flashOk)
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 text-green-700 flex justify-between"
                x-data="{ show: true }" x-show="show">
                <div>{{ $flashOk }}</div><button @click="show = false">✕</button>
            </div>
            @endif @if ($flashError)
                <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700 flex justify-between"
                    x-data="{ show: true }" x-show="show">
                    <div>{{ $flashError }}</div><button @click="show = false">✕</button>
                </div>
                @endif @if (session('success'))
                    <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-green-500/15 text-green-700 flex justify-between"
                        x-data="{ show: true }" x-show="show">
                        <div>{{ session('success') }}</div><button @click="show = false">✕</button>
                    </div>
                    @endif <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full text-sm">
                                <thead
                                    class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-800/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Rol</th>
                                        <th class="px-4 py-3 text-left">Descripción</th>
                                        <th class="px-4 py-3 text-center">Nro. Permisos</th>
                                        <th class="px-4 py-3 text-center">Usuarios Asignados</th>
                                        <th class="px-4 py-3 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                    @foreach ($roles as $r)
                                        <tr wire:key="rol-{{ $r->id }}">
                                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                                                {{ $r->nombre }}</td>
                                            <td class="px-4 py-3 text-gray-500">{{ $r->descripcion ?? '—' }}</td>
                                            <td class="px-4 py-3 text-center"><span
                                                    class="inline-flex text-xs font-medium bg-emerald-100 text-emerald-600 rounded-full px-2 py-0.5">{{ $r->permisos_count }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center"><span
                                                    class="inline-flex text-xs font-medium bg-indigo-100 text-indigo-600 rounded-full px-2 py-0.5">{{ $r->usuarios_count }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex items-center justify-end gap-2"> <a
                                                        href="{{ route('roles.edit', $r) }}"
                                                        class="text-gray-400 hover:text-emerald-500">Editar</a>
                                                    @if ($r->nombre !== 'administrador' && $r->usuarios_count === 0)
                                                        <button wire:click="eliminar({{ $r->id }})"
                                                            wire:confirm="¿Eliminar este rol?"
                                                            class="text-gray-400 hover:text-red-500">Borrar</button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
    </div>
</div>
