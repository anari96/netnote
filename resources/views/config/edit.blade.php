@extends('main')

@section('content')
<form action="{{ route('config.update', $config->id) }}" method="post">
    @csrf
    @method("PUT")
    <input type="text" name="name" id="name" value="{{$config->name}}">
    <button>Edit Google Drive</button>
</form>
@endsection