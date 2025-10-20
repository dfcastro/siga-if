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

                    {{-- Mensagens de Sessão --}}
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
                    {{-- Código Novo --}}
                    <div class="col-span-6 sm:col-span-3">
                        <label for="report_month" class="block font-medium text-sm text-gray-700">Mês do
                            Relatório</label>
                        {{-- Usamos .live para que a página atualize os dados assim que o mês for alterado --}}
                        <input type="month" wire:model.live="reportMonth" id="report_month"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green">
                        @error('reportMonth')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Navegação das Abas --}}
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

                    {{-- Conteúdo das Abas --}}
                    <div wire:loading.class="opacity-50">
                        <div wire:loading.class="opacity-50">
                            @if ($submissionType === 'private')
                                <div class="mb-6">
                                    {{-- CORREÇÃO AQUI: O botão agora chama a ação do Livewire --}}
                                    <x-primary-button wire:click="confirmSubmission('private')">
                                        Submeter Relatório de Particulares
                                    </x-primary-button>
                                </div>

                                {{-- O formulário antigo não é mais necessário e pode ser removido --}}
                                {{-- <form id="privateForm" ...></form> --}}

                                @include('livewire.partials.guard-report-private-table')
                                <div class="mt-4">{{ $privateEntries->links() }}</div>
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

        {{-- O seu modal de diálogo, agora controlado pelo Livewire --}}
        <x-confirmation-dialog wire:model.live="showConfirmationModal">
            <x-slot name="title">{{ $confirmationTitle }}</x-slot>
            <x-slot name="content">{{ $confirmationMessage }}</x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="$set('showConfirmationModal', false)" wire:loading.attr="disabled">
                    Cancelar
                </x-secondary-button>
                <x-danger-button class="ms-3" wire:click="executeConfirmedAction" wire:loading.attr="disabled">
                    Confirmar
                </x-danger-button>
            </x-slot>
        </x-confirmation-dialog>

        {{-- Script para ouvir o evento e submeter o formulário --}}
        @push('scripts')
            <script>
                document.addEventListener('livewire:initialized', () => {
                    @this.on('submit-form', ({
                        formId
                    }) => {
                        const form = document.getElementById(formId);
                        if (form) {
                            form.submit();
                        }
                    });
                });
            </script>
        @endpush
    </div>
