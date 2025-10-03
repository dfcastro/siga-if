<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Relatórios Pendentes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Notificações de Sucesso ou Erro (após submissão do formulário) --}}
                    @if (session()->has('message'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('message') }}</p>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- Filtros de Data --}}
                    <div class="flex flex-wrap items-center space-y-4 md:space-y-0 md:space-x-4 mb-6">
                        <div>
                            <x-input-label for="startDate" :value="__('Data de Início')" />
                            <x-text-input wire:model.live="startDate" id="startDate" class="block mt-1 w-full"
                                type="date" />
                        </div>
                        <div>
                            <x-input-label for="endDate" :value="__('Data de Fim')" />
                            <x-text-input wire:model.live="endDate" id="endDate" class="block mt-1 w-full"
                                type="date" />
                        </div>
                    </div>

                    <div class="border-b border-gray-200 mb-4">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button wire:click.prevent="setSubmissionType('private')"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $submissionType === 'private' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Veículos Particulares
                            </button>
                            <button wire:click.prevent="setSubmissionType('official')"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $submissionType === 'official' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Veículos Oficiais
                            </button>
                        </nav>
                    </div>

                    <div wire:loading.class="opacity-50">
                        {{-- ABA DE VEÍCULOS PARTICULARES --}}
                        @if ($submissionType === 'private')
                            <div class="mb-6">
                                @if ($privateEntries->total() > 0)
                                    <form action="{{ route('reports.submitGuardReport') }}" method="POST"
                                        onsubmit="return confirm('Tem a certeza que deseja submeter os {{ $privateEntries->total() }} registos de veículos particulares deste período?');">
                                        @csrf
                                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                                        <input type="hidden" name="submission_type" value="private">
                                        <x-primary-button type="submit">
                                            Submeter Relatório de Particulares ({{ $privateEntries->total() }} registos)
                                        </x-primary-button>
                                    </form>
                                @else
                                    <p class="text-sm text-gray-500">Nenhum registo de veículo particular pendente para
                                        o período selecionado.</p>
                                @endif
                            </div>
                            @include('livewire.partials.guard-report-private-table')
                            <div class="mt-4">
                                {{ $privateEntries->links() }}
                            </div>

                            {{-- ABA DE VEÍCULOS OFICIAIS --}}
                        @else
                            @if ($selectedVehicleId)
                                @include('livewire.partials.guard-report-official-details')
                            @else
                                @include('livewire.partials.guard-report-official-list')
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
