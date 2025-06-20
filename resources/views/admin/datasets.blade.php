@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">User Churn Dataset Admin Panel</h1>
    <div class="mb-4">
        <button id="generate-btn" class="bg-blue-600 text-white px-4 py-2 rounded">Generate Dataset</button>
        <a href="/admin/datasets/download" class="ml-4 underline text-blue-700">Download CSV</a>
    </div>
    <div class="mb-4">
        <h2 class="font-semibold mb-2">Dataset Entries</h2>
        <div id="dataset-table">Loading...</div>
    </div>
    <div>
        <h2 class="font-semibold mb-2">Logs</h2>
        <pre id="logs" class="bg-gray-100 p-2 rounded h-48 overflow-auto">Loading...</pre>
    </div>
</div>
<script>
function fetchDataset() {
    fetch('/api/user-churn-dataset')
        .then(res => res.json())
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                document.getElementById('dataset-table').innerHTML = 'No data available.';
                return;
            }
            let html = '<table class="table-auto w-full text-xs"><thead><tr>';
            Object.keys(data[0]).forEach(key => html += `<th class="border px-2 py-1">${key}</th>`);
            html += '</tr></thead><tbody>';
            data.forEach(row => {
                html += '<tr>';
                Object.values(row).forEach(val => html += `<td class="border px-2 py-1">${val}</td>`);
                html += '</tr>';
            });
            html += '</tbody></table>';
            document.getElementById('dataset-table').innerHTML = html;
        });
}
function fetchLogs() {
    fetch('/admin/datasets/logs')
        .then(res => res.json())
        .then(data => {
            document.getElementById('logs').textContent = data.logs || 'No logs.';
        });
}
document.getElementById('generate-btn').onclick = function() {
    fetch('/admin/datasets/generate', {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
        .then(res => res.json())
        .then(() => { fetchDataset(); fetchLogs(); });
};
fetchDataset();
fetchLogs();
</script>
@endsection
