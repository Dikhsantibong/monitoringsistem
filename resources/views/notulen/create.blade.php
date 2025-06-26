@extends('layouts.app')

@section('styles')
<style>
    .notulen-form {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .notulen-header {
        border: 1px solid #ddd;
        padding: 1rem;
        margin-bottom: 2rem;
        border-radius: 4px;
    }

    .notulen-header table {
        width: 100%;
        border-collapse: collapse;
    }

    .notulen-header td {
        padding: 0.5rem;
        vertical-align: top;
    }

    .notulen-header td:first-child {
        width: 200px;
        font-weight: 500;
    }

    .notulen-content {
        margin-top: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #374151;
    }

    .form-textarea {
        width: 100%;
        min-height: 150px;
        padding: 0.75rem;
        border: 1px solid #D1D5DB;
        border-radius: 0.375rem;
        resize: vertical;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #0095B7;
        ring: 2px solid #0095B7;
    }

    .btn-submit {
        background-color: #0095B7;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background-color: #007a94;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <form action="{{ route('notulen.store') }}" method="POST" class="notulen-form">
        @csrf
        <input type="hidden" name="nomor_urut" value="{{ request('nomor_urut') }}">
        <input type="hidden" name="unit" value="{{ request('unit') }}">
        <input type="hidden" name="bidang" value="{{ request('bidang') }}">
        <input type="hidden" name="sub_bidang" value="{{ request('sub_bidang') }}">
        <input type="hidden" name="bulan" value="{{ request('bulan') }}">
        <input type="hidden" name="tahun" value="{{ request('tahun') }}">

        <div class="notulen-header">
            <table>
                <tr>
                    <td>Pimpinan Rapat</td>
                    <td>: <input type="text" name="pimpinan_rapat" class="border p-1 w-full" required></td>
                </tr>
                <tr>
                    <td>Tempat</td>
                    <td>: <input type="text" name="tempat" class="border p-1 w-full" required></td>
                </tr>
                <tr>
                    <td>Agenda</td>
                    <td>: <input type="text" name="agenda" class="border p-1 w-full" required></td>
                </tr>
                <tr>
                    <td>Peserta</td>
                    <td>: <input type="text" name="peserta" class="border p-1 w-full" required></td>
                </tr>
                <tr>
                    <td>Hari/Tanggal</td>
                    <td>: <input type="date" name="tanggal" class="border p-1" required></td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>: <input type="time" name="waktu_mulai" class="border p-1" required> - <input type="time" name="waktu_selesai" class="border p-1" required> WIB</td>
                </tr>
            </table>
        </div>

        <div class="notulen-content">
            <div class="form-group">
                <label class="form-label">A. Pembahasan</label>
                <textarea name="pembahasan" class="form-textarea" required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">B. Tindak Lanjut</label>
                <textarea name="tindak_lanjut" class="form-textarea" required></textarea>
            </div>

            <div class="mt-6 flex justify-between">
                <div>
                    <p class="font-medium">Mengetahui,</p>
                    <p class="mt-4">Pimpinan Rapat</p>
                    <div class="mt-20">
                        <input type="text" name="pimpinan_rapat_nama" class="border-b border-black text-center" style="min-width: 200px;" required>
                    </div>
                </div>

                <div class="text-right">
                    <p>Surabaya, <input type="date" name="tanggal_tanda_tangan" class="border-b border-black" required></p>
                    <p class="mt-4">Notulis</p>
                    <div class="mt-20">
                        <input type="text" name="notulis_nama" class="border-b border-black text-center" style="min-width: 200px;" required>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <button type="submit" class="btn-submit">
                    Simpan Notulen
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
