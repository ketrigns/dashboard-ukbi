<header class="shadow-lg pb-2 bg-white">
  <div class="flex items-center justify-between px-5 py-3">
    <!-- Logo -->
    <img src="{{ asset('assets/images/logo-web.png') }}" alt="Logo" class="h-10">

    <!-- Tombol Hamburger -->
    <button id="menu-toggle" class="md:hidden text-[#1F2859] focus:outline-none">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
        class="w-7 h-7">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
  </div>

  <!-- Navigasi -->
  <nav id="nav-menu"
    class="flex flex-col md:flex-row md:items-center md:justify-between md:gap-0 gap-2 px-5 transition-all duration-300 ease-in-out overflow-hidden max-h-0 md:max-h-none md:opacity-100 opacity-0 md:opacity-100">
    <a href="{{ url('/') }}"
      class="flex-grow border-e border-[#1F2859] text-center font-medium py-2
        {{ Request::is('/') ? 'bg-[#1F2859] text-white rounded-full' : 'hover:text-[#1F2859]' }}">
      Dashboard
    </a>

    <a href="{{ url('/kategori') }}"
      class="flex-grow border-e border-[#1F2859] text-center font-medium py-2 
        {{ Request::is('kategori*') ? 'bg-[#1F2859] text-white rounded-full' : 'hover:text-[#1F2859]' }}">
      Kategori
    </a>

    <a href="{{ url('/predikat') }}"
      class="flex-grow border-e border-[#1F2859] text-center font-medium py-2 
        {{ Request::is('predikat*') ? 'bg-[#1F2859] text-white rounded-full' : 'hover:text-[#1F2859]' }}">
      Predikat
    </a>

    <a href="{{ url('/wilayah') }}"
      class="flex-grow border-e border-[#1F2859] text-center font-medium py-2 
        {{ Request::is('wilayah*') ? 'bg-[#1F2859] text-white rounded-full' : 'hover:text-[#1F2859]' }}">
      Wilayah
    </a>

    <a href="{{ url('/tahun') }}"
      class="flex-grow text-center font-medium py-2 
        {{ Request::is('tahun*') ? 'bg-[#1F2859] text-white rounded-full' : 'hover:text-[#1F2859]' }}">
      Tahun
    </a>
  </nav>

  <script>
    const toggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');

    toggle.addEventListener('click', () => {
      if (navMenu.classList.contains('max-h-0')) {
        navMenu.classList.remove('max-h-0', 'opacity-0');
        navMenu.classList.add('max-h-96', 'opacity-100', 'py-3');
      } else {
        navMenu.classList.add('max-h-0', 'opacity-0');
        navMenu.classList.remove('max-h-96', 'opacity-100', 'py-3');
      }
    });
  </script>
</header>
