{{-- resources/views/components/dashboard-card.blade.php --}}
@props(['title', 'value', 'icon'])

<div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm 
            hover:shadow-xl hover:-translate-y-1 hover:border-blue-200 
            transition-all duration-300 ease-in-out cursor-pointer group">

    <div class="flex justify-between items-center">
        <div>
            <p class="text-gray-500 text-sm">{{ $title }}</p>
            <h2 class="text-3xl font-semibold text-gray-800 mt-2">
                {{ $value }}
            </h2>
        </div>

        {{-- 🔥 ESTILO UNIFICADO AZUL --}}
        <div class="w-14 h-14 rounded-2xl bg-blue-100 
                    flex items-center justify-center 
                    group-hover:scale-110 transition duration-300">
            <i data-lucide="{{ $icon }}" class="w-6 h-6 text-blue-600"></i>
        </div>
    </div>
</div>