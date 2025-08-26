<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logo-siga-navigation.png') }}" alt="SIGA-IF" class="block h-9 w-auto">
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 10.414V18a1 1 0 01-1 1h-2a1 1 0 01-1-1v-4a1 1 0 00-1-1H8a1 1 0 00-1 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V10.414a1 1 0 01.293-.707l7-7z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('Início') }}
                    </x-nav-link>
                    <x-nav-link :href="route('entries.create')" :active="request()->routeIs('entries.create')">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('Entrada/Saída') }}
                    </x-nav-link>
                    <x-nav-link :href="route('fleet.index')" :active="request()->routeIs('fleet.index')">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M3.5 2.75a.75.75 0 00-1.5 0v14.5a.75.75 0 001.5 0v-4.392l1.657-.348a6.449 6.449 0 014.271.572 7.948 7.948 0 005.965.524l2.078-1.038a.75.75 0 000-1.342l-2.078-1.038a7.948 7.948 0 00-5.965.524 6.449 6.449 0 01-4.271.572L3.5 4.642V2.75z" />
                        </svg>
                        {{ __('Frota Oficial') }}
                    </x-nav-link>

                    {{-- Links de Gerenciamento para Perfis Autorizados --}}
                    @if (in_array(auth()->user()->role, ['admin', 'porteiro', 'fiscal']))
                        <x-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.index')">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M11.622 3.22a.75.75 0 01.458 1.156l-3.374 5.624a.75.75 0 01-1.156.458L3.22 7.142a.75.75 0 01.458-1.156l3.374 2.024L9.86 3.22a.75.75 0 011.762 0zm-2.326 9.24a.75.75 0 01-1.156-.458L4.766 6.382a.75.75 0 011.156-.458l3.374 5.624-2.024 3.374z"
                                    clip-rule="evenodd" />
                                <path fill-rule="evenodd"
                                    d="M12.234 4.376a.75.75 0 011.156-.458l3.374 2.024a.75.75 0 010 1.342l-3.374 2.024a.75.75 0 01-1.156-.458L13.622 7.5l-1.388-2.314zM10.75 12a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5a.75.75 0 01.75-.75z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ __('Veículos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('drivers.index')" :active="request()->routeIs('drivers.index')">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.095a1.23 1.23 0 00.41-1.412A9.957 9.957 0 0010 12c-2.31 0-4.438.784-6.131 2.095z" />
                            </svg>
                            {{ __('Motoristas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('reports')" :active="request()->routeIs('reports')">
                            {{-- Ícone SVG (Gráfico de Barras) --}}
                            <svg class="h-5 w-5 me-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            {{ __('Relatórios') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>{{ Auth::user()->name }}</div>
                            </div>

                            <div class="ml-1">
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
                        {{-- Itens do Dropdown de Gerenciamento --}}
                        @if (in_array(auth()->user()->role, ['admin', 'porteiro', 'fiscal']))
                            <div class="border-b border-gray-200">
                                <div class="px-4 py-2 text-xs text-gray-400">Gerenciamento</div>
                                {{-- Apenas Admin pode ver Usuários --}}
                                @if (auth()->user()->role === 'admin')
                                    <x-dropdown-link :href="route('users.index')">
                                        {{ __('Usuários') }}
                                    </x-dropdown-link>
                                @endif
                                <x-dropdown-link :href="route('vehicles.index')">
                                    {{ __('Veículos') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('drivers.index')">
                                    {{ __('Motoristas') }}
                                </x-dropdown-link>

                            </div>
                        @endif

                        {{-- Itens do Dropdown de Perfil --}}
                        <div class="px-4 py-2 text-xs text-gray-400">Conta</div>
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Meu Perfil') }}
                        </x-dropdown-link>

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

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Início') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('entries.create')" :active="request()->routeIs('entries.create')">
                {{ __('Entrada/Saída') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('fleet.index')" :active="request()->routeIs('fleet.index')">
                {{ __('Frota Oficial') }}
            </x-responsive-nav-link>

            {{-- Links de Gerenciamento para Perfis Autorizados (Mobile) --}}
            @if (in_array(auth()->user()->role, ['admin', 'porteiro', 'fiscal']))
                <x-responsive-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.index')">
                    {{ __('Veículos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('drivers.index')" :active="request()->routeIs('drivers.index')">
                    {{ __('Motoristas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports')" :active="request()->routeIs('reports')">
                    {{ __('Relatórios') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if (in_array(auth()->user()->role, ['admin', 'porteiro', 'fiscal']))
                    <div class="border-b border-gray-200 pb-2 mb-2">
                        <div class="px-4 text-sm font-semibold text-gray-500">Gerenciamento</div>
                        @if (auth()->user()->role === 'admin')
                            <x-responsive-nav-link :href="route('users.index')">
                                {{ __('Usuários') }}
                            </x-responsive-nav-link>
                        @endif
                        <x-responsive-nav-link :href="route('vehicles.index')">
                            {{ __('Veículos') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('drivers.index')">
                            {{ __('Motoristas') }}
                        </x-responsive-nav-link>
                    </div>
                @endif
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Meu Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Sair') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
