<div>
    {{-- CABEÇALHO DA PÁGINA --}}
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        <h1 class="text-2xl font-medium text-gray-900">
            Status de Submissão de Relatórios
        </h1>
        <p class="mt-2 text-gray-600">
            Acompanhe aqui o status dos relatórios mensais submetidos.
        </p>
    </div>

    <div class="bg-gray-200 bg-opacity-25 p-6 lg:p-8">
        <div class="mb-4 flex justify-end">
            <div>
                <label for="year_filter" class="text-sm font-medium text-gray-700 sr-only">Ano:</label>
                <select wire:model.live="selectedYear" id="year_filter"
                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-ifnmg-green focus:border-ifnmg-green sm:text-sm rounded-md shadow-sm"
                    aria-label="Selecionar Ano">
                    @forelse ($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @empty
                        <option>{{ Carbon::now()->year }}</option>
                    @endforelse
                </select>
            </div>
        </div>
        @php
            // Lógica para decidir se as abas devem ser exibidas
            $user = Auth::user();
            $canSeeOfficial = $user->role !== 'fiscal' || in_array($user->fiscal_type, ['official', 'both']);
            $canSeePrivate = $user->role !== 'fiscal' || in_array($user->fiscal_type, ['private', 'both']);

            // ### ALTERAÇÃO: Porteiros não veem abas, vão direto para seus relatórios ###
            $showTabs = $canSeeOfficial && $canSeePrivate && $user->role !== 'porteiro';
        @endphp

        @if ($showTabs)
            <div class="border-b border-gray-200 mb-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button wire:click="$set('reportType', 'official')"
                        class="{{ $reportType === 'official' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Relatórios de Veículos Oficiais
                    </button>
                    <button wire:click="$set('reportType', 'private')"
                        class="{{ $reportType === 'private' ? 'border-ifnmg-green text-ifnmg-green' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Relatórios de Veículos Particulares
                    </button>
                </nav>
            </div>
        @endif

        <div>
            {{-- TÍTULO DINÂMICO CONFORME A ABA E O ANO --}}
            <div class="mb-6 pb-4 border-b border-gray-200">
                @if ($reportType === 'official')
                    <h2 class="text-xl font-semibold text-gray-800">
                        Relatórios da Frota Oficial
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Exibindo o status de submissão por veículo para o ano de <strong>{{ $selectedYear }}</strong>.
                    </p>
                @elseif ($reportType === 'private')
                    <h2 class="text-xl font-semibold text-gray-800">
                        Relatórios de Veículos Particulares
                    </h2>
                    @if (Auth::user()->role === 'porteiro')
                        <p class="mt-1 text-sm text-gray-600">
                            Exibindo o status das suas submissões para o ano de <strong>{{ $selectedYear }}</strong>.
                        </p>
                    @else
                        <p class="mt-1 text-sm text-gray-600">
                            Exibindo o status de submissão por porteiro para o ano de
                            <strong>{{ $selectedYear }}</strong>.
                        </p>
                    @endif
                @endif
            </div>
            {{-- LÓGICA PARA RELATÓRIOS OFICIAIS (Permanece igual) --}}
            @if ($reportType === 'official' && $canSeeOfficial && $user->role !== 'porteiro')
                <div class="space-y-4">
                    @forelse ($vehicles as $vehicle)
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="bg-gray-50 p-4 border-b">
                                <h3 class="font-bold text-gray-800">{{ $vehicle->model }}
                                    ({{ $vehicle->license_plate }})
                                </h3>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
                                @foreach ($months as $month)
                                    @php
                                        $monthKey = $month->format('Y-m');
                                        $submission = $submissions[$vehicle->id][$monthKey] ?? null;
                                        $status = $submission->status ?? 'not_submitted';

                                        $statusClasses = [
                                            'approved' => 'bg-green-100 text-green-800 border-green-200',
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                            'not_submitted' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        ];
                                        $statusLabels = [
                                            'approved' => 'Aprovado',
                                            'pending' => 'Pendente',
                                            'rejected' => 'Reprovado',
                                            'not_submitted' => 'Não Enviado',
                                        ];
                                    @endphp
                                    <div class="border rounded-md p-3 text-center {{ $statusClasses[$status] }}">
                                        <div class="font-semibold text-sm">{{ $month->translatedFormat('M/y') }}</div>
                                        <div class="text-xs mt-1">{{ $statusLabels[$status] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="py-4 px-6 text-center text-gray-500 bg-white rounded-lg shadow">Nenhum veículo
                            oficial para exibir.</div>
                    @endforelse
                </div>

                {{-- LÓGICA PARA RELATÓRIOS PARTICULARES --}}
            @elseif ($reportType === 'private' && $canSeePrivate)
                {{-- ### INÍCIO DA ALTERAÇÃO ### --}}

                {{-- Visão do Porteiro (igual à lógica antiga) --}}
                @if ($user->role === 'porteiro')
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="bg-gray-50 p-4 border-b">
                            <h3 class="font-bold text-gray-800">
                                Seus Relatórios de Veículos Particulares
                            </h3>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
                            @foreach ($months as $month)
                                @php
                                    $monthKey = $month->format('Y-m');
                                    $submission = $submissions[$monthKey] ?? null;
                                    $status = $submission->status ?? 'not_submitted';
                                    $statusClasses = [
                                        'approved' => 'bg-green-100 text-green-800 border-green-200',
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                        'not_submitted' => 'bg-gray-100 text-gray-600 border-gray-200',
                                    ];
                                    $statusLabels = [
                                        'approved' => 'Aprovado',
                                        'pending' => 'Pendente',
                                        'rejected' => 'Reprovado',
                                        'not_submitted' => 'Não Enviado',
                                    ];
                                @endphp
                                <div class="border rounded-md p-3 text-center {{ $statusClasses[$status] }}">
                                    <div class="font-semibold text-sm">{{ $month->translatedFormat('M/y') }}</div>
                                    <div class="text-xs mt-1">{{ $statusLabels[$status] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Visão do Admin/Fiscal (Nova lógica agrupada por porteiro) --}}
                @else
                    <div class="space-y-4">
                        @forelse ($porteiros as $porteiro)
                            <div class="bg-white rounded-lg shadow overflow-hidden">
                                <div class="bg-gray-50 p-4 border-b">
                                    <h3 class="font-bold text-gray-800">
                                        Porteiro: {{ $porteiro->name }}
                                    </h3>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 p-4">
                                    @foreach ($months as $month)
                                        @php
                                            $monthKey = $month->format('Y-m');
                                            // Busca a submissão para este porteiro específico
                                            $submission = $submissions[$porteiro->id][$monthKey] ?? null;
                                            $status = $submission->status ?? 'not_submitted';

                                            $statusClasses = [
                                                'approved' => 'bg-green-100 text-green-800 border-green-200',
                                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                                'not_submitted' => 'bg-gray-100 text-gray-600 border-gray-200',
                                            ];
                                            $statusLabels = [
                                                'approved' => 'Aprovado',
                                                'pending' => 'Pendente',
                                                'rejected' => 'Reprovado',
                                                'not_submitted' => 'Não Enviado',
                                            ];
                                        @endphp
                                        <div class="border rounded-md p-3 text-center {{ $statusClasses[$status] }}">
                                            <div class="font-semibold text-sm">{{ $month->translatedFormat('M/y') }}
                                            </div>
                                            <div class="text-xs mt-1">{{ $statusLabels[$status] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="py-4 px-6 text-center text-gray-500 bg-white rounded-lg shadow">
                                Nenhum porteiro encontrado no sistema.
                            </div>
                        @endforelse
                    </div>
                @endif
                {{-- ### FIM DA ALTERAÇÃO ### --}}

            @endif
        </div>
    </div>
</div>
