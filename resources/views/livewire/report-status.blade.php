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

        @php
            // Lógica para decidir se as abas devem ser exibidas
            $user = Auth::user();
            $canSeeOfficial = $user->role !== 'fiscal' || in_array($user->fiscal_type, ['official', 'both']);
            $canSeePrivate = $user->role !== 'fiscal' || in_array($user->fiscal_type, ['private', 'both']);
            $showTabs = $canSeeOfficial && $canSeePrivate;
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
            {{-- LÓGICA PARA RELATÓRIOS OFICIAIS --}}
            @if ($reportType === 'official' && $canSeeOfficial)
                <div class="space-y-4">
                    @forelse ($vehicles as $vehicle)
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="bg-gray-50 p-4 border-b">
                                <h3 class="font-bold text-gray-800">{{ $vehicle->model }}
                                    ({{ $vehicle->license_plate }})</h3>
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
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-gray-50 p-4 border-b">
                        <h3 class="font-bold text-gray-800">
                            @if ($user->role === 'porteiro')
                                Seus Relatórios de Veículos Particulares
                            @else
                                Relatórios de Veículos Particulares
                            @endif
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
            @endif
        </div>
    </div>
</div>
