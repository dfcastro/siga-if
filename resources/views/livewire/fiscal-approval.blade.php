<div>
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2H7a2 2 0 01-2-2v-2">
                    </path>
                </svg>
                Vistos em Relatórios da Portaria
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Analise os relatórios mensais e registre sua ciência (visto) para arquivamento definitivo.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-6 shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-semibold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-t-xl shadow-sm border-b border-gray-200 px-2 sm:px-6 pt-2">
            <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto" aria-label="Tabs">
                <button wire:click="$set('activeTab', 'pending')"
                    class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 {{ $activeTab === 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    ⏳ Aguardando Visto
                </button>
                <button wire:click="$set('activeTab', 'approved')"
                    class="whitespace-nowrap py-4 px-3 border-b-2 font-bold text-sm sm:text-base transition-colors flex items-center gap-2 {{ $activeTab === 'approved' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    ✅ Visto Registrado (Arquivados)
                </button>
            </nav>
        </div>

        <div class="bg-white rounded-b-xl shadow-sm border border-t-0 border-gray-200 min-h-[400px] p-0 sm:p-6"
            wire:loading.class="opacity-50 pointer-events-none transition-opacity">

            {{-- DESKTOP --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mês
                                / Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Submetido Por</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Envio</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($submissions as $sub)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 uppercase tracking-wide">
                                        {{ \Carbon\Carbon::parse($sub->start_date)->translatedFormat('F/Y') }}</div>
                                    <div class="mt-1">
                                        @if ($sub->type === 'private')
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 border border-green-200">🛂
                                                Particulares</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">🚗
                                                Oficial ({{ $sub->vehicle?->license_plate ?? 'N/D' }})</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-800">
                                        {{ $sub->guardUser?->name ?? 'Usuário Removido' }}</div>
                                    @if ($activeTab !== 'pending')
                                        <div class="text-xs text-gray-500 mt-0.5">Visto por:
                                            {{ $sub->assignedFiscal?->name ?? 'N/D' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($sub->submitted_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    <button wire:click="viewDetails({{ $sub->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition shadow-sm">
                                        👁 Detalhes
                                    </button>
                                    @if ($activeTab === 'pending')
                                        <button wire:click="approve({{ $sub->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded text-white hover:bg-green-700 transition shadow-sm"
                                            title="Confirmar ciência dos dados">
                                            ✓ Dar Visto
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Nenhum relatório
                                    encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE --}}
            <div class="md:hidden divide-y divide-gray-100">
                @forelse ($submissions as $sub)
                    <div class="p-4 relative bg-white">
                        <div
                            class="absolute left-0 top-0 bottom-0 w-1 {{ $activeTab === 'pending' ? 'bg-yellow-400' : 'bg-green-500' }}">
                        </div>
                        <div class="pl-2">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-gray-900 uppercase">
                                    {{ \Carbon\Carbon::parse($sub->start_date)->translatedFormat('F / Y') }}</h3>
                                <div class="text-right">
                                    @if ($sub->type === 'private')
                                        <span
                                            class="inline-block bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded border border-green-200">PARTICULARES</span>
                                    @else
                                        <span
                                            class="inline-block bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-1 rounded border border-blue-200">OFICIAL
                                            ({{ $sub->vehicle?->license_plate ?? 'N/D' }})</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-sm text-gray-700 mb-2 space-y-1">
                                <p><span class="text-gray-400">👤 Porteiro:</span> <span
                                        class="font-semibold">{{ $sub->guardUser?->name ?? 'Usuário Removido' }}</span>
                                </p>
                                <p><span class="text-gray-400">📅 Envio:</span>
                                    {{ \Carbon\Carbon::parse($sub->submitted_at)->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="mt-4 flex flex-col gap-2">
                                <button wire:click="viewDetails({{ $sub->id }})"
                                    class="w-full flex justify-center items-center px-4 py-2 bg-gray-50 border border-gray-300 rounded-md text-sm font-bold text-gray-700 hover:bg-gray-100 transition shadow-sm">
                                    👁 Ver Detalhes
                                </button>

                                @if ($activeTab === 'pending')
                                    <button wire:click="approve({{ $sub->id }})"
                                        class="w-full flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md text-sm font-bold text-white hover:bg-green-700 transition shadow-sm">
                                        ✓ Dar Visto e Arquivar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 bg-gray-50 rounded-b-xl border-dashed border-t">Nenhum
                        relatório encontrado.</div>
                @endforelse
            </div>
            <div class="mt-4 px-4 pb-4 sm:px-0 sm:pb-0">{{ $submissions->links() }}</div>
        </div>
    </div>

    {{-- MODAL DE DETALHES --}}
    <x-modal wire:model.live="isDetailsModalOpen" maxWidth="5xl">
        <div class="px-4 sm:px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">
                Detalhes do Relatório
                @if ($selectedSubmission)
                    <span class="text-sm font-normal text-gray-500 ml-2 block sm:inline">
                        ({{ \Carbon\Carbon::parse($selectedSubmission->start_date)->translatedFormat('F/Y') }})
                    </span>
                @endif
            </h3>
            <button wire:click="closeDetailsModal" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <div class="p-0 sm:p-6 bg-white max-h-[70vh] overflow-y-auto">
            @if ($selectedSubmission)

                {{-- INFORMAÇÕES GERAIS E PESQUISA --}}
                <div
                    class="p-4 sm:mb-6 bg-gray-50 border-b sm:border border-gray-200 sm:rounded-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="text-sm flex flex-col sm:flex-row gap-4 sm:gap-8">
                        <div><span class="font-bold text-gray-500 uppercase text-xs block">Porteiro</span>
                            {{ $selectedSubmission->guardUser?->name ?? 'Usuário Removido' }}</div>
                        <div><span class="font-bold text-gray-500 uppercase text-xs block">Data de Envio</span>
                            {{ \Carbon\Carbon::parse($selectedSubmission->submitted_at)->format('d/m/Y H:i') }}</div>
                        @if ($selectedSubmission->observation)
                            <div class="sm:col-span-2"><span
                                    class="font-bold text-gray-500 uppercase text-xs block">Observação do
                                    Porteiro</span> {{ $selectedSubmission->observation }}</div>
                        @endif
                    </div>

                    {{-- NOVA BARRA DE PESQUISA --}}
                    <div class="w-full md:w-64 relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="detailSearch" type="text"
                            placeholder="Buscar placa, motorista..."
                            class="block w-full border-gray-300 rounded-lg pl-9 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                    </div>
                </div>

                {{-- DESKTOP DETAILS (COM ESPAÇAMENTO IN/OUT CORRIGIDO) --}}
                <div
                    class="hidden md:block overflow-x-auto border border-gray-200 rounded-lg shadow-sm relative min-h-[100px]">
                    <div wire:loading wire:target="detailSearch"
                        class="absolute inset-0 bg-white bg-opacity-70 flex items-center justify-center z-10">
                        <svg class="animate-spin h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            @if ($selectedSubmission->type === 'private')
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Veículo
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Condutor
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">
                                        Entrada/Saída</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Motivo
                                    </th>
                                </tr>
                            @else
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Veículo
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Condutor
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Período /
                                        Destino</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Distância
                                    </th>
                                </tr>
                            @endif
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($details as $detail)
                                <tr class="hover:bg-gray-50">
                                    @if ($selectedSubmission->type === 'private')
                                        <td class="px-4 py-3 text-sm">
                                            <div class="font-bold text-gray-900">
                                                {{ $detail->vehicle?->model ?? 'N/D' }}</div>
                                            <div class="font-mono text-gray-500 text-xs">
                                                {{ $detail->vehicle?->license_plate ?? 'N/D' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-800">
                                            {{ $detail->driver?->name ?? 'N/D' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <div class="flex items-center gap-1"><span
                                                    class="w-8 inline-block text-green-500 font-bold text-xs">IN</span>
                                                {{ $detail->entry_at ? $detail->entry_at->format('d/m/y H:i') : '-' }}
                                            </div>
                                            <div class="flex items-center gap-1 mt-0.5"><span
                                                    class="w-8 inline-block text-red-500 font-bold text-xs">OUT</span>
                                                {{ $detail->exit_at ? $detail->exit_at->format('d/m/y H:i') : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $detail->entry_reason }}</td>
                                    @else
                                        <td class="px-4 py-3 text-sm">
                                            <div class="font-bold text-gray-900">
                                                {{ $detail->vehicle?->model ?? 'N/D' }}</div>
                                            <div class="font-mono text-gray-500 text-xs">
                                                {{ $detail->vehicle?->license_plate ?? 'N/D' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-800">
                                            {{ $detail->driver?->name ?? 'N/D' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <div class="font-medium text-gray-800 mb-1">{{ $detail->destination }}
                                            </div>
                                            <div class="flex items-center gap-1 text-xs"><span
                                                    class="w-8 inline-block text-blue-500 font-bold">OUT</span>
                                                {{ $detail->departure_datetime ? \Carbon\Carbon::parse($detail->departure_datetime)->format('d/m H:i') : '-' }}
                                            </div>
                                            <div class="flex items-center gap-1 mt-0.5 text-xs"><span
                                                    class="w-8 inline-block text-green-500 font-bold">IN</span>
                                                {{ $detail->arrival_datetime ? \Carbon\Carbon::parse($detail->arrival_datetime)->format('d/m H:i') : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span
                                                class="bg-gray-100 font-bold text-gray-700 px-2 py-1 rounded text-xs">{{ number_format(($detail->arrival_odometer ?? 0) - ($detail->departure_odometer ?? 0), 0, ',', '.') }}
                                                km</span>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-gray-500">Nenhum registo encontrado
                                        com a sua pesquisa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE DETAILS --}}
                <div class="md:hidden divide-y divide-gray-100 relative min-h-[100px]">
                    <div wire:loading wire:target="detailSearch"
                        class="absolute inset-0 bg-white bg-opacity-70 flex items-center justify-center z-10">
                        <svg class="animate-spin h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    @forelse ($details as $detail)
                        <div class="p-4 bg-white">
                            @if ($selectedSubmission->type === 'private')
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-gray-900 leading-none">
                                            {{ $detail->vehicle?->model ?? 'N/D' }}</h4>
                                        <p class="text-xs font-mono text-gray-500 mt-1">
                                            {{ $detail->vehicle?->license_plate ?? 'N/D' }}</p>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-700 mb-2 border-l-2 border-green-400 pl-2 ml-1">
                                    <span class="text-gray-400 text-xs">Motorista:</span> <span
                                        class="font-medium">{{ $detail->driver?->name ?? 'N/D' }}</span>
                                    <br><span class="text-gray-400 text-xs">Motivo:</span>
                                    {{ Str::limit($detail->entry_reason, 35) }}
                                </div>
                                <div
                                    class="bg-gray-50 rounded p-2 flex justify-between text-xs font-medium text-gray-600">
                                    <span><span class="text-green-500 mr-1">IN:</span>
                                        {{ $detail->entry_at ? $detail->entry_at->format('d/m H:i') : '-' }}</span>
                                    <span><span class="text-red-500 mr-1">OUT:</span>
                                        {{ $detail->exit_at ? $detail->exit_at->format('d/m H:i') : '-' }}</span>
                                </div>
                            @else
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-gray-900 leading-none">
                                            {{ $detail->vehicle?->model ?? 'N/D' }}</h4>
                                        <p class="text-xs font-mono text-gray-500 mt-1">
                                            {{ $detail->vehicle?->license_plate ?? 'N/D' }}</p>
                                    </div>
                                    <span
                                        class="bg-gray-100 font-bold text-gray-700 px-2 py-1 rounded text-[10px] border border-gray-200">
                                        {{ number_format(($detail->arrival_odometer ?? 0) - ($detail->departure_odometer ?? 0), 0, ',', '.') }}
                                        km
                                    </span>
                                </div>
                                <div class="text-sm text-gray-700 mb-2 border-l-2 border-blue-400 pl-2 ml-1">
                                    <span class="font-medium block mb-0.5">{{ $detail->destination }}</span>
                                    <span class="text-gray-500 text-xs">Condutor:</span> <span
                                        class="font-medium text-xs">{{ $detail->driver?->name ?? 'N/D' }}</span>
                                </div>
                                <div
                                    class="bg-gray-50 rounded p-2 flex justify-between text-xs font-medium text-gray-600">
                                    <span><span class="text-red-500 mr-1">OUT:</span>
                                        {{ $detail->departure_datetime ? \Carbon\Carbon::parse($detail->departure_datetime)->format('d/m H:i') : '-' }}</span>
                                    <span><span class="text-green-500 mr-1">IN:</span>
                                        {{ $detail->arrival_datetime ? \Carbon\Carbon::parse($detail->arrival_datetime)->format('d/m H:i') : '-' }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">Nenhum registo encontrado com a sua pesquisa.</div>
                    @endforelse
                </div>

            @endif
        </div>

        <div
            class="px-4 sm:px-6 py-4 bg-gray-50 border-t flex flex-col sm:flex-row justify-end items-center gap-3 rounded-b-lg">
            <button wire:click="closeDetailsModal"
                class="w-full sm:w-auto px-6 py-2.5 bg-white border border-gray-300 rounded-md text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                Fechar
            </button>
            @if ($selectedSubmission && $selectedSubmission->status === 'pending')
                <button wire:click="approve({{ $selectedSubmission->id }})"
                    class="w-full sm:w-auto px-6 py-2.5 bg-green-600 border border-transparent rounded-md text-sm font-bold text-white hover:bg-green-700 shadow-sm transition">
                    ✓ Dar Visto e Arquivar
                </button>
            @endif
        </div>
    </x-modal>
</div>
