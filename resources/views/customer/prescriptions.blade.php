@extends('layouts.customer-app')

@section('title', 'My Prescriptions')

@section('page-css')
<style>
    .prescriptions-panel { max-width: 1200px; margin: 0 auto; padding: 32px; background: #fff; border: 1px solid #e4e8e7; border-radius: 10px; box-shadow: 0 2px 6px rgba(23, 54, 40, .06); }
    .prescriptions-heading { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding-bottom: 18px; border-bottom: 1px solid #edf0ef; }
    .prescriptions-heading h1 { margin: 0; color: #202124; font-size: 30px; line-height: 1.2; letter-spacing: -.7px; }
    .prescription-search { display: flex; align-items: center; gap: 12px; width: 288px; padding: 10px 12px; border: 1px solid #cdd9d4; border-radius: 8px; color: #79827f; font-size: 14px; }
    .prescription-search:focus-within { border-color: #00805e; box-shadow: 0 0 0 3px rgba(0, 128, 94, .12); }
    .prescription-search input { width: 100%; border: 0; outline: 0; background: transparent; color: #27312d; font: inherit; }
    .prescription-search input::placeholder { color: #79827f; }
    .prescription-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; margin-top: 24px; }
    .prescription-card { display: flex; min-height: 235px; flex-direction: column; padding: 16px; border: 1px solid #dfe5e3; border-radius: 8px; background: #f8faf9; box-shadow: 0 1px 2px rgba(27, 46, 37, .03); }
    .card-top { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
    .medicine-name { margin: 3px 0 7px; color: #212426; font-size: 18px; line-height: 1.3; font-weight: 700; }
    .status-badge { display: inline-block; padding: 4px 9px; border-radius: 4px; font-size: 12px; line-height: 1; font-weight: 650; text-transform: capitalize; flex-shrink: 0; }
    .status-active { color: #146449; background: #ccefe2; }
    .status-filled { color: #175f99; background: #dbeeff; }
    .status-expired { color: #b42318; background: #f9dddd; }
    .doctor-name { display: flex; align-items: center; gap: 5px; margin: 0 0 16px; color: #58625e; font-size: 14px; }
    .doctor-icon { width: 11px; height: 9px; border: 1.5px solid #58625e; border-radius: 7px 7px 3px 3px; position: relative; margin-top: 6px; }
    .doctor-icon::before { content: ''; width: 4px; height: 4px; position: absolute; top: -7px; left: 2px; border: 1.5px solid #58625e; border-radius: 50%; background: #f8faf9; }
    .rx-data { margin: 0; }
    .rx-row { display: flex; justify-content: space-between; gap: 16px; padding: 9px 0; border-bottom: 1px solid #e5e9e7; color: #58625e; font-size: 14px; }
    .rx-row dt, .rx-row dd { margin: 0; }
    .rx-row dd { color: #303633; font-weight: 600; text-align: right; }
    .details-link { display: inline-flex; align-items: center; gap: 8px; align-self: flex-end; margin-top: auto; padding-top: 18px; color: #00805e; font-size: 13px; font-weight: 700; text-decoration: none; }
    .details-link:hover { color: #005b43; }
    .details-link .arrow { font-size: 20px; line-height: 12px; }
    .empty-prescriptions { grid-column: 1 / -1; padding: 42px 20px; color: #69736f; text-align: center; }
    .prescription-card.is-hidden { display: none; }
    .search-no-results { display: none; grid-column: 1 / -1; padding: 42px 20px; color: #69736f; text-align: center; }
    .search-no-results.is-visible { display: block; }
    .loading-text { grid-column: 1 / -1; padding: 42px 20px; color: #69736f; text-align: center; }
    @media (max-width: 900px) { .prescription-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 600px) { .prescriptions-panel { padding: 20px; } .prescriptions-heading { align-items: flex-start; flex-direction: column; } .prescription-search { width: 100%; } .prescription-grid { grid-template-columns: 1fr; } }
</style>

@vite(['resources/js/app.js'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    const grid = document.getElementById('prescriptionGrid');
    const searchInput = document.getElementById('medicine-search');
    const noResults = document.getElementById('search-no-results');

    async function loadPrescriptions() {
        try {
            const response = await window.axios.get('/api/prescriptions');
            const prescriptions = response.data.data;

            if (!prescriptions.length) {
                grid.innerHTML = '<p class="empty-prescriptions">No prescriptions are available.</p>';
                return;
            }

            grid.innerHTML = prescriptions.map(function (rx) {
                const doctorName = `${rx.doctor_first_name} ${rx.doctor_last_name ?? ''}`.trim();
                const detailUrl = "{{ route('customer.prescriptions.details', '__ID__') }}".replace('__ID__', rx.prescription_id);
                const issueDate = new Date(rx.issue_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                const expiryDate = new Date(rx.expiry_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

                return `
                    <article class="prescription-card" data-search="${doctorName.toLowerCase()} ${rx.prescription_id}">
                        <div class="card-top">
                            <h2 class="medicine-name">Prescription #${rx.prescription_id}</h2>
                            <span class="status-badge status-${rx.status.toLowerCase()}">${rx.status.charAt(0).toUpperCase() + rx.status.slice(1)}</span>
                        </div>
                        <p class="doctor-name"><span class="doctor-icon"></span>Dr. ${doctorName}</p>
                        <dl class="rx-data">
                            <div class="rx-row"><dt>Issue Date:</dt><dd>${issueDate}</dd></div>
                            <div class="rx-row"><dt>Expiry Date:</dt><dd>${expiryDate}</dd></div>
                        </dl>
                        <a class="details-link" href="${detailUrl}">View Details <span class="arrow">→</span></a>
                    </article>
                `;
            }).join('');

        } catch (err) {
            if (err.response?.status === 401) {
                window.location.href = "{{ route('customer.login') }}";
                return;
            }
            grid.innerHTML = '<p class="empty-prescriptions">Failed to load prescriptions.</p>';
            console.error('Failed to load prescriptions:', err);
        }
    }

    searchInput.addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();
        const cards = document.querySelectorAll('.prescription-card');
        let visibleCount = 0;

        cards.forEach(function (card) {
            const matches = (card.dataset.search || '').includes(term);
            card.classList.toggle('is-hidden', !matches);
            if (matches) visibleCount++;
        });

        noResults.classList.toggle('is-visible', term !== '' && visibleCount === 0);
    });

    loadPrescriptions();
});
</script>
@endsection

@section('content')
<section class="prescriptions-panel">
    <div class="prescriptions-heading">
        <h1>My Prescriptions</h1>
        <label class="prescription-search" for="medicine-search">
            <span class="search-icon" aria-hidden="true"></span>
            <input id="medicine-search" type="search" placeholder="Search by doctor or ID..." autocomplete="off">
        </label>
    </div>
    <div class="prescription-grid" id="prescriptionGrid">
        <p class="loading-text">Loading prescriptions...</p>
    </div>
    <p class="search-no-results" id="search-no-results">No prescriptions match that search.</p>
</section>
@endsection
