<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk menghitung skor
        function calculateScore(group) {
            const awalSelect = document.querySelector(`select[name="peserta[${group}][awal]"]`);
            const akhirSelect = document.querySelector(`select[name="peserta[${group}][akhir]"]`);
            const skorInput = document.querySelector(`input[name="peserta[${group}][skor]"]`);
            const keteranganInput = document.querySelector(`input[name="peserta[${group}][keterangan]"]`);
            if (!awalSelect || !akhirSelect || !skorInput || !keteranganInput) {
                console.error('Elemen tidak ditemukan untuk group:', group);
                return;
            }
            const awalValue = parseInt(awalSelect.value);
            const akhirValue = parseInt(akhirSelect.value);
            const totalChecks = awalValue + akhirValue;
            let score = 0;
            let keterangan = '';
            // Logika perhitungan skor
            if (totalChecks === 0) {
                score = 0;
                keterangan = 'Tidak hadir dalam rapat';
            } else if (totalChecks === 1) {
                score = 50;
                if (awalValue === 1) {
                    keterangan = 'Hanya hadir di awal rapat';
                } else {
                    keterangan = 'Hanya hadir di akhir rapat';
                }
            } else if (totalChecks === 2) {
                score = 100;
                keterangan = 'Hadir penuh';
            }
            skorInput.value = score;
            keteranganInput.value = keterangan;
        }
        // Fungsi untuk render form peserta dengan event listener skor
        function renderPesertaForm() {
            const container = document.getElementById('pesertaContainer');
            container.innerHTML = pesertaList.map((peserta, index) => `
                <div class="grid grid-cols-6 gap-4 items-center mb-2">
                    <label class="text-sm">${index + 1}. ${peserta.jabatan}</label>
                    <div class="text-center">
                        <select name="peserta[${peserta.id}][awal]" class="w-20 border rounded text-center peserta-select" data-group="${peserta.id}">
                            <option value="1">✓</option>
                            <option value="0" selected>✗</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <select name="peserta[${peserta.id}][akhir]" class="w-20 border rounded text-center peserta-select" data-group="${peserta.id}">
                            <option value="1">✓</option>
                            <option value="0" selected>✗</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <input type="number" name="peserta[${peserta.id}][skor]" class="w-20 border rounded text-center" readonly>
                    </div>
                    <div class="col-span-2">
                        <input type="text" name="peserta[${peserta.id}][keterangan]" class="w-full border rounded px-2 py-1 text-sm" readonly>
                    </div>
                </div>
            `).join('');
            // Tambahkan event listener untuk perhitungan skor
            document.querySelectorAll('.peserta-select').forEach(select => {
                select.addEventListener('change', function() {
                    const group = this.getAttribute('data-group');
                    calculateScore(group);
                });
            });
            // Hitung skor awal untuk semua peserta
            pesertaList.forEach(peserta => {
                calculateScore(peserta.id);
            });
        }
        // Debugging
        console.log('Script loaded');
        const modal = document.getElementById('pesertaModal');
        const managePesertaBtn = document.getElementById('managePesertaBtn');
        // Debugging
        console.log('Modal element:', modal);
        console.log('Manage Peserta Button:', managePesertaBtn);
        if (managePesertaBtn) {
            managePesertaBtn.addEventListener('click', function() {
                console.log('Button clicked'); // Debugging
                if (modal) {
                    modal.classList.remove('hidden');
                    renderPesertaTable();
                } else {
                    console.error('Modal element not found');
                }
            });
        } else {
            console.error('Manage Peserta Button not found');
        }
        // Inisialisasi data peserta dari database
        let pesertaList = @json($defaultPeserta);
        // Fungsi untuk memperbarui jumlah peserta
        function updatePesertaCount() {
            const count = pesertaList.length;
            const pesertaCount = document.getElementById('pesertaCount');
            if (pesertaCount) {
                pesertaCount.textContent = count;
            }
        }
        // Fungsi untuk render tabel peserta yang diperbaiki
        function renderPesertaTable() {
            const tbody = document.getElementById('pesertaTableBody');
            if (!tbody) {
                console.error('Tbody element tidak ditemukan');
                return;
            }
            tbody.innerHTML = '';
            pesertaList.forEach((peserta, index) => {
                const row = document.createElement('tr');
                row.className = 'peserta-row hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-4 py-2 text-center">${index + 1}</td>
                    <td class="px-4 py-2">
                        <input type="text" 
                               value="${peserta.jabatan}" 
                               class="w-full border rounded px-2 py-1 jabatan-input focus:border-blue-500 focus:ring-1 focus:ring-blue-500" 
                               data-id="${peserta.id}"
                               placeholder="Masukkan Jabatan">
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button type="button" 
                                class="text-red-500 hover:text-red-700 delete-btn transition-colors duration-150"
                                onclick="deletePeserta(${peserta.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
            // Update jumlah peserta setiap kali tabel di-render
            updatePesertaCount();
        }
        // Fungsi untuk menghapus peserta dengan SweetAlert
        window.deletePeserta = function(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Peserta ini akan dihapus dari daftar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proses penghapusan
                    pesertaList = pesertaList.filter(p => p.id !== id);
                    renderPesertaTable();
                    renderPesertaForm();
                    // Tampilkan notifikasi sukses
                    Swal.fire(
                        'Terhapus!',
                        'Peserta berhasil dihapus.',
                        'success'
                    );
                }
            });
        }
        // Event listener untuk tombol tambah peserta
        document.getElementById('addPesertaBtn').addEventListener('click', function() {
            try {
                console.log('Add Participant button clicked'); // Debugging
                const newId = pesertaList.length > 0 
                    ? Math.max(...pesertaList.map(p => parseInt(p.id))) + 1 
                    : 1;
                pesertaList.push({
                    id: newId,
                    jabatan: 'Jabatan Baru'
                });
                console.log('New participant added:', { id: newId, jabatan: 'Jabatan Baru' }); // Debugging
                renderPesertaTable();
                // Scroll ke baris baru
                const tbody = document.getElementById('pesertaTableBody');
                const lastRow = tbody.lastElementChild;
                if (lastRow) {
                    lastRow.scrollIntoView({ behavior: 'smooth' });
                    // Focus pada input jabatan baru
                    const input = lastRow.querySelector('.jabatan-input');
                    if (input) {
                        input.focus();
                        input.select();
                    }
                }
            } catch (error) {
                console.error('Error menambah peserta:', error); // Logging error
                alert('Terjadi kesalahan saat menambah peserta');
            }
        });
        // Event listener untuk tombol simpan dengan AJAX
        document.getElementById('savePesertaBtn').addEventListener('click', async function() {
            try {
                // Debug data before sending
                console.log('Current pesertaList:', pesertaList);
                // Update pesertaList dari input fields dengan validasi data
                const updatedPesertaList = [];
                document.querySelectorAll('.jabatan-input').forEach(input => {
                    const id = parseInt(input.dataset.id);
                    const jabatan = input.value.trim();
                    // Validasi data
                    if (!jabatan) {
                        throw new Error('Jabatan tidak boleh kosong');
                    }
                    updatedPesertaList.push({
                        id: id,
                        jabatan: jabatan,
                        // Tambahkan flag untuk membedakan data baru dan existing
                        is_new: !pesertaList.find(p => p.id === id)?.id
                    });
                });
                console.log('Updated pesertaList:', updatedPesertaList);
                // Kirim data ke server
                const response = await fetch('{{ route("admin.peserta.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        peserta: updatedPesertaList,
                        _method: 'POST' // Explicitly set method
                    })
                });
                // Debug response
                console.log('Response Status:', response.status);
                const responseBody = await response.json();
                console.log('Response Body:', responseBody);
                if (!response.ok) {
                    throw new Error(responseBody.message || 'Gagal menyimpan perubahan');
                }
                // Update local pesertaList with server response
                pesertaList = responseBody.data || updatedPesertaList;
                // Render ulang form peserta
                renderPesertaForm();
                renderPesertaTable();
                // Sembunyikan modal
                document.getElementById('pesertaModal').classList.add('hidden');
                // Tampilkan pesan sukses
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data peserta berhasil disimpan',
                    showConfirmButton: true,
                    timer: 1500,
                    timerProgressBar: true
                });
            } catch (error) {
                console.error('Error details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Terjadi kesalahan saat menyimpan data',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#d33'
                });
            }
        });
        // Event listener untuk tombol tutup modal
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('pesertaModal').classList.add('hidden');
        });
        // Event listener untuk tombol kelola peserta
        document.getElementById('managePesertaBtn').addEventListener('click', function() {
            document.getElementById('pesertaModal').classList.remove('hidden');
            renderPesertaTable();
        });
        // Render awal
        renderPesertaTable();
        updatePesertaCount();
    });
</script>
@push('scripts')
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@push('styles')
<!-- Optional: Include SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css">
@endpush
