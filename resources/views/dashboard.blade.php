<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Card de Boas-Vindas (Mantido no topo) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 ">
                    <h3 class="text-lg font-medium">Bem-vindo(a) ao SIGA-IF, {{ Auth::user()->name }}!</h3>
                    <p class="mt-1 text-sm text-gray-600">Utilize os atalhos abaixo para agilizar suas
                        tarefas diárias.</p>
                </div>
            </div>

            {{-- SEÇÃO DE ALERTAS E AÇÕES PENDENTES --}}
            <div class="flex flex-col space-y-6 mb-8">
                {{-- Alerta para saídas pendentes (Veículos no Pátio) --}}
                @livewire('pending-exits')

                
                
            </div>

            {{-- PAINEL PRINCIPAL DE ESTATÍSTICAS --}}
            @livewire('dashboard-stats')

            {{-- CARTÕES DE ATALHO (Links) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                {{-- Atalho para Registrar Entrada/Saída --}}
                <a href="{{ route('entries.create') }}"
                    class="bg-white  p-6 rounded-lg shadow-sm flex items-center space-x-4 hover:bg-gray-50  transition-colors">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700 ">Registrar Entrada/Saída</p>
                        <p class="text-sm text-gray-500 ">Veículos particulares</p>
                    </div>
                </a>

                {{-- Atalho para Diário de Bordo --}}
                <a href="{{ route('fleet.index') }}"
                    class="bg-white  p-6 rounded-lg shadow-sm flex items-center space-x-4 hover:bg-gray-50  transition-colors">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700 ">Diário de Bordo</p>
                        <p class="text-sm text-gray-500 ">Frota oficial</p>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
