@extends('layouts.app')

@section('content')
<div class="flex h-screen bg-gray-50 overflow-auto">
    @include('components.sidebar')

    <div id="main-content" class="flex-1 main-content">
    <header class="bg-white shadow-sm sticky top-0 z-10">
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

                        <h1 class="text-xl font-semibold text-gray-800">Data Material Master</h1>
                    </div>

                    <div class="relative">
                        <button id="dropdownToggle" class="flex items-center" onclick="toggleDropdown()">
                            <img src="{{ Auth::user()->avatar ?? asset('foto_profile/admin1.png') }}"
                                class="w-7 h-7 rounded-full mr-2">
                            <span class="text-gray-700 text-sm">{{ Auth::user()->name }}</span>
                            <i class="fas fa-caret-down ml-2 text-gray-600"></i>
                        </button>
                        <div id="dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-10">
                            <a href="{{ route('logout') }}" 
                               class="block px-4 py-2 text-gray-800 hover:bg-gray-200"
                               onclick="event.preventDefault(); 
                                        document.getElementById('logout-form').submit();">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                                <input type="hidden" name="redirect" value="{{ route('homepage') }}">
                            </form>
                        </div>
                    </div>
                </div>
            </header>

        <div class="p-6">
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Material Master</h2>
                    <!-- Flash Messages
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif -->

                    <!-- Toolbar: Upload & Search -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Upload Card -->
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Upload Material (Excel)</h3>
                            <p class="text-gray-500 text-sm mb-3">Format yang didukung: .xlsx, .xls</p>
                            <form action="{{ route('admin.material-master.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-3" id="uploadForm">
                                @csrf
                                <input id="excel_file" type="file" name="excel_file" accept=".xlsx,.xls" class="hidden" />
                                <label for="excel_file" class="flex items-center justify-center w-full p-4 border-2 border-dashed border-gray-300 rounded-md hover:border-blue-500 hover:bg-blue-50 cursor-pointer transition">
                                    <div class="text-center">
                                        <i class="fas fa-file-excel text-2xl text-gray-400 mb-1"></i>
                                        <div class="text-gray-600 text-sm">Klik untuk memilih file atau seret ke sini</div>
                                    </div>
                                </label>
                                <div id="file_info" class="hidden text-sm text-gray-700">File dipilih: <span id="file_name" class="font-medium"></span></div>
                                <div id="loader" class="hidden flex justify-center items-center"><svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg><span class="ml-2 text-blue-600">Mengupload...</span></div>
                                <div class="flex justify-end">
                                    <button id="upload_btn" type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50" disabled>Upload</button>
                                </div>
                            </form>
                        </div>

                        <!-- Search Card -->
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Pencarian</h3>
                            <form method="GET" action="{{ route('admin.material-master.index') }}" class="flex gap-2">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, deskripsi, kategori, quantity, price, value..." class="flex-1 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cari</button>
                                @if(request('search'))
                                    <a href="{{ route('admin.material-master.index') }}" class="px-4 py-2 rounded border">Reset</a>
                                @endif
                            </form>
                        </div>
                    </div>
                    @error('excel_file')
                        <div class="text-red-600 mt-2 text-sm">{{ $message }}</div>
                    @enderror

                    @if($lastUpdate)
                        <div class="mb-4 text-sm text-gray-600">
                            <span class="font-semibold">Terakhir diupdate:</span> {{ \Carbon\Carbon::parse($lastUpdate)->translatedFormat('d F Y H:i') }}
                        </div>
                    @endif

                    <!-- Tabel Data Material Master -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="sticky top-0 z-10" style="background-color: #0A749B; color: white">
                                <tr>
                                    <th class="px-4 py-2 border-b text-center border-r">No</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Discritc Code</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Warehouse</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Bin Code</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Inventory Statistic Code</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Inventory Statistic Desc</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Material Num</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Stock Code</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Description</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Stock Class</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Stock Type</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Inventory Category</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Unit Of Issue</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Minimum SOH</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Maximum SOH</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Quantity</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Inventory Price</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Inventory Value</th>
                                    <th class="px-4 py-2 border-b text-center border-r">Waktu Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($materials as $material)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-2 border-b border-r border-gray-200 text-center">{{ ($materials->currentPage() - 1) * $materials->perPage() + $loop->iteration }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->discritc_code }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->warehouse }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->bin_code }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->inventory_statistic_code }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200" style="min-width:220px;max-width:400px;">{{ $material->inventory_statistic_desc }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->material_num }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->stock_code }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->description }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->stock_class }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->stock_type }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->inventory_category }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200">{{ $material->unit_of_issue }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->minimum_soh }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->maximum_soh }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->quantity }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->inventory_price }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->inventory_value }}</td>
                                        <td class="px-4 py-2 border-b border-r border-gray-200 text-right">{{ $material->updated_at }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="19" class="px-4 py-2 text-center text-gray-500">Belum ada data material master.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Menampilkan
                            {{ ($materials->currentPage() - 1) * $materials->perPage() + 1 }}
                            hingga
                            {{ min($materials->currentPage() * $materials->perPage(), $materials->total()) }}
                            dari
                            {{ $materials->total() }}
                            entri
                        </div>
                        <div class="flex items-center gap-1">
                            {{ $materials->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #region -->
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Upload UI enhancement
const input = document.getElementById('excel_file');
const uploadBtn = document.getElementById('upload_btn');
const fileInfo = document.getElementById('file_info');
const fileName = document.getElementById('file_name');
const loader = document.getElementById('loader');
const uploadForm = document.getElementById('uploadForm');
if (input) {
  input.addEventListener('change', function() {
    if (this.files && this.files.length > 0) {
      fileName.textContent = this.files[0].name;
      fileInfo.classList.remove('hidden');
      uploadBtn.removeAttribute('disabled');
    } else {
      fileName.textContent = '';
      fileInfo.classList.add('hidden');
      uploadBtn.setAttribute('disabled', 'disabled');
    }
  });
}
if (uploadForm) {
  uploadForm.addEventListener('submit', function(e) {
    loader.classList.remove('hidden');
    uploadBtn.setAttribute('disabled', 'disabled');
  });
}
</script>
@endpush


@endsection 