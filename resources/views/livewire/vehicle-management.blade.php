<div>
    {{-- Alertas (mantidos como estão) --}}
    @if (session('successMessage'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg relative mb-6 shadow-md"
            role="alert">
            <div class="flex">
                <div class="py-1"><svg class="h-6 w-6 text-green-500 mr-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg></div>
                <div>
                    <p class="font-bold">Sucesso!</p>
                    <p class="text-sm">{{ session('successMessage') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if (session('error'))
        {{-- Adicionado alerta de erro se houver --}}
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
            class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg relative mb-6 shadow-md"
            role="alert">
            <div class="flex">
                <div class="py-1"><svg class="h-6 w-6 text-red-500 mr-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <div>
                    <p class="font-bold">Erro!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Card Principal (mantido) --}}
    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
        <div class="p-6 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Gerenciamento de Veículos</h2>
                <p class="text-sm text-gray-500 mt-1">Adicione, edite e visualize todos os veículos cadastrados.</p>
            </div>
            {{-- Botão Novo Veículo com permissão --}}
            @if ($this->canCreateVehicle())
                {{-- Ajuste da permissão para criar com base no tipo padrão --}}
                <div class="mt-4 sm:mt-0">
                    <x-primary-button wire:click="create">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Novo Veículo
                    </x-primary-button>
                </div>
            @endif
        </div>

        <div class="p-6">
            {{-- Controles de Busca e Filtro (mantidos) --}}
            <div class="md:flex justify-between items-center mb-6">
                <div class="relative w-full md:w-1/3">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><svg
                            class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd" />
                        </svg></div>
                    <input wire:model.live.debounce.500ms="search" type="text"
                        placeholder="Buscar por placa, modelo, cor ou proprietário..."
                        class="block w-full border-gray-300 rounded-md shadow-sm pl-10 focus:border-ifnmg-green focus:ring-ifnmg-green mb-4 md:mb-0 text-sm">
                </div>
                <div class="flex space-x-2 rounded-md shadow-sm" role="group">
                    <button wire:click="$set('filter', 'active')" type="button"
                        class="px-4 py-2 text-sm font-medium {{ $filter === 'active' ? 'bg-ifnmg-green text-white ring-1 ring-ifnmg-green' : 'bg-white text-gray-900 hover:bg-gray-50' }} border border-gray-200 rounded-l-lg focus:z-10 focus:ring-2 focus:ring-ifnmg-green transition duration-150 ease-in-out">
                        Ativos
                    </button>
                    <button wire:click="$set('filter', 'trashed')" type="button"
                        class="px-4 py-2 text-sm font-medium {{ $filter === 'trashed' ? 'bg-ifnmg-green text-white ring-1 ring-ifnmg-green' : 'bg-white text-gray-900 hover:bg-gray-50' }} border-t border-b border-r border-gray-200 rounded-r-md focus:z-10 focus:ring-2 focus:ring-ifnmg-green transition duration-150 ease-in-out">
                        Lixeira
                    </button>
                </div>
            </div>

            {{-- Tabela Desktop com Melhorias Visuais --}}
            <div class="hidden lg:block overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                <table class="min-w-full bg-white divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Placa</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Modelo</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Cor</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Tipo</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Proprietário</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($vehicles as $vehicle)
                            <tr
                                class="{{ $vehicle->trashed() ? 'bg-red-50/50' : 'odd:bg-white even:bg-gray-50/50' }} hover:bg-blue-50/60 transition duration-150 ease-in-out">
                                <td class="px-6 py-4 align-middle text-sm font-medium text-gray-900 font-mono">
                                    {{ $vehicle->license_plate }}</td>
                                <td class="px-6 py-4 align-middle text-sm text-gray-600 truncate max-w-xs"
                                    title="{{ $vehicle->model }}">{{ $vehicle->model }}</td>
                                <td class="px-6 py-4 align-middle text-sm text-gray-600 capitalize">
                                    {{ $vehicle->color }}</td>
                                <td class="px-6 py-4 align-middle text-center">
                                    <span
                                        class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vehicle->type == 'Oficial' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                                        {{ $vehicle->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 align-middle text-sm text-gray-600 truncate max-w-xs"
                                    title="{{ $vehicle->driver->name ?? '' }}">
                                    {{ $vehicle->driver->name ?? ($vehicle->type == 'Oficial' ? '-' : 'N/A') }}
                                </td>
                                {{-- ### CÉLULA DE AÇÕES COM GRUPO DE BOTÕES ### --}}
                                <td class="px-6 py-4 align-middle text-center whitespace-nowrap text-sm">
                                    <div class="inline-flex rounded-md shadow-sm" role="group">
                                        @if ($vehicle->trashed())
                                            @if ($this->canManageVehicle($vehicle))
                                                <button wire:click="restore({{ $vehicle->id }})" type="button"
                                                    title="Restaurar"
                                                    class="relative inline-flex items-center px-3 py-1.5 rounded-l-md border border-gray-300 bg-white text-xs font-medium text-green-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-ifnmg-green focus:border-ifnmg-green transition ease-in-out duration-150">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9H4V4m.002 16v-5h-.581m15.357-2a8.001 8.001 0 00-14.774-2H20v5" />
                                                    </svg> {{-- Ícone de restaurar --}}
                                                    <span class="ml-1 hidden sm:inline">Restaurar</span>
                                                </button>
                                                <button wire:click="confirmForceDelete({{ $vehicle->id }})"
                                                    type="button" title="Excluir Permanentemente"
                                                    class="relative inline-flex items-center px-3 py-1.5 rounded-r-md border border-gray-300 bg-white text-xs font-medium text-red-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-ifnmg-green focus:border-ifnmg-green transition ease-in-out duration-150 -ml-px">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    <span class="ml-1 hidden sm:inline">Excluir Perm.</span>
                                                </button>
                                            @else
                                                <span
                                                    class="px-3 py-1.5 text-xs text-gray-400 italic border border-gray-300 rounded-md bg-gray-50">Sem
                                                    permissão</span>
                                            @endif
                                        @else
                                            <button wire:click="showHistory({{ $vehicle->id }})" type="button"
                                                title="Histórico" {{-- Arredonda à esquerda se for o único botão, ou o primeiro visível --}}
                                                class="relative inline-flex items-center px-3 py-1.5 {{ !$this->canManageVehicle($vehicle) ? 'rounded-md' : 'rounded-l-md' }} border border-gray-300 bg-white text-xs font-medium text-blue-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-ifnmg-green focus:border-ifnmg-green transition ease-in-out duration-150">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                    </path>
                                                </svg>
                                                <span class="ml-1 hidden sm:inline">Histórico</span>
                                            </button>
                                            @if ($this->canManageVehicle($vehicle))
                                                <button wire:click="edit({{ $vehicle->id }})" type="button"
                                                    title="Editar"
                                                    class="relative inline-flex items-center px-3 py-1.5 border border-gray-300 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-ifnmg-green focus:border-ifnmg-green transition ease-in-out duration-150 -ml-px">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                    <span class="ml-1 hidden sm:inline">Editar</span>
                                                </button>
                                                <button wire:click="confirmDelete({{ $vehicle->id }})"
                                                    type="button" title="Mover para Lixeira"
                                                    class="relative inline-flex items-center px-3 py-1.5 rounded-r-md border border-gray-300 bg-white text-xs font-medium text-red-600 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-ifnmg-green focus:border-ifnmg-green transition ease-in-out duration-150 -ml-px">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    <span class="ml-1 hidden sm:inline">Excluir</span>
                                                </button>
                                            @else
                                                <span
                                                    class="px-3 py-1.5 text-xs text-gray-400 italic border-t border-b border-r border-gray-300 rounded-r-md bg-gray-50 -ml-px">Não
                                                    editável</span>
                                            @endif
                                        @endif
                                    </div>
                                    {{-- ### FIM DO CÓDIGO DOS BOTÕES ATUALIZADO ### --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">Nenhum veículo
                                    encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Cards Mobile/Tablet com Melhorias Visuais --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:hidden">
                @forelse ($vehicles as $vehicle)
                    <div
                        class="bg-white border rounded-lg p-4 shadow-sm flex flex-col justify-between {{ $vehicle->trashed() ? 'bg-red-50/50 opacity-70' : '' }} transition duration-150 ease-in-out">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-lg text-gray-800 font-mono">
                                    {{ $vehicle->license_plate }}</h3>
                                <span
                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vehicle->type == 'Oficial' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-green-100 text-green-800 border border-green-200' }} ml-2 flex-shrink-0">
                                    {{ $vehicle->type }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $vehicle->model }} - {{ $vehicle->color }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <strong>Proprietário:</strong>
                                {{ $vehicle->driver->name ?? ($vehicle->type == 'Oficial' ? '-' : 'N/A') }}
                            </p>
                        </div>
                        {{-- Botões Mobile com estilo melhorado --}}
                        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-2 justify-end">
                            @if ($vehicle->trashed())
                                @if ($this->canManageVehicle($vehicle))
                                    <button wire:click="restore({{ $vehicle->id }})" type="button"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-green-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ifnmg-green transition">Restaurar</button>
                                    <button wire:click="confirmForceDelete({{ $vehicle->id }})" type="button"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">Excluir
                                        Perm.</button>
                                @else
                                    <span class="text-xs text-gray-400 italic w-full text-right">Sem permissão</span>
                                @endif
                            @else
                                <button wire:click="showHistory({{ $vehicle->id }})" type="button"
                                    class="flex-1 inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-blue-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ifnmg-green transition">Histórico</button>
                                @if ($this->canManageVehicle($vehicle))
                                    <button wire:click="edit({{ $vehicle->id }})" type="button"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ifnmg-green transition">Editar</button>
                                    <button wire:click="confirmDelete({{ $vehicle->id }})" type="button"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">Excluir</button>
                                @else
                                    <span class="text-xs text-gray-400 italic w-full text-right"
                                        title="Apenas Admin/Fiscais podem gerenciar motoristas autorizados.">Não
                                        editável</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 col-span-1 md:col-span-2 py-8">Nenhum veículo cadastrado.</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>

    {{-- Modais (mantidos como estão) --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            x-data="{ open: @entangle('isModalOpen') }" x-show="open" @keydown.escape.window="open = false" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.away="open = false">
                {{-- Conteúdo Modal Edição/Criação --}}
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">{{ $vehicleId ? 'Editar Veículo' : 'Criar Novo Veículo' }}</h3>
                </div>
                <form wire:submit="store">
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
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
                            }" x-init="$watch('plate', () => formatPlate())">
                                <x-input-label for="license_plate_modal" value="Placa" />
                                <x-text-input type="text" id="license_plate_modal" x-model="plate"
                                    {{-- Liga ao Alpine --}} placeholder="AAA-1234 ou AAA1B23"
                                    class="mt-1 block w-full uppercase font-mono text-sm" maxlength="8"
                                    {{-- AAA-1234 --}} />
                                <x-input-error for="license_plate" class="mt-1" />
                            </div>
                            {{-- ### FIM DO BLOCO SUBSTITUÍDO ### --}}
                            <div><x-input-label for="model" value="Modelo" /><x-text-input type="text"
                                    id="model" wire:model="model" maxlength="25"
                                    oninput="this.value = this.value.toUpperCase()"
                                    class="mt-1 block w-full text-sm" /><x-input-error for="model"
                                    class="mt-1" /></div>
                            <div>
                                <x-input-label for="color_selector" value="Cor" />
                                <select id="color_selector" wire:model="color"
                                    class="mt-1 block w-full border-gray-300 focus:border-ifnmg-green focus:ring-ifnmg-green rounded-md shadow-sm text-sm">
                                    <option value="">Selecione...</option>
                                    @foreach ($commonColors as $colorOption)
                                        <option value="{{ $colorOption }}">{{ $colorOption }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="color" class="mt-1" />
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="type" value="Tipo de Veículo" />
                                <select wire:model.live="type" id="type"
                                    class="block mt-1 w-full border-gray-300 focus:border-ifnmg-green focus:ring-ifnmg-green rounded-md shadow-sm text-sm">
                                    @php $user = auth()->user(); @endphp
                                    @if ($user->role === 'admin' || ($user->role === 'fiscal'))
                                        <option value="Oficial">Oficial</option>
                                    @endif
                                    @if (
                                        $user->role === 'admin' ||
                                            $user->role === 'porteiro' ||
                                            ($user->role === 'fiscal' && in_array($user->fiscal_type, ['private', 'both'])))
                                        <option value="Particular">Particular</option>
                                    @endif
                                </select>
                                <x-input-error for="type" class="mt-1" />
                            </div>
                            @if ($type === 'Particular')
                                <div>
                                    <x-input-label for="driver_search" value="Proprietário(s) / Motorista(s)" />

                                    {{-- ÁREA DE TAGS (MOTORISTAS SELECIONADOS) --}}
                                    @if (count($selected_drivers) > 0)
                                        <div class="flex flex-wrap gap-2 mb-3 mt-2">
                                            @foreach ($selected_drivers as $driver)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200 shadow-sm">
                                                    👤 {{ $driver['name'] }}
                                                    <button type="button"
                                                        wire:click="removeDriver({{ $driver['id'] }})"
                                                        class="flex-shrink-0 ml-2 inline-flex text-green-600 hover:text-red-600 focus:outline-none transition">
                                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- BARRA DE PESQUISA PARA ADICIONAR MAIS --}}
                                    <div x-data="{ open: @entangle('show_driver_dropdown') }" @click.away="open = false" class="relative">
                                        <x-text-input type="text" id="driver_search"
                                            class="mt-1 block w-full text-sm"
                                            wire:model.live.debounce.300ms="driver_search" @focus="open = true"
                                            autocomplete="off" placeholder="Digite o nome para adicionar..." />

                                        <div x-show="open"
                                            class="absolute z-10 w-full bg-white rounded-md shadow-lg mt-1 border max-h-48 overflow-y-auto">
                                            @if ($this->foundDrivers->isNotEmpty())
                                                <ul>
                                                    @foreach ($this->foundDrivers as $driver)
                                                        <li class="p-3 hover:bg-green-50 cursor-pointer text-sm flex justify-between items-center transition"
                                                            wire:click="selectDriver({{ $driver->id }}, '{{ addslashes($driver->name) }}')">
                                                            <div>
                                                                <span
                                                                    class="block font-medium text-gray-800">{{ $driver->name }}</span>
                                                                <span class="block text-xs text-gray-500">
                                                                    CPF:
                                                                    {{ strlen($driver->document) === 11 ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $driver->document) : ($driver->document ?: 'Não informado') }}
                                                                </span>
                                                            </div>
                                                            <span class="text-xs text-ifnmg-green font-bold">+
                                                                Adicionar</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @elseif(strlen($driver_search) >= 2)
                                                <p class="p-3 text-gray-500 text-sm">Nenhum motorista encontrado.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 text-right space-x-2">
                        <x-secondary-button type="button" @click="open = false">Fechar</x-secondary-button>
                        <x-primary-button type="submit">{{ $vehicleId ? 'Atualizar' : 'Salvar' }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    @if ($isConfirmModalOpen)
        {{-- Modal Confirmação Exclusão --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md"
                @click.away="$wire.set('isConfirmModalOpen', false)">
                <div class="p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900">
                                {{ $filter === 'active' ? 'Mover para a Lixeira' : 'Confirmar Exclusão Permanente' }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">Você tem certeza que deseja
                                    <strong>{{ $filter === 'active' ? 'mover o veículo' : 'excluir PERMANENTEMENTE o veículo' }}</strong>
                                    <strong class="font-mono">"{{ $vehiclePlateToDelete }}"</strong>?
                                </p>
                                @if ($filter === 'trashed')
                                    <p class="mt-2 text-xs text-red-700 font-semibold">Esta ação não pode ser desfeita.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex flex-row-reverse space-x-2 space-x-reverse"> <x-danger-button
                        wire:click="{{ $filter === 'active' ? 'deleteVehicle' : 'forceDeleteVehicle' }}">Confirmar</x-danger-button>
                    <x-secondary-button wire:click="closeConfirmModal">Cancelar</x-secondary-button>
                </div>
            </div>
        </div>
    @endif
    @if ($isHistoryModalOpen && $vehicleForHistory)
        {{-- Modal Histórico Aprimorado --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col"
                @click.away="closeHistoryModal">

                {{-- Cabeçalho do Modal --}}
                <div
                    class="px-6 py-4 border-b bg-gray-50 rounded-t-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Histórico do Veículo
                        </h3>
                        <p class="text-sm text-gray-600 mt-1 font-medium">
                            {{ $vehicleForHistory->model }}
                            <span
                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-gray-200 text-gray-800">
                                {{ $vehicleForHistory->license_plate }}
                            </span>
                            <span
                                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $vehicleForHistory->type === 'Oficial' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $vehicleForHistory->type }}
                            </span>
                        </p>
                    </div>
                    <div class="relative w-full sm:w-64">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="historySearch" type="text"
                            placeholder="Buscar no histórico..."
                            class="block w-full border-gray-300 rounded-md shadow-sm pl-10 focus:border-ifnmg-green focus:ring-ifnmg-green text-sm">
                    </div>
                </div>

                {{-- Corpo do Modal (Timeline) --}}
                <div class="p-6 overflow-y-auto bg-gray-50/50 flex-1">
                    @if ($vehicleHistory && $vehicleHistory->isNotEmpty())
                        <div class="relative pl-6">
                            {{-- Linha da Timeline --}}
                            <div class="absolute left-9 top-0 h-full w-0.5 bg-gray-200"></div>

                            @foreach ($vehicleHistory as $entry)
                                <div class="relative mb-8">
                                    {{-- Ícone da Timeline --}}
                                    <div class="absolute left-0 top-1.5 -translate-x-1/2 transform">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full shadow-sm border-2 border-white {{ $entry->end_time ? ($entry->type === 'Oficial' ? 'bg-blue-500' : 'bg-green-500') : 'bg-yellow-400 animate-pulse' }}">
                                            @if ($entry->type === 'Oficial')
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 003.375-3.375h1.5a1.125 1.125 0 011.125 1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375m15.75 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125-1.125h-1.5a3.375 3.375 0 00-3.375 3.375v1.875" />
                                                </svg>
                                            @else
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8.25 9.75h7.5a.75.75 0 01.75.75v3.75a.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75v-3.75a.75.75 0 01.75-.75z" />
                                                </svg>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Card de Detalhes --}}
                                    <div
                                        class="ml-12 p-5 bg-white border border-gray-200 rounded-xl shadow-sm hover:border-gray-300 transition-colors">

                                        <div
                                            class="flex flex-col sm:flex-row justify-between sm:items-start mb-4 gap-2">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span
                                                        class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($entry->start_time)->format('d/m/Y') }}</span>
                                                    @if (!$entry->end_time)
                                                        <span
                                                            class="px-2 py-0.5 text-[10px] uppercase font-bold bg-yellow-100 text-yellow-800 rounded-full">Em
                                                            Andamento</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500 font-medium">
                                                    Horário: <span
                                                        class="text-gray-900">{{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }}</span>
                                                    @if ($entry->end_time)
                                                        até <span
                                                            class="text-gray-900">{{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div
                                                class="text-left sm:text-right bg-gray-50 px-3 py-2 rounded-md border border-gray-100">
                                                <p
                                                    class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-0.5">
                                                    Condutor / Motorista</p>
                                                <p class="text-sm font-bold text-gray-800">{{ $entry->driver_name }}
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 pt-4">
                                            {{-- Bloco Esquerdo: Motivo/Destino e Passageiros --}}
                                            <div class="space-y-3">
                                                <div>
                                                    <span
                                                        class="text-xs text-gray-500 font-semibold uppercase">{{ $entry->type === 'Oficial' ? 'Destino' : 'Motivo' }}</span>
                                                    <p class="text-sm text-gray-800 mt-0.5">{{ $entry->detail }}</p>
                                                </div>
                                                @if ($entry->type === 'Oficial' && $entry->passengers)
                                                    <div>
                                                        <span
                                                            class="text-xs text-gray-500 font-semibold uppercase flex items-center gap-1">
                                                            <svg class="w-3.5 h-3.5" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                                </path>
                                                            </svg>
                                                            Passageiros
                                                        </span>
                                                        <p class="text-sm text-gray-800 mt-0.5">
                                                            {{ $entry->passengers }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Bloco Direito: Auditoria e Odômetro --}}
                                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 space-y-2">
                                                @if ($entry->type === 'Oficial')
                                                    <div
                                                        class="flex justify-between items-center text-xs pb-2 border-b border-gray-200">
                                                        <div class="flex gap-4">
                                                            <div><span class="text-gray-500 block">KM Saída</span>
                                                                <span
                                                                    class="font-bold text-gray-800">{{ number_format($entry->departure_odometer, 0, ',', '.') }}</span>
                                                            </div>
                                                            @if ($entry->arrival_odometer)
                                                                <div><span class="text-gray-500 block">KM
                                                                        Chegada</span> <span
                                                                        class="font-bold text-gray-800">{{ number_format($entry->arrival_odometer, 0, ',', '.') }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @if ($entry->distance_traveled)
                                                            <div class="text-right">
                                                                <span
                                                                    class="text-blue-600 font-bold block bg-blue-50 px-2 py-0.5 rounded">{{ $entry->distance_traveled }}
                                                                    km</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                <div class="text-xs space-y-1 pt-1">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">Liberação (Saída):</span>
                                                        <span
                                                            class="font-medium text-gray-800">{{ $entry->guard_start ?? 'Não registrado' }}</span>
                                                    </div>
                                                    @if ($entry->end_time)
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-500">Retorno (Chegada):</span>
                                                            <span
                                                                class="font-medium text-gray-800">{{ $entry->guard_end ?? 'Não registrado' }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Observações de Retorno (Se houver) --}}
                                        @if ($entry->type === 'Oficial' && $entry->return_observation)
                                            <div class="mt-3 pt-3 border-t border-dashed border-gray-200">
                                                <div
                                                    class="bg-yellow-50 text-yellow-800 text-xs p-2 rounded border border-yellow-100 flex gap-2">
                                                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                        </path>
                                                    </svg>
                                                    <div>
                                                        <span class="font-bold block mb-0.5">Observação no
                                                            Retorno:</span>
                                                        {{ $entry->return_observation }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Nenhum histórico de movimentação
                                encontrado{{ !empty($this->historySearch) ? ' para a busca realizada' : '' }}.</p>
                        </div>
                    @endif

                    @if ($vehicleHistory)
                        <div class="mt-4">{{ $vehicleHistory->links('livewire::simple-tailwind') }}</div>
                    @endif
                </div>

                {{-- Rodapé do Modal --}}
                <div class="px-6 py-4 bg-white text-right border-t rounded-b-lg">
                    <x-secondary-button wire:click="closeHistoryModal">Fechar Histórico</x-secondary-button>
                </div>
            </div>
        </div>
    @endif
</div>
