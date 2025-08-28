@php
    $color = match(strtolower($status)) {
        'closed' => 'bg-green-100 text-green-800',
        'open' => 'bg-yellow-100 text-yellow-800',
        'overdue' => 'bg-red-100 text-red-800',
        'proses' => 'bg-blue-100 text-blue-800',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp
<span class="inline-block px-2 py-1 rounded text-xs font-semibold {{ $color }}">{{ ucfirst($status) }}</span>
