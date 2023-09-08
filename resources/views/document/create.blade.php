@extends('main')

@section('content')
<h2>Tambah Dokumen</h2>
<form action="{{ route("document.store")}}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('document.form')
</form>
@endsection