@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Courier Recommendation Dashboard</h1>

    <!-- Create Shipment -->
    <div class="card mb-4">
        <div class="card-header">Create Shipment</div>
        <div class="card-body">
            <form id="create-shipment-form" class="form-inline flex-wrap">
                <input type="number" name="order_id" placeholder="Order ID" class="form-control mb-2 mr-2" required>
                <input type="text" name="destination_pincode" placeholder="Pincode" class="form-control mb-2 mr-2" required>
                <input type="text" name="package_weight" placeholder="Weight" class="form-control mb-2 mr-2" required>
                <input type="text" name="package_dimensions" placeholder="Dimensions" class="form-control mb-2 mr-2" required>
                <button type="submit" class="btn btn-primary mb-2">Create</button>
            </form>
            <div id="create-shipment-msg" class="text-success mt-2"></div>
        </div>
    </div>

    <!-- Shipments Table -->
    <div class="card mb-4">
        <div class="card-header">Shipments</div>
        <div class="card-body">
            <div class="form-inline mb-2">
                <label for="shipment-pincode-filter" class="mr-2">Filter by Pincode:</label>
                <input type="text" id="shipment-pincode-filter" class="form-control mr-2 w-auto" placeholder="Enter pincode">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="shipments-table">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Order</th>
                            <th>Pincode</th>
                            <th>Status</th>
                            <th>Recommended Courier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <nav>
                <ul class="pagination justify-content-center" id="pagination"></ul>
            </nav>
        </div>
    </div>

    <!-- Trends -->
    <div class="card mb-4">
        <div class="card-header">Trends (Last 30 Days)</div>
        <div class="card-body">
            <div class="form-inline mb-2">
                <label for="courier-filter" class="mr-2">Filter by Courier:</label>
                <select id="courier-filter" class="form-control mr-2 w-auto">
                    <option value="">All Couriers</option>
                </select>
            </div>
            <canvas id="trendChart" width="600" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Bootstrap 4 CDN -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let currentPage = 1;
let lastPage = 1;
let currentPincodeFilter = '';

async function fetchShipments(page = 1, pincode = '') {
    currentPincodeFilter = pincode;
    let url = `/api/shipments?page=${page}`;
    if (pincode) {
        url += `&pincode=${encodeURIComponent(pincode)}`;
    }
    const res = await fetch(url);
    const data = await res.json();
    const tbody = document.querySelector('#shipments-table tbody');
    tbody.innerHTML = '';
    for (const s of data.shipments) {
        let courierName = '';
        if (s.courier_id) {
            if (window.courierMap && window.courierMap[s.courier_id]) {
                courierName = window.courierMap[s.courier_id];
            } else {
                courierName = 'Courier ' + s.courier_id;
            }
        }
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${s.id}</td>
            <td>${s.order_id}</td>
            <td>${s.destination_pincode}</td>
            <td>${s.status}</td>
            <td>${courierName}</td>
            <td>
                <button onclick="getRecommendation(${s.id}, this)" class="btn btn-success btn-sm">Get</button>
                <button onclick="showDeliveryForm(${s.id})" class="btn btn-warning btn-sm ml-1">Add Result</button>
            </td>
        `;
        tbody.appendChild(tr);
    }
    renderPagination(data.meta.current_page, data.meta.last_page);
}

function renderPagination(current, last) {
    currentPage = current;
    lastPage = last;
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = 'page-item' + (current === 1 ? ' disabled' : '');
    prevLi.innerHTML = `<a class="page-link" href="#" onclick="if(currentPage>1){fetchShipments(currentPage-1, currentPincodeFilter);}" return false;">Previous</a>`;
    pagination.appendChild(prevLi);

    // Show up to 5 page numbers, centered around current page
    let start = Math.max(1, current - 2);
    let end = Math.min(last, start + 4);
    if (end - start < 4) start = Math.max(1, end - 4);
    for (let i = start; i <= end; i++) {
        const li = document.createElement('li');
        li.className = 'page-item' + (i === current ? ' active' : '');
        li.innerHTML = `<a class="page-link" href="#" onclick="fetchShipments(${i}, currentPincodeFilter);return false;">${i}</a>`;
        pagination.appendChild(li);
    }

    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = 'page-item' + (current === last ? ' disabled' : '');
    nextLi.innerHTML = `<a class="page-link" href="#" onclick="if(currentPage<lastPage){fetchShipments(currentPage+1, currentPincodeFilter);}" return false;">Next</a>`;
    pagination.appendChild(nextLi);
}

async function getRecommendation(shipmentId, btn) {
    const res = await fetch(`/api/shipments/${shipmentId}/recommend-courier`);
    const data = await res.json();
    // Find the row for this shipment using the button reference
    const row = btn.closest('tr');
    if (row) {
        // The courier name is in the 5th cell (index 4)
        row.children[4].innerText = data.courier ? data.courier.name : 'N/A';
    }
}

function showDeliveryForm(shipmentId) {
    // Build a select dropdown for couriers
    let courierOptions = '';
    for (const id in window.courierMap) {
        courierOptions += `<option value="${id}">${window.courierMap[id]}</option>`;
    }
    // Get the current courier_id for the shipment (if available)
    const shipmentRow = Array.from(document.querySelectorAll('#shipments-table tbody tr')).find(tr => tr.children[0].innerText == shipmentId);
    let currentCourierId = '';
    if (shipmentRow) {
        currentCourierId = Object.keys(window.courierMap).find(id => window.courierMap[id] === shipmentRow.children[4].innerText) || '';
    }
    const formHtml = `
        <form id="popup-delivery-form">
            <div class="form-group">
                <label for="courier_id">Courier</label>
                <select class="form-control" name="courier_id" id="courier_id" required>
                    <option value="">Select Courier</option>
                    ${courierOptions}
                </select>
            </div>
            <div class="form-group">
                <label for="delivered_at">Delivered At</label>
                <input type="text" class="form-control" name="delivered_at" id="delivered_at" value="${new Date().toISOString().slice(0,19).replace('T',' ')}" required>
            </div>
            <div class="form-group">
                <label for="success">Success?</label>
                <select class="form-control" name="success" id="success">
                    <option value="true">Yes</option>
                    <option value="false">No</option>
                </select>
            </div>
            <div class="form-group">
                <label for="rto">RTO?</label>
                <select class="form-control" name="rto" id="rto">
                    <option value="false">No</option>
                    <option value="true">Yes</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    `;
    // Remove any existing modal with the same ID to avoid duplicate modals
    const existingModal = document.getElementById('deliveryModal');
    if (existingModal) existingModal.remove();
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div class="modal fade" id="deliveryModal" tabindex="-1" role="dialog" aria-labelledby="deliveryModalLabel" style="display:block;background:rgba(0,0,0,0.5);">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deliveryModalLabel">Add Delivery Result</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="this.closest('.modal').remove();">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                ${formHtml}
              </div>
            </div>
          </div>
        </div>
    `;
    document.body.appendChild(modal);
    // Prefill courier if available
    if (currentCourierId) {
        modal.querySelector('select[name="courier_id"]').value = currentCourierId;
    }
    modal.querySelector('#popup-delivery-form').onsubmit = async function(e) {
        e.preventDefault();
        const form = e.target;
        const courier_id = form.courier_id.value;
        const delivered_at = form.delivered_at.value;
        const success = form.success.value === 'true';
        const rto = form.rto.value === 'true';
        await fetch(`/api/shipments/${shipmentId}/delivery-result`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({courier_id, delivered_at, success, rto})
        });
        modal.remove();
        fetchShipments(currentPage, currentPincodeFilter);
    };
}

document.getElementById('create-shipment-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const order_id = form.order_id.value;
    const destination_pincode = form.destination_pincode.value;
    const package_details = {
        weight: form.package_weight.value,
        dimensions: form.package_dimensions.value
    };
    const res = await fetch('/api/shipments', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({order_id, destination_pincode, package_details})
    });
    if (res.ok) {
        document.getElementById('create-shipment-msg').innerText = 'Shipment created!';
        fetchShipments(1);
        form.reset();
    } else {
        document.getElementById('create-shipment-msg').innerText = 'Error creating shipment.';
    }
});

async function fetchCouriersForDropdown() {
    // Fetch all couriers for the dropdown and build a map for shipment list
    const res = await fetch('/api/couriers');
    const couriers = await res.json();
    window.courierMap = {};
    const select = document.getElementById('courier-filter');
    select.innerHTML = '<option value="">All Couriers</option>';
    couriers.forEach(courier => {
        window.courierMap[courier.id] = courier.name;
        const opt = document.createElement('option');
        opt.value = courier.id;
        opt.text = courier.name;
        select.appendChild(opt);
    });
}

document.getElementById('courier-filter').addEventListener('change', fetchTrends);

async function fetchTrends() {
    const courierId = document.getElementById('courier-filter').value;
    const url = courierId ? `/api/trends/last-30-days?courier_id=${courierId}` : '/api/trends/last-30-days';
    const res = await fetch(url);
    const data = await res.json();
    renderTrendChart(data);
}

function renderTrendChart(trends) {
    const ctx = document.getElementById('trendChart').getContext('2d');
    if (window.trendChartInstance) window.trendChartInstance.destroy();

    const courierId = document.getElementById('courier-filter').value;
    let datasets = [];
    let chartType = 'bar';
    if (!courierId) {
        // Group by courier name
        const grouped = {};
        trends.forEach(t => {
            const name = t.courier ? t.courier.name : (t.courier_id || 'Unknown');
            if (!grouped[name]) grouped[name] = {dates: [], success: []};
            grouped[name].dates.push(t.date);
            grouped[name].success.push(t.success_rate);
        });
        const colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14'];
        let colorIdx = 0;
        for (const name in grouped) {
            datasets.push({
                label: name,
                data: grouped[name].success,
                backgroundColor: colors[colorIdx % colors.length],
                borderColor: colors[colorIdx % colors.length],
                borderWidth: 1
            });
            colorIdx++;
        }
        // Use the dates from the first group for labels
        var labels = Object.values(grouped)[0] ? Object.values(grouped)[0].dates : [];
    } else {
        datasets = [
            {
                label: trends[0] && trends[0].courier ? trends[0].courier.name + ' Success Rate' : 'Success Rate',
                data: trends.map(t => t.success_rate),
                backgroundColor: '#007bff',
                borderColor: '#007bff',
                borderWidth: 1
            },
            {
                label: 'RTO Rate',
                data: trends.map(t => t.rto_rate),
                backgroundColor: 'red',
                borderColor: 'red',
                borderWidth: 1
            }
        ];
        var labels = trends.map(t => t.date);
    }
    window.trendChartInstance = new Chart(ctx, {
        type: chartType,
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                xAxes: [{
                    stacked: false,
                    barPercentage: 0.7,
                    categoryPercentage: 0.6
                }],
                yAxes: [{
                    stacked: false,
                    ticks: {
                        beginAtZero: true,
                        max: 1
                    }
                }]
            }
        }
    });
}

document.getElementById('shipment-pincode-filter').addEventListener('input', function() {
    const pincode = this.value.trim();
    fetchShipments(1, pincode);
});

fetchShipments();
fetchCouriersForDropdown();

// Expose functions to global scope for inline onclick handlers
window.getRecommendation = getRecommendation;
window.showDeliveryForm = showDeliveryForm;
console.log('Dashboard JS loaded');
</script>
@endsection
