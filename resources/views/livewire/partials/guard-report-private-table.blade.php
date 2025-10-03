<table class="w-full text-sm text-left text-gray-500">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
        <tr>
            <th class="px-6 py-3">Veículo</th>
            <th class="px-6 py-3">Condutor</th>
            <th class="px-6 py-3">Data/Hora Entrada</th>
        </tr>
    </thead>
    <tbody class="space-y-4 sm:space-y-0">
        @forelse ($privateEntries as $entry)
            <tr
                class="bg-white block sm:table-row p-4 mb-4 sm:p-0 sm:mb-0 border rounded-lg shadow-sm sm:border-b sm:rounded-none sm:shadow-none">
                <td class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                    <span class="font-bold text-gray-600 sm:hidden">Veículo</span> <span
                        class="text-right">{{ $entry->vehicle->model ?? 'N/A' }} <span
                            class="block text-xs">{{ $entry->vehicle->license_plate ?? 'N/A' }}</span></span></td>
                <td
                    class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                    <span class="font-bold text-gray-600 sm:hidden">Condutor</span> <span
                        class="text-right">{{ $entry->driver->name ?? 'N/A' }}</span></td>
                <td class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell"><span
                        class="font-bold text-gray-600 sm:hidden">Entrada</span> <span
                        class="text-right">{{ $entry->entry_at->format('d/m/Y H:i') }}</span></td>
            </tr>
        @empty
            <tr class="bg-white sm:border-b">
                <td colspan="3" class="px-6 py-4 text-center text-gray-500">Nenhum registo pendente.</td>
            </tr>
        @endforelse
    </tbody>
</table>
