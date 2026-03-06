<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. MENSAGEM DE BOAS-VINDAS (Topo) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-4 sm:p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-800">Olá,
                            {{ explode(' ', trim(Auth::user()->name))[0] }}!</h3>
                        <p class="text-sm text-gray-500">Bem-vindo ao painel de controle do SIGA-IF.</p>
                    </div>
                    <div class="text-right">
                        <span
                            class="text-xs font-bold px-2 py-1 bg-ifnmg-green-100 text-ifnmg-green-800 rounded uppercase tracking-wider">
                            PERFIL: {{ Auth::user()->role }}
                        </span>
                        <div class="mt-1 text-xs text-gray-400 font-medium">{{ now()->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- 2. CARTÕES DE ATALHO RÁPIDO (Dinâmicos por Perfil) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- ================================================= --}}
                {{-- ATALHOS DA PORTARIA (Apenas Admin e Porteiro) --}}
                {{-- ================================================= --}}
                @if (in_array(Auth::user()->role, ['admin', 'porteiro']))
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
                            <h4 class="text-lg font-bold text-gray-800 group-hover:text-blue-700">Registrar
                                Entrada/Saída</h4>
                            <p class="text-sm text-gray-500 group-hover:text-blue-600">Controle de veículos particulares
                            </p>
                        </div>
                        <div class="ml-auto text-gray-300 group-hover:text-blue-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </a>

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
                        <div class="ml-auto text-gray-300 group-hover:text-green-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </a>
                @endif

                {{-- ================================================= --}}
                {{-- ATALHOS DA FISCALIZAÇÃO E AUDITORIA (Admin e Fiscal) --}}
                {{-- ================================================= --}}
                @if (in_array(Auth::user()->role, ['admin', 'fiscal']))
                    <a href="{{ route('fiscal.approval') }}"
                        class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md flex items-center space-x-4 border-l-4 border-yellow-500 transition-all hover:bg-yellow-50 group">
                        <div class="bg-yellow-100 p-4 rounded-full group-hover:bg-white transition-colors">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2H7a2 2 0 01-2-2v-2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800 group-hover:text-yellow-700">Aprovações Pendentes
                            </h4>
                            <p class="text-sm text-gray-500 group-hover:text-yellow-600">Dar visto em relatórios</p>
                        </div>
                        <div class="ml-auto text-gray-300 group-hover:text-yellow-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </a>

                    <a href="{{ route('reports') }}"
                        class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md flex items-center space-x-4 border-l-4 border-indigo-500 transition-all hover:bg-indigo-50 group">
                        <div class="bg-indigo-100 p-4 rounded-full group-hover:bg-white transition-colors">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-800 group-hover:text-indigo-700">Pesquisa e Extratos
                            </h4>
                            <p class="text-sm text-gray-500 group-hover:text-indigo-600">Auditoria e PDFs do Histórico
                            </p>
                        </div>
                        <div class="ml-auto text-gray-300 group-hover:text-indigo-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </a>
                @endif
            </div>

            {{-- 3. SEÇÃO DE ALERTAS E PENDÊNCIAS --}}
            {{-- Escondido para fiscais, pois eles não operam a portaria ao vivo --}}
            @if (in_array(Auth::user()->role, ['admin', 'porteiro']))
                <div class="flex flex-col">
                    @livewire('pending-exits')
                </div>
            @endif

            {{-- 4. PAINEL DE ESTATÍSTICAS (Visão geral para todos os perfis) --}}
            <div>
                @livewire('dashboard-stats')
            </div>

        </div>
    </div>
</x-app-layout>
