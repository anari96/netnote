@extends('main')

@section('content')
    @if(isset($config))
    <p> Nama : {{ $config->name }}</p>
    <p> Email : {{ $config->auth_name }}</p>
    <a href="{{ route("config.revokeAccess", $config) }}">Hapus Drive</a>
    @else
    <p> Drive Belum di Setting</p>
    <a href="{{ route("config.create") }}">Tambah Drive</a>
    @endif
@endsection