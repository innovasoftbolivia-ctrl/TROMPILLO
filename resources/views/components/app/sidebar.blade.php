@php
    $grupos = [
        'Operación' => [
            ['seg' => 'dashboard', 'label' => 'Dashboard', 'route' => 'dashboard', 'root' => true, 'permiso' => null, 'icon' => 'M4 13h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1zm0 8h6a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1zm10 0h6a1 1 0 0 0 1-1v-8a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1zM13 4v4a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1z'],
            ['seg' => 'operaciones', 'label' => 'Operaciones', 'route' => 'operaciones', 'permiso' => 'reportes.ver', 'icon' => 'M4 3h4a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zm11 0h5a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z'],
            ['seg' => 'reportes', 'label' => 'Reportes', 'route' => 'reportes', 'permiso' => 'reportes.ver', 'icon' => 'M3 3v18h18v-2H5V3H3zm4 12h2v-5H7v5zm4 0h2V7h-2v8zm4 0h2v-3h-2v3z'],
            ['seg' => 'vuelos', 'label' => 'Vuelos', 'route' => 'vuelos.index', 'permiso' => 'vuelos.gestionar', 'icon' => 'M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5L21 16z'],
            ['seg' => 'reservas', 'label' => 'Reservas', 'route' => 'reservas.index', 'permiso' => 'reservas.gestionar', 'icon' => 'M2 9a3 3 0 0 0 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 0 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v2z'],
            ['seg' => 'rutas', 'label' => 'Rutas', 'route' => 'rutas.index', 'permiso' => 'catalogos.gestionar', 'icon' => 'M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z'],
            ['seg' => 'carga', 'label' => 'Carga', 'route' => 'carga.index', 'permiso' => 'reservas.gestionar', 'icon' => 'M21 7.5 12 3 3 7.5v9L12 21l9-4.5v-9zM12 5.3l6 3-6 3-6-3 6-3zM5 9.6l6 3v6.1l-6-3V9.6zm14 0v6.1l-6 3v-6.1l6-3z'],
        ],
        'Comercial' => [
            ['seg' => 'ventas', 'label' => 'Ventas', 'route' => 'ventas.index', 'permiso' => 'reservas.gestionar', 'icon' => 'M4 4h16a1 1 0 0 1 1 1v14l-3-2-3 2-3-2-3 2-3-2-3 2V5a1 1 0 0 1 1-1zm3 5h10v2H7V9zm0 4h7v2H7v-2z'],
            ['seg' => 'facturas', 'label' => 'Facturas', 'route' => 'facturas.index', 'permiso' => 'reservas.gestionar', 'icon' => 'M6 2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6H6zm7 1.5L18.5 9H13V3.5zM8 12h8v2H8v-2zm0 4h6v2H8v-2z'],
        ],
        'Flota' => [
            ['seg' => 'aeronaves', 'label' => 'Aeronaves', 'route' => 'aeronaves.index', 'permiso' => 'flota.gestionar', 'icon' => 'M22 12c0-.6-.4-1-1-1h-6l-4-7h-2l2 7H6l-2-2H2l1 3-1 3h2l2-2h5l-2 7h2l4-7h6c.6 0 1-.4 1-1z'],
            ['seg' => 'mantenimientos', 'label' => 'Mantenimiento', 'route' => 'mantenimientos.index', 'permiso' => 'mantenimiento.gestionar', 'icon' => 'M22.7 19l-9.1-9.1a5 5 0 0 0-6.9-6.4l3.5 3.5-2.1 2.1-3.5-3.5a5 5 0 0 0 6.4 6.9l9.1 9.1a1 1 0 0 0 1.4 0l1.2-1.2a1 1 0 0 0 0-1.4z'],
        ],
        'Personal' => [
            ['seg' => 'empleados', 'label' => 'Empleados', 'route' => 'empleados.index', 'permiso' => 'personal.gestionar', 'icon' => 'M16 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-8 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0 2c-2.7 0-8 1.3-8 4v3h9v-3c0-1 .4-1.9 1-2.6-.6-.3-1.3-.4-2-.4zm8 0c-.3 0-.6 0-1 .1 1.2.9 2 2.1 2 3.9v3h7v-3c0-2.7-5.3-4-8-4z'],
            ['seg' => 'pilotos', 'label' => 'Pilotos', 'route' => 'pilotos.index', 'permiso' => 'personal.gestionar', 'icon' => 'M9 2a2 2 0 0 0-2 2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-2a2 2 0 0 0-2-2H9zm3 5a2 2 0 1 1 0 4 2 2 0 0 1 0-4zm-4 9c0-1.7 2.7-2.5 4-2.5s4 .8 4 2.5v1H8v-1z'],
        ],
        'Registros' => [
            ['seg' => 'aeropuertos', 'label' => 'Aeropuertos', 'route' => 'aeropuertos.index', 'permiso' => 'catalogos.gestionar', 'icon' => 'M6 2v20h5v-4a1 1 0 0 1 2 0v4h5V2H6zm3 3h2v2H9V5zm4 0h2v2h-2V5zM9 9h2v2H9V9zm4 0h2v2h-2V9zM9 13h2v2H9v-2zm4 0h2v2h-2v-2z'],
            ['seg' => 'personas', 'label' => 'Personas / Pasajeros', 'route' => 'personas.index', 'permiso' => 'catalogos.gestionar', 'icon' => 'M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-4 0-8 2-8 5v1h16v-1c0-3-4-5-8-5z'],
        ],
        'Administración' => [
            ['seg' => 'usuarios', 'label' => 'Usuarios', 'route' => 'usuarios.index', 'permiso' => 'usuarios.gestionar', 'icon' => 'M15 14c-2.7 0-8 1.3-8 4v2h16v-2c0-2.7-5.3-4-8-4zm0-2a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM5 13l-1-2H1v-2h3l1-2 2 4-2 4-2-2h0z'],
            ['seg' => 'roles', 'label' => 'Roles y permisos', 'route' => 'roles.index', 'permiso' => 'usuarios.gestionar', 'icon' => 'M12 1 3 5v6c0 5 3.8 9.7 9 11 5.2-1.3 9-6 9-11V5l-9-4zm0 10.9h7c-.5 3.6-3.1 6.9-7 8V12H5V6.3l7-3.1v8.7z'],
        ],
    ];
    $usuario = auth()->user();
    $puede = fn ($permiso) => $permiso === null || ($usuario && $usuario->can($permiso));
    // Filtra ítems y grupos según los permisos del usuario
    $grupos = collect($grupos)->map(fn ($items) => array_values(array_filter($items, fn ($it) => $puede($it['permiso'] ?? null))))->filter(fn ($items) => count($items) > 0)->all();
    $segActual = Request::segment(1);
@endphp
<div class="min-w-fit">
    <!-- Sidebar backdrop (mobile only) -->
    <div
        class="fixed inset-0 bg-gray-900/30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'"
        aria-hidden="true"
        x-cloak
    ></div>

    <!-- Sidebar -->
    <div
        id="sidebar"
        class="flex lg:flex! flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-[100dvh] overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-20 lg:sidebar-expanded:!w-64 2xl:w-64! shrink-0 bg-white dark:bg-gray-800 p-4 transition-all duration-200 ease-in-out {{ $variant === 'v2' ? 'border-r border-gray-200 dark:border-gray-700/60' : 'rounded-r-2xl shadow-xs' }}"
        :class="sidebarOpen ? 'max-lg:translate-x-0' : 'max-lg:-translate-x-64'"
        @click.outside="sidebarOpen = false"
        @keydown.escape.window="sidebarOpen = false"
    >

        <!-- Sidebar header -->
        <div class="flex justify-between mb-10 pr-3 sm:px-2">
            <!-- Close button -->
            <button class="lg:hidden text-gray-500 hover:text-gray-400" @click.stop="sidebarOpen = !sidebarOpen" aria-controls="sidebar" :aria-expanded="sidebarOpen">
                <span class="sr-only">Close sidebar</span>
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
                </svg>
            </button>
            <!-- Logo + marca -->
            <a class="flex items-center gap-2" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/emblema-trompillo.svg') }}" alt="El Trompillo" class="shrink-0 w-9 h-11" />
                <div class="lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200 leading-tight">
                    <div class="text-sm font-bold text-gray-800 dark:text-gray-100">Trompillo</div>
                    <div class="text-[11px] text-gold-600 dark:text-gold-400 font-semibold tracking-wide uppercase">Aerolínea</div>
                </div>
            </a>
        </div>

        <!-- Links -->
        <div class="space-y-8">
            @foreach ($grupos as $titulo => $items)
                <div>
                    <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-semibold pl-3">
                        <span class="hidden lg:block lg:sidebar-expanded:hidden 2xl:hidden text-center w-6" aria-hidden="true">•••</span>
                        <span class="lg:hidden lg:sidebar-expanded:block 2xl:block">{{ $titulo }}</span>
                    </h3>
                    <ul class="mt-3">
                        @foreach ($items as $item)
                            @php
                                $activo = $segActual === $item['seg'] || (($item['root'] ?? false) && $segActual === null);
                            @endphp
                            <li class="pl-4 pr-3 py-2 rounded-lg mb-0.5 last:mb-0 @if($activo){{ 'bg-emerald-500' }}@endif">
                                <a wire:navigate class="block truncate transition {{ $activo ? 'text-white' : 'text-gray-800 dark:text-gray-100 hover:text-gray-900 dark:hover:text-white' }}" href="{{ route($item['route']) }}">
                                    <div class="flex items-center">
                                        <svg class="shrink-0 fill-current {{ $activo ? 'text-white' : 'text-gray-400 dark:text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                            <path d="{{ $item['icon'] }}" />
                                        </svg>
                                        <span class="text-sm font-medium ml-4 lg:opacity-0 lg:sidebar-expanded:opacity-100 2xl:opacity-100 duration-200">{{ $item['label'] }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <!-- Expand / collapse button -->
        <div class="pt-3 hidden lg:inline-flex 2xl:hidden justify-end mt-auto">
            <div class="w-12 pl-4 pr-3 py-2">
                <button class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 transition-colors" @click="sidebarExpanded = !sidebarExpanded">
                    <span class="sr-only">Expand / collapse sidebar</span>
                    <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 sidebar-expanded:rotate-180" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M15 16a1 1 0 0 1-1-1V1a1 1 0 1 1 2 0v14a1 1 0 0 1-1 1ZM8.586 7H1a1 1 0 1 0 0 2h7.586l-2.793 2.793a1 1 0 1 0 1.414 1.414l4.5-4.5A.997.997 0 0 0 12 8.01M11.924 7.617a.997.997 0 0 0-.217-.324l-4.5-4.5a1 1 0 0 0-1.414 1.414L8.586 7M12 7.99a.996.996 0 0 0-.076-.373Z" />
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>
