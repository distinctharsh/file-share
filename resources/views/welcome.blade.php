<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Network File Sharing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 700px;">
        <h2 class="mb-4 text-center">Local File Sharing App</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Upload Form -->
        <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data" class="mb-4">
            @csrf
            <div class="input-group">
                <input type="file" name="file" class="form-field form-control" required>
                <button type="submit" class="btn btn-primary">Upload File</button>
            </div>
        </form>

        <hr>

        <!-- Download List -->
        <h4>Uploaded Files</h4>
        <ul class="list-group">
            @forelse($files as $file)
                @php $filename = basename($file); @endphp
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $filename }}</span>
                    <a href="{{ route('file.download', $filename) }}" class="btn btn-sm btn-success">Download</a>
                </li>
            @empty
                <li class="list-group-item text-muted text-center">Koi file upload nahi hui hai.</li>
            @endforelse
        </ul>
    </div>
</body>
</html>