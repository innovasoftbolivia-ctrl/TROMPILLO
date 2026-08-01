<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
        <div class="mb-6"><a href="{{ route('roles.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a roles</a>
            <h1 class="text-2xl md:text-3xl font-bold mt-2">{{ $isEdit ? 'Editar Rol' : 'Nuevo Rol' }}</h1>
        </div>
        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700">{{ session('error') }}</div>
            @endif <form wire:submit="guardar">
                @if ($errors->any())
                    <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>@endif <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6 mb-6">
                        <h2 class="font-bold text-lg mb-4 border-b border-gray-100 dark:border-slate-700 pb-2">
                            Información del Rol</h2>
                        <div class="grid grid-cols-12 gap-6">
                            <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Nombre
                                    del Rol <span class="text-red-500">*</span></label><input wire:model.blur="nombre"
                                    type="text" class="form-input w-full"
                                    {{ $rol && $rol->nombre === 'administrador' ? 'readonly' : '' }}></div>
                            <div class="col-span-12"><label
                                    class="block text-sm font-medium mb-1">Descripción</label><input
                                    wire:model.blur="descripcion" type="text" class="form-input w-full"></div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6 mb-6">
                        <h2 class="font-bold text-lg mb-4 border-b border-gray-100 dark:border-slate-700 pb-2">Permisos
                            Asignados</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($permisos as $p)
                                <label
                                    class="flex items-center p-3 border border-gray-100 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:bg-slate-800/50 cursor-pointer">
                                    <input type="checkbox" wire:model="permisosSeleccionados"
                                        value="{{ $p->id }}"
                                        class="form-checkbox h-5 w-5 text-emerald-500 rounded border-gray-300">
                                    <div class="ml-3"> <span
                                            class="block text-sm font-medium text-gray-800 dark:text-gray-200">{{ $p->clave }}</span>
                                        <span class="block text-xs text-gray-500">{{ $p->descripcion }}</span> </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3"> <a href="{{ route('roles.index') }}"
                            class="btn border-gray-200 text-gray-800 dark:text-gray-200">Cancelar</a> <button
                            type="submit" class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span
                                wire:loading.remove
                                wire:target="guardar">{{ $isEdit ? 'Guardar Cambios' : 'Crear Rol' }}</span><span
                                wire:loading wire:target="guardar">Guardando…</span></button> </div>
            </form>
    </div>
</div>
