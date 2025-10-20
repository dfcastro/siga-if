<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Aprovação e Arquivo de Relatórios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session()->has('message'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('message') }}</p>
                        </div>
                    @endif

                    <div class="border-b border-gray-200 mb-4">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button wire:click.prevent="setFilter('pending')"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $filterStatus === 'pending' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Pendentes
                            </button>
                            <button wire:click.prevent="setFilter('approved')"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $filterStatus === 'approved' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Aprovados (Arquivo)
                            </button>
                        </nav>
                    </div>
                    @if (auth()->user()->role === 'admin' || auth()->user()->fiscal_type === 'both')
                        <div class="mt-4 flex space-x-4 text-sm">
                            <button wire:click.prevent="setTypeFilter('')"
                                class="{{ $typeFilter === '' ? 'text-indigo-600 font-semibold' : 'text-gray-500' }}">Todos</button>
                            <button wire:click.prevent="setTypeFilter('official')"
                                class="{{ $typeFilter === 'official' ? 'text-indigo-600 font-semibold' : 'text-gray-500' }}">Apenas
                                Oficiais</button>
                            <button wire:click.prevent="setTypeFilter('private')"
                                class="{{ $typeFilter === 'private' ? 'text-indigo-600 font-semibold' : 'text-gray-500' }}">Apenas
                                Particulares</button>
                        </div>
                    @endif
                    <div class="shadow-md sm:rounded-lg overflow-hidden">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
                                <tr>
                                    <th class="px-6 py-3">Porteiro</th>
                                    <th class="px-6 py-3">Período</th>
                                    @if ($filterStatus === 'pending')
                                        <th class="px-6 py-3">Submissão</th>
                                    @else
                                        <th class="px-6 py-3">Aprovado Por</th>
                                        <th class="px-6 py-3">Aprovação</th>
                                    @endif
                                    <th class="px-6 py-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="space-y-4 sm:space-y-0">
                                @forelse ($submissions as $submission)
                                    <tr
                                        class="bg-white block sm:table-row p-4 mb-4 sm:p-0 sm:mb-0 border rounded-lg shadow-sm sm:border-b sm:rounded-none sm:shadow-none">
                                        <td
                                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                            <span class="font-bold text-gray-600 sm:hidden">Tipo</span>
                                            <span class="text-right">
                                                @if ($submission->type === 'official')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Oficial</span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Particular</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td
                                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                            <span class="font-bold text-gray-600 sm:hidden">Porteiro</span> <span
                                                class="text-right font-medium text-gray-900">{{ $submission->guardUser?->name ?? 'Usuário Removido' }}</span>
                                        </td>
                                        <td
                                            class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                            <span class="font-bold text-gray-600 sm:hidden">Período</span> <span
                                                class="text-right">{{ $submission->start_date->format('d/m/Y') }} a
                                                {{ $submission->end_date->format('d/m/Y') }}</span>
                                        </td>
                                        @if ($filterStatus === 'pending')
                                            <td
                                                class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                                <span class="font-bold text-gray-600 sm:hidden">Submissão</span> <span
                                                    class="text-right">{{ $submission->submitted_at->format('d/m/Y H:i') }}</span>
                                            </td>
                                        @else
                                            <td
                                                class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                                <span class="font-bold text-gray-600 sm:hidden">Aprovado Por</span>
                                                <span
                                                    class="text-right">{{ $submission->fiscal?->name ?? 'N/A' }}</span>
                                            </td>
                                            <td
                                                class="flex justify-between items-center py-2 sm:py-4 sm:px-6 sm:table-cell border-b sm:border-none">
                                                <span class="font-bold text-gray-600 sm:hidden">Aprovação</span> <span
                                                    class="text-right">{{ $submission->approved_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                            </td>
                                        @endif
                                        <td class="pt-3 sm:pt-0 sm:py-4 sm:px-6 sm:table-cell"><x-secondary-button
                                                class="w-full sm:w-auto"
                                                wire:click="viewSubmission({{ $submission->id }})">Ver
                                                Detalhes</x-secondary-button></td>
                                    </tr>
                                @empty
                                    <tr class="bg-white sm:border-b">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum relatório
                                            {{ $filterStatus === 'pending' ? 'pendente' : 'aprovado' }} encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $submissions->links() }}</div>

                </div>
            </div>
        </div>
    </div>

    <x-modal wire:model.defer="showDetailsModal" maxWidth="7xl">
        @if ($selectedSubmission)
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Detalhes do Relatório</h2>
                <div class="mt-4 space-y-2 text-sm">
                    <p><strong>Porteiro:</strong> {{ $selectedSubmission->guardUser?->name ?? 'Usuário Removido' }}</p>
                    <p><strong>Período:</strong> {{ $selectedSubmission->start_date->format('d/m/Y') }} a
                        {{ $selectedSubmission->end_date->format('d/m/Y') }}</p>
                    @if ($selectedSubmission->status === 'approved')
                        <p class="mt-2 text-green-700"><strong>Aprovado por:</strong>
                            {{ $selectedSubmission->fiscal?->name ?? 'N/A' }} em
                            {{ $selectedSubmission->approved_at?->format('d/m/Y H:i') }}</p>
                    @endif
                </div>

                @php $privateEntries = $submissionEntries->whereInstanceOf(\App\Models\PrivateEntry::class); @endphp
                @if ($privateEntries->isNotEmpty())
                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-800 mb-2 p-2 bg-blue-50 rounded-t-lg border-b">Registos
                            de Veículos Particulares:</h3>
                        <div class="relative overflow-x-auto sm:rounded-b-lg max-h-[70vh] overflow-y-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2">Veículo</th>
                                        <th class="px-4 py-2">Condutor</th>
                                        <th class="px-4 py-2">Período (Entrada → Saída)</th>
                                        <th class="px-4 py-2">Motivo</th>
                                        <th class="px-4 py-2">Porteiro (Saída)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($privateEntries as $entry)
                                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                            <td class="px-4 py-2 font-medium">{{ $entry->vehicle?->model ?? 'N/A' }}
                                                ({{ $entry->vehicle?->license_plate ?? '' }})
                                            </td>
                                            <td class="px-4 py-2">{{ $entry->driver?->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                {{ $entry->entry_at?->format('d/m H:i') }} →
                                                {{ $entry->exit_at?->format('d/m H:i') ?? '(No pátio)' }}</td>
                                            <td class="px-4 py-2">{{ $entry->entry_reason }}</td>
                                            <td class="px-4 py-2">{{ $entry->guard_on_exit ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @php $officialTrips = $submissionEntries->whereInstanceOf(\App\Models\OfficialTrip::class); @endphp
                @if ($officialTrips->isNotEmpty())
                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-800 mb-2 p-2 bg-green-50 rounded-t-lg border-b">
                            Registos de Veículos Oficiais:</h3>
                        <div class="relative overflow-x-auto sm:rounded-b-lg max-h-[70vh] overflow-y-auto">
                            <table class="w-full text-sm text-left text-gray-500" style="table-layout: fixed;">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2 w-[15%]">Veículo</th>
                                        <th class="px-4 py-2 w-[15%]">Condutor</th>
                                        <th class="px-4 py-2 w-[20%]">Passageiros</th>
                                        <th class="px-4 py-2 w-[20%]">Período (Saída → Chegada)</th>
                                        <th class="px-4 py-2 w-[15%]">KM (S/C/R)</th>
                                        <th class="px-4 py-2 w-[15%]">Destino</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($officialTrips as $trip)
                                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                            <td class="px-4 py-2 font-medium whitespace-nowrap">
                                                {{ $trip->vehicle?->model ?? 'N/A' }}
                                                ({{ $trip->vehicle?->license_plate ?? '' }})
                                            </td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $trip->driver?->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-4 py-2 break-words">{{ $trip->passengers ?: 'N/A' }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                {{ $trip->departure_datetime?->format('d/m H:i') }} →
                                                {{ $trip->arrival_datetime?->format('d/m H:i') ?? '(Em Viagem)' }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $trip->departure_odometer }} /
                                                {{ $trip->arrival_odometer }} / <span
                                                    class="font-bold">{{ $trip->arrival_datetime ? $trip->arrival_odometer - $trip->departure_odometer : 'N/A' }}</span>
                                            </td>
                                            <td class="px-4 py-2 break-words">{{ $trip->destination }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-100 font-bold sticky bottom-0">
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-right text-gray-800 uppercase">
                                            Distância Total Rodada:</td>
                                        <td colspan="2" class="px-4 py-3 text-left font-mono text-gray-900">
                                            {{ number_format($totalDistance, 0, ',', '.') }} km
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        {{-- LEGENDA ADICIONADA AQUI --}}
                        <div class="text-xs text-gray-500 mt-2 px-1">
                            <strong>* Legenda KM (S/C/R):</strong> Saída / Chegada / Rodado
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex justify-end space-x-4">
                    <x-secondary-button wire:click="cancelView">Fechar</x-secondary-button>
                    @if ($selectedSubmission?->status === 'pending')
                        <x-primary-button wire:click="approveSubmission"
                            wire:confirm="Tem a certeza que deseja aprovar este relatório?">Dar Visto e
                            Arquivar</x-primary-button>
                    @endif
                </div>
            </div>
        @endif
    </x-modal>
</div>
