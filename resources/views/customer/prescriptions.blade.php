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
    .prescription-card { display: flex; min-height: 265px; flex-direction: column; padding: 16px; border: 1px solid #dfe5e3; border-radius: 8px; background: #f8faf9; box-shadow: 0 1px 2px rgba(27, 46, 37, .03); }
    .card-top { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
    .medicine-name { margin: 3px 0 7px; color: #212426; font-size: 18px; line-height: 1.3; font-weight: 700; }
    .status-badge { display: inline-block; padding: 4px 9px; border-radius: 4px; font-size: 12px; line-height: 1; font-weight: 650; text-transform: capitalize; }
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
    @media (max-width: 900px) { .prescription-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 600px) { .prescriptions-panel { padding: 20px; } .prescriptions-heading { align-items: flex-start; flex-direction: column; } .prescription-search { width: 100%; } .prescription-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<section class="prescriptions-panel">
    <div class="prescriptions-heading">
        <h1>My Prescriptions</h1>
        <label class="prescription-search" for="medicine-search"><span class="search-icon" aria-hidden="true"></span><input id="medicine-search" type="search" placeholder="Search by medicine..." autocomplete="off"></label>
    </div>
    <div class="prescription-grid">
        @forelse($prescriptions as $prescription)
            <article class="prescription-card" data-medicine="{{ strtolower($prescription->medicine_name) }}">
                <div class="card-top">
                    <h2 class="medicine-name">{{ $prescription->medicine_name }}</h2>
                    <span class="status-badge status-{{ strtolower($prescription->status) }}">{{ ucfirst($prescription->status) }}</span>
                </div>
                <p class="doctor-name"><span class="doctor-icon" aria-hidden="true"></span>Dr. {{ $prescription->doctor_first_name }} {{ $prescription->doctor_last_name }}</p>
                <dl class="rx-data">
                    <div class="rx-row"><dt>Prescription ID:</dt><dd>#{{ $prescription->prescription_id }}</dd></div>
                    <div class="rx-row"><dt>Issue Date:</dt><dd>{{ \Carbon\Carbon::parse($prescription->issue_date)->format('M d, Y') }}</dd></div>
                    <div class="rx-row"><dt>Expiry Date:</dt><dd>{{ \Carbon\Carbon::parse($prescription->expiry_date)->format('M d, Y') }}</dd></div>
                </dl>
                <a class="details-link" href="{{ route('customer.prescriptions.details', $prescription->prescription_id) }}">View Details <span class="arrow" aria-hidden="true">→</span></a>
            </article>
        @empty
            <p class="empty-prescriptions">No prescriptions are available.</p>
        @endforelse
        <p class="search-no-results" id="search-no-results">No prescriptions match that medicine.</p>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('medicine-search');
        const prescriptionCards = document.querySelectorAll('.prescription-card');
        const noResults = document.getElementById('search-no-results');

        if (!searchInput) {
            return;
        }

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.trim().toLowerCase();
            let visibleCards = 0;

            prescriptionCards.forEach(function (card) {
                const medicineName = card.dataset.medicine || '';
                const matches = medicineName.includes(searchTerm);

                card.classList.toggle('is-hidden', !matches);

                if (matches) {
                    visibleCards++;
                }
            });

            noResults.classList.toggle('is-visible', searchTerm !== '' && visibleCards === 0);
        });
    });
</script>
@endsection
