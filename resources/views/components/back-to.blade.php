<a href="{{ $link }}" class="
        mb-5

        inline-flex
        items-center
        gap-2

        px-5
        py-3

        rounded-xl

        bg-blue-50
        dark:bg-blue-900/30

        text-blue-600
        dark:text-blue-400

        font-semibold
        text-sm

        border
        border-blue-100
        dark:border-blue-800

        hover:bg-blue-100
        dark:hover:bg-blue-900/50

        transition-all

        shadow-sm"
    >
    <svg width="18" height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2">

        <path d="M19 12H5" />
        <path d="M12 19l-7-7 7-7" />

    </svg>
    {{ $slot }}
</a>