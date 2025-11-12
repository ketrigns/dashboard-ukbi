@extends('layouts.admin.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Input Data UKBI</h4>
      </div>
      <form action="{{ route('data-ukbi.store') }}" method="POST" class="p-6">
        @csrf

        <div class="grid lg:grid-cols-2 gap-6">
          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">No Pendaftaran</label>
            <input type="text" name="no_pendaftaran" id="simpleinput" class="form-input" required>
          </div>

          <div>
            <label for="example-date" class="text-default-800 text-sm font-medium inline-block mb-2">Tanggal Ujian</label>
            <input class="form-input" name="tanggal_ujian" id="example-date" type="date" name="date" required>
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Nama Peserta</label>
            <input type="text" name="nama_peserta" id="simpleinput" class="form-input" required>
          </div>

          <div>
            {{-- <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Terdaftar
              Sebagai</label>
            <input type="text" name="terdaftar_sbg" id="simpleinput" class="form-input"> --}}
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Terdaftar
              Sebagai</label>
            <div class="combobox-container">
              <input type="text" name="terdaftar_sbg" id="custom-input-terdaftar-sbg"
                placeholder="Ketik atau pilih Profesi..." class="form-input" autocomplete="off" required>
              <div class="options-list" id="options-list-terdaftar-sbg"></div>
            </div>
          </div>

          <div>
            <label for="example-select" class="text-default-800 text-sm font-medium inline-block mb-2">Jenis
              Kelamin</label>
            <select name="jenis_kelamin" class="form-select" id="example-select" required>
              <option>Laki-laki</option>
              <option>Perempuan</option>
            </select>
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="simpleinput" class="form-input" required>
          </div>

          <div>
            <label for="example-date" class="text-default-800 text-sm font-medium inline-block mb-2">Tanggal Lahir</label>
            <input class="form-input" name="tanggal_lahir" id="example-date" type="date" name="date" required>
          </div>

          {{-- <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Kota</label>
            <input type="text" name="kota" id="simpleinput" class="form-input">
          </div> --}}

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Kota</label>
            <div class="combobox-container">
              <input type="text" name="kota" id="custom-input" placeholder="Ketik atau pilih Kota..." class="form-input"
                autocomplete="off" required>
              <div class="options-list" id="options-list"></div>
            </div>
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Titik Koordinat
              Peta</label>
            <input type="text" name="titik_koordinat_peta" id="simpleinput" class="form-input" required>
          </div>

          <div id="kelas-container" style="display: none;">
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Kelas</label>
            <select name="kelas" id="simpleinput" class="form-input">
              <option value="">Pilih Kelas</option>
              <option value="Kelas 7">Kelas 7</option>
              <option value="Kelas 8">Kelas 8</option>
              <option value="Kelas 9">Kelas 9</option>
              <option value="Kelas 10">Kelas 10</option>
              <option value="Kelas 11">Kelas 11</option>
              <option value="Kelas 12">Kelas 12</option>
            </select>
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Instansi</label>
            <div class="combobox-container">
              <input type="text" name="instansi" id="custom-input-instansi" placeholder="Ketik atau pilih Instansi..."
                class="form-input" autocomplete="off" required>
              <div class="options-list" id="options-list-instansi"></div>
            </div>
          </div>

          <div>
            <label for="example-number" class="text-default-800 text-sm font-medium inline-block mb-2">Seksi 1</label>
            <input class="form-input" id="example-number" type="number" name="seksi_1" required>
          </div>

          <div>
            <label for="example-number" class="text-default-800 text-sm font-medium inline-block mb-2">Seksi 2</label>
            <input class="form-input" id="example-number" type="number" name="seksi_2">
          </div>
          <div>
            <label for="example-number" class="text-default-800 text-sm font-medium inline-block mb-2">Seksi 3</label>
            <input class="form-input" id="example-number" type="number" name="seksi_3">
          </div>
          <div>
            <label for="example-number" class="text-default-800 text-sm font-medium inline-block mb-2">Seksi 4</label>
            <input class="form-input" id="example-number" type="number" name="seksi_4">
          </div>
          <div>
            <label for="example-number" class="text-default-800 text-sm font-medium inline-block mb-2">Seksi 5</label>
            <input class="form-input" id="example-number" type="number" name="seksi_5">
          </div>
          <div>
            <label for="example-number" class="text-default-800 text-sm font-medium inline-block mb-2">Skor</label>
            <input class="form-input" id="example-number" type="number" name="skor" required>
          </div>
          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Predikat</label>
            <div class="combobox-container">
              <input type="text" name="predikat" id="custom-input-predikat" placeholder="Ketik atau pilih Predikat..."
                class="form-input" autocomplete="off" required>
              <div class="options-list" id="options-list-predikat"></div>
            </div>
          </div>
          <div class="flex items-end">
            <button type="submit" class="btn border-primary text-primary hover:bg-primary hover:text-white">Tambah
              Data</button>
          </div>
        </div>
      </form>
    </div> <!-- end card -->
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

    // --- Tampilkan/Sembunyikan field Kelas berdasarkan profesi ---
    const kelasContainer = document.getElementById('kelas-container');

    function cekProfesi() {
      const val = inputElTerdaftarSbg.value.trim().toLowerCase();
      const selectKelas = document.querySelector('#kelas-container select');

      if (val.includes('pelajar')) {
        kelasContainer.style.display = 'block';
        selectKelas.setAttribute('required', 'required'); // ✅ buat jadi wajib
      } else {
        kelasContainer.style.display = 'none';
        selectKelas.removeAttribute('required'); // 🚫 hapus required kalau bukan pelajar
      }
    }



    // Jalankan saat input berubah
    inputElTerdaftarSbg.addEventListener('input', cekProfesi);
    inputElTerdaftarSbg.addEventListener('change', cekProfesi);

    // Jalankan juga ketika user klik dari daftar opsi
    optionsListElTerdaftarSbg.addEventListener('click', cekProfesi);


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