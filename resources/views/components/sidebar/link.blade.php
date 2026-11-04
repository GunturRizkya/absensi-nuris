@props(['href' => '#', 'active' => false, 'badge' => null])

<a href="{{ $href }}" class="relative flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ $active ? 'bg-blue-50 text-blue-600 font-bold shadow-2xs' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' }}">
    @if($active)
        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-blue-600"></span>
    @endif
    <span class="shrink-0 [&>svg]:w-5 [&>svg]:h-5 {{ $active ? '[&>svg]:text-blue-600' : '[&>svg]:text-slate-400' }}">{{ $icon ?? '' }}</span>
    <span class="flex-1 truncate">{{ $slot }}</span>
    @if(! is_null($badge) && $badge > 0)
        <span class="ml-auto min-w-5 px-1.5 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-bold text-center shrink-0">{{ $badge }}</span>
    @endif
</a>
