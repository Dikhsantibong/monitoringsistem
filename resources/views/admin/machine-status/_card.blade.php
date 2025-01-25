@foreach($powerPlants as $powerPlant)
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">{{ $powerPlant->name }}</h3>
        </div>
        
        @foreach($powerPlant->machines as $machine)
            @php
                $log = $machine->logs->first();
                $statusClass = $log && $log->status ? 
                    ($log->status === 'Operasi' ? 'bg-green-100 text-green-800' : 
                    ($log->status === 'Gangguan' ? 'bg-red-100 text-red-800' : 
                    'bg-yellow-100 text-yellow-800')) : 'bg-gray-100 text-gray-800';
            @endphp
            
            <div class="border rounded-lg p-4 mb-4 last:mb-0">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">{{ $machine->name }}</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }} mt-1">
                            {{ $log->status ?? 'Tidak ada status' }}
                        </span>
                        
                        @if($log)
                            <div class="mt-4 space-y-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Deskripsi:</p>
                                    <p class="text-sm text-gray-800">{{ $log->deskripsi ?: '-' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Action Plan:</p>
                                    <p class="text-sm text-gray-800">{{ $log->action_plan ?: '-' }}</p>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Tanggal Mulai:</p>
                                        <p class="text-sm text-gray-800">
                                            {{ $log->tanggal_mulai ? $log->tanggal_mulai->format('d/m/Y') : '-' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Target Selesai:</p>
                                        <p class="text-sm text-gray-800">
                                            {{ $log->target_selesai ? $log->target_selesai->format('d/m/Y') : '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mt-2">Tidak ada data log untuk mesin ini</p>
                        @endif
                    </div>
                    
                    @if($log && $log->image_url)
                        <div class="ml-4 flex-shrink-0">
                            <img src="{{ asset('storage/' . $log->image_url) }}" 
                                 alt="Gambar Mesin" 
                                 class="w-32 h-32 object-cover rounded-lg cursor-pointer shadow-sm hover:shadow-md transition-shadow"
                                 onclick="showLargeImage('{{ asset('storage/' . $log->image_url) }}')"
                                 title="Klik untuk memperbesar">
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endforeach

@push('scripts')
<script>
function showLargeImage(imageUrl) {
    Swal.fire({
        imageUrl: imageUrl,
        imageWidth: '80%',
        imageHeight: '80vh',
        imageAlt: 'Gambar Mesin',
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
            image: 'object-contain max-h-[80vh]'
        }
    });
}
</script>
@endpush 