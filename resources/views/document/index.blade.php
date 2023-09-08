@extends('main')

@section('content')
<h1>Dokumen</h1>
<a href="{{route("document.create")}}">Tambah Dokumen</a>
<table>
    <tr>
        <th>Keterangan</th>
        <th>Tanggal</th>
        <th>#</th>
    </tr>
    @foreach($documents as $document)
        <tr>
            <td>{{ $document->description }}</td>
            <td>{{ $document->date }}</td>
            <td><a href="{{ route('document.show',$document->id) }}">Detail</a></td>
        </tr>
    @endforeach
</table>
@endsection