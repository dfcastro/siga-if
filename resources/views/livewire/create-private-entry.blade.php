<div x-data="{ tab: 'entrada' }">
    {{-- Seção de Mensagens de Alerta (Mantida e Estilizada) --}}
    @if ($successMessage)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition.opacity.duration.500ms
            class="bg-green-50 border border-green-200 border-l-4 border-l-green-500 text-green-800 p-4 rounded-lg relative mb-6 shadow-sm flex items-center"
            role="alert">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm uppercase tracking-wider text-green-900">Operação Concluída</p>
                <p class="text-sm mt-0.5">{{ $successMessage }}</p>
            </div>
        </div>
    @endif

    @if (auth()->user()->role !== 'fiscal')

        {{-- NAVEGAÇÃO DAS ABAS (TABS) EM DESTAQUE --}}
        <div class="bg-white shadow-sm sm:rounded-t-xl border-b border-gray-200 mb-6">
            <nav class="flex" aria-label="Tabs">
                <button @click="tab = 'entrada'"
                    :class="tab === 'entrada' ? 'border-ifnmg-green text-ifnmg-green bg-green-50/30' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="w-1/2 py-5 px-2 text-center border-b-4 font-bold text-sm sm:text-base transition-all duration-200 flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span>Nova Entrada</span>
                </button>
                <button @click="tab = 'saida'"
                    :class="tab === 'saida' ? 'border-ifnmg-green text-ifnmg-green bg-green-50/30' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="w-1/2 py-5 px-2 text-center border-b-4 font-bold text-sm sm:text-base transition-all duration-200 flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    <div class="flex items-center gap-2">
                        <span>Pátio / Saídas</span>
                        @if (count($currentVehicles) > 0)
                            <span
                                class="bg-red-500 text-white py-0.5 px-2.5 rounded-full text-[11px] font-black shadow-sm flex items-center justify-center">
                                {{ count($currentVehicles) }}
                            </span>
                        @endif
                    </div>
                </button>
            </nav>
        </div>

        <div>
            {{-- ========================================================= --}}
            {{-- ABA 1: REGISTRO DE ENTRADA --}}
            {{-- ========================================================= --}}
            <div x-show="tab === 'entrada'" x-transition.opacity.duration.300ms
                class="bg-white overflow-hidden shadow-md sm:rounded-b-xl sm:rounded-t-none rounded-xl border border-gray-100">

                <form wire:submit="save" class="p-0">

                    {{-- DESTAQUE 1: MEGA BUSCA RÁPIDA (Fundo Verde Claro) --}}
                    <div class="bg-green-50/50 border-b border-green-100 p-6 sm:p-8">
                        <div class="max-w-3xl mx-auto">
                            <label for="search"
                                class="block text-sm font-bold text-green-800 mb-2 uppercase tracking-wide flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Identificação Rápida
                            </label>
                            <div class="relative">
                                <input type="text" id="search" wire:model.live.debounce.500ms="search"
                                    placeholder="Digite a PLACA, NOME ou CPF..." autocomplete="off"
                                    class="block w-full rounded-xl border-2 border-green-200 pl-4 pr-12 py-4 text-lg sm:text-xl shadow-inner focus:border-ifnmg-green focus:ring-ifnmg-green font-medium placeholder-gray-400 transition-all">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <div wire:loading wire:target="search"
                                        class="animate-spin rounded-full h-6 w-6 border-b-2 border-ifnmg-green"></div>
                                </div>

                                @if (strlen($search) >= 3)
                                    <div
                                        class="absolute z-40 w-full mt-2 bg-white border-2 border-green-100 rounded-xl shadow-2xl max-h-72 overflow-y-auto overflow-hidden">
                                        @if (!empty($searchResults) && count($searchResults) > 0)
                                            <ul class="divide-y divide-gray-100">
                                                @foreach ($searchResults as $result)
                                                    <li class="px-5 py-4 cursor-pointer hover:bg-green-50 text-sm transition-colors flex items-center gap-3"
                                                        wire:click="selectVehicle('{{ $result['id'] }}')">
                                                        <div class="bg-gray-100 p-2 rounded-lg text-gray-500">
                                                            {!! str_starts_with($result['id'], 'V_')
                                                                ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>'
                                                                : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>' !!}
                                                        </div>
                                                        <div class="font-medium text-gray-800">{{ $result['text'] }}
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="p-8 text-center text-gray-500">
                                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                                <p class="font-semibold text-gray-700">Nenhum registro encontrado.</p>
                                                <p class="text-xs mt-1">Preencha os dados abaixo para criar um novo
                                                    registro.</p>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 space-y-8 max-w-5xl mx-auto">

                        {{-- DESTAQUE 2: DADOS DO VEÍCULO COM VISUALIZADOR DE PLACA --}}
                        <div>
                            <h3
                                class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                                Informações do Veículo</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                <div x-data="{
                                    plate: $wire.entangle('license_plate').live,
                                    formatPlate() {
                                        let clean = String(this.plate || '').toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 7);
                                        let formatted = '';
                                        for (let i = 0; i < clean.length; i++) {
                                            const char = clean[i];
                                            if (i <= 2 && /[A-Z]/.test(char)) formatted += char;
                                            else if (i === 3 && /[0-9]/.test(char)) formatted += char;
                                            else if (i === 4 && /[A-Z0-9]/.test(char)) formatted += char;
                                            else if (i >= 5 && /[0-9]/.test(char)) formatted += char;
                                        }
                                        if (/^[A-Z]{3}[0-9]{4}$/.test(formatted)) {
                                            formatted = formatted.substring(0, 3) + '-' + formatted.substring(3);
                                        }
                                        this.plate = formatted;
                                    }
                                }" x-init="$watch('plate', () => formatPlate())" class="md:col-span-1">
                                    <label for="license_plate"
                                        class="block text-sm font-medium text-gray-700 mb-1">Placa</label>
                                    <input type="text" id="license_plate" x-model="plate"
                                        placeholder="AAA-1234 ou ABC1D23"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green font-mono uppercase text-lg @error('license_plate') border-red-500 @enderror">
                                    @error('license_plate')
                                        <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                                    @enderror

                                    {{-- MÁGICA: A Placa Visual Mercosul --}}
                                    <div x-show="plate.length > 2" x-transition
                                        class="mt-3 bg-white border border-gray-300 rounded-md shadow-md overflow-hidden flex flex-col w-32 select-none">
                                        <div class="bg-blue-600 h-4 w-full flex items-center justify-between px-1">
                                            <span class="text-[6px] text-white font-bold">MERCOSUL</span>
                                            <span class="text-[6px] text-white font-bold">BR</span>
                                        </div>
                                        <div class="text-center py-1.5 bg-white">
                                            <span
                                                class="font-mono text-lg font-bold text-black tracking-widest block leading-none"
                                                x-text="plate || '---'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="vehicle_model"
                                        class="block text-sm font-medium text-gray-700 mb-1">Modelo do Veículo</label>
                                    <input type="text" id="vehicle_model" wire:model.live="vehicle_model"
                                        placeholder="Ex: Celta Prata, Moto Honda Titan..."
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green text-lg @error('vehicle_model') border-red-500 @enderror">
                                    @error('vehicle_model')
                                        <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- DESTAQUE 3: ÁREA DO CONDUTOR / VISITANTE (Fundo sutil) --}}
                        <div class="bg-gray-50 p-5 sm:p-6 rounded-xl border border-gray-200">
                            <h3
                                class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b border-gray-200 pb-2">
                                Identificação do Condutor</h3>

                            <div class="flex justify-between items-end mb-2">
                                <label for="driver_search" class="block text-sm font-medium text-gray-700">Responsável
                                    pela Entrada</label>
                                @if ($selected_driver_id && !$showNewVisitorForm)
                                    <button type="button"
                                        wire:click="$set('selected_driver_id', null); $set('driver_search', '')"
                                        class="text-xs text-red-600 hover:text-red-800 font-bold bg-red-100 hover:bg-red-200 px-3 py-1 rounded-full transition-colors flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Limpar Motorista
                                    </button>
                                @endif
                            </div>

                            {{-- Tags de Sugestão Inteligentes --}}
                            @if (count($suggestedDrivers) > 0 && !$selected_driver_id && !$showNewVisitorForm)
                                <div class="mb-4 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                    <p class="text-xs text-blue-800 font-semibold mb-2 uppercase tracking-wide">
                                        Cadastrados neste veículo:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($suggestedDrivers as $driver)
                                            <button type="button"
                                                wire:click="useSuggestedDriver({{ $driver->id }}, '{{ addslashes($driver->name) }}')"
                                                class="px-4 py-2 bg-white border border-blue-200 text-blue-700 rounded-lg text-sm hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm font-bold flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                                {{ $driver->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($showNewVisitorForm)
                                <div
                                    class="bg-white p-5 border-2 border-blue-300 rounded-lg shadow-inner animate-fade-in-down relative">
                                    <div
                                        class="absolute top-0 right-0 -mt-3 mr-3 bg-blue-100 text-blue-800 text-xs font-black px-2 py-0.5 rounded uppercase border border-blue-200">
                                        Novo Cadastro</div>
                                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                                </path>
                                            </svg>
                                            Dados do Novo Visitante
                                        </h4>
                                        <button type="button" wire:click="cancelNewVisitor"
                                            class="text-xs font-bold text-gray-400 hover:text-red-500 hover:bg-red-50 px-2 py-1 rounded transition-colors uppercase">Cancelar</button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <x-input-label for="new_visitor_name" value="Nome Completo"
                                                class="text-xs font-bold text-gray-600" />
                                            <x-text-input wire:model.live="new_visitor_name" id="new_visitor_name"
                                                class="block mt-1 w-full bg-gray-50 focus:bg-white" type="text" />
                                            <x-input-error for="new_visitor_name" class="mt-1" />
                                        </div>
                                        <div>
                                            <x-input-label for="new_visitor_document" value="CPF"
                                                class="text-xs font-bold text-gray-600" />
                                            <x-text-input wire:model.live="new_visitor_document"
                                                id="new_visitor_document"
                                                class="block mt-1 w-full font-mono bg-gray-50 focus:bg-white"
                                                type="tel" x-mask="999.999.999-99" />
                                            <x-input-error for="new_visitor_document" class="mt-1" />
                                        </div>
                                        <div>
                                            <x-input-label for="new_visitor_phone" value="Telefone (Opcional)"
                                                class="text-xs font-bold text-gray-600" />
                                            <x-text-input wire:model="new_visitor_phone" id="new_visitor_phone"
                                                class="block mt-1 w-full font-mono bg-gray-50 focus:bg-white"
                                                type="tel" x-mask="(99) 99999-9999" />
                                            <x-input-error for="new_visitor_phone" class="mt-1" />
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="relative">
                                    <div class="flex items-center">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 {{ $selected_driver_id ? 'text-green-500' : 'text-gray-400' }}"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <x-text-input wire:model.live.debounce.300ms="driver_search"
                                            id="driver_search"
                                            class="block w-full pl-10 text-lg {{ $selected_driver_id ? 'bg-green-50 border-green-400 text-green-900 font-bold shadow-inner' : 'bg-white' }}"
                                            placeholder="Digite para buscar um motorista..." :disabled="$selected_driver_id"
                                            autocomplete="off" />
                                    </div>

                                    @if (!empty($driver_search) && !$selected_driver_id)
                                        <div
                                            class="absolute z-30 w-full bg-white rounded-lg shadow-xl mt-2 border border-gray-200 overflow-hidden">
                                            @if (count($drivers) > 0)
                                                <ul class="max-h-56 overflow-y-auto divide-y divide-gray-100">
                                                    @foreach ($drivers as $driver)
                                                        <li class="px-5 py-3 hover:bg-green-50 cursor-pointer flex justify-between items-center transition-colors"
                                                            wire:click="selectDriver({{ $driver->id }}, '{{ addslashes($driver->name) }}')">
                                                            <span
                                                                class="font-bold text-gray-800">{{ $driver->name }}</span>
                                                            <span
                                                                class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded">CPF:
                                                                {{ $driver->formatted_document }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="p-6 text-center bg-gray-50">
                                                    <p class="mb-3 text-gray-600 font-medium">Condutor
                                                        "{{ $driver_search }}" não encontrado.</p>
                                                    <button type="button" wire:click="prepareNewVisitorForm"
                                                        class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-md transition-transform active:scale-95 flex items-center justify-center gap-2 mx-auto">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                        Cadastrar Novo Visitante
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <x-input-error for="selected_driver_id" class="mt-2 font-semibold" />
                        </div>

                        {{-- DESTAQUE 4: MOTIVO DA ENTRADA --}}
                        <div>
                            <h3
                                class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                                Destino / Motivo</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <select id="entry_reason" wire:model.live="entry_reason"
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-ifnmg-green focus:ring-ifnmg-green text-lg bg-gray-50 @error('entry_reason') border-red-500 @enderror">
                                        <option value="">Selecione o motivo...</option>
                                        @foreach ($predefinedReasons as $reason)
                                            <option value="{{ $reason }}">{{ $reason }}</option>
                                        @endforeach
                                        <option value="Outro" class="font-bold text-gray-900">✏️ Outro (Digitar
                                            Manualmente)</option>
                                    </select>
                                    @error('entry_reason')
                                        <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                @if ($entry_reason === 'Outro')
                                    <div class="animate-fade-in">
                                        <input type="text" id="other_reason" wire:model="other_reason"
                                            placeholder="Especifique o local/motivo..."
                                            class="block w-full rounded-lg border-blue-300 bg-blue-50 shadow-inner focus:border-blue-500 focus:ring-blue-500 text-lg @error('other_reason') border-red-500 @enderror"
                                            autofocus>
                                        @error('other_reason')
                                            <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            {{-- CAMPO: OBSERVAÇÕES E PASSAGEIROS --}}
                            @if ($entry_reason === 'Transporte de Alunos (Ônibus/Vans)')
                                <div
                                    class="mt-5 bg-blue-50 p-4 rounded-xl border border-blue-200 animate-fade-in shadow-inner">
                                    <label for="observation"
                                        class="block text-xs font-black text-blue-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                                        🚌 Informações do Transporte / Alunos
                                    </label>
                                    <input type="text" id="observation" wire:model="observation"
                                        placeholder="Ex: Ônibus de Rubim com 40 alunos..."
                                        class="block w-full rounded-lg border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white text-base py-2.5">
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- BOTÃO SALVAR GIGANTE --}}
                    <div class="bg-gray-50 border-t border-gray-200 p-6 sm:p-8 flex justify-end rounded-b-xl">
                        <button type="submit"
                            class="w-full sm:w-1/3 flex items-center justify-center rounded-xl bg-ifnmg-green px-8 py-4 text-lg font-black text-white shadow-lg hover:bg-green-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-300 transform active:scale-95"
                            wire:loading.attr="disabled">
                            <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span wire:loading.remove wire:target="save" class="mr-2">
                                <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                            <span>LIBERAR ENTRADA</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- ========================================================= --}}
            {{-- ABA 2: REGISTRO DE SAÍDA (PÁTIO) --}}
            {{-- ========================================================= --}}
            <div x-show="tab === 'saida'" style="display: none;"
                class="bg-white overflow-hidden shadow-md sm:rounded-b-xl sm:rounded-t-none rounded-xl border border-gray-100">
                <div
                    class="p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Veículos no Pátio</h2>
                            <p class="text-sm text-gray-500 font-medium">Libere a saída dos veículos estacionados.</p>
                        </div>
                    </div>
                    <div class="relative w-full sm:w-72">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="exitSearch"
                            placeholder="Buscar placa ou nome..."
                            class="block w-full rounded-xl border-gray-300 pl-11 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base py-3 bg-white">
                    </div>
                </div>

                <div class="p-6">
                    {{-- Tabela para Desktop --}}
                    <div class="hidden lg:block overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
                        <table class="min-w-full bg-white divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Veículo</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Condutor</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Horário / Status</th>
                                    <th
                                        class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($currentVehicles as $entry)
                                    <tr class="hover:bg-red-50/40 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-gray-100 border border-gray-300 rounded px-2 py-1">
                                                    <span
                                                        class="font-mono font-bold text-lg text-gray-900 tracking-wider block">{{ $entry->license_plate }}</span>
                                                </div>
                                                <span
                                                    class="text-sm font-medium text-gray-600">{{ $entry->vehicle_model ?? 'N/I' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <div class="font-bold text-gray-800">
                                                {{ $entry->driver->name ?? 'Visitante sem nome' }}</div>
                                            @if ($entry->driver->telefone)
                                                <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                        </path>
                                                    </svg>
                                                    {{ $entry->driver->telefone }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-green-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span
                                                    class="text-sm font-bold text-gray-900">{{ $entry->entry_at->format('H:i') }}</span>
                                            </div>
                                            <span
                                                class="text-[10px] uppercase font-bold tracking-wider text-green-700 bg-green-100 px-2 py-0.5 rounded-full mt-1 inline-block">No
                                                Campus</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <button wire:click="confirmExit({{ $entry->id }})"
                                                class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-red-600 border border-red-200 shadow-sm hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all opacity-80 group-hover:opacity-100">
                                                Registrar Saída
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                    </path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div
                                                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h3 class="text-sm font-medium text-gray-900">Pátio Vazio</h3>
                                            <p class="mt-1 text-sm text-gray-500">Nenhum veículo aguardando saída.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Cards para Mobile/Tablet --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:hidden">
                        @forelse ($currentVehicles as $entry)
                            <div
                                class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex flex-col justify-between relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-green-400"></div>
                                <div class="pl-3">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="bg-gray-100 border border-gray-300 rounded px-2.5 py-1">
                                            <span
                                                class="font-mono font-bold text-xl text-gray-900 tracking-wider">{{ $entry->license_plate }}</span>
                                        </div>
                                        <div
                                            class="flex items-center gap-1 bg-green-50 text-green-700 px-2.5 py-1 rounded-lg border border-green-100 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span
                                                class="font-bold text-sm">{{ $entry->entry_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2 mb-4">
                                        <p class="text-sm text-gray-600 font-medium">🚗
                                            {{ $entry->vehicle_model ?? 'Modelo não informado' }}</p>
                                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                            <p class="font-bold text-gray-800 text-sm">👤
                                                {{ $entry->driver->name ?? 'N/A' }}</p>
                                            @if ($entry->driver->telefone)
                                                <p class="text-xs text-gray-500 ml-5 mt-1">
                                                    {{ $entry->driver->telefone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <button wire:click="confirmExit({{ $entry->id }})"
                                        class="w-full flex justify-center items-center gap-2 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm font-bold text-red-600 shadow-sm hover:bg-red-600 hover:text-white transition-colors active:scale-95">
                                        Registrar Saída
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-1 sm:col-span-2 text-center py-12 bg-gray-50 rounded-xl border-2 border-gray-200 border-dashed">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                                    </path>
                                </svg>
                                <p class="text-gray-500 font-medium">Nenhum veículo no pátio neste momento.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Mensagem para o fiscal (Mantida) --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-xl relative shadow-sm flex items-start gap-4"
            role="alert">
            <svg class="w-6 h-6 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <span class="block font-bold mb-1">Acesso Restrito</span>
                <span class="block text-sm sm:inline">Você tem permissão apenas para visualização de relatórios. O
                    registro de entradas e saídas de frota na cancela é uma atribuição exclusiva da equipe de
                    portaria.</span>
            </div>
        </div>
    @endif

    {{-- Modal de confirmação (MANTIDO INTACTO) --}}
    @if ($showExitConfirmationModal)
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                @click.away="$wire.set('showExitConfirmationModal', false)">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col items-center text-center">
                        <div
                            class="flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4 shadow-inner">
                            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 mb-2">Confirmar Saída</h3>
                        <p class="text-sm text-gray-500 mb-6">O veículo abaixo está deixando o campus?</p>

                        @if ($entryToExit)
                            <div class="w-full bg-gray-50 p-4 rounded-xl border border-gray-200 text-left space-y-3">
                                <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                                    <span class="text-sm font-semibold text-gray-500 uppercase">Placa</span>
                                    <span
                                        class="font-mono text-xl font-bold text-gray-900 bg-white border border-gray-300 px-2 py-0.5 rounded shadow-sm">{{ $entryToExit->license_plate }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-500 uppercase">Modelo</span>
                                    <span
                                        class="font-medium text-gray-800 text-right">{{ $entryToExit->vehicle_model }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-500 uppercase">Condutor</span>
                                    <span
                                        class="font-bold text-gray-800 text-right">{{ $entryToExit->driver->name ?? 'Não informado' }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div
                    class="px-6 py-4 bg-gray-50 flex flex-col-reverse sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse gap-3 sm:gap-0 border-t border-gray-200">
                    <button wire:click="executeExit"
                        class="w-full sm:w-1/2 inline-flex justify-center items-center rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white shadow-md hover:bg-red-700 transition-all active:scale-95">
                        Confirmar Saída
                    </button>
                    <button wire:click="$set('showExitConfirmationModal', false)"
                        class="w-full sm:w-1/2 inline-flex justify-center items-center rounded-xl bg-white px-4 py-3 text-sm font-bold text-gray-700 shadow-sm border border-gray-300 hover:bg-gray-50 transition-all active:scale-95">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
