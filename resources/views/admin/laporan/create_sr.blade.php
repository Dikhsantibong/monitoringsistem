@extends('layouts.app')

@section('content')
   

    <div class="flex h-screen bg-gray-50 overflow-auto">
        <!-- Sidebar -->
       @include('components.sidebar')

        <!-- Main Content -->
        <div id="main-content" class="flex-1 main-content">
            <header class="bg-white shadow-sm sticky top-0 z-20">
                <div class="flex justify-between items-center px-6 py-3">
                    <div class="flex items-center gap-x-3">
                        <!-- Mobile Menu Toggle -->
                    <button id="mobile-menu-toggle"
                        class="md:hidden relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-[#009BB9] hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <!--  Menu Toggle Sidebar-->
                    <button id="desktop-menu-toggle"
                    class="hidden md:block relative items-center justify-center rounded-md text-gray-400 hover:bg-[#009BB9] p-2 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                    aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                    <h1 class="text-xl font-semibold text-gray-800">Laporan SR/WO</h1>
                    </div>
                    
                    @include('components.timer')
                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-7 h-7 rounded-full mr-2">
                            <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">

                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <div class="pt-2">
                <x-admin-breadcrumb :breadcrumbs="[
                    ['name' => 'Laporan SR/WO', 'url' => route('admin.laporan.sr_wo')], 
                    ['name' => 'Tambah SR', 'url' => null]
                ]" />
            </div>  

<main class="px-6">
    <div class="bg-white rounded-lg shadow p-6 sm:p-3">
<div class="container mx-auto mt-8">
    <h2 class="text-2xl font-bold mb-4">Tambah Service Request (SR)</h2>
    <form id="srForm" action="{{ route('admin.laporan.store-sr') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="sr_id">
                ID SR
            </label>
            <input type="number" name="sr_id" id="sr_id" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Deskripsi
            </label>
            <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded-lg" required></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                Status
            </label>
            <select name="status" id="status" class="w-full px-3 py-2 border rounded-lg" required>
                <option value="Open">Open</option>
                <option value="Closed">Closed</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="tanggal">
                Tanggal
            </label>
            <input type="date" name="tanggal" id="tanggal" class="w-full px-3 py-2 border rounded-lg" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="downtime">
                Downtime
            </label>
            <select name="downtime" id="downtime" class="w-full px-3 py-2 border rounded-lg" required>
                <option value="ya">Ya</option>
                <option value="tidak">Tidak</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="tipe_sr">
                Tipe SR
            </label>
            <select name="tipe_sr" id="tipe_sr" class="w-full px-3 py-2 border rounded-lg" required>
                <option value="CM">CM</option>
                <option value="EJ">EJ</option>
                <option value="FLM">FLM</option>
                <option value="PDM">PDM</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="priority">
                Priority
            </label>
            <select name="priority" id="priority" class="w-full px-3 py-2 border rounded-lg" required>
                <option value="emergency">Emergency</option>
                <option value="normal">Normal</option>
                <option value="outage">Outage</option>
                <option value="urgent">Urgent</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="unit">
                Unit
            </label>
            <select name="unit" id="unit" class="w-full px-3 py-2 border rounded-lg" required>
                @foreach($powerPlants as $powerPlant)
                    <option value="{{ $powerPlant->id }}">{{ $powerPlant->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.laporan.sr_wo') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center">
                <i class="fas fa-save mr-2"></i> Simpan
            </button>
        </div>
    </form>
</div>
</main>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('srForm');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Tambahkan loading state
    let isSubmitting = false;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Prevent double submission
        if (isSubmitting) return;
        
        try {
            isSubmitting = true;
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;
            
            const formData = new FormData(form);
            
            // Debug formData
            console.log('Form Data:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            });

            // Debug response
            console.log('Response status:', response.status);
            
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'SR berhasil ditambahkan',
                    showConfirmButton: false,
                    timer: 1500
                });
                window.location.href = '{{ route("admin.laporan.sr_wo") }}';
            } else {
                throw new Error(data.message || 'Terjadi kesalahan saat menyimpan data');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'Terjadi kesalahan saat menyimpan SR'
            });
        } finally {
            isSubmitting = false;
            submitButton.disabled = false;
            submitButton.innerHTML = `<i class="fas fa-save mr-2"></i> Simpan`;
        }
    });
});
</script>
@endpush
@endsection 