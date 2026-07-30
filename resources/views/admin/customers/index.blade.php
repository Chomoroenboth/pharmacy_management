@extends('layouts.admin-app')

@section('title', 'Customers')

@section('page-css')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }
    .btn-primary {
        background: #10b981; border: none; border-radius: 6px;
        padding: 10px 20px; font-size: 14px; font-weight: 600; color: #fff;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        cursor: pointer;
    }

    .cust-table { width: 100%; border-collapse: collapse; }
    .cust-table th {
        background: #f3f4f6; text-align: left; padding: 14px 20px;
        font-size: 12px; color: #3c4a42; font-weight: 600; letter-spacing: 0.02em;
    }
    .cust-table td { padding: 18px 20px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .cust-table tr:last-child td { border-bottom: none; }
    .cust-name { font-weight: 600; }
    .actions-cell { display: flex; gap: 14px; align-items: center; }
    .icon-btn { background: none; border: none; cursor: pointer; font-size: 15px; color: #3c4a42; text-decoration: none; }

    .table-footer {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 20px; font-size: 13px; color: #6c7a71;
    }
    .pagination { display: flex; gap: 6px; }
    .page-btn {
        min-width: 32px; height: 32px; border: 1px solid #e1e2e4; border-radius: 6px;
        background: #fff; color: #3c4a42; font-size: 13px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .page-btn.active { background: #10b981; border-color: #10b981; color: #fff; font-weight: 600; }
</style>
@endsection

        @section('content')

    <div class="page-header">
        <h1>Customers</h1>
        <a href="{{ route('admin.customers.create') }}" class="btn-primary">+ Add Customer</a>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="cust-table">
            <thead>
                <tr>
                    <th>CUSTOMER ID</th>
                    <th>FULL NAME</th>
                    <th>PHONE</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody id="cust-table-body">
                <tr class="loading-row"><td colspan="4">Loading customers...</td></tr>
            </tbody>
        </table>

        <div class="table-footer">
            <div id="cust-showing-text">Showing 0 of 0 entries</div>
            <div class="pagination" id="cust-pagination"></div>
        </div>
    </div>

@stop
@section('page-js')
<script>
(function () {
    const perPage = 5;

    function authToken() {
        return localStorage.getItem('auth_token');
    }

    function renderRows(customers) {
        const tbody = document.getElementById('cust-table-body');

        if (!customers.length) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="4">No customers found.</td></tr>';
            return;
        }

        tbody.innerHTML = customers.map(c => `
            <tr>
                <td>#${c.display_id}</td>
                <td class="cust-name">${c.full_name}</td>
                <td>${c.phone_number ?? '—'}</td>
                <td>
                    <div class="actions-cell">
                        <a href="/admin/customers/${c.user_id}" class="icon-btn" title="View">&#128065;</a>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderPagination(meta) {
        document.getElementById('cust-showing-text').textContent =
            meta.total === 0
                ? 'Showing 0 of 0 entries'
                : `Showing ${((meta.current_page - 1) * meta.per_page) + 1} to ${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total} entries`;

        const pag = document.getElementById('cust-pagination');
        let html = `<button class="page-btn" ${meta.current_page <= 1 ? 'disabled' : ''} data-page="${meta.current_page - 1}">&lsaquo;</button>`;

        for (let p = 1; p <= meta.last_page; p++) {
            html += `<button class="page-btn ${p === meta.current_page ? 'active' : ''}" data-page="${p}">${p}</button>`;
        }

        html += `<button class="page-btn" ${meta.current_page >= meta.last_page ? 'disabled' : ''} data-page="${meta.current_page + 1}">&rsaquo;</button>`;
        pag.innerHTML = html;

        pag.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const page = parseInt(btn.dataset.page, 10);
                if (page >= 1 && page <= meta.last_page) {
                    loadCustomers(page);
                }
            });
        });
    }

    async function loadCustomers(page = 1) {
        try {
            const res = await fetch(`/api/staff/customers?per_page=${perPage}&page=${page}`, {
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
            document.getElementById('cust-table-body').innerHTML =
                '<tr class="empty-row"><td colspan="4">Failed to load customers.</td></tr>';
            console.error(err);
        }
    }

    loadCustomers();
})();
</script>
@stop
