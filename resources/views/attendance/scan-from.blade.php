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
                <form action="{{ route('attendance.submit') }}" method="POST" onsubmit="return validateForm()">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" name="name" id="name" required class="form-input">
                    </div>

                    <div class="mb-4">
                        <label for="division">Divisi</label>
                        <select name="division" id="division" required class="form-select">
                            <option value="">Pilih Divisi</option>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Finance</option>
                            <option value="Operations">Operations</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="position">Jabatan</label>
                        <input type="text" name="position" id="position" required class="form-input">
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
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

    if (!name || !division || !position || !token) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Mohon lengkapi semua field yang diperlukan'
        });
        return false;
    }

    return true;
}
</script>
@endsection 