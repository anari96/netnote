@extends('main')

@section('content')
<h2>{{ $document->description }}</h2>

<a href="{{ Route("document.index") }}">Kembali</a>

<h4>List File</h4>
<table>
    <tr>
        <th>Description</th>
        <th>Link File</th>
    </tr>
    @foreach($document->detail as $detail)
    <tr>
        <td>{{$detail->description}}</td>
        <td>
            @foreach($detail->file as $file)
                <a href="{{ "https://drive.google.com/file/d/".$file->cloud_path."/view?usp=sharing" }}">{{ "https://drive.google.com/file/d/".$file->cloud_path."/view?usp=sharing" }}</a><br>
            @endforeach
        </td>
    </tr>
    @endforeach
</table>
@endsection