<!doctype html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DB Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    {{-- If you want broadcasting with Echo + Pusher or laravel-websockets, include Echo here --}}
</head>
<body>
    <h1>Database Manager</h1>

    <div id="instances">
        @foreach($instances as $inst)
            <div id="instance-{{ $inst->id }}">
                <h3>{{ $inst->name }} ({{ $inst->type }})</h3>
                <p>Status: <span class="status">{{ $inst->status }}</span></p>
                <p>Last: {{ $inst->last_message }}</p>
                <button onclick="start({{ $inst->id }})">Start</button>
                <button onclick="stop({{ $inst->id }})">Stop</button>
                <button onclick="refresh({{ $inst->id }})">Refresh</button>
            </div>
            <hr>
        @endforeach
    </div>

<script>
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
    const el = document.querySelector(`#instance-${id} .status`);
    if(el) el.textContent = status;
}

// Optional: basic polling every 15 seconds
setInterval(function(){
    document.querySelectorAll('[id^="instance-"]').forEach(div=>{
        const id = div.id.replace('instance-','');
        refresh(id);
    });
}, 15000);

// OPTIONAL: setup a websocket listener to update real-time
// If you configure broadcasting, Echo can listen to 'db-status' channel and update UI on DatabaseStatusChanged events.

</script>
</body>
</html>
