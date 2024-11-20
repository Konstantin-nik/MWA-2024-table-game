<div class="mb-4">
    @include('components.form.label')
    <div class="flex items-center">
        <input 
            type="checkbox" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="1" 
            {{ old($name, $value) ? 'checked' : '' }}
            class="mr-2 leading-tight">
        <label for="{{ $name }}" class="text-gray-600">{{ $placeholder }}</label>
    </div>
</div>