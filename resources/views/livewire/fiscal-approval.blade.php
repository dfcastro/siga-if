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

                    {{-- Mensagens de Sessão --}}
                    @if (session()->has('message'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('message') }}</p>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        {{-- Adicionado para consistência --}}
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- Abas de Status --}}
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
                            {{-- Você pode adicionar um filtro 'rejected' aqui se implementar essa funcionalidade --}}
                        </nav>
                    </div>

                    {{-- Filtros de Tipo (Visível para Admin ou Fiscal 'both') --}}
                    @if (auth()->user()->role === 'admin' || auth()->user()->fiscal_type === 'both')
                        <div class="mb-4 flex space-x-4 text-sm"> {{-- Ajustado margin bottom --}}
                            <button wire:click.prevent="setTypeFilter('')"
                                class="{{ $typeFilter === '' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-indigo-600' }} transition duration-150 ease-in-out">Todos</button>
                            <button wire:click.prevent="setTypeFilter('official')"
                                class="{{ $typeFilter === 'official' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-indigo-600' }} transition duration-150 ease-in-out">Apenas
                                Oficiais</button>
                            <button wire:click.prevent="setTypeFilter('private')"
                                class="{{ $typeFilter === 'private' ? 'text-indigo-600 font-semibold' : 'text-gray-500 hover:text-indigo-600' }} transition duration-150 ease-in-out">Apenas
                                Particulares</button>
                        </div>
                    @endif

                    {{-- Tabela Principal de Submissões --}}
                    <div class="shadow-sm border border-gray-200 sm:rounded-lg overflow-hidden"> {{-- Estilo melhorado --}}
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 hidden sm:table-header-group">
                                <tr>
                                    <th class="px-6 py-3">Tipo</th>
                                    <th class="px-6 py-3">Porteiro</th>
                                    <th class="px-6 py-3">Período</th>
                                    {{-- Colunas dinâmicas baseadas no status --}}
                                    @if ($filterStatus === 'pending')
                                        <th class="px-6 py-3">Submetido em</th>
                                    @else
                                        {{-- approved (ou rejected no futuro) --}}
                                        <th class="px-6 py-3">Aprovado Por</th>
                                        <th class="px-6 py-3">Data Aprovação</th>
                                    @endif
                                    <th class="px-6 py-3 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 sm:divide-y-0"> {{-- Ajuste para mobile --}}
                                @forelse ($submissions as $submission)
                                    <tr
                                        class="bg-white block sm:table-row mb-4 sm:mb-0 border sm:border-0 rounded-lg sm:rounded-none shadow-sm sm:shadow-none">
                                        {{-- Melhorias mobile --}}

                                        {{-- Célula Tipo (Mobile + Desktop) --}}
                                        <td
                                            class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                            <span class="font-bold text-gray-600 sm:hidden mr-2">Tipo:</span>
                                            <span class="text-right">
                                                @if ($submission->type === 'official')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Oficial</span>
                                                    <div class="text-xs text-gray-500 sm:hidden mt-1">
                                                        {{ $submission->vehicle?->model }}
                                                        ({{ $submission->vehicle?->license_plate }})</div>
                                                    {{-- Info extra mobile --}}
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Particular</span>
                                                @endif
                                            </span>
                                        </td>

                                        {{-- Célula Porteiro --}}
                                        <td
                                            class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                            <span class="font-bold text-gray-600 sm:hidden mr-2">Porteiro:</span>
                                            <span
                                                class="text-right font-medium text-gray-900">{{ $submission->guardUser?->name ?? 'Usuário Removido' }}</span>
                                        </td>

                                        {{-- Célula Período --}}
                                        <td
                                            class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                            <span class="font-bold text-gray-600 sm:hidden mr-2">Período:</span>
                                            <span
                                                class="text-right">{{ $submission->start_date->format('M/Y') }}</span>
                                            {{-- Simplificado --}}
                                        </td>

                                        {{-- Células Dinâmicas --}}
                                        @if ($filterStatus === 'pending')
                                            <td
                                                class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                                <span class="font-bold text-gray-600 sm:hidden mr-2">Submetido:</span>
                                                <span
                                                    class="text-right text-xs">{{ $submission->submitted_at->diffForHumans() }}</span>
                                                {{-- Mais amigável --}}
                                            </td>
                                        @else
                                            <td
                                                class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                                <span class="font-bold text-gray-600 sm:hidden mr-2">Aprovado
                                                    Por:</span>
                                                <span
                                                    class="text-right">{{ $submission->fiscal?->name ?? 'N/A' }}</span>
                                            </td>
                                            <td
                                                class="flex justify-between items-center px-4 py-2 sm:px-6 sm:py-4 sm:table-cell border-b sm:border-b-0">
                                                <span class="font-bold text-gray-600 sm:hidden mr-2">Aprovação:</span>
                                                <span
                                                    class="text-right text-xs">{{ $submission->approved_at?->diffForHumans() ?? 'N/A' }}</span>
                                                {{-- Mais amigável --}}
                                            </td>
                                        @endif

                                        {{-- Célula Ações --}}
                                        <td class="px-4 py-3 sm:px-6 sm:py-4 sm:table-cell text-center sm:text-left">
                                            {{-- Ajustes de alinhamento --}}
                                            <x-secondary-button class="w-full sm:w-auto"
                                                wire:click="viewSubmission({{ $submission->id }})">
                                                Ver Detalhes
                                            </x-secondary-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white">
                                        <td colspan="{{ $filterStatus === 'pending' ? '5' : '6' }}"
                                            class="px-6 py-4 text-center text-gray-500"> {{-- Ajuste colspan --}}
                                            Nenhum relatório
                                            {{ $filterStatus === 'pending' ? 'pendente' : 'aprovado' }} encontrado para
                                            os filtros selecionados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginação --}}
                    <div class="mt-4">{{ $submissions->links() }}</div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Detalhes --}}
    <x-modal wire:model.defer="showDetailsModal" maxWidth="7xl">
        @if ($selectedSubmission)
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Detalhes do Relatório</h2>

                {{-- Informações Gerais --}}
                <div class="mb-6 pb-4 border-b border-gray-200 space-y-2 text-sm">
                    <div class="flex justify-between"><span><strong>Tipo:</strong></span>
                        @if ($selectedSubmission->type === 'official')
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Oficial</span>
                        @else
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Particular</span>
                        @endif
                    </div>
                    @if ($selectedSubmission->type === 'official' && $selectedSubmission->vehicle)
                        <div class="flex justify-between"><span><strong>Veículo:</strong></span>
                            <span>{{ $selectedSubmission->vehicle->model }}
                                ({{ $selectedSubmission->vehicle->license_plate }})</span></div>
                    @endif
                    <div class="flex justify-between"><span><strong>Porteiro:</strong></span>
                        <span>{{ $selectedSubmission->guardUser?->name ?? 'Usuário Removido' }}</span></div>
                    <div class="flex justify-between"><span><strong>Período:</strong></span>
                        <span>{{ $selectedSubmission->start_date->format('d/m/Y') }} a
                            {{ $selectedSubmission->end_date->format('d/m/Y') }}</span></div>
                    <div class="flex justify-between"><span><strong>Submetido em:</strong></span>
                        <span>{{ $selectedSubmission->submitted_at->format('d/m/Y H:i') }}</span></div>
                    @if ($selectedSubmission->status === 'approved')
                        <div class="flex justify-between text-green-700"><span><strong>Aprovado por:</strong></span>
                            <span>{{ $selectedSubmission->fiscal?->name ?? 'N/A' }} em
                                {{ $selectedSubmission->approved_at?->format('d/m/Y H:i') }}</span></div>
                    @endif
                    @if ($selectedSubmission->type === 'official' && $selectedSubmission->observation)
                        <div class="pt-2"><strong>Observação do Porteiro:</strong>
                            <p class="text-gray-600 italic bg-gray-50 p-2 rounded border mt-1">
                                {{ $selectedSubmission->observation }}</p>
                        </div>
                    @endif
                </div>

                {{-- Tabela de Entradas Particulares --}}
                @php $privateEntries = $submissionEntries->whereInstanceOf(\App\Models\PrivateEntry::class); @endphp
                @if ($privateEntries->isNotEmpty())
                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-800 mb-2 p-2 bg-blue-50 rounded-t-lg border-b">Registos
                            de Veículos Particulares:</h3>
                        <div class="relative overflow-x-auto sm:rounded-b-lg max-h-[60vh] overflow-y-auto border">
                            {{-- Altura máxima e borda --}}
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 z-10">
                                    {{-- Sticky header --}}
                                    <tr>
                                        <th class="px-4 py-2">Veículo (Placa)</th>
                                        <th class="px-4 py-2">Condutor</th>
                                        <th class="px-4 py-2">Entrada</th>
                                        <th class="px-4 py-2">Saída</th>
                                        <th class="px-4 py-2">Motivo</th>
                                        <th class="px-4 py-2">Porteiro (Saída)</th> {{-- Cabeçalho Corrigido --}}
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($privateEntries as $entry)
                                        <tr class="hover:bg-gray-50"> {{-- Efeito Hover --}}
                                            <td class="px-4 py-2 font-medium">
                                                {{ $entry->vehicle_model ?? 'N/A' }} <span
                                                    class="font-mono">({{ $entry->license_plate ?? '' }})</span>
                                            </td>
                                            <td class="px-4 py-2">{{ $entry->driver?->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                {{ $entry->entry_at?->format('d/m H:i') }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                {{ $entry->exit_at?->format('d/m H:i') ?? '-' }}</td>
                                            <td class="px-4 py-2">{{ $entry->entry_reason }}</td>
                                            {{-- ### CORREÇÃO APLICADA AQUI ### --}}
                                            <td class="px-4 py-2">{{ $entry->guardExit?->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Tabela de Viagens Oficiais --}}
                @php $officialTrips = $submissionEntries->whereInstanceOf(\App\Models\OfficialTrip::class); @endphp
                @if ($officialTrips->isNotEmpty())
                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-800 mb-2 p-2 bg-green-50 rounded-t-lg border-b">
                            Registos de Viagens Oficiais:</h3>
                        <div class="relative overflow-x-auto sm:rounded-b-lg max-h-[60vh] overflow-y-auto border">
                            {{-- Altura máxima e borda --}}
                            <table class="w-full text-sm text-left text-gray-500" style="table-layout: fixed;">
                                {{-- Layout fixo ajuda na largura das colunas --}}
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 z-10">
                                    {{-- Sticky header --}}
                                    <tr>
                                        {{-- Larguras ajustadas para melhor visualização --}}
                                        <th class="px-4 py-2 w-[18%]">Condutor</th>
                                        <th class="px-4 py-2 w-[22%]">Passageiros</th>
                                        <th class="px-4 py-2 w-[18%]">Saída</th>
                                        <th class="px-4 py-2 w-[18%]">Chegada</th>
                                        <th class="px-4 py-2 w-[10%] text-right">KM Rodado</th>
                                        <th class="px-4 py-2 w-[14%]">Destino</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($officialTrips as $trip)
                                        <tr class="hover:bg-gray-50"> {{-- Efeito Hover --}}
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                {{ $trip->driver?->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 break-words">{{ $trip->passengers ?: '-' }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                {{ $trip->departure_datetime?->format('d/m H:i') }}
                                                ({{ $trip->departure_odometer }} km)</td>
                                            <td class="px-4 py-2 whitespace-nowrap">
                                                {{ $trip->arrival_datetime?->format('d/m H:i') }}
                                                ({{ $trip->arrival_odometer }} km)</td>
                                            <td class="px-4 py-2 text-right font-medium">
                                                {{ $trip->distance_traveled ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 break-words">{{ $trip->destination }}</td>
                                            {{-- Adicionar colunas para Porteiros se necessário --}}
                                            {{-- <td class="px-4 py-2">{{ $trip->guardDeparture?->name ?? 'N/A' }}</td> --}}
                                            {{-- <td class="px-4 py-2">{{ $trip->guardArrival?->name ?? 'N/A' }}</td> --}}
                                        </tr>
                                        {{-- Linha opcional para observação de retorno --}}
                                        @if ($trip->return_observation)
                                            <tr class="bg-gray-50/50">
                                                <td colspan="6" class="px-4 py-1 text-xs italic text-gray-600">
                                                    <strong>Obs. Retorno:</strong> {{ $trip->return_observation }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-100 font-bold sticky bottom-0"> {{-- Sticky footer --}}
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-right text-gray-800 uppercase">
                                            Distância Total Rodada:</td>
                                        <td class="px-4 py-3 text-right font-mono text-gray-900">
                                            {{ number_format($totalDistance, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3">km</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Botões de Ação --}}
                <div class="mt-6 flex justify-end space-x-4">
                    <x-secondary-button wire:click="cancelView">Fechar</x-secondary-button>
                    @if ($selectedSubmission?->status === 'pending')
                        <x-primary-button wire:click="approveSubmission"
                            wire:confirm="Tem a certeza que deseja aprovar este relatório?"
                            wire:loading.attr="disabled" {{-- Desabilitar durante o loading --}} wire:target="approveSubmission"
                            {{-- Mostrar loading neste botão --}}>
                            <span wire:loading wire:target="approveSubmission">Aprovando...</span>
                            {{-- Texto durante loading --}}
                            <span wire:loading.remove wire:target="approveSubmission">Dar Visto e Arquivar</span>
                            {{-- Texto normal --}}
                        </x-primary-button>
                    @endif
                    {{-- Você pode adicionar um botão de 'Reprovar' aqui --}}
                </div>
            </div>
        @endif
    </x-modal>
</div>
