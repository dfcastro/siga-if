<x-app-layout>
    <div class="py-8"> {{-- Reduzi o padding vertical para aproveitar melhor o topo --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"> {{-- Adicionei space-y-6 para espaçamento uniforme --}}

            {{-- 1. CARTÕES DE ATALHO RÁPIDO (Movido para o topo para acesso imediato) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Atalho para Registrar Entrada/Saída --}}
                <a href="{{ route('entries.create') }}"
                    class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md flex items-center space-x-4 border-l-4 border-blue-500 transition-all hover:bg-blue-50 group">
                    <div class="bg-blue-100 p-4 rounded-full group-hover:bg-white transition-colors">
                        <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 group-hover:text-blue-700">Registrar Entrada/Saída</h4>
                        <p class="text-sm text-gray-500 group-hover:text-blue-600">Controle de veículos particulares</p>
                    </div>
                    {{-- Ícone de seta para indicar ação --}}
                    <div class="ml-auto text-gray-300 group-hover:text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </a>

                {{-- Atalho para Diário de Bordo --}}
                <a href="{{ route('fleet.index') }}"
                    class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md flex items-center space-x-4 border-l-4 border-green-500 transition-all hover:bg-green-50 group">
                    <div class="bg-green-100 p-4 rounded-full group-hover:bg-white transition-colors">
                        <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 group-hover:text-green-700">Diário de Bordo</h4>
                        <p class="text-sm text-gray-500 group-hover:text-green-600">Gestão da frota oficial</p>
                    </div>
                    {{-- Ícone de seta para indicar ação --}}
                    <div class="ml-auto text-gray-300 group-hover:text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </a>
            </div>

            {{-- 2. MENSAGEM DE BOAS-VINDAS (Discreta, abaixo dos botões de ação) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-4 sm:p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Olá, {{ Auth::user()->name }}!</h3>
                        <p class="text-sm text-gray-500">Bem-vindo ao painel de controle do SIGA-IF.</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-600 rounded">
                        {{ now()->format('d/m/Y') }}
                    </span>
                </div>
            </div>

            {{-- 3. SEÇÃO DE ALERTAS E PENDÊNCIAS (Importante para operação) --}}
            <div class="flex flex-col">
                {{-- Alerta para saídas pendentes (Veículos no Pátio) --}}
                @livewire('pending-exits')
            </div>

            {{-- 4. PAINEL DE ESTATÍSTICAS (Visão geral) --}}
            <div>
                @livewire('dashboard-stats')
            </div>

        </div>
    </div>
</x-app-layout>