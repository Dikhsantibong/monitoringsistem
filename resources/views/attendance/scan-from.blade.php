@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Form Kehadiran
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            
            @if ($token)
                <form class="space-y-6" action="{{ route('attendance.submit') }}" method="POST" onsubmit="return validateForm()">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nama Lengkap
                        </label>
                        <div class="mt-1">
                            <input id="name" 
                                   name="name" 
                                   type="text" 
                                   required
                                   value="{{ old('name') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="division" class="block text-sm font-medium text-gray-700">
                            Divisi
                        </label>
                        <div class="mt-1">
                            <select id="division" 
                                    name="division" 
                                    required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Pilih Divisi</option>
                                <option value="IT" {{ old('division') == 'IT' ? 'selected' : '' }}>IT</option>
                                <option value="HR" {{ old('division') == 'HR' ? 'selected' : '' }}>HR</option>
                                <option value="Finance" {{ old('division') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Operations" {{ old('division') == 'Operations' ? 'selected' : '' }}>Operations</option>
                            </select>
                        </div>
                        @error('division')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700">
                            Jabatan
                        </label>
                        <div class="mt-1">
                            <input id="position" 
                                   name="position" 
                                   type="text" 
                                   required
                                   value="{{ old('position') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Kehadiran
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center text-red-600">
                    <p>QR Code tidak valid atau sudah kadaluarsa.</p>
                    <p class="mt-2">Silakan scan ulang QR Code yang baru.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function validateForm() {
    const name = document.getElementById('name').value.trim();
    const division = document.getElementById('division').value;
    const position = document.getElementById('position').value.trim();
    const token = document.querySelector('input[name="token"]').value;

    console.log('Submitting form with values:', { name, division, position, token });

    if (!name || !division || !position || !token) {
        alert('Mohon lengkapi semua field yang diperlukan');
        return false;
    }

    return true;
}
</script>
@endsection 