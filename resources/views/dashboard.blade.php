<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Início') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @livewire('pending-exits')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Bem-vindo(a) ao SIGA-IF, {{ Auth::user()->name }}!</h3>
                    <p class="mt-1 text-sm text-gray-600">Utilize os atalhos abaixo para agilizar suas tarefas diárias.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white p-6 rounded-lg shadow-sm flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Veículos Particulares no Pátio</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ $privateVehiclesIn }}</p>
                    </div>
                    <div class="bg-indigo-100 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 003.375-3.375h1.5a1.125 1.125 0 011.125 1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375m15.75 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125h-1.5a3.375 3.375 0 00-3.375 3.375v1.875" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Frota Oficial em Viagem</p>
                        <p class="text-3xl font-bold text-teal-600">{{ $officialTripsOngoing }}</p>
                    </div>
                    <div class="bg-teal-100 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-teal-600" xmlns="http://www.w.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </div>
                </div>

                <a href="{{ route('entries.create') }}"
                    class="bg-white p-6 rounded-lg shadow-sm flex flex-col items-center justify-center text-center hover:bg-gray-50 transition-colors">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </div>
                    <p class="mt-3 font-semibold text-gray-700">Registrar Entrada/Saída</p>
                    <p class="text-xs text-gray-500">Veículos particulares</p>
                </a>

                <a href="{{ route('fleet.index') }}"
                    class="bg-white p-6 rounded-lg shadow-sm flex flex-col items-center justify-center text-center hover:bg-gray-50 transition-colors">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <p class="mt-3 font-semibold text-gray-700">Diário de Bordo</p>
                    <p class="text-xs text-gray-500">Frota oficial</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
