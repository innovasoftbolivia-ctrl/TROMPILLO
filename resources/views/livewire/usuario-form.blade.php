<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
        <div class="mb-6"><a href="{{ route('usuarios.index') }}"
                class="text-sm text-emerald-500 hover:text-emerald-600 font-medium">&larr; Volver a usuarios</a>
            <h1 class="text-2xl md:text-3xl font-bold mt-2">{{ $isEdit ? 'Editar Usuario' : 'Nuevo Usuario' }}</h1>
        </div>
        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700">{{ session('error') }}</div>
            @endif <form wire:submit="guardar" class="bg-white dark:bg-slate-800 shadow-xs rounded-xl p-6">
                @if ($errors->any())
                    <div class="mb-6 px-4 py-3 rounded-lg text-sm bg-red-500/10 text-red-700">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Nombre Completo
                            <span class="text-red-500">*</span></label><input wire:model.blur="name" type="text"
                            class="form-input w-full"></div>
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Email <span
                                class="text-red-500">*</span></label><input wire:model.blur="email" type="email"
                            class="form-input w-full"></div>
                    <div class="col-span-12 sm:col-span-12"><label class="block text-sm font-medium mb-1">Rol en Sistema
                            <span class="text-red-500">*</span></label><select wire:model="role_id"
                            class="form-select w-full">
                            <option value="">— Seleccionar Rol —</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Contraseña
                            @if (!$isEdit)
                                <span class="text-red-500">*</span>
                            @endif
                        </label><input wire:model.blur="password" type="password" class="form-input w-full"
                            placeholder="{{ $isEdit ? 'Dejar en blanco para no cambiar' : '' }}"></div>
                    <div class="col-span-12 sm:col-span-6"><label class="block text-sm font-medium mb-1">Confirmar
                            Contraseña</label><input wire:model.blur="password_confirmation" type="password"
                            class="form-input w-full"></div>
                </div>
                <div
                    class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('usuarios.index') }}"
                        class="btn border-gray-200 text-gray-800 dark:text-gray-200">Cancelar</a> <button type="submit"
                        class="btn bg-emerald-500 hover:bg-emerald-600 text-white"><span wire:loading.remove
                            wire:target="guardar">{{ $isEdit ? 'Guardar Cambios' : 'Crear Usuario' }}</span><span
                            wire:loading wire:target="guardar">Guardando…</span></button> </div>
            </form>
    </div>
</div>
