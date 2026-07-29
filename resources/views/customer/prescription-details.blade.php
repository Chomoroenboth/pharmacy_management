@extends('layouts.customer-app')

@section('title', 'Prescription Details')

@section('page-css')
<style>
    .prescription-details { max-width: 946px; margin: 24px auto 0; padding: 30px 32px 42px; background: #fff; border: 1px solid #edf0ef; border-radius: 12px; box-shadow: 0 2px 8px rgba(25, 56, 40, .08); }
    .details-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding-bottom: 24px; border-bottom: 1px solid #edf0ef; }
    .back-link { display: inline-flex; align-items: center; gap: 9px; color: #007c5a; font-size: 13px; font-weight: 750; letter-spacing: .25px; text-decoration: none; text-transform: uppercase; }
    .back-arrow { font-size: 26px; font-weight: 400; line-height: 12px; }
    .validation-banner { display: inline-flex; align-items: center; gap: 7px; padding: 6px 12px; border-radius: 15px; font-size: 12px; font-weight: 650; }
    .validation-banner.valid { color: #087953; background: #d5f5e8; }
    .validation-banner.expired { color: #b42318; background: #fce1e1; }
    .validation-icon { display: grid; width: 15px; height: 15px; place-items: center; border-radius: 50%; background: currentColor; color: #fff; font-size: 10px; font-weight: 800; }
    .details-title { margin: 25px 0 24px; color: #202124; font-size: 26px; letter-spacing: -.45px; }
    .expired-alert { display: flex; align-items: flex-start; gap: 12px; margin: -6px 0 24px; padding: 16px 18px; border: 1px solid #f1a7a7; border-radius: 8px; background: #fce1e1; color: #8b1d18; }
    .alert-icon { display: grid; width: 20px; height: 20px; flex: 0 0 20px; place-items: center; border-radius: 50%; background: #c9231d; color: #fff; font-size: 13px; font-weight: 800; }
    .expired-alert strong { display: block; margin-bottom: 4px; font-size: 15px; }
    .expired-alert p { margin: 0; color: #683b38; font-size: 14px; line-height: 1.5; }
    .info-summary { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 29px 60px; padding: 25px 24px; border: 1px solid #dfe5e3; border-radius: 8px; background: #f4f6f7; }
    .info-block h2 { margin: 0 0 17px; color: #33413b; font-size: 12px; font-weight: 750; letter-spacing: .45px; text-transform: uppercase; }
    .info-list { display: grid; gap: 13px; margin: 0; }
    .info-list div { display: grid; gap: 4px; }
    .info-list dt { color: #69736f; font-size: 12px; font-weight: 650; text-transform: uppercase; }
    .info-list dd { margin: 0; color: #202124; font-size: 15px; font-weight: 600; line-height: 1.35; }
    .medicine-section { margin-top: 28px; }
    .medicine-section h2 { margin: 0; padding-bottom: 12px; border-bottom: 1px solid #edf0ef; color: #202124; font-size: 19px; }
    .medicine-table-wrap { overflow-x: auto; margin-top: 23px; }
    .medicine-table { width: 100%; min-width: 680px; border-collapse: collapse; }
    .medicine-table th { padding: 13px 16px; background: #f1f3f4; color: #46514c; font-size: 12px; font-weight: 750; letter-spacing: .3px; text-align: left; text-transform: uppercase; }
    .medicine-table td { padding: 17px 16px; border-bottom: 1px solid #e4e8e6; color: #3d4642; font-size: 14px; line-height: 1.4; vertical-align: top; }
    .medicine-table td:first-child { color: #202124; font-weight: 700; }
    .loading-text { padding: 42px 0; color: #69736f; text-align: center; }
    @media (max-width: 700px) { .prescription-details { margin-top: 0; padding: 24px 20px 30px; } .details-toolbar { align-items: flex-start; flex-direction: column; } .details-title { font-size: 23px; } .info-summary { grid-template-columns: 1fr; gap: 26px; padding: 22px 18px; } }
</style>

@vite(['resources/js/app.js'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    const prescriptionId = {{ (int) $prescriptionId }};
    const container = document.getElementById('detailsContainer');

    async function loadDetails() {
        try {
            const [rxResponse, profileResponse] = await Promise.all([
                window.axios.get('/api/prescriptions/' + prescriptionId),
                window.axios.get('/api/profile'),
            ]);

            const prescription = rxResponse.data.data.prescription;
            const medicines = rxResponse.data.data.medicines;
            const patient = profileResponse.data.data;

            const isExpired = prescription.status === 'expired';
            const doctorName = `${prescription.doctor_first_name} ${prescription.doctor_last_name ?? ''}`.trim();

            const medicineRows = medicines.map(function (m) {
                return `
                    <tr>
                        <td>${m.medicine_name}</td>
                        <td>${m.dosage ?? '—'}</td>
                        <td>${m.quantity}</td>
                        <td>${m.instructions ?? '—'}</td>
                    </tr>
                `;
            }).join('');

            container.innerHTML = `
                <div class="details-toolbar">
                    <a class="back-link" href="{{ route('customer.prescriptions') }}"><span class="back-arrow">←</span>Back to Prescriptions</a>
                    ${isExpired
                        ? '<span class="validation-banner expired"><span class="validation-icon">!</span>Expired Prescription</span>'
                        : '<span class="validation-banner valid"><span class="validation-icon">✓</span>Valid Prescription</span>'}
                </div>
                <h1 class="details-title">Prescription #${prescription.prescription_id}</h1>
                ${isExpired ? `
                    <div class="expired-alert" role="alert">
                        <span class="alert-icon">!</span>
                        <div>
                            <strong>Expired Prescription</strong>
                            <p>This prescription expired on ${new Date(prescription.expiry_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}. A new authorization is required for further dispensing.</p>
                        </div>
                    </div>
                ` : ''}
                <div class="info-summary">
                    <section class="info-block">
                        <h2>Prescription Information</h2>
                        <dl class="info-list">
                            <div><dt>Prescription ID</dt><dd>#${prescription.prescription_id}</dd></div>
                            <div><dt>Issue Date</dt><dd>${new Date(prescription.issue_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</dd></div>
                            <div><dt>Expiry Date</dt><dd>${new Date(prescription.expiry_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</dd></div>
                            <div><dt>Status</dt><dd>${prescription.status.charAt(0).toUpperCase() + prescription.status.slice(1)}</dd></div>
                            <div><dt>Notes</dt><dd>${prescription.notes || 'No notes available.'}</dd></div>
                        </dl>
                    </section>
                    <section class="info-block">
                        <h2>Doctor Information</h2>
                        <dl class="info-list">
                            <div><dt>Doctor Name</dt><dd>Dr. ${doctorName}</dd></div>
                            <div><dt>Clinic</dt><dd>${prescription.doctor_clinic ?? '—'}</dd></div>
                        </dl>
                    </section>
                    <section class="info-block">
                        <h2>Patient Information</h2>
                        <dl class="info-list">
                            <div><dt>First Name</dt><dd>${patient.first_name}</dd></div>
                            <div><dt>Last Name</dt><dd>${patient.last_name ?? '—'}</dd></div>
                            <div><dt>Date of Birth</dt><dd>${patient.date_of_birth ? new Date(patient.date_of_birth).toLocaleDateString('en-US') : '—'}</dd></div>
                        </dl>
                    </section>
                </div>
                <section class="medicine-section">
                    <h2>Prescribed Medicines</h2>
                    <div class="medicine-table-wrap">
                        <table class="medicine-table">
                            <thead>
                                <tr><th>Medicine</th><th>Dosage</th><th>Quantity</th><th>Instructions</th></tr>
                            </thead>
                            <tbody>${medicineRows}</tbody>
                        </table>
                    </div>
                </section>
            `;

        } catch (err) {
            if (err.response?.status === 401) {
                window.location.href = "{{ route('customer.login') }}";
                return;
            }
            container.innerHTML = '<p class="loading-text">Could not load this prescription. It may not exist or you may not have access to it.</p>';
            console.error('Failed to load prescription details:', err);
        }
    }

    loadDetails();
});
</script>
@endsection

@section('content')
<section class="prescription-details" id="detailsContainer">
    <p class="loading-text">Loading prescription details...</p>
</section>
@endsection
