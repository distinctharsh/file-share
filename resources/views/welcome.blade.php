<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Local File Hub</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f9;
        }
        .navbar-brand-text {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .table-custom tbody tr {
            transition: background-color 0.15s ease;
        }
        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: #fafafa;
            transition: all 0.2s ease;
        }
        .upload-zone:hover {
            border-color: #0d6efd;
            background: #f0f7ff;
        }
        /* Scrollable table container */
        .table-scroll-container {
            max-height: 380px;
            overflow-y: auto;
        }
        .table-scroll-container thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 1;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm px-3">
        <div class="container-fluid max-w-7xl">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <div class="bg-primary text-white rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-hdd-network-fill"></i>
                </div>
                <span class="navbar-brand-text fs-5">FileHub <span class="badge bg-primary text-wrap fs-6 fw-normal ms-1 px-2 py-1">Local Pro</span></span>
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 d-none d-sm-inline-flex align-items-center gap-2">
                    <span class="spinner-grow spinner-grow-sm" role="status"></span> Server Active
                </span>
                <div class="vr text-white-50 d-none d-sm-block"></div>
                <div class="text-white-50 small d-none d-md-block">
                    <i class="bi bi-pc-display me-1"></i> Local Sharing Node
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid py-4 px-3 px-lg-5 flex-grow-1">
        
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Stats Overview Row -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Files</div>
                            <div class="fs-3 fw-bold text-dark mt-1">{{ count($files) }}</div>
                        </div>
                        <div class="icon-box bg-primary-subtle text-primary">
                            <i class="bi bi-files fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Storage Mode</div>
                            <div class="fs-5 fw-bold text-dark mt-1">Public Disk</div>
                        </div>
                        <div class="icon-box bg-info-subtle text-info">
                            <i class="bi bi-folder-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Max File Size</div>
                            <div class="fs-4 fw-bold text-dark mt-1">100 MB</div>
                        </div>
                        <div class="icon-box bg-warning-subtle text-warning">
                            <i class="bi bi-lightning-charge fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Access Type</div>
                            <div class="fs-5 fw-bold text-dark mt-1">LAN / Local</div>
                        </div>
                        <div class="icon-box bg-success-subtle text-success">
                            <i class="bi bi-wifi fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Left Side: Upload Card -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 fw-bold text-dark"><i class="bi bi-cloud-arrow-up text-primary me-2"></i>Quick File Upload</h6>
                    </div>
                    <div class="card-body pt-0">
                        <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="upload-zone p-4 text-center mb-3">
                                <i class="bi bi-file-earmark-arrow-up text-primary fs-1 d-block mb-2"></i>
                                <span class="d-block text-secondary small mb-2 fw-medium">Select file from system</span>
                                <input type="file" name="file" class="form-control form-control-sm" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-upload"></i> Upload to Storage
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Side: File Manager Table (With Fixed Height & Scroll) -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                        <h6 class="m-0 fw-bold text-dark"><i class="bi bi-folder2-open text-primary me-2"></i>Shared Repository</h6>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ count($files) }} Items</span>
                    </div>

                    <div class="table-responsive table-scroll-container">
                        <table class="table table-custom align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">File Name</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($files as $file)
                                    @php $filename = basename($file); @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 bg-light rounded text-primary">
                                                    <i class="bi bi-file-earmark-code fs-5"></i>
                                                </div>
                                                <div class="text-truncate" style="max-width: 380px;">
                                                    <span class="fw-semibold text-dark d-block text-truncate">{{ $filename }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('file.download', $filename) }}" class="btn btn-sm btn-primary-subtle text-primary border-primary-subtle fw-semibold px-3 rounded-pill">
                                                <i class="bi bi-download me-1"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block text-secondary opacity-50 mb-2"></i>
                                            <span class="fw-medium">Koi file upload nahi hui hai.</span>
                                            <p class="small text-muted mb-0">Baayein taraf wale form se file upload karein.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <!-- Footer -->
    <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
        Local Storage Manager &bull; Powered by Laravel & Bootstrap 5
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>