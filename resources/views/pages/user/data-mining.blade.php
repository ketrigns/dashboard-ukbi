@extends('layouts.app')

@section('title', 'Hasil Data Mining')

@section('content')
  <div>
    <h1 class="text-2xl text-[45px] font-bold">Hasil Data Mining</h1>

    <div class="mt-8">
      @if($data)
        <img src="{{ asset('storage/' . $data->gambar) }}" alt="Gambar Hasil Data Mining"
          class="w-full h-auto rounded-lg shadow-md">
        <div class="mt-5">
          {!! $data->deskripsi !!}
        </div>
      @else
        <div class="p-4 text-center text-gray-500 bg-gray-100 rounded-lg">
          <p>Gambar hasil data mining Belum diupload.</p>
        </div>
      @endif

    </div>

  </div>
@endsection