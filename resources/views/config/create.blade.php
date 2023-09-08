@extends('main')

@section('content')
<form action="{{ route('config.store') }}" method="post">
    @csrf
    <input type="text" name="name" id="name">
    <button>Add Google Drive</button>
</form>
@endsection