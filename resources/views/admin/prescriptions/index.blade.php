@extends('layouts.admin-app')

@section('title', 'Prescriptions')

@section('page-css')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }
    .btn-primary { background: #10b981; border: none; border-radius: 6px; padding: 10px 20px; font-size: 14px; font-weight: 600; color: #fff; text-decoration: none; }

    .rx-table { width: 100%; border-collapse: collapse; }
    .rx-table th { background: #f3f4f6; text-align: left; padding: 14px 20px; font-size: 12px; color: #3c4a42; font-weight: 600; }
    .rx-table td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .rx-code { color: #006c49; font-weight: 600; }
    .status-pill { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; }
    .status-active { background: #adedd3; color: #306d58; }
    .status-expired { background: #ffdad6; color: #930a0a; }
    .status-filled { background: #e7e8ea; color: #3c4a42; }
    .view-link { color: #3c4a42; font-size: 16px; text-decoration: none; cursor: pointer; }

    .table-footer { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; font-size: 13px; color: #6c7a71; }
    .pagination { display: flex; gap: 6px; }
    .page-btn { min-width: 32px; height: 32px; border: 1px solid #e1e2e4; border-radius: 6px; background: #fff; color: #3c4a42; font-size: 13px; cursor: pointer; }
    .page-btn.active { background: #10b981; border-color: #10b981; color: #fff; font-weight: 600; }
    .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .loading-row td, .empty-row td { text-align: center; padding: 32px; color: #6c7a71; }
</style>
@endsection

@section('content')

    @if (session('message'))
        <div style="background:#d1fae5; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
            {{ session('message') }}
        </div>
    @endif

    <div class="page-header">
        <h1>Prescriptions</h1>
        <a href="{{ route('admin.prescriptions.create') }}" class="btn-primary">+ Add Prescription</a>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="rx-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Doctor</th>
                    <th>Date Issued</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="rx-table-body">
                <tr class="loading-row"><td colspan="7">Loading prescriptions...</td></tr>
            </tbody>
        </table>

        <div class="table-footer">
            <div id="rx-showing-text">Showing 0 to 0 of 0 entries</div>
            <div class="pagination" id="rx-pagination"></div>
        </div>
    </div>

@stop

@section('page-js')
<script>
(function () {
    let currentPage = 1;
    const perPage = 5;

    function authToken() {
        return localStorage.getItem('auth_token');
    }

    function statusClass(status) {
        return 'status-' + status;
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    }

    function renderRows(prescriptions) {
        const tbody = document.getElementById('rx-table-body');

        if (!prescriptions.length) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="7">No prescriptions found.</td></tr>';
            return;
        }

        tbody.innerHTML = prescriptions.map(rx => `
            <tr>
                <td class="rx-code">${rx.display_code}</td>
                <td>${rx.customer_name}</td>
                <td>${rx.doctor_name}</td>
                <td>${formatDate(rx.issue_date)}</td>
                <td>${formatDate(rx.expiry_date)}</td>
                <td><span class="status-pill ${statusClass(rx.status)}">${rx.status.charAt(0).toUpperCase() + rx.status.slice(1)}</span></td>
                <td><a href="/admin/prescriptions/${rx.prescription_id}" class="view-link">&#x1F441;</a></td>
            </tr>
        `).join('');
    }

    function renderPagination(meta) {
        document.getElementById('rx-showing-text').textContent =
            meta.total === 0
                ? 'Showing 0 of 0 entries'
                : `Showing ${((meta.current_page - 1) * meta.per_page) + 1} to ${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total} entries`;

        const pag = document.getElementById('rx-pagination');
        let html = `<button class="page-btn" ${meta.current_page <= 1 ? 'disabled' : ''} data-page="${meta.current_page - 1}">Previous</button>`;

        for (let p = 1; p <= meta.last_page; p++) {
            html += `<button class="page-btn ${p === meta.current_page ? 'active' : ''}" data-page="${p}">${p}</button>`;
        }

        html += `<button class="page-btn" ${meta.current_page >= meta.last_page ? 'disabled' : ''} data-page="${meta.current_page + 1}">Next</button>`;
        pag.innerHTML = html;

        pag.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const page = parseInt(btn.dataset.page, 10);
                if (page >= 1 && page <= meta.last_page) {
                    loadPrescriptions(page);
                }
            });
        });
    }

    async function loadPrescriptions(page = 1) {
        currentPage = page;
        try {
            const res = await fetch(`/api/prescriptions?per_page=${perPage}&page=${page}`, {
                headers: {
                    'Authorization': `Bearer ${authToken()}`,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401) {
                window.location.href = '/admin/login';
                return;
            }

            const json = await res.json();
            renderRows(json.data);
            renderPagination(json.meta);
        } catch (err) {
            document.getElementById('rx-table-body').innerHTML =
                '<tr class="empty-row"><td colspan="7">Failed to load prescriptions.</td></tr>';
            console.error(err);
        }
    }

    loadPrescriptions(currentPage);
})();
</script>
@endsection
