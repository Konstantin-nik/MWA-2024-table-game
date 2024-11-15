<div>
    @foreach ($contests as $contest)
    <dev class="mt-2">
        <h3>{{ $contest->name }} room</h3>
        {{ count($contest->users) }}
        /
        {{ $contest->size }} Users 
    </dev>

    @endforeach
</div>
