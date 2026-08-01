<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm whitespace-nowrap']) }}>
    {{ $slot }}
</button>
