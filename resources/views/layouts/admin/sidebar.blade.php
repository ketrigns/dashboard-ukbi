<aside id="app-menu"
    class="hs-overlay fixed inset-y-0 start-0 z-60 hidden w-sidenav min-w-sidenav bg-slate-800 overflow-y-auto -translate-x-full transform transition-all duration-200 hs-overlay-open:translate-x-0 lg:bottom-0 lg:end-auto lg:z-30 lg:block lg:translate-x-0 rtl:translate-x-full rtl:hs-overlay-open:translate-x-0 rtl:lg:translate-x-0 print:hidden [--body-scroll:true] [--overlay-backdrop:true] lg:[--overlay-backdrop:false]">

    <div class="flex flex-col h-full">
        <!-- Sidenav Logo -->
        <div class="sticky top-0 flex h-topbar items-center justify-center px-3 bg-white">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo-web.png') }}" alt="logo" class="flex">
            </a>
        </div>

        <div class="p-4 h-[calc(100%-theme('spacing.topbar'))] flex-grow" data-simplebar>
            <!-- Menu -->
            <div class="admin-menu hs-accordion-group flex w-full flex-col gap-1">
                <div class="px-3 py-2 text-xs uppercase font-medium text-default-500"
                    style="list-style: none; margin: 0; padding: 0;">Menu</div>

                <div class="menu-item " style="list-style: none; margin: 0; padding: 0;">
                    <a class="{{ request()->routeIs('dashboard.*')
    ? 'active'
    : '' }} group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5"
                        href="{{ route('dashboard.index') }}">
                        <i class="i-lucide-calendar size-5"></i>
                        Dashboard
                    </a>
                </div>

                <div class="menu-item " style="list-style: none; margin: 0; padding: 0;">
                    <a class="{{ request()->routeIs('data-ukbi.*')
    ? 'active'
    : '' }} group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5"
                        href="{{ route('data-ukbi.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 32 32"><path fill="currentColor" d="M26 22a3.96 3.96 0 0 0-2.02.567l-3.813-3.814a4.965 4.965 0 0 0 0-5.506l2.547-2.547A3.028 3.028 0 1 0 21.3 9.286l-2.547 2.547a4.965 4.965 0 0 0-5.506 0L9.433 8.019A3.96 3.96 0 0 0 10 6a4 4 0 1 0-4 4a3.96 3.96 0 0 0 2.02-.567l3.813 3.814a4.965 4.965 0 0 0 0 5.506l-3.814 3.814A3.96 3.96 0 0 0 6 22a4 4 0 1 0 4 4a3.96 3.96 0 0 0-.567-2.02l3.814-3.813a5 5 0 0 0 1.753.732v3.285a3 3 0 1 0 2 0v-3.285a5 5 0 0 0 1.753-.732l3.814 3.814A3.96 3.96 0 0 0 22 26a4 4 0 1 0 4-4m-10-9a3 3 0 1 1-3 3a3.003 3.003 0 0 1 3-3M4 6a2 2 0 1 1 2 2a2 2 0 0 1-2-2m2 22a2 2 0 1 1 2-2a2 2 0 0 1-2 2m20 0a2 2 0 1 1 2-2a2.003 2.003 0 0 1-2 2"/></svg>
                        Data UKBI
                    </a>
                </div>

                <div class="menu-item" style="list-style: none; margin: 0; padding: 0;">
                    <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5 {{ request()->routeIs('hasil-data-mining.*')
    ? 'active'
    : '' }}" href="{{ route('hasil-data-mining.index') }}">
                        <i class="i-lucide-image size-5"></i>
                        Hasil Data Mining
                    </a>
                </div>
                @if(auth()->user()->role === 'admin')
                            <div class="mt-4 px-3 py-2 text-xs uppercase font-medium text-default-500"
                                style="list-style: none; margin: 0; padding: 0; margin-top: 20px;">AREA ADMINISTRATOR</div>
                            <div class="menu-item" style="list-style: none; margin: 0; padding: 0;">
                                <a class="group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5 {{ request()->routeIs('users.*')
                    ? 'active'
                    : '' }}" href="{{ route('users.index') }}">
                                    <i class="i-lucide-user-circle size-5"></i>
                                    Manajemen Pengguna
                                </a>
                            </div>

                @endif
            </div>
        </div>

    </div>
</aside>