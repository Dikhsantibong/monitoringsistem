<tbody class="divide-y divide-gray-200">
    @forelse($powerPlants as $index => $unit)
    <tr class="hover:bg-gray-50 transition-colors">
        <td class="text-center py-2 whitespace-nowrap border border-gray-300">
            {{ ($powerPlants->currentPage() - 1) * $powerPlants->perPage() + $loop->iteration }}
        </td>
        <!-- ... kolom lainnya ... -->
    </tr>
    @empty
    <tr>
        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
            Tidak ada data unit yang tersedia
        </td>
    </tr>
    @endforelse
</tbody> 