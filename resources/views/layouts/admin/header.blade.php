<header class="app-header sticky top-0 z-50 min-h-topbar flex items-center bg-white">
    <div class="px-6 w-full flex items-center justify-between gap-4">
        <div class="flex items-center gap-5">
            <!-- Sidenav Menu Toggle Button -->
            <button
                class="flex items-center text-default-500 rounded-full cursor-pointer p-2 bg-white border border-default-200 hover:bg-primary/15 hover:text-primary hover:border-primary/5 transition-all"
                data-hs-overlay="#app-menu" aria-label="Toggle navigation">
                <i class="i-lucide-align-left text-2xl"></i>
            </button>

            <!-- Topbar Brand Logo -->
            <a href="index.html" class="md:hidden flex">
                <img src="{{ asset('assets/images/logo-ukbi.png') }}" class="h-5" alt="Small logo">
            </a>

            <!-- Topbar Search -->

        </div>

        <div class="flex items-center gap-5">
            <!-- Profile Dropdown Button -->
            <div class="relative">
                <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                    <button type="button" class="hs-dropdown-toggle">
                        <img src="{{ auth()->user()->profile_pic
    ? asset('storage/' . auth()->user()->profile_pic)
    : asset('assets/images/gbr-admin.jpeg') }}" class="rounded-full h-10">


                    </button>
                    <div
                        class="hs-dropdown-menu duration mt-2 min-w-48 rounded-lg border border-default-200 bg-white p-2 opacity-0 shadow-md transition-[opacity,margin] hs-dropdown-open:opacity-100 hidden">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                href="{{ route('profile') }}">
                                Profile
                            </a>

                            <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                href="{{ route('logout') }}" onclick="event.preventDefault();
                    this.closest('form').submit();">
                                Log Out
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>