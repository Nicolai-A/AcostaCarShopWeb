<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ACOSTACARSHOP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-[#F4F6F9] font-[Inter]" x-data="{ open: true }">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside 
        :class="open ? 'w-64' : 'w-20'"
        class="bg-[#0F172A] text-gray-400 flex flex-col transition-all duration-300">

        <!-- Logo -->
        <div class="px-6 py-5 text-white font-semibold text-lg border-b border-gray-800 flex items-center justify-between">
            <div class="text-center">

                <span x-show="open">CARSHOP AND SERVICES</span>

            </div>

            <button @click="open = !open">
                <i data-lucide="menu" class="w-5 h-5 text-gray-400"></i>
            </button>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-2 py-6 space-y-1 text-sm">

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" 
       class="relative flex items-center gap-3 px-4 py-2 rounded-lg 
       {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800' }} group">

        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
        <span x-show="open">Dashboard</span>

        <span 
            x-show="!open"
            class="absolute left-16 bg-black text-white text-xs px-2 py-1 rounded-md shadow-lg
            opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap">
            Dashboard
        </span>
    </a>

    <!-- Clientes -->
    <a href="{{ route('clientes.index') }}" 
       class="relative flex items-center gap-3 px-4 py-2 rounded-lg 
       {{ request()->routeIs('clientes.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800' }} group">

        <i data-lucide="users" class="w-4 h-4"></i>
        <span x-show="open">Clientes</span>

        <span 
            x-show="!open"
            class="absolute left-16 bg-black text-white text-xs px-2 py-1 rounded-md shadow-lg
            opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap">
            Clientes
        </span>
    </a>

    <!-- Vehículos -->
    <a href="{{ route('vehiculos.index') }}" 
    class="relative flex items-center gap-3 px-4 py-2 rounded-lg 
    {{ request()->routeIs('vehiculos.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800' }} group">

        <i data-lucide="car" class="w-4 h-4"></i>
        <span x-show="open">Vehículos</span>

        <span 
            x-show="!open"
            class="absolute left-16 bg-black text-white text-xs px-2 py-1 rounded-md shadow-lg
            opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap">
            Vehículos
        </span>
    </a>

    <!-- Servicios -->
    <a href="{{ route('servicios.index') }}" 
    class="relative flex items-center gap-3 px-4 py-2 rounded-lg 
    {{ request()->routeIs('servicios.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800' }} group">

        <i data-lucide="ClipboardList" class="w-4 h-4"></i>
        <span x-show="open">Servicios</span>

        <span 
            x-show="!open"
            class="absolute left-16 bg-black text-white text-xs px-2 py-1 rounded-md shadow-lg
            opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap">
            Servicios
        </span>
    </a>
    <!-- Ordenes -->
    <a href="{{ route('ordenes.index') }}" 
    class="relative flex items-center gap-3 px-4 py-2 rounded-lg 
    {{ request()->routeIs('ordenes.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-800' }} group">

        <i data-lucide="ClipboardList" class="w-4 h-4"></i>
        <span x-show="open">Ordenes</span>

        <span 
            x-show="!open"
            class="absolute left-16 bg-black text-white text-xs px-2 py-1 rounded-md shadow-lg
            opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap">
            Ordenes
        </span>
    </a>
</nav>


    </aside>


    <!-- MAIN -->
    <div class="flex-1 flex flex-col transition-all duration-300">

        <!-- TOPBAR -->
        <!-- <header class="bg-white px-8 py-4 flex justify-between items-center border-b">

            <input type="text"
                   placeholder="Buscar..."
                   class="w-1/2 bg-gray-100 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">

            <div class="flex items-center gap-6">

                <div class="relative group cursor-pointer">
                    <i data-lucide="bell" class="w-5 h-5 text-gray-500 group-hover:text-blue-500 transition"></i>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </div>

                <div class="flex items-center gap-3 group cursor-pointer">
                    <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold group-hover:scale-110 transition">
                        A
                    </div>
                    <div class="text-sm">
                        <p class="font-medium text-gray-700">Administrador</p>
                        <p class="text-gray-400 text-xs">admin@acostacarshop.com</p>
                    </div>
                </div>

            </div>

        </header> -->

        <!-- CONTENT -->
        <main class="p-8">
            @yield('content')
        </main>


    </div>

</div>
@yield('scripts')
</body>

</html>

