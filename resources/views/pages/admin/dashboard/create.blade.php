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
            <input type="text" name="no_pendaftaran" id="simpleinput" class="form-input">
          </div>

          <div>
            <label for="example-date" class="text-default-800 text-sm font-medium inline-block mb-2">Tanggal Ujian</label>
            <input class="form-input" name="tanggal_ujian" id="example-date" type="date" name="date">
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Nama Peserta</label>
            <input type="text" name="nama_peserta" id="simpleinput" class="form-input">
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Terdaftar
              Sebagai</label>
            <input type="text" name="terdaftar_sbg" id="simpleinput" class="form-input">
          </div>

          <div>
            <label for="example-select" class="text-default-800 text-sm font-medium inline-block mb-2">Jenis
              Kelamin</label>
            <select name="jenis_kelamin" class="form-select" id="example-select">
              <option>Laki-laki</option>
              <option>Perempuan</option>
            </select>
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="simpleinput" class="form-input">
          </div>

          <div>
            <label for="example-date" class="text-default-800 text-sm font-medium inline-block mb-2">Tanggal Lahir</label>
            <input class="form-input" name="tanggal_lahir" id="example-date" type="date" name="date">
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Kota</label>
            <input type="text" name="kota" id="simpleinput" class="form-input">
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Titik Koordinat
              Peta</label>
            <input type="text" name="titik_koordinat_peta" id="simpleinput" class="form-input">
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Kelas</label>
            <input type="text" name="kelas" id="simpleinput" class="form-input">
          </div>

          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Instansi</label>
            <input type="text" name="instansi" id="simpleinput" class="form-input">
          </div>

          <div>
            <label for="example-number" class="text-default-800 text-sm font-medium inline-block mb-2">Seksi 1</label>
            <input class="form-input" id="example-number" type="number" name="seksi_1">
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
            <input class="form-input" id="example-number" type="number" name="skor">
          </div>
          <div>
            <label for="simpleinput" class="text-default-800 text-sm font-medium inline-block mb-2">Predikat</label>
            <input type="text" name="predikat" id="simpleinput" class="form-input">
          </div>
          <div>
            <button type="submit" class="btn border-primary text-primary hover:bg-primary hover:text-white">Tambah
              Data</button>
          </div>
        </div>
      </form>
    </div> <!-- end card -->
  </div>
@endsection