@extends('layouts.admin.app')

@section('content')
<div class="flex items-center gap-3 text-sm font-semibold mb-5">
        <a href="{{ route('data-ukbi.index') }}" class="text-sm font-medium text-default-700">Data UKBI</a>
        <i class="i-tabler-chevron-right text-lg flex-shrink-0 text-default-500 rtl:rotate-180"></i>
        <p class="text-sm font-bold text-default-900">Edit Data UKBI</p>
    </div>
  <div class="flex flex-col gap-6">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Data UKBI</h4>
      </div>

      @if(auth()->user()->role === 'petugas')
        {{-- Arahkan ke route khusus untuk mengajukan perubahan --}}
        <form action="{{ route('data-ukbi.propose-update', $dataUkbi) }}" method="POST" class="p-6">
      @else
        {{-- Admin bisa langsung update datanya --}}
        <form action="{{ route('data-ukbi.update', $dataUkbi) }}" method="POST" class="p-6">
      @endif
        @csrf

        @if(auth()->user()->role === 'admin')
          @method('PUT') {{-- Biasanya update utama pakai PUT/PATCH --}}
        @endif

        <div class="grid lg:grid-cols-2 gap-6">
          <div>
            <label for="no_pendaftaran" class="text-default-800 text-sm font-medium inline-block mb-2">No
              Pendaftaran</label>
            <input type="text" name="no_pendaftaran" id="no_pendaftaran" class="form-input"
              value="{{ old('no_pendaftaran', $dataUkbi->no_pendaftaran) }}">
          </div>

          <div>
            <label for="tanggal_ujian" class="text-default-800 text-sm font-medium inline-block mb-2">Tanggal
              Ujian</label>
            <input class="form-input" name="tanggal_ujian" id="tanggal_ujian" type="date"
              value="{{ old('tanggal_ujian', $dataUkbi->tanggal_ujian) }}">
          </div>

          <div>
            <label for="nama_peserta" class="text-default-800 text-sm font-medium inline-block mb-2">Nama Peserta</label>
            <input type="text" name="nama_peserta" id="nama_peserta" class="form-input"
              value="{{ old('nama_peserta', $dataUkbi->nama_peserta) }}">
          </div>

          <div>
            {{-- <label for="terdaftar_sbg" class="text-default-800 text-sm font-medium inline-block mb-2">Terdaftar
              Sebagai</label>
            <input type="text" name="terdaftar_sbg" id="terdaftar_sbg" class="form-input"
              value="{{ old('terdaftar_sbg', $dataUkbi->terdaftar_sbg) }}"> --}}
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Terdaftar
              Sebagai</label>
            <div class="combobox-container">
              <input type="text" name="terdaftar_sbg" id="custom-input-terdaftar-sbg"
                placeholder="Ketik atau pilih Profesi..." class="form-input" autocomplete="off"
                value="{{ old('terdaftar_sbg', $dataUkbi->terdaftar_sbg) }}">
              <div class="options-list" id="options-list-terdaftar-sbg"></div>
            </div>
          </div>

          <div>
            <label for="jenis_kelamin" class="text-default-800 text-sm font-medium inline-block mb-2">Jenis
              Kelamin</label>
            <select name="jenis_kelamin" class="form-select" id="jenis_kelamin">
              <option value="Laki-laki" {{ old('jenis_kelamin', $dataUkbi->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
              <option value="Perempuan" {{ old('jenis_kelamin', $dataUkbi->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
          </div>

          <div>
            <label for="tempat_lahir" class="text-default-800 text-sm font-medium inline-block mb-2">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-input"
              value="{{ old('tempat_lahir', $dataUkbi->tempat_lahir) }}">
          </div>

          <div>
            <label for="tanggal_lahir" class="text-default-800 text-sm font-medium inline-block mb-2">Tanggal
              Lahir</label>
            <input class="form-input" name="tanggal_lahir" id="tanggal_lahir" type="date"
              value="{{ old('tanggal_lahir', $dataUkbi->tanggal_lahir) }}">
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Kota</label>
            <div class="combobox-container">
              <input type="text" name="kota" id="custom-input" placeholder="Ketik atau pilih Kota..." class="form-input"
                autocomplete="off" value="{{ old('kota', $dataUkbi->kota) }}">
              <div class="options-list" id="options-list"></div>
            </div>
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Titik Koordinat
              Peta</label>
            <input type="text" name="titik_koordinat_peta" id="simpleinput" class="form-input"
              value="{{ old('titik_koordinat_peta', $dataUkbi->titik_koordinat_peta) }}">
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Instansi</label>
            <div class="combobox-container">
              <input type="text" name="instansi" id="custom-input-instansi" placeholder="Ketik atau pilih Instansi..."
                class="form-input" autocomplete="off" value="{{ old('instansi', $dataUkbi->instansi) }}">
              <div class="options-list" id="options-list-instansi"></div>
            </div>
          </div>

          @for ($i = 1; $i <= 5; $i++)
            <div>
              <label for="seksi_{{ $i }}" class="text-default-800 text-sm font-medium inline-block mb-2">Seksi
                {{ $i }}</label>
              <input class="form-input" id="seksi_{{ $i }}" type="number" name="seksi_{{ $i }}"
                value="{{ old('seksi_' . $i, $dataUkbi->{'seksi_' . $i}) }}">
            </div>
          @endfor

          <div>
            <label for="skor" class="text-default-800 text-sm font-medium inline-block mb-2">Skor</label>
            <input class="form-input" id="skor" type="number" name="skor" value="{{ old('skor', $dataUkbi->skor) }}">
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Predikat</label>
            <div class="combobox-container">
              <input type="text" name="predikat" id="custom-input-predikat" placeholder="Ketik atau pilih Predikat..."
                class="form-input" autocomplete="off" value="{{ old('predikat', $dataUkbi->predikat) }}">
              <div class="options-list" id="options-list-predikat"></div>
            </div>
          </div>

          <div class="flex items-end">
            <button type="submit" class="btn border-primary text-primary hover:bg-primary hover:text-white">{{ auth()->user()->role === 'petugas' ? 'Ajukan Perubahan' : 'Simpan Perubahan' }}</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>

    // INPUT KOTA
    const DATA_KOTA = @json($kota);

    // Ambil elemen HTML
    const inputEl = document.getElementById('custom-input');
    const optionsListEl = document.getElementById('options-list');
    const inputKoordinat = document.querySelector('input[name="titik_koordinat_peta"]');

    // --- Fungsi untuk mem-filter dan menampilkan opsi ---
    function tampilkanOpsi(filter = '') {
      optionsListEl.innerHTML = '';

      const filterLower = filter.toLowerCase();

      // Filter berdasarkan kota
      const filteredData = DATA_KOTA.filter(item =>
        item.kota.toLowerCase().includes(filterLower)
      );

      filteredData.forEach(item => {
        const optionEl = document.createElement('div');
        optionEl.className = 'option-item';
        optionEl.textContent = item.kota;

        // Saat diklik, isi input kota dan koordinat otomatis
        optionEl.addEventListener('click', () => {
          inputEl.value = item.kota;
          inputKoordinat.value = item.titik_koordinat_peta || ''; // isi otomatis
          optionsListEl.classList.remove('show');
        });

        optionsListEl.appendChild(optionEl);
      });

      if (filteredData.length > 0) {
        optionsListEl.classList.add('show');
      } else {
        optionsListEl.classList.remove('show');
      }
    }

    inputEl.addEventListener('input', () => tampilkanOpsi(inputEl.value));
    inputEl.addEventListener('focus', () => tampilkanOpsi(inputEl.value));
    inputEl.addEventListener('blur', () => {
      setTimeout(() => optionsListEl.classList.remove('show'), 150);
    });


    // INPUT PROFESI
    const DATA_PROFESI = @json($terdaftarSbg->pluck('terdaftar_sbg'));
    // Ambil elemen HTML yang kita butuhkan
    const inputElTerdaftarSbg = document.getElementById('custom-input-terdaftar-sbg');
    const optionsListElTerdaftarSbg = document.getElementById('options-list-terdaftar-sbg');
    // --- Fungsi untuk mem-filter dan menampilkan opsi ---
    function tampilkanOpsiTerdaftarSbg(filter = '') {
      // 1. Kosongkan list sebelumnya
      optionsListElTerdaftarSbg.innerHTML = '';

      const filterLower = filter.toLowerCase();

      // 2. Filter data berdasarkan apa yang diketik user
      const filteredData = DATA_PROFESI.filter(item =>
        item.toLowerCase().includes(filterLower)
      );

      // 3. Masukkan hasil filter ke HTML
      filteredData.forEach(item => {
        const optionEl = document.createElement('div');
        optionEl.className = 'option-item';
        optionEl.textContent = item;

        // 4. Tambahkan event 'click' untuk setiap item
        optionEl.addEventListener('click', () => {
          inputElTerdaftarSbg.value = item; // Set nilai input
          optionsListElTerdaftarSbg.classList.remove('show'); // Sembunyikan dropdown
        });

        optionsListElTerdaftarSbg.appendChild(optionEl);
      });

      // 5. Tampilkan atau sembunyikan dropdown
      if (filteredData.length > 0) {
        optionsListElTerdaftarSbg.classList.add('show');
      } else {
        optionsListElTerdaftarSbg.classList.remove('show');
      }
    }
    // --- Event Listener untuk Input ---
    // 1. Saat user MENGETIK di input
    inputElTerdaftarSbg.addEventListener('input', () => {
      tampilkanOpsiTerdaftarSbg(inputElTerdaftarSbg.value);
    });
    // 2. Saat user FOKUS (klik) ke input
    inputElTerdaftarSbg.addEventListener('focus', () => {
      // Tampilkan semua opsi (atau yg sesuai filter jika sudah ada isinya)
      tampilkanOpsiTerdaftarSbg(inputElTerdaftarSbg.value);
    });
    // 3. Saat user PINDAH FOKUS (klik di luar input)
    inputElTerdaftarSbg.addEventListener('blur', () => {
      // Kita pakai trik setTimeout
      // Ini agar event 'click' di item dropdown sempat berjalan
      // sebelum dropdown-nya terlanjur disembunyikan.
      setTimeout(() => {
        optionsListElTerdaftarSbg.classList.remove('show');
      }, 150); // tunda 150 milidetik
    });

    // INPUT INSTANSI
    const DATA_INSTANSI = @json($instansi->pluck('instansi'));
    // Ambil elemen HTML yang kita butuhkan
    const inputElInstansi = document.getElementById('custom-input-instansi');
    const optionsListElInstansi = document.getElementById('options-list-instansi');
    // --- Fungsi untuk mem-filter dan menampilkan opsi ---
    function tampilkanOpsiInstansi(filter = '') {
      // 1. Kosongkan list sebelumnya
      optionsListElInstansi.innerHTML = '';

      const filterLower = filter.toLowerCase();

      // 2. Filter data berdasarkan apa yang diketik user
      const filteredData = DATA_INSTANSI.filter(item =>
        item.toLowerCase().includes(filterLower)
      );

      // 3. Masukkan hasil filter ke HTML
      filteredData.forEach(item => {
        const optionEl = document.createElement('div');
        optionEl.className = 'option-item';
        optionEl.textContent = item;

        // 4. Tambahkan event 'click' untuk setiap item
        optionEl.addEventListener('click', () => {
          inputElInstansi.value = item; // Set nilai input
          optionsListElInstansi.classList.remove('show'); // Sembunyikan dropdown
        });

        optionsListElInstansi.appendChild(optionEl);
      });

      // 5. Tampilkan atau sembunyikan dropdown
      if (filteredData.length > 0) {
        optionsListElInstansi.classList.add('show');
      } else {
        optionsListElInstansi.classList.remove('show');
      }
    }
    // --- Event Listener untuk Input ---
    // 1. Saat user MENGETIK di input
    inputElInstansi.addEventListener('input', () => {
      tampilkanOpsiInstansi(inputElInstansi.value);
    });
    // 2. Saat user FOKUS (klik) ke input
    inputElInstansi.addEventListener('focus', () => {
      // Tampilkan semua opsi (atau yg sesuai filter jika sudah ada isinya)
      tampilkanOpsiInstansi(inputElInstansi.value);
    });
    // 3. Saat user PINDAH FOKUS (klik di luar input)
    inputElInstansi.addEventListener('blur', () => {
      // Kita pakai trik setTimeout
      // Ini agar event 'click' di item dropdown sempat berjalan
      // sebelum dropdown-nya terlanjur disembunyikan.
      setTimeout(() => {
        optionsListElInstansi.classList.remove('show');
      }, 150); // tunda 150 milidetik
    });


    // INPUT PREDIKAT
    const DATA_PREDIKAT = @json($predikat->pluck('predikat'));
    // Ambil elemen HTML yang kita butuhkan
    const inputElPredikat = document.getElementById('custom-input-predikat');
    const optionsListElPredikat = document.getElementById('options-list-predikat');
    // --- Fungsi untuk mem-filter dan menampilkan opsi ---
    function tampilkanOpsiPredikat(filter = '') {
      // 1. Kosongkan list sebelumnya
      optionsListElPredikat.innerHTML = '';

      const filterLower = filter.toLowerCase();

      // 2. Filter data berdasarkan apa yang diketik user
      const filteredData = DATA_PREDIKAT.filter(item =>
        item.toLowerCase().includes(filterLower)
      );

      // 3. Masukkan hasil filter ke HTML
      filteredData.forEach(item => {
        const optionEl = document.createElement('div');
        optionEl.className = 'option-item';
        optionEl.textContent = item;

        // 4. Tambahkan event 'click' untuk setiap item
        optionEl.addEventListener('click', () => {
          inputElPredikat.value = item; // Set nilai input
          optionsListElPredikat.classList.remove('show'); // Sembunyikan dropdown
        });

        optionsListElPredikat.appendChild(optionEl);
      });

      // 5. Tampilkan atau sembunyikan dropdown
      if (filteredData.length > 0) {
        optionsListElPredikat.classList.add('show');
      } else {
        optionsListElPredikat.classList.remove('show');
      }
    }
    // --- Event Listener untuk Input ---
    // 1. Saat user MENGETIK di input
    inputElPredikat.addEventListener('input', () => {
      tampilkanOpsiPredikat(inputElPredikat.value);
    });
    // 2. Saat user FOKUS (klik) ke input
    inputElPredikat.addEventListener('focus', () => {
      // Tampilkan semua opsi (atau yg sesuai filter jika sudah ada isinya)
      tampilkanOpsiPredikat(inputElPredikat.value);
    });
    // 3. Saat user PINDAH FOKUS (klik di luar input)
    inputElPredikat.addEventListener('blur', () => {
      // Kita pakai trik setTimeout
      // Ini agar event 'click' di item dropdown sempat berjalan
      // sebelum dropdown-nya terlanjur disembunyikan.
      setTimeout(() => {
        optionsListElPredikat.classList.remove('show');
      }, 150); // tunda 150 milidetik
    });

  </script>
@endsection