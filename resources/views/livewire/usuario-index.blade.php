<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl font-bold">Usuarios 👥</h1>
                <p class="text-sm text-gray-500 mt-1">Gestión de cuentas de acceso al sistema.</p>
            </div> <a href="{{ route('usuarios.create') }}"
                class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span class="ml-2">Nuevo Usuario</span></a>
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
                @endif
                <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-4 mb-6">
                    <div class="grid grid-cols-12 gap-4 items-end">
                        <div class="col-span-12 sm:col-span-6"><label
                                class="block text-xs font-medium text-gray-500 mb-1">Buscar (Nombre,
                                Email)</label><input wire:model.live.debounce.400ms="buscar" type="text"
                                class="form-input w-full"></div>
                        <div class="col-span-6 sm:col-span-4"><label
                                class="block text-xs font-medium text-gray-500 mb-1">Rol</label><select
                                wire:model.live="role_id" class="form-select w-full">
                                <option value="">Todos</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-2 flex gap-2">
                            @if ($buscar !== '' || $role_id !== '')
                                <button wire:click="limpiarFiltros" class="btn border-gray-200 text-gray-600 w-full">✕
                                    Limpiar</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-sm">
                            <thead
                                class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-800/50">
                                <tr>
                                    <th class="px-4 py-3 text-left">Nombre</th>
                                    <th class="px-4 py-3 text-left">Email</th>
                                    <th class="px-4 py-3 text-left">Rol (RBAC)</th>
                                    <th class="px-4 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @forelse ($usuarios as $u)
                                    <tr wire:key="user-{{ $u->id }}">
                                        <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ $u->email }}</td>
                                        <td class="px-4 py-3"><span
                                                class="inline-flex text-xs font-medium rounded-full px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $u->role ? $u->role->nombre : 'Sin Rol' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2"> <a
                                                    href="{{ route('usuarios.edit', $u) }}"
                                                    class="text-gray-400 hover:text-emerald-500">Editar</a>
                                                @if (auth()->id() !== $u->id)
                                                    <button wire:click="eliminar({{ $u->id }})"
                                                        wire:confirm="¿Eliminar este usuario?"
                                                        class="text-gray-400 hover:text-red-500">Borrar</button>
                                                @endif
                                            </div>
                                        </td>
                                </tr> @empty <tr>
                                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">No hay usuarios.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-6">{{ $usuarios->links() }}</div>
    </div>
</div>
