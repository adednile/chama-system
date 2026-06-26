<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-rose-600 hover:opacity-90 active:scale-[0.98] border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 shadow-md hover:shadow-lg']) }}>
    {{ $slot }}
</button>
