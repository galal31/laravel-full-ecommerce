<svg class="sh-icon {{ $iconClass ?? '' }}" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('search') <circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/> @break
        @case('heart') <path d="M20.8 4.8a5.4 5.4 0 0 0-7.6 0L12 6l-1.2-1.2a5.4 5.4 0 0 0-7.6 7.6L12 21l8.8-8.6a5.4 5.4 0 0 0 0-7.6Z"/> @break
        @case('cart') <path d="M3 3h2l2.2 12h12L22 7H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/> @break
        @case('user') <circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/> @break
        @case('arrow') <path d="M5 12h14m-6-6 6 6-6 6"/> @break
        @case('grid') <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/> @break
        @case('spark') <path d="m12 2 3 7 7 3-7 3-3 7-3-7-7-3 7-3 3-7Z"/> @break
        @case('close') <path d="m6 6 12 12M6 18 18 6"/> @break
        @case('bag') <path d="M5 7h14l1 14H4L5 7Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/> @break
    @endswitch
</svg>
