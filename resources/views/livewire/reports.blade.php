<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtrar Relatório</h3>
            <form wire:submit="generateReport">
                {{-- Linha 1 de Filtros --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Filtro Tipo de Relatório --}}
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700">Tipo de Relatório</label>
                        <select wire:model.live="reportType" id="report_type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="oficial">Viagens Oficiais</option>
                            <option value="particular">Entradas Particulares</option>
                        </select>
                    </div>
                    {{-- Filtro Data Início --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Data de Início</label>
                        <input type="date" wire:model="startDate" id="start_date"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('startDate')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    {{-- Filtro Data Fim --}}
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Data Final</label>
                        <input type="date" wire:model="endDate" id="end_date"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('endDate')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Linha 2 de Filtros --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    {{-- Filtro Veículo --}}
                    <div>
                        <label for="vehicle" class="block text-sm font-medium text-gray-700">Veículo (Opcional)</label>
                        <select wire:model="selectedVehicle" id="vehicle"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos os Veículos</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->model }}
                                    ({{ $vehicle->license_plate }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Filtro Motorista --}}
                    <div>
                        <label for="driver" class="block text-sm font-medium text-gray-700">Motorista
                            (Opcional)</label>
                        <select wire:model="selectedDriver" id="driver"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos os Motoristas</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <svg wire:loading wire:target="generateReport"
                            class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span wire:loading.remove>Gerar Relatório</span>
                        <span wire:loading>Gerando...</span>
                    </button>
                    @if ($reportType === 'oficial')
                        <a href="{{ route('reports.official.pdf', ['vehicle_id' => $selectedVehicle, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 @if (empty($selectedVehicle)) opacity-50 cursor-not-allowed @endif"
                            @if (empty($selectedVehicle)) onclick="event.preventDefault(); alert('Por favor, selecione um veículo para gerar o relatório oficial.');" @endif>
                            Exportar PDF
                        </a>
                    @else
                        <a href="{{ route('reports.private.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Exportar PDF
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela de Resultados --}}
    <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Resultados para: <span
                    class="font-bold text-blue-600">{{ $reportType === 'oficial' ? 'Viagens Oficiais' : 'Entradas Particulares' }}</span>
            </h3>

            @if ($reportType === 'oficial')
                {{-- Tabela para Viagens OFICIAIS --}}
                <div class="overflow-x-auto border border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Veículo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Motorista
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Período</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Distância
                                    (km)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($results as $trip)
                                <tr>
                                    <td class="px-4 py-4 align-middle">
                                        <div class="text-sm font-semibold">
                                            {{ $trip->vehicle?->model ?? 'Veículo Excluído' }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $trip->vehicle?->license_plate ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 align-middle text-sm">
                                        {{ $trip->driver?->name ?? 'Motorista Excluído' }}</td>
                                    <td class="px-4 py-4 align-middle text-sm">
                                        De {{ $trip->departure_datetime->format('d/m/Y H:i') }}<br>
                                        Até {{ $trip->arrival_datetime->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-4 align-middle text-sm font-semibold">
                                        {{ number_format($trip->arrival_odometer - $trip->departure_odometer, 0, ',', '.') }}
                                        km</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-gray-500">Nenhum resultado
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Tabela para Entradas PARTICULARES --}}
                <div class="overflow-x-auto border border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Veículo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Motorista
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                    Entrada/Saída</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Observação
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($results as $entry)
                                <tr>
                                    {{-- CORREÇÃO PRINCIPAL APLICADA AQUI --}}
                                    <td class="px-4 py-4 align-middle">
                                        @if ($entry->vehicle)
                                            {{-- Se o veículo é cadastrado, usa a relação --}}
                                            <div class="text-sm font-semibold">{{ $entry->vehicle->model }}</div>
                                            <div class="text-xs text-gray-500">{{ $entry->vehicle->license_plate }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-1 italic">Cadastrado</div>
                                        @else
                                            {{-- Se não, usa os dados da própria entrada --}}
                                            <div class="text-sm font-semibold">
                                                {{ $entry->vehicle_model ?? 'Não informado' }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $entry->license_plate ?? 'Não informada' }}</div>
                                            <div class="text-xs text-gray-400 mt-1 italic">Não Cadastrado</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 align-middle text-sm">
                                        {{ $entry->driver?->name ?? 'Motorista Excluído' }}</td>
                                    <td class="px-4 py-4 align-middle text-sm">
                                        Entrou:
                                        {{ \Carbon\Carbon::parse($entry->entry_at)->format('d/m/Y H:i') }}<br>
                                        @if ($entry->exit_at)
                                            Saiu: {{ \Carbon\Carbon::parse($entry->exit_at)->format('d/m/Y H:i') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 align-middle text-sm">{{ $entry->observation ?: 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-gray-500">Nenhum resultado
                                        encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Paginação --}}
            @if ($results instanceof \Illuminate\Pagination\LengthAwarePaginator && $results->hasPages())
                <div class="mt-4">
                    {{ $results->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
