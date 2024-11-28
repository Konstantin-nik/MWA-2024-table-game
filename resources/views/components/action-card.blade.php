<div class="p-2 rounded w-16 h-24 flex items-center justify-center 
    {{ $type === 'action' ? 'bg-gray-500 text-white' : 'bg-gray-300 text-black' }}">
    @if ($type === 'action')
        <span class="font-bold text-center text-sm">{{ $getActionName() }}</span>
    @else
        <span class="font-bold text-lg">{{ $content }}</span>
    @endif
</div>
