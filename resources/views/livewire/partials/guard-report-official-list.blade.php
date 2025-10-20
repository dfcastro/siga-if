<div>
    <table class="w-full text-sm text-left text-gray-500">
        {{-- CABEÇALHO PARA DESKTOP --}}
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
            <tr>
                <th class="px-6 py-3">Modelo</th>
                <th class="px-6 py-3">Placa</th>
                <th class="px-6 py-3">Saída Mais Antiga</th>
                <th class="px-6 py-3 text-center">Registos Pendentes</th>
                <th class="px-6 py-3 text-right">Ações</th>
            </tr>
        </thead>
        <tbody class="space-y-4 sm:space-y-0">
            @forelse ($vehiclesWithOfficialTrips as $item)
                @if (isset($item['vehicle']))
                    <tr
                        class="bg-white block sm:table-row p-4 mb-4 sm:p-0 sm:mb-0 border rounded-lg shadow-sm sm:border-b sm:rounded-none sm:shadow-none">
                        
                        {{-- Célula Modelo --}}
                        <td class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Modelo</span>
                            <span class="text-right font-medium text-gray-800">{{ $item['vehicle']->model }}</span>
                        </td>

                        {{-- Célula Placa --}}
                        <td class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Placa</span>
                            <span class="text-right font-mono text-gray-600">{{ $item['vehicle']->license_plate }}</span>
                        </td>

                        {{-- Célula Saída Mais Antiga --}}
                        <td class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Saída Mais Antiga</span>
                            <span class="text-right text-gray-600">
                                {{ \Carbon\Carbon::parse($item['oldest_trip_date'])->format('d/m/Y H:i') }}
                            </span>
                        </td>

                        {{-- Célula Registos Pendentes --}}
                        <td
                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell text-left sm:text-center border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Registos Pendentes</span>
                            <span class="text-right font-semibold text-gray-800">{{ $item['count'] }}</span>
                        </td>
                        
                        {{-- Célula Ações --}}
                        <td class="pt-3 sm:py-4 sm:px-6 sm:table-cell sm:text-right">
                            <x-secondary-button class="w-full sm:w-auto"
                                wire:click="selectVehicle({{ $item['vehicle']->id }})">Preparar Relatório</x-secondary-button>
                        </td>
                    </tr>
                @endif
            @empty
                <tr class="bg-white sm:border-b">
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum veículo oficial com registos
                        pendentes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Links da Paginação --}}
    <div class="mt-4">
        @if ($officialTrips)
            {{ $officialTrips->links() }}
        @endif
    </div>
</div>