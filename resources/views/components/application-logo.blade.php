<svg viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg" fill="none" {{ $attributes }}>
    <defs>
        <linearGradient id="wr-gradient" x1="20" y1="24" x2="140" y2="144" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#1d4ed8" />
            <stop offset="0.45" stop-color="#2563eb" />
            <stop offset="1" stop-color="#0ea5e9" />
        </linearGradient>
        <linearGradient id="wr-shine" x1="40" y1="28" x2="92" y2="96" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#ffffff" stop-opacity="0.75" />
            <stop offset="1" stop-color="#ffffff" stop-opacity="0" />
        </linearGradient>
        <clipPath id="wr-clip">
            <rect width="160" height="160" rx="40" />
        </clipPath>
    </defs>

    <g clip-path="url(#wr-clip)">
        <rect width="160" height="160" rx="40" fill="url(#wr-gradient)" />
        <path d="M-10 60 C40 0, 120 0, 170 60" stroke="url(#wr-shine)" stroke-width="28" opacity="0.35" />
        <circle cx="48" cy="42" r="12" fill="#ffffff" fill-opacity="0.2" />
        <circle cx="132" cy="120" r="18" fill="#38bdf8" fill-opacity="0.45" />

        <path d="M32 50 L50 116 L72 74 L94 116 L112 50" stroke="#ffffff" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M116 116 V50 H132 C144 50 152 58 152 70 C152 82 144 90 132 90 H116 L148 116" stroke="#ffffff" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" />
    </g>

    <rect x="2.5" y="2.5" width="155" height="155" rx="37.5" stroke="#ffffff" stroke-opacity="0.12" stroke-width="5" />
</svg>
