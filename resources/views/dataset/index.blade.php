@extends('layouts.app')

@section('content')
<div class="container">
    <h1>ML Dataset Builder</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('dataset.trigger') }}">
        @csrf
        <button class="btn btn-primary">Generate Dataset</button>
    </form>

    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Dataset</th>
                <th>Status</th>
                <th>Records</th>
                <th>File</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->dataset_name }}</td>
                <td>{{ $log->status }}</td>
                <td>{{ $log->record_count }}</td>
                <td>
                    @if($log->file_path)
                        <a href="{{ route('dataset.download', $log->id) }}">Download</a>
                    @endif
                </td>
                <td>{{ $log->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
</div>
@endsection
