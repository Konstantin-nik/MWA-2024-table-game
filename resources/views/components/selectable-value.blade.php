@if ($isSelected)
    <div class="px-2 py-1 {{ $selectedColor }} text-center rounded border">{{ $value }}</div>
@else
    <div class="px-2 py-1 {{ $unselectedColor }} text-center rounded border">{{ $value }}</div>
@endif