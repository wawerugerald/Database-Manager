<!doctype html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SafiriDB Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        #instances {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background: white;
            width: 300px;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
            border-left: 6px solid #0077cc;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin: 0 0 10px;
            color: #222;
        }

        .status {
            font-weight: bold;
        }

        .status.running { color: #28a745; }
        .status.stopped { color: #dc3545; }
        .status.unknown { color: #6c757d; }

        .btn-group {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        button {
            flex: 1;
            padding: 10px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-start { background: #28a745; color: white; }
        .btn-stop { background: #dc3545; color: white; }
        .btn-refresh { background: #0077cc; color: white; }

        .btn-start:hover { background: #218838; }
        .btn-stop:hover { background: #c82333; }
        .btn-refresh:hover { background: #0062a5; }

        .last-msg {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <h1>SafiriDB Manager</h1>

    <div id="instances">
        @foreach($instances as $inst)
        <div class="card" id="instance-{{ $inst->id }}">
            <h3>{{ $inst->name }} <small>({{ $inst->type }})</small></h3>

            <p>Status: 
                <span class="status {{ strtolower($inst->status) }}">
                    {{ $inst->status }}
                </span>
            </p>

            <p class="last-msg">{{ $inst->last_message }}</p>

            <div class="btn-group">
                <button class="btn-start" onclick="start({{ $inst->id }})">Start</button>
                <button class="btn-stop" onclick="stop({{ $inst->id }})">Stop</button>
                <button class="btn-refresh" onclick="refresh({{ $inst->id }})">Refresh</button>
            </div>
        </div>
        @endforeach
    </div>

<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function start(id){
    axios.post(`/instances/${id}/start`)
        .then(r => updateStatus(id, r.data.status))
        .catch(e => alert(e.response?.data?.message ?? 'Error starting'));
}

function stop(id){
    axios.post(`/instances/${id}/stop`)
        .then(r => updateStatus(id, r.data.status))
        .catch(e => alert(e.response?.data?.message ?? 'Error stopping'));
}

function refresh(id){
    axios.get(`/instances/${id}/status`)
        .then(r => updateStatus(id, r.data.status))
        .catch(e => console.error(e));
}

function updateStatus(id, status){
    const statusEl = document.querySelector(`#instance-${id} .status`);
    statusEl.textContent = status;
    statusEl.className = "status " + status.toLowerCase();
}

// Polling every 15 seconds
setInterval(() => {
    document.querySelectorAll('[id^="instance-"]').forEach(div => {
        refresh(div.id.replace('instance-', ''));
    });
}, 15000);

</script>
</body>
</html>
