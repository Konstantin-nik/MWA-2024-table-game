<div>
    @foreach ($rooms as $room)
    <dev class="mt-2">
        <h3>{{ $room->name }} room</h3>
        {{ count($room->users) }}
        /
        {{ $room->size }} Users 
    </dev>

    @endforeach
</div>
