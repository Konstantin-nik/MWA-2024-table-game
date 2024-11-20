<div class="mb-4">
    @include('components.form.label')
    <input 
        type="number" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        placeholder="{{ $placeholder }}" 
        value="{{ old($name, $value) }}" 
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error($name) border-red-500 @enderror">
    @include('components.form.error-handling')
</div>