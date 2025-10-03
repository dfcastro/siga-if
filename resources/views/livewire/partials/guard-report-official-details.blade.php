<div>
    <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
        <div>
            @if ($selectedVehicleEntries->isNotEmpty())
                <h3 class="text-lg font-medium text-gray-900">
                    Preparar Relatório para: <span
                        class="text-indigo-600">{{ $selectedVehicleEntries->first()->vehicle->model }}
                        ({{ $selectedVehicleEntries->first()->vehicle->license_plate }})</span>
                </h3>
                <p class="text-sm text-gray-600">Encontrados {{ $selectedVehicleEntries->count() }} registos pendentes
                    para este veículo no período.</p>
            @endif
        </div>
        <x-secondary-button wire:click="$set('selectedVehicleId', null)">Voltar à Lista</x-secondary-button>
    </div>

    <div class="mb-6">
        <form action="{{ route('reports.submitGuardReport') }}" method="POST"
            onsubmit="return confirm('Tem a certeza que deseja submeter o relatório para este veículo?');">
            @csrf
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <input type="hidden" name="submission_type" value="official">
            <input type="hidden" name="vehicle_id" value="{{ $selectedVehicleId }}">
            <x-primary-button type="submit">
                Submeter Relatório do Veículo
            </x-primary-button>
        </form>
    </div>

    {{-- Tabela responsiva --}}
    <div class="shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
                <tr>
                    <th class="px-6 py-3">Condutor</th>
                    <th class="px-6 py-3">Saída</th>
                    <th class="px-6 py-3">Chegada</th>
                    <th class="px-6 py-3">Destino</th>
                    <th class="px-6 py-3">Passageiros</th>
                </tr>
            </thead>
            <tbody class="space-y-4 sm:space-y-0">
                @foreach ($selectedVehicleEntries as $trip)
                    <tr
                        class="bg-white flex flex-col sm:table-row p-4 mb-4 sm:p-0 sm:mb-0 border rounded-lg shadow-sm sm:border-b sm:rounded-none sm:shadow-none">
                        <td
                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Condutor</span> <span
                                class="text-right">{{ $trip->driver->name ?? 'N/A' }}</span></td>
                        <td
                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Saída</span> <span
                                class="text-right">{{ $trip->departure_datetime->format('d/m/Y H:i') }}</span></td>
                        <td
                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Chegada</span> <span
                                class="text-right">{{ $trip->arrival_datetime?->format('d/m/Y H:i') ?? '(Em viagem)' }}</span>
                        </td>
                        <td
                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                            <span class="font-bold text-gray-600 sm:hidden">Destino</span> <span
                                class="text-right">{{ $trip->destination }}</span></td>
                        <td class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell"><span
                                class="font-bold text-gray-600 sm:hidden">Passageiros</span> <span
                                class="text-right break-all">{{ $trip->passengers ?: 'N/A' }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
