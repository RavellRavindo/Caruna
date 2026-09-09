<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 min-w-0 justify-between">
            <div class="flex min-w-0">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-extrabold tracking-wider text-indigo-600 transition hover:text-indigo-700 sm:text-2xl">
                        CARUNA
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-6 lg:-my-px lg:ms-10 lg:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Menu Khusus Admin -->
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.caregivers.index')" :active="request()->routeIs('admin.caregivers.*')">
                            {{ __('Verifikasi Caregiver') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.withdrawals.index')" :active="request()->routeIs('admin.withdrawals.*')">
                            {{ __('Manajemen Dana') }}
                        </x-nav-link>
                    @endif

                    <!-- Tambahan: Menu Khusus Caregiver (Opsional, untuk melengkapi) -->
                    @if(Auth::check() && Auth::user()->role === 'caregiver')
                        @if(Auth::user()->caregiver?->isVerified())
                            <x-nav-link :href="route('caregiver.bookings')" :active="request()->routeIs('caregiver.bookings')">
                                {{ __('Pesanan Masuk') }}
                            </x-nav-link>
                            <x-nav-link :href="route('caregiver.wallet')" :active="request()->routeIs('caregiver.wallet')">
                                {{ __('Dompet Saya') }}
                            </x-nav-link>
                        @endif
                    @endif

                    @if(Auth::check() && Auth::user()->role === 'client')
                        <x-nav-link :href="route('caregivers.index')" :active="request()->routeIs('caregivers.*')">
                            {{ __('Caregiver') }}
                        </x-nav-link>
                        <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
                            {{ __('Pasien') }}
                        </x-nav-link>
                        <x-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">
                            {{ __('Pesanan') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden shrink-0 sm:ms-6 sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex max-w-40 items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                            <div class="truncate">{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex shrink-0 items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Menu Khusus Admin (Mobile) -->
            @if(Auth::check() && Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.caregivers.index')" :active="request()->routeIs('admin.caregivers.*')">
                    {{ __('Verifikasi Caregiver') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.withdrawals.index')" :active="request()->routeIs('admin.withdrawals.*')">
                    {{ __('Manajemen Dana') }}
                </x-responsive-nav-link>
            @endif

            <!-- Tambahan: Menu Khusus Caregiver (Mobile) -->
            @if(Auth::check() && Auth::user()->role === 'caregiver')
                @if(Auth::user()->caregiver?->isVerified())
                    <x-responsive-nav-link :href="route('caregiver.bookings')" :active="request()->routeIs('caregiver.bookings')">
                        {{ __('Pesanan Masuk') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('caregiver.wallet')" :active="request()->routeIs('caregiver.wallet')">
                        {{ __('Dompet Saya') }}
                    </x-responsive-nav-link>
                @endif
            @endif

            @if(Auth::check() && Auth::user()->role === 'client')
                <x-responsive-nav-link :href="route('caregivers.index')" :active="request()->routeIs('caregivers.*')">
                    {{ __('Caregiver') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
                    {{ __('Pasien Saya') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bookings.index')" :active="request()->routeIs('bookings.*')">
                    {{ __('Pesanan Saya') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="truncate text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                <div class="truncate text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
