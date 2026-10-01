<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Local Network Hub & Live Chat</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
        }
        .drop-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .drop-zone:hover {
            border-color: #0d6efd;
            background: #f8fafc;
        }
        .table-scroll-container {
            max-height: 380px;
            overflow-y: auto;
        }
        .table-scroll-container thead th {
            position: sticky;
            top: 0;
            background-color: #f8fafc;
            z-index: 2;
            border-bottom: 2px solid #e2e8f0;
        }
        .chat-box {
            height: 320px;
            overflow-y: auto;
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .chat-message {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            position: relative;
        }
        .chat-message.received {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            align-self: flex-start;
        }
        .delete-msg-btn {
            color: #dc3545;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
            background: none;
            border: none;
            padding: 0;
            font-size: 12px;
        }
        .delete-msg-btn:hover {
            opacity: 1;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm px-3">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <div class="bg-primary text-white rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-hdd-network-fill fs-5"></i>
                </div>
                <span class="fw-bold fs-5">FileHub <span class="badge bg-primary text-wrap fs-6 fw-normal ms-1 px-2 py-0.5">LAN Node</span></span>
            </a>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">
                <span class="spinner-grow spinner-grow-sm text-success" role="status"></span> Active Node
            </span>
        </div>
    </nav>

    <div class="container-fluid py-4 px-3 px-lg-5 flex-grow-1">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Storage Status Bar -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-dark small">
                        <i class="bi bi-hdd-fill text-primary me-1"></i> Storage Status
                    </span>
                    <span class="badge bg-light text-dark border font-monospace storage-info-badge">
                        {{ $storageInfo['used'] }} / {{$storageInfo['total'] }}
                    </span>
                </div>
                
                <div class="progress" style="height: 10px; border-radius: 6px;">
                    <div class="progress-bar {{ $storageInfo['percentage'] > 85 ? 'bg-danger' : ($storageInfo['percentage'] > 60 ? 'bg-warning' : 'bg-primary') }}" 
                         role="progressbar" 
                         style="width: {{ $storageInfo['percentage'] }}%;" 
                         aria-valuenow="{{ $storageInfo['percentage'] }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>

                <div class="d-flex justify-content-between text-muted small mt-2" style="font-size: 11px;">
                    <span class="storage-used-text">Used: {{ $storageInfo['percentage'] }}%</span>
                    <span class="storage-free-text">Free Space: {{ $storageInfo['free'] }}</span>
                </div>
            </div>
        </div>

        <!-- Top Section: Upload & Explorer -->
        <div class="row g-4 mb-4">
            
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h6 class="m-0 fw-bold text-dark"><i class="bi bi-cloud-arrow-up text-primary me-2"></i>Upload File</h6>
                    </div>
                    <div class="card-body pt-0">
                        <form id="uploadForm" onsubmit="uploadFileAjax(event)">
                            @csrf
                            <div class="drop-zone p-4 text-center mb-3">
                                <i class="bi bi-cloud-upload text-primary fs-1 d-block mb-2"></i>
                                <span class="d-block text-dark fw-semibold mb-1" id="fileNameDisplay">Select file from system</span>
                                <span class="text-muted small d-block mb-3">Supports Documents, Zip, Images & Code</span>
                                <input type="file" name="file" class="form-control form-control-sm" required onchange="handleFileSelect(this)">
                            </div>
                            <button type="submit" id="uploadBtn" class="btn btn-primary w-100 py-2.5 fw-semibold rounded-3">
                                <i class="bi bi-upload"></i> Upload File
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white py-3 px-4 border-bottom">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-6">
                                <h6 class="m-0 fw-bold text-dark"><i class="bi bi-folder2-open text-primary me-2"></i>Shared Files Explorer</h6>
                            </div>
                            <div class="col-12 col-md-6">
                                <input type="text" id="searchInput" class="form-control form-control-sm bg-light" placeholder="Search file by name..." onkeyup="filterFiles()">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive table-scroll-container">
                        <table class="table align-middle mb-0" id="fileTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">File Name</th>
                                    <th>Date & Time</th>
                                    <th>Size</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($files as $file)
                                    @php 
                                        $ext =$file['ext'];
                                        $icon = match($ext) {
                                            'pdf' => 'bi-file-earmark-pdf text-danger',
                                            'zip', 'rar', '7z' => 'bi-file-earmark-zip text-warning',
                                            'jpg', 'jpeg', 'png', 'gif' => 'bi-file-earmark-image text-success',
                                            'mp4', 'mkv' => 'bi-file-earmark-play text-info',
                                            'php', 'js', 'html', 'css', 'sql' => 'bi-file-earmark-code text-primary',
                                            default => 'bi-file-earmark text-secondary'
                                        };

                                        $bytes =$file['size'];
                                        if ($bytes >= 1048576) { $size = number_format($bytes / 1048576, 2) . ' MB'; }
                                        elseif ($bytes >= 1024) { $size = number_format($bytes / 1024, 2) . ' KB'; }
                                        else { $size =$bytes . ' bytes'; }
                                    @endphp
                                    <tr class="file-row">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="bi {{ $icon }} fs-4"></i>
                                                <div class="text-truncate" style="max-width: 250px;">
                                                    <span class="fw-semibold text-dark d-block text-truncate file-name">{{ $file['name'] }}</span>
                                                    <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 10px;">{{ strtoupper($ext ?: 'FILE') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="text-muted small fw-medium">{{ $file['date_formatted'] }}</span></td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $size }}</span></td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('file.download', $file['name']) }}" class="btn btn-sm btn-primary fw-semibold rounded-2 me-1">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <form action="{{ route('file.delete', $file['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete file?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block opacity-50 mb-2"></i> No files uploaded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Live Chat -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-dark"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Network Live Chat (No Database)</h6>
                        <div class="d-flex align-items-center gap-2">
                            <button id="enableNotifBtn" onclick="requestNotificationPermission()" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-bell-fill me-1"></i> Enable Notifications
                            </button>
                            <span class="badge bg-primary-subtle text-primary">JSON Storage Mode</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chat-box mb-3" id="chatBox"></div>

                        <form id="chatForm" onsubmit="sendChatMessage(event)">
                            <div class="input-group">
                                <input type="text" id="chatInput" class="form-control" placeholder="Type a message or share text/link..." required autocomplete="off">
                                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                    <i class="bi bi-send-fill me-1"></i> Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
        Local File & Chat Hub &bull; Powered by Laravel & Bootstrap 5
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            document.getElementById('fileNameDisplay').innerText = input.files[0].name;
        }
    }

    function filterFiles() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let rows = document.querySelectorAll('#fileTable tbody tr.file-row');

        rows.forEach(row => {
            let name = row.querySelector('.file-name').innerText.toLowerCase();
            row.style.display = name.includes(input) ? "" : "none";
        });
    }

    function uploadFileAjax(e) {
        e.preventDefault();

        let form = document.getElementById('uploadForm');
        let formData = new FormData(form);
        let uploadBtn = document.getElementById('uploadBtn');

        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';

        fetch("{{ route('file.upload') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: formData
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || 'Server error occurred');
            }
            return data;
        })
        .then(data => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="bi bi-upload"></i> Upload File';

            if (data.success) {
                form.reset();
                document.getElementById('fileNameDisplay').innerText = 'Select file from system';

                if (data.storageInfo) {
                    let pb = document.querySelector('.progress-bar');
                    pb.style.width = data.storageInfo.percentage + '%';
                    pb.setAttribute('aria-valuenow', data.storageInfo.percentage);
                    document.querySelector('.storage-info-badge').innerText = `${data.storageInfo.used} / ${data.storageInfo.total}`;
                    document.querySelector('.storage-used-text').innerText = `Used: ${data.storageInfo.percentage}%`;
                    document.querySelector('.storage-free-text').innerText = `Free Space: ${data.storageInfo.free}`;
                }

                let ext = data.file.ext;
                let icon = 'bi-file-earmark text-secondary';
                if (ext === 'pdf') icon = 'bi-file-earmark-pdf text-danger';
                else if (['zip', 'rar', '7z'].includes(ext)) icon = 'bi-file-earmark-zip text-warning';
                else if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) icon = 'bi-file-earmark-image text-success';
                else if (['mp4', 'mkv'].includes(ext)) icon = 'bi-file-earmark-play text-info';
                else if (['php', 'js', 'html', 'css', 'sql'].includes(ext)) icon = 'bi-file-earmark-code text-primary';

                let tbody = document.querySelector('#fileTable tbody');
                let emptyRow = tbody.querySelector('td[colspan="4"]');
                if (emptyRow) emptyRow.parentElement.remove();

                let newRowHtml = `
                    <tr class="file-row">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi ${icon} fs-4"></i>
                                <div class="text-truncate" style="max-width: 250px;">
                                    <span class="fw-semibold text-dark d-block text-truncate file-name">${data.file.name}</span>
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 10px;">${(ext || 'FILE').toUpperCase()}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-muted small fw-medium">${data.file.date_formatted}</span></td>
                        <td><span class="badge bg-light text-dark border px-2 py-1">${data.file.size}</span></td>
                        <td class="text-end pe-4">
                            <a href="${data.file.download_url}" class="btn btn-sm btn-primary fw-semibold rounded-2 me-1">
                                <i class="bi bi-download"></i>
                            </a>
                            <form action="${data.file.delete_url}" method="POST" class="d-inline" onsubmit="return confirm('Delete file?');">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                `;

                tbody.insertAdjacentHTML('afterbegin', newRowHtml);
            } else {
                alert(data.message || 'Upload nahi ho saka!');
            }
        })
        .catch(err => {
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = '<i class="bi bi-upload"></i> Upload File';
            alert('Upload error: ' + err.message);
        });
    }
    function checkNotificationPermission() {
        let btn = document.getElementById('enableNotifBtn');
        if (!("Notification" in window)) {
            if (btn) btn.style.display = 'none';
            return;
        }

        if (Notification.permission === "granted") {
            if (btn) {
                btn.className = "btn btn-sm btn-success rounded-pill disabled";
                btn.innerHTML = '<i class="bi bi-bell-fill me-1"></i> Notifications Active';
            }
        } else if (Notification.permission === "denied") {
            if (btn) {
                btn.className = "btn btn-sm btn-danger rounded-pill";
                btn.innerHTML = '<i class="bi bi-bell-slash-fill me-1"></i> Notifications Blocked';
            }
        }
    }

    function requestNotificationPermission() {
        if (!("Notification" in window)) {
            alert("Aapka browser desktop notifications support nahi karta.");
            return;
        }

        Notification.requestPermission().then(permission => {
            if (permission === "granted") {
                new Notification("FileHub", {
                    body: "Notifications active ho gaye hain!",
                    icon: "https://cdn-icons-png.flaticon.com/512/1041/1041916.png"
                });
            }
            checkNotificationPermission();
        });
    }

    function showDesktopNotification(msg) {
        if ("Notification" in window && Notification.permission === "granted" && document.hidden) {
            let notification = new Notification("Naya Message - FileHub", {
                body: `${msg.ip}: ${msg.message}`,
                icon: "https://cdn-icons-png.flaticon.com/512/1041/1041916.png",
                tag: "chat-notification"
            });

            notification.onclick = function() {
                window.focus();
                this.close();
            };
        }
    }

    let lastMessageCount = -1;

    function fetchMessages() {
        fetch("{{ route('messages.get') }}")
            .then(res => res.json())
            .then(data => {
                let isFirstLoad = (lastMessageCount === -1);

                if (data.length !== lastMessageCount) {
                    let chatBox = document.getElementById('chatBox');
                    chatBox.innerHTML = '';

                    if (data.length === 0) {
                        chatBox.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-chat-square-text fs-3 d-block opacity-50 mb-1"></i>No messages yet. Send a message to start chat!</div>';
                    } else {
                        data.forEach((msg) => {
                            let html = `
                                <div class="chat-message received shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between gap-3 mb-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">IP: ${msg.ip}</span>
                                            <span class="text-muted" style="font-size: 11px;"><i class="bi bi-clock me-1"></i>${msg.time}</span>
                                        </div>
                                        <button class="delete-msg-btn" onclick="deleteMessage('${msg.id}')" title="Delete message">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                    <div class="text-dark fw-medium">${msg.message}</div>
                                </div>
                            `;
                            chatBox.innerHTML += html;
                        });

                        if (!isFirstLoad && data.length > lastMessageCount) {
                            let latestMessage = data[data.length - 1];
                            showDesktopNotification(latestMessage);
                        }
                    }

                    chatBox.scrollTop = chatBox.scrollHeight;
                    lastMessageCount = data.length;
                }
            });
    }

    function sendChatMessage(e) {
        e.preventDefault();
        let input = document.getElementById('chatInput');
        let message = input.value.trim();

        if (!message) return;

        fetch("{{ route('messages.send') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: message })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                fetchMessages();
            }
        });
    }

    function deleteMessage(id) {
        if (!confirm("Is message ko delete karna chahte hain?")) return;

        fetch("{{ route('messages.delete') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                lastMessageCount = -1;
                fetchMessages();
            } else {
                alert("Message delete nahi ho saka.");
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        checkNotificationPermission();
    });

    setInterval(fetchMessages, 2000);
    fetchMessages();
</script>
</body>
</html>