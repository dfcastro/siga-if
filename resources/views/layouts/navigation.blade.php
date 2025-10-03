<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logo-siga-navigation.png') }}" alt="SIGA-IF" class="block h-9 w-auto">
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- LINKS PRINCIPAIS --}}
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Início') }}
                    </x-nav-link>
                    <x-nav-link :href="route('entries.create')" :active="request()->routeIs('entries.create')">
                        {{ __('Entrada/Saída') }}
                    </x-nav-link>
                    <x-nav-link :href="route('fleet.index')" :active="request()->routeIs('fleet.index')">
                        {{ __('Frota Oficial') }}
                    </x-nav-link>

                    {{-- MENU DROPDOWN DE GERENCIAMENTO (Agora visível para o Porteiro) --}}
                    @if (in_array(Auth::user()->role, ['admin', 'fiscal', 'porteiro']))
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('vehicles.index') || request()->routeIs('drivers.index') || request()->routeIs('users.index') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                        <div>Gerenciamento</div>
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
                                    <x-dropdown-link :href="route('vehicles.index')">Veículos</x-dropdown-link>
                                    <x-dropdown-link :href="route('drivers.index')">Motoristas</x-dropdown-link>
                                    @if (Auth::user()->role === 'admin')
                                        <x-dropdown-link :href="route('users.index')">Usuários</x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif

                    {{-- MENU DROPDOWN DE RELATÓRIOS --}}
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('guard.report') || request()->routeIs('reports.personal') || request()->routeIs('reports') || request()->routeIs('fiscal.approval') ? 'border-indigo-400' : 'border-transparent' }} text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>Relatórios</div>
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
                                @if (Auth::user()->role === 'porteiro')
                                    <x-dropdown-link :href="route('guard.report')">Submeter Relatórios</x-dropdown-link>
                                    <x-dropdown-link :href="route('reports.personal')">Gerar PDF Mensal</x-dropdown-link>
                                @endif
                                @if (in_array(Auth::user()->role, ['admin', 'fiscal']))
                                    <x-dropdown-link :href="route('reports')">Visão Geral</x-dropdown-link>
                                    <x-dropdown-link :href="route('fiscal.approval')">Aprovações Pendentes</x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- (Código do dropdown do usuário, sem alterações) --}}
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                {{-- (Código do botão hamburger, sem alterações) --}}
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Início') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('entries.create')"
                :active="request()->routeIs('entries.create')">{{ __('Entrada/Saída') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('fleet.index')"
                :active="request()->routeIs('fleet.index')">{{ __('Frota Oficial') }}</x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Meu Perfil') }}</x-responsive-nav-link>

                {{-- SEÇÃO DE GERENCIAMENTO (MOBILE - Agora visível para o Porteiro) --}}
                @if (in_array(Auth::user()->role, ['admin', 'fiscal', 'porteiro']))
                    <div class="border-t border-gray-200"></div>
                    <div class="px-4 pt-2 text-xs text-gray-400">Gerenciamento</div>
                    <x-responsive-nav-link :href="route('vehicles.index')">Veículos</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('drivers.index')">Motoristas</x-responsive-nav-link>
                    @if (Auth::user()->role === 'admin')
                        <x-responsive-nav-link :href="route('users.index')">Usuários</x-responsive-nav-link>
                    @endif
                @endif

                {{-- SEÇÃO DE RELATÓRIOS (MOBILE) --}}
                <div class="border-t border-gray-200"></div>
                <div class="px-4 pt-2 text-xs text-gray-400">Relatórios</div>
                @if (Auth::user()->role === 'porteiro')
                    <x-responsive-nav-link :href="route('guard.report')">Submeter Relatórios</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('reports.personal')">Gerar PDF Mensal</x-responsive-nav-link>
                @endif
                @if (in_array(Auth::user()->role, ['admin', 'fiscal']))
                    <x-responsive-nav-link :href="route('reports')">Visão Geral</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('fiscal.approval')">Aprovações Pendentes</x-responsive-nav-link>
                @endif

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
