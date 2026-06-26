<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-gold-500 to-gold-700 hover:opacity-95 active:scale-[0.98] border border-transparent rounded-xl font-bold text-xs text-brand-navy uppercase tracking-widest focus:outline-none transition ease-in-out duration-150 shadow-md hover:shadow-lg']) }}>
    {{ $slot }}
</button>
