<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
    {{-- Container Principal da Navegação --}}
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logo-siga-navigation.png') }}" alt="SIGA-IF" class="block h-9 w-auto">
                    </a>
                </div>

                {{-- Links Principais (Desktop) --}}
                <div class="hidden sm:flex sm:items-center sm:gap-x-2 sm:ms-10">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                        Início
                    </x-nav-link>
                    <x-nav-link :href="route('entries.create')" :active="request()->routeIs('entries.create')" icon="arrows-right-left">
                        Entrada/Saída
                    </x-nav-link>
                    <x-nav-link :href="route('fleet.index')" :active="request()->routeIs('fleet.index')" icon="truck">
                        Frota Oficial
                    </x-nav-link>

                    {{-- Dropdown de Gerenciamento --}}
                    @if (in_array(Auth::user()->role, ['admin', 'fiscal', 'porteiro']))
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                @php
                                    $isActive = request()->routeIs(['vehicles.index', 'drivers.index', 'users.index']);
                                @endphp
                                <button @class([
                                    'inline-flex items-center gap-x-2 px-3 py-2 rounded-md text-sm font-medium focus:outline-none transition duration-150 ease-in-out',
                                    'bg-ifnmg-green-100 text-ifnmg-green-800' => $isActive,
                                    'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !$isActive,
                                ])>
                                    <x-icon name="cog" />
                                    <span>Gerenciamento</span>
                                    <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('vehicles.index')">Veículos</x-dropdown-link>
                                <x-dropdown-link :href="route('drivers.index')">Motoristas</x-dropdown-link>
                                @if (Auth::user()->role === 'admin')
                                    <x-dropdown-link :href="route('users.index')">Usuários</x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    @endif

                    {{-- Dropdown de Relatórios --}}
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            @php
                                $isActive = request()->routeIs(['guard.report', 'reports', 'fiscal.approval']);
                            @endphp
                            <button @class([
                                'inline-flex items-center gap-x-2 px-3 py-2 rounded-md text-sm font-medium focus:outline-none transition duration-150 ease-in-out',
                                'bg-ifnmg-green-100 text-ifnmg-green-800' => $isActive,
                                'text-gray-500 hover:bg-gray-100 hover:text-gray-700' => !$isActive,
                            ])>
                                <x-icon name="document-chart-bar" />
                                <span>Relatórios</span>
                                <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            @if (Auth::user()->role === 'porteiro')
                                <x-dropdown-link :href="route('guard.report')">Submeter Relatórios</x-dropdown-link>
                                <x-dropdown-link :href="route('reports')">Gerar PDF Mensal</x-dropdown-link>
                            @endif
                            @if (in_array(Auth::user()->role, ['admin', 'fiscal']))
                                <x-dropdown-link :href="route('reports')">Visão Geral</x-dropdown-link>
                                <x-dropdown-link :href="route('fiscal.approval')">Aprovações Pendentes</x-dropdown-link>
                            @endif
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            {{-- Dropdown do Usuário (Direita) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Meu Perfil') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Botão Hamburguer (Mobile) --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Menu Mobile Responsivo --}}
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Início') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('entries.create')"
                :active="request()->routeIs('entries.create')">{{ __('Entrada/Saída') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('fleet.index')"
                :active="request()->routeIs('fleet.index')">{{ __('Frota Oficial') }}</x-responsive-nav-link>
        </div>

        {{-- Seções do Menu Mobile --}}
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Meu Perfil') }}</x-responsive-nav-link>

                {{-- Gerenciamento (Mobile) --}}
                @if (in_array(Auth::user()->role, ['admin', 'fiscal', 'porteiro']))
                    <div class="border-t border-gray-200"></div>
                    <div class="px-4 pt-3 pb-1 text-xs text-gray-400 font-semibold">Gerenciamento</div>
                    <x-responsive-nav-link :href="route('vehicles.index')">Veículos</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('drivers.index')">Motoristas</x-responsive-nav-link>
                    @if (Auth::user()->role === 'admin')
                        <x-responsive-nav-link :href="route('users.index')">Usuários</x-responsive-nav-link>
                    @endif
                @endif

                {{-- Relatórios (Mobile) --}}
                <div class="border-t border-gray-200"></div>
                <div class="px-4 pt-3 pb-1 text-xs text-gray-400 font-semibold">Relatórios</div>
                @if (Auth::user()->role === 'porteiro')
                    <x-responsive-nav-link :href="route('guard.report')">Submeter Relatórios</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports')">Gerar PDF Mensal</x-responsive-nav-link>
                @endif
                @if (in_array(Auth::user()->role, ['admin', 'fiscal']))
                    <x-responsive-nav-link :href="route('reports')">Visão Geral</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('fiscal.approval')">Aprovações Pendentes</x-responsive-nav-link>
                @endif

                {{-- Logout (Mobile) --}}
                <div class="border-t border-gray-200"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Sair') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
