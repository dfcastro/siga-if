<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
    {{-- CONTAINER PRINCIPAL DA NAVEGAÇÃO --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- LADO ESQUERDO: Logo e Links --}}
            <div class="flex items-center">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="transition-transform hover:scale-105">
                        <img src="{{ asset('images/logo-siga-navigation.png') }}" alt="SIGA-IF" class="block h-9 w-auto">
                    </a>
                </div>

                {{-- Links Principais (Desktop) --}}
                <div class="hidden lg:flex lg:items-center lg:gap-x-1 lg:ms-8 h-full">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                        Início
                    </x-nav-link>

                    @if (in_array(Auth::user()->role, ['admin', 'porteiro']))
                        <x-nav-link :href="route('entries.create')" :active="request()->routeIs('entries.create')" icon="arrows-right-left">
                            Entrada/Saída
                        </x-nav-link>
                        <x-nav-link :href="route('fleet.index')" :active="request()->routeIs('fleet.index')" icon="truck">
                            Frota Oficial
                        </x-nav-link>
                    @endif

                    {{-- DROPDOWN: GERENCIAMENTO --}}
                    @if (in_array(Auth::user()->role, ['admin', 'fiscal', 'porteiro']))
                        <div class="hidden sm:flex sm:items-center h-full ml-2">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    @php
                                        $isActive = request()->routeIs([
                                            'vehicles.index',
                                            'drivers.index',
                                            'users.index',
                                        ]);
                                    @endphp
                                    <button @class([
                                        'inline-flex items-center px-3 py-2 text-sm font-medium leading-5 transition duration-150 ease-in-out h-16 border-b-2 focus:outline-none gap-2',
                                        'border-ifnmg-green text-gray-900' => $isActive,
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => !$isActive,
                                    ])>
                                        <x-icon name="cog" class="w-4 h-4" />
                                        <span>Cadastros</span>
                                        <svg class="ml-1 h-4 w-4 fill-current opacity-70"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('vehicles.index')">🚗 Veículos</x-dropdown-link>
                                    <x-dropdown-link :href="route('drivers.index')">🧑 Motoristas</x-dropdown-link>
                                    @if (Auth::user()->role === 'admin')
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <x-dropdown-link :href="route('users.index')">👥 Usuários do Sistema</x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    {{-- DROPDOWN: RELATÓRIOS E AUDITORIA --}}
                    <div class="hidden sm:flex sm:items-center h-full ml-2">
                        <x-dropdown align="left" width="56">
                            <x-slot name="trigger">
                                @php
                                    $isActive = request()->routeIs([
                                        'guard.report',
                                        'reports',
                                        'fiscal.approval',
                                        'reports.status',
                                    ]);
                                @endphp
                                <button @class([
                                    'inline-flex items-center px-3 py-2 text-sm font-medium leading-5 transition duration-150 ease-in-out h-16 border-b-2 focus:outline-none gap-2',
                                    'border-ifnmg-green text-gray-900' => $isActive,
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' => !$isActive,
                                ])>
                                    <x-icon name="document-chart-bar" class="w-4 h-4" />
                                    <span>Relatórios</span>
                                    <svg class="ml-1 h-4 w-4 fill-current opacity-70" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                {{-- Ações Exclusivas --}}
                                @if (Auth::user()->role === 'porteiro')
                                    <x-dropdown-link :href="route('guard.report')" class="font-semibold text-ifnmg-green">
                                        Submeter Fechamento Mensal
                                    </x-dropdown-link>
                                    <div class="border-t border-gray-100 my-1"></div>
                                @endif

                                @if (in_array(Auth::user()->role, ['admin', 'fiscal']))
                                    <x-dropdown-link :href="route('fiscal.approval')" class="font-semibold text-ifnmg-green">
                                        Aprovações Pendentes
                                    </x-dropdown-link>
                                    <div class="border-t border-gray-100 my-1"></div>
                                @endif

                                {{-- Ações Comuns --}}
                                <x-dropdown-link :href="route('reports.status')">Acompanhar Status</x-dropdown-link>
                                <x-dropdown-link :href="route('reports')">Pesquisa e Extratos (PDF)</x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                </div>
            </div>

            {{-- LADO DIREITO: Perfil e Menu Mobile --}}
            <div class="flex items-center">

                {{-- Dropdown do Usuário (Desktop) --}}
                <div class="hidden lg:flex lg:items-center lg:ms-6 h-full">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 h-16 border-b-2 border-transparent">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-500 font-bold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col items-start leading-tight">
                                        <span
                                            class="font-bold text-gray-800">{{ explode(' ', trim(Auth::user()->name))[0] }}</span>
                                        <span
                                            class="text-[10px] uppercase tracking-wider text-gray-400">{{ Auth::user()->role }}</span>
                                    </div>
                                </div>
                                <svg class="ml-2 h-4 w-4 fill-current opacity-70" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">⚙️ {{ __('Meu Perfil') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-red-600 hover:bg-red-50">
                                    🚪 {{ __('Sair do Sistema') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- Botão Hamburguer (Mobile) --}}
                <div class="-me-2 flex items-center lg:hidden">
                    <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MENU MOBILE RESPONSIVO --}}
    {{-- ========================================== --}}
    <div x-show="open" x-collapse class="lg:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg">

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">🏠 {{ __('Início') }}</x-responsive-nav-link>

            @if (in_array(Auth::user()->role, ['admin', 'porteiro']))
                <x-responsive-nav-link :href="route('entries.create')" :active="request()->routeIs('entries.create')">🔄
                    {{ __('Entrada/Saída') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('fleet.index')" :active="request()->routeIs('fleet.index')">🚗
                    {{ __('Frota Oficial') }}</x-responsive-nav-link>
            @endif
        </div>

        {{-- Cadastros Mobile --}}
        @if (in_array(Auth::user()->role, ['admin', 'fiscal', 'porteiro']))
            <div class="px-4 py-2 bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider">Cadastros</div>
            <div class="space-y-1 pb-2">
                <x-responsive-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.index')">Veículos</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('drivers.index')" :active="request()->routeIs('drivers.index')">Motoristas</x-responsive-nav-link>
                @if (Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">Usuários</x-responsive-nav-link>
                @endif
            </div>
        @endif

        {{-- Relatórios Mobile --}}
        <div class="px-4 py-2 bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider">Relatórios e
            Auditoria</div>
        <div class="space-y-1 pb-2">
            @if (Auth::user()->role === 'porteiro')
                <x-responsive-nav-link :href="route('guard.report')" :active="request()->routeIs('guard.report')"
                    class="font-semibold text-ifnmg-green">Submeter Fechamento</x-responsive-nav-link>
            @endif

            @if (in_array(Auth::user()->role, ['admin', 'fiscal']))
                <x-responsive-nav-link :href="route('fiscal.approval')" :active="request()->routeIs('fiscal.approval')"
                    class="font-semibold text-ifnmg-green">Aprovações Pendentes</x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('reports.status')" :active="request()->routeIs('reports.status')">Acompanhar Status</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports')" :active="request()->routeIs('reports')">Pesquisa e Extratos
                (PDF)</x-responsive-nav-link>
        </div>

        {{-- Área do Usuário Mobile --}}
        <div class="pt-4 pb-4 border-t border-gray-200 bg-gray-50">
            <div class="px-4 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-white border border-gray-300 flex items-center justify-center text-gray-500 font-bold text-lg shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-base text-gray-800 leading-tight">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-4 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">⚙️ {{ __('Configurar Perfil') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                        🚪 {{ __('Sair do Sistema') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>

    </div>
</nav>
