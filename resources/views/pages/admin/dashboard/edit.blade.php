@extends('layouts.admin.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Data UKBI</h4>
      </div>

      <form action="{{ route('data-ukbi.update', $dataUkbi) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid lg:grid-cols-2 gap-6">
          <div>
            <label for="no_pendaftaran" class="text-default-800 text-sm font-medium inline-block mb-2">No Pendaftaran</label>
            <input type="text" name="no_pendaftaran" id="no_pendaftaran" class="form-input" value="{{ old('no_pendaftaran', $dataUkbi->no_pendaftaran) }}">
          </div>

          <div>
            <label for="tanggal_ujian" class="text-default-800 text-sm font-medium inline-block mb-2">Tanggal Ujian</label>
            <input class="form-input" name="tanggal_ujian" id="tanggal_ujian" type="date" value="{{ old('tanggal_ujian', $dataUkbi->tanggal_ujian) }}">
          </div>

          <div>
            <label for="nama_peserta" class="text-default-800 text-sm font-medium inline-block mb-2">Nama Peserta</label>
            <input type="text" name="nama_peserta" id="nama_peserta" class="form-input" value="{{ old('nama_peserta', $dataUkbi->nama_peserta) }}">
          </div>

          <div>
            <label for="terdaftar_sbg" class="text-default-800 text-sm font-medium inline-block mb-2">Terdaftar Sebagai</label>
            <input type="text" name="terdaftar_sbg" id="terdaftar_sbg" class="form-input" value="{{ old('terdaftar_sbg', $dataUkbi->terdaftar_sbg) }}">
          </div>

          <div>
            <label for="jenis_kelamin" class="text-default-800 text-sm font-medium inline-block mb-2">Jenis Kelamin</label>
            <select name="jenis_kelamin" class="form-select" id="jenis_kelamin">
              <option value="Laki-laki" {{ old('jenis_kelamin', $dataUkbi->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
              <option value="Perempuan" {{ old('jenis_kelamin', $dataUkbi->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>
          </div>

          <div>
            <label for="tempat_lahir" class="text-default-800 text-sm font-medium inline-block mb-2">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-input" value="{{ old('tempat_lahir', $dataUkbi->tempat_lahir) }}">
          </div>

          <div>
            <label for="tanggal_lahir" class="text-default-800 text-sm font-medium inline-block mb-2">Tanggal Lahir</label>
            <input class="form-input" name="tanggal_lahir" id="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $dataUkbi->tanggal_lahir) }}">
          </div>

          <div>
            <label for="kota" class="text-default-800 text-sm font-medium inline-block mb-2">Kota</label>
            <input type="text" name="kota" id="kota" class="form-input" value="{{ old('kota', $dataUkbi->kota) }}">
          </div>

          <div>
            <label for="titik_koordinat_peta" class="text-default-800 text-sm font-medium inline-block mb-2">Titik Koordinat Peta</label>
            <input type="text" name="titik_koordinat_peta" id="titik_koordinat_peta" class="form-input" value="{{ old('titik_koordinat_peta', $dataUkbi->titik_koordinat_peta) }}">
          </div>

          <div>
            <label for="kelas" class="text-default-800 text-sm font-medium inline-block mb-2">Kelas</label>
            <input type="text" name="kelas" id="kelas" class="form-input" value="{{ old('kelas', $dataUkbi->kelas) }}">
          </div>

          <div>
            <label for="instansi" class="text-default-800 text-sm font-medium inline-block mb-2">Instansi</label>
            <input type="text" name="instansi" id="instansi" class="form-input" value="{{ old('instansi', $dataUkbi->instansi) }}">
          </div>

          @for ($i = 1; $i <= 5; $i++)
            <div>
              <label for="seksi_{{ $i }}" class="text-default-800 text-sm font-medium inline-block mb-2">Seksi {{ $i }}</label>
              <input class="form-input" id="seksi_{{ $i }}" type="number" name="seksi_{{ $i }}" value="{{ old('seksi_'.$i, $dataUkbi->{'seksi_'.$i}) }}">
            </div>
          @endfor

          <div>
            <label for="skor" class="text-default-800 text-sm font-medium inline-block mb-2">Skor</label>
            <input class="form-input" id="skor" type="number" name="skor" value="{{ old('skor', $dataUkbi->skor) }}">
          </div>

          <div>
            <label for="predikat" class="text-default-800 text-sm font-medium inline-block mb-2">Predikat</label>
            <input type="text" name="predikat" id="predikat" class="form-input" value="{{ old('predikat', $dataUkbi->predikat) }}">
          </div>

          <div>
            <button type="submit" class="btn border-primary text-primary hover:bg-primary hover:text-white">Simpan Perubahan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
