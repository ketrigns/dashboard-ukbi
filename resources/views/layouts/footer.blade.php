<footer class="bg-[#1F2859] text-gray-300 pt-16 pb-8 border-t border-[#3445a1]/30">
  <div class="mx-auto px-6">

    <!-- Grid Utama -->
    <!-- Mengubah layout menjadi 3 kolom (md:grid-cols-3) agar proporsional setelah 1 kolom dihapus -->
    <div class="grid grid-cols-1 md:grid-cols-3 print:grid-cols-3 gap-8 mb-12">
      <!-- Kolom 1: Tentang UKBI -->
      <div class="flex flex-col">
        <div class="flex items-center gap-3 mb-5">
          <!-- Placeholder Logo (Icon Buku/Bahasa) -->
          <div class="bg-white rounded">

            <img src="{{ asset('assets/images/logo-web.png') }}" alt="Logo" class="h-10">
          </div>
          
        </div>
        <p class="text-sm leading-relaxed text-gray-300 mb-6 text-justify">
          UKBI adalah sarana uji untuk mengukur tingkat kemahiran seseorang dalam berbahasa Indonesia, baik lisan maupun
          tulis, yang terdiri dari lima seksi pengujian.
        </p>

        <!-- Social Media Icons -->
        <div class="flex gap-3">
          <a href="#"
            class="w-8 h-8 rounded bg-[#2c397a] flex items-center justify-center text-white hover:bg-white hover:text-[#1F2859] transition-all duration-300">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#"
            class="w-8 h-8 rounded bg-[#2c397a] flex items-center justify-center text-white hover:bg-white hover:text-[#1F2859] transition-all duration-300">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#"
            class="w-8 h-8 rounded bg-[#2c397a] flex items-center justify-center text-white hover:bg-white hover:text-[#1F2859] transition-all duration-300">
            <i class="fab fa-youtube"></i>
          </a>
          <a href="#"
            class="w-8 h-8 rounded bg-[#2c397a] flex items-center justify-center text-white hover:bg-white hover:text-[#1F2859] transition-all duration-300">
            <i class="fas fa-globe"></i>
          </a>
        </div>
      </div>

      <!-- Kolom 2: Menu Navigasi (Sesuai Header) -->
      <div>
        <h3 class="text-white font-bold text-lg mb-6 relative inline-block">
          Jelajahi Data
          <span class="absolute bottom-[-8px] left-0 w-10 h-1 bg-yellow-500 rounded-full"></span>
        </h3>
        <div class="space-y-3 text-sm">
          <div>
            <a href="{{ url('/') }}"
              class="flex items-center hover:text-white hover:translate-x-1 transition-all duration-300 group">
              <i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400 group-hover:text-yellow-400"></i> Dashboard
            </a>
          </div>
          <div>
            <a href="{{ url('/kategori') }}"
              class="flex items-center hover:text-white hover:translate-x-1 transition-all duration-300 group">
              <i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400 group-hover:text-yellow-400"></i> Kategori
            </a>
          </div>
          <div>
            <a href="{{ url('/predikat') }}"
              class="flex items-center hover:text-white hover:translate-x-1 transition-all duration-300 group">
              <i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400 group-hover:text-yellow-400"></i> Predikat
            </a>
          </div>
          <div>
            <a href="{{ url('/wilayah') }}"
              class="flex items-center hover:text-white hover:translate-x-1 transition-all duration-300 group">
              <i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400 group-hover:text-yellow-400"></i> Wilayah
            </a>
          </div>
          <div>
            <a href="{{ url('/tahun') }}"
              class="flex items-center hover:text-white hover:translate-x-1 transition-all duration-300 group">
              <i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400 group-hover:text-yellow-400"></i> Tahun
            </a>
          </div>
          <div>
            <a href="{{ url('/data-mining') }}"
              class="flex items-center hover:text-white hover:translate-x-1 transition-all duration-300 group">
              <i class="fas fa-chevron-right text-[10px] mr-2 text-blue-400 group-hover:text-yellow-400"></i> Hasil Data
              Mining
            </a>
          </div>
        </div>
      </div>

      <!-- Kolom 3 (Sebelumnya Kolom 4): Kontak & Bantuan -->
      <div>
        <h3 class="text-white font-bold text-lg mb-6 relative inline-block">
          Kontak Kami
          <span class="absolute bottom-[-8px] left-0 w-10 h-1 bg-yellow-500 rounded-full"></span>
        </h3>

        <div class="space-y-4">
          <!-- Email -->
          <div class="flex items-start gap-3 text-sm group">
            <div
              class="w-8 h-8 shrink-0 rounded-full bg-[#2c397a] flex items-center justify-center text-yellow-400 group-hover:bg-yellow-400 group-hover:text-[#1F2859] transition-colors">
              <i class="fas fa-envelope"></i>
            </div>
            <span class="group-hover:text-white transition-colors mt-1.5">bahasajambi@kemendikbud.go.id</span>
          </div>

          <!-- Telepon -->
          <div class="flex items-start gap-3 text-sm group">
            <div
              class="w-8 h-8 shrink-0 rounded-full bg-[#2c397a] flex items-center justify-center text-yellow-400 group-hover:bg-yellow-400 group-hover:text-[#1F2859] transition-colors">
              <i class="fas fa-phone"></i>
            </div>
            <span class="group-hover:text-white transition-colors mt-1.5">(0741) 669466</span>
          </div>

          <!-- Alamat -->
          <div class="flex items-start gap-3 text-sm group">
            <!-- shrink-0: Mencegah ikon menjadi lonjong/gepeng -->
            <div
              class="w-8 h-8 shrink-0 rounded-full bg-[#2c397a] flex items-center justify-center text-yellow-400 group-hover:bg-yellow-400 group-hover:text-[#1F2859] transition-colors">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <!-- mt-1.5: Menurunkan teks sedikit agar sejajar dengan tengah ikon -->
            <span class="group-hover:text-white transition-colors mt-1 leading-relaxed">
              Jalan Arif Rahman Hakim No. 101, Telanaipura, Jambi, Indonesia, 36124
            </span>
          </div>
        </div>
      </div>

    </div>

    <!-- Garis Pemisah Tipis -->
    <div class="border-t border-white/10 my-8"></div>

    <!-- Footer Bottom -->
    <div class="flex flex-col md:flex-row justify-between items-center text-xs text-gray-400">
      <p>&copy; 2025 Dashboard UKBI | Balai Bahasa Provinsi Jambi.</p>
    </div>
  </div>
</footer>