<table class="w-full text-sm text-left text-gray-500">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
        <tr>
            <th class="px-6 py-3">Veículo Oficial</th>
            <th class="px-6 py-3 text-center">Registos Pendentes</th>
            <th class="px-6 py-3">Ações</th>
        </tr>
    </thead>
    <tbody class="space-y-4 sm:space-y-0">
        @forelse ($vehiclesWithOfficialTrips as $item)
            <tr
                class="bg-white block sm:table-row p-4 mb-4 sm:p-0 sm:mb-0 border rounded-lg shadow-sm sm:border-b sm:rounded-none sm:shadow-none">
                <td class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                    <span class="font-bold text-gray-600 sm:hidden">Veículo</span> <span
                        class="text-right">{{ $item['vehicle']->model }} <span
                            class="block text-xs">{{ $item['vehicle']->license_plate }}</span></span></td>
                <td
                    class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell text-left sm:text-center border-b sm:border-none">
                    <span class="font-bold text-gray-600 sm:hidden">Registos Pendentes</span> <span
                        class="text-right">{{ $item['count'] }}</span></td>
                <td class="pt-3 sm:py-4 sm:px-6 sm:table-cell"><x-secondary-button class="w-full sm:w-auto"
                        wire:click="selectVehicle({{ $item['vehicle']->id }})">Preparar Relatório</x-secondary-button>
                </td>
            </tr>
        @empty
            <tr class="bg-white sm:border-b">
                <td colspan="3" class="px-6 py-4 text-center text-gray-500">Nenhum veículo oficial com registos
                    pendentes.</td>
            </tr>
        @endforelse
    </tbody>
</table>
