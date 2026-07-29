@extends('layouts.customer-app')

@section('title', 'Customer Profile')

@section('page-css')
<style>
    .profile-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .profile-header h1 { font-size: 28px; }
    .btn-edit {
        background: #fff; border: 1px solid #e1e2e4; border-radius: 6px;
        padding: 8px 16px; font-size: 13px; color: #006c49; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        cursor: pointer;
    }

    .top-row { display: flex; gap: 24px; margin-bottom: 24px; align-items: stretch; }
    .top-row .card { flex: 1; margin-bottom: 0; }

    .card-header-row { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f3f4f6; padding-bottom: 12px; margin-bottom: 16px; }

    .avatar-row { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
    .avatar-lg {
        width: 64px; height: 64px; border-radius: 50%;
        background: #adedd3; color: #306d58;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 22px; flex-shrink: 0;
    }
    .avatar-name { font-size: 20px; font-weight: 700; }
    .id-badge {
        display: inline-block; background: #edeef0; color: #3c4a42;
        font-size: 12px; padding: 3px 8px; border-radius: 4px; margin-top: 4px;
    }

    .field-row { padding: 12px 0; border-top: 1px solid #f3f4f6; }
    .field-row:first-child { border-top: none; }
    .field-label { font-size: 12px; color: #6c7a71; font-weight: 600; letter-spacing: 0.02em; margin-bottom: 4px; }
    .field-value { font-size: 15px; color: #191c1e; }

    .allergy-section-label { font-size: 12px; color: #6c7a71; font-weight: 600; letter-spacing: 0.02em; margin-bottom: 10px; }
    .allergy-badges { display: flex; gap: 8px; flex-wrap: wrap; }
    .badge-allergy-warn {
        background: #ffdad6; color: #930a0a;
        font-size: 13px; padding: 6px 12px; border-radius: 4px; font-weight: 500;
    }
    .badge-allergy-info {
        background: #e7e8ea; color: #3c4a42;
        font-size: 13px; padding: 6px 12px; border-radius: 4px; font-weight: 500;
    }

    .rx-count {
        display: inline-block; background: #adedd3; color: #306d58;
        font-size: 12px; font-weight: 600; padding: 2px 9px; border-radius: 20px; margin-left: 8px;
    }

    .rx-table { width: 100%; border-collapse: collapse; }
    .rx-table th {
        background: #f3f4f6; text-align: left; padding: 14px 20px;
        font-size: 12px; color: #3c4a42; font-weight: 600; letter-spacing: 0.02em;
    }
    .rx-table td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
    .rx-name { font-weight: 600; }
    .rx-sub { font-size: 12px; color: #6c7a71; margin-top: 2px; }
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; }
    .status-active { background: #adedd3; color: #306d58; }
    .status-filled { background: #e7e8ea; color: #3c4a42; }
    .dot { width: 6px; height: 6px; border-radius: 50%; }
    .dot-active { background: #10b981; }
    .dot-filled { background: #6c7a71; }

    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.5); z-index: 100;
        align-items: flex-start; justify-content: center;
        padding: 60px 20px; overflow-y: auto;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 8px; width: 100%; max-width: 640px;
        padding: 32px;
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .modal-header h2 { font-size: 22px; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; color: #191c1e; }

    .modal-section-title { font-size: 15px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

    .form-row { display: flex; gap: 16px; margin-bottom: 16px; }
    .form-field { flex: 1; }
    .form-field label {
        display: block; font-size: 11px; font-weight: 600; color: #3c4a42;
        letter-spacing: 0.03em; margin-bottom: 6px;
    }
    .form-field input {
        width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px;
        font-size: 14px; font-family: inherit; color: #191c1e;
    }

    .allergy-edit-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
    .badge-removable {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 13px; padding: 6px 10px; border-radius: 4px; font-weight: 500;
    }
    .badge-removable button {
        background: none; border: none; cursor: pointer; font-size: 13px;
        line-height: 1; color: inherit; padding: 0;
    }
    .add-allergy-row { display: flex; gap: 8px; margin-bottom: 24px; }
    .add-allergy-row input {
        flex: 1; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px;
    }
    .btn-add-allergy {
        background: #edeef0; border: none; border-radius: 6px;
        padding: 10px 20px; font-size: 14px; font-weight: 500; color: #191c1e; cursor: pointer;
    }

    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 8px; }
    .btn-cancel {
        background: #fff; border: 1px solid #d7d9dc; border-radius: 6px;
        padding: 10px 24px; font-size: 14px; font-weight: 500; color: #191c1e; cursor: pointer;
    }
    .btn-save {
        background: #10b981; border: none; border-radius: 6px;
        padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer;
    }
    .loading-text { color: #6b7280; padding: 16px 0; }
</style>

@vite(['resources/js/app.js'])
<script>
let currentUser = null;

document.addEventListener('DOMContentLoaded', function () {
    loadProfile();
    loadAllergies();
});

async function loadProfile() {
    try {
        const response = await window.axios.get('/api/profile');
        currentUser = response.data.data;

        const initials = (currentUser.first_name?.[0] ?? '') + (currentUser.last_name?.[0] ?? '');
        document.getElementById('avatarInitials').textContent = initials.toUpperCase();
        document.getElementById('avatarName').textContent = `${currentUser.first_name} ${currentUser.last_name ?? ''}`.trim();
        document.getElementById('idBadge').textContent = `# CUS-${currentUser.user_id}`;
        document.getElementById('displayPhone').textContent = currentUser.phone_number ?? '—';
        document.getElementById('displayEmail').textContent = currentUser.email;
        document.getElementById('displayDob').textContent = currentUser.date_of_birth ?? '—';

    } catch (err) {
        if (err.response?.status === 401) {
            window.location.href = "{{ route('customer.login') }}";
        }
        console.error('Failed to load profile:', err);
    }
}

async function loadAllergies() {
    const badgesEl = document.getElementById('allergyBadges');
    const editListEl = document.getElementById('allergyEditList');

    try {
        const response = await window.axios.get('/api/profile/allergies');
        const allergies = response.data.data;

        if (!allergies.length) {
            badgesEl.innerHTML = '<span class="rx-sub">No known allergies</span>';
            editListEl.innerHTML = '';
            return;
        }

        badgesEl.innerHTML = allergies.map(a =>
            `<span class="badge-allergy-warn">${a.allergy_name}</span>`
        ).join('');

        editListEl.innerHTML = allergies.map(a => `
            <span class="badge-removable badge-allergy-warn" data-allergy-id="${a.allergy_id}">
                ${a.allergy_name}
                <button type="button" onclick="removeAllergy(${a.allergy_id}, this)">&times;</button>
            </span>
        `).join('');

    } catch (err) {
        console.error('Failed to load allergies:', err);
    }
}

async function removeAllergy(allergyId, buttonEl) {
    try {
        await window.axios.delete('/api/profile/allergies/' + allergyId);
        buttonEl.closest('.badge-removable').remove();
        loadAllergies(); // refresh the display badges too
    } catch (err) {
        console.error('Failed to remove allergy:', err);
    }
}

async function addAllergy() {
    const input = document.getElementById('newAllergyInput');
    const name = input.value.trim();
    if (!name) return;

    try {
        await window.axios.post('/api/profile/allergies', { allergy_name: name });
        input.value = '';
        loadAllergies();
    } catch (err) {
        console.error('Failed to add allergy:', err);
    }
}

function openEditModal(e) {
    e.preventDefault();
    document.getElementById('edit_first_name').value = currentUser?.first_name ?? '';
    document.getElementById('edit_last_name').value = currentUser?.last_name ?? '';
    document.getElementById('edit_phone').value = currentUser?.phone_number ?? '';
    document.getElementById('edit_email').value = currentUser?.email ?? '';
    document.getElementById('editProfileModal').classList.add('open');
}

function closeEditModal() {
    document.getElementById('editProfileModal').classList.remove('open');
}

async function saveProfile(e) {
    e.preventDefault();

    try {
        await window.axios.put('/api/profile', {
            first_name: document.getElementById('edit_first_name').value,
            last_name: document.getElementById('edit_last_name').value,
            phone_number: document.getElementById('edit_phone').value,
        });

        closeEditModal();
        loadProfile();
    } catch (err) {
        console.error('Failed to save profile:', err);
        alert('Failed to save changes. Please try again.');
    }

    return false;
}
</script>
@endsection

@section('content')

    <div class="profile-header">
        <h1>Customer Profile</h1>
        <a href="#" class="btn-edit" onclick="openEditModal(event)">&#9998; Edit Profile</a>
    </div>

    <div class="top-row">
        <div class="card">
            <div class="card-header-row">
                <div class="card-title" style="margin-bottom:0;">Personal Information</div>
            </div>

            <div class="avatar-row">
                <div class="avatar-lg" id="avatarInitials">--</div>
                <div>
                    <div class="avatar-name" id="avatarName">Loading...</div>
                    <div class="id-badge" id="idBadge">—</div>
                </div>
            </div>

            <div class="field-row">
                <div class="field-label">PHONE NUMBER</div>
                <div class="field-value" id="displayPhone">Loading...</div>
            </div>
            <div class="field-row">
                <div class="field-label">EMAIL ADDRESS</div>
                <div class="field-value" id="displayEmail">Loading...</div>
            </div>
            <div class="field-row">
                <div class="field-label">DATE OF BIRTH</div>
                <div class="field-value" id="displayDob">Loading...</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header-row">
                <div class="card-title" style="margin-bottom:0;">Medical</div>
            </div>

            <div class="allergy-section-label">KNOWN ALLERGIES / ALERTS</div>
            <div class="allergy-badges" id="allergyBadges">
                <span class="rx-sub">Loading...</span>
            </div>
        </div>
    </div>

    {{-- TODO: Prescriptions data isn't wired to a real API endpoint yet — this table is still
         placeholder data from the controller. Needs its own connection pass once the
         prescriptions API response shape is confirmed. --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 24px 24px 16px;">
            <span class="card-title" style="margin-bottom:0;">Active Prescriptions</span>
            <span class="rx-count">{{ isset($prescriptions) ? $prescriptions->count() : 0 }}</span>
        </div>
        <table class="rx-table">
            <thead>
                <tr>
                    <th>MEDICATION</th>
                    <th>DOSAGE / INSTRUCTIONS</th>
                    <th>PRESCRIBER</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptions ?? [] as $rx)
                <tr>
                    <td>
                        <div class="rx-name">{{ $rx->medicine_name }}</div>
                        <div class="rx-sub">Rx: {{ $rx->rx_number }}</div>
                    </td>
                    <td>
                        <div>{{ $rx->dosage }}</div>
                        <div class="rx-sub">{{ $rx->instructions }}</div>
                    </td>
                    <td>{{ $rx->prescriber }}</td>
                    <td>
                        <span class="status-pill status-{{ $rx->status }}">
                            <span class="dot dot-{{ $rx->status }}"></span>
                            {{ ucfirst($rx->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal-overlay" id="editProfileModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>

            <form onsubmit="return saveProfile(event)">
                <div class="modal-section-title"> Personal Information</div>

                <div class="form-row">
                    <div class="form-field">
                        <label>FIRST NAME</label>
                        <input type="text" id="edit_first_name">
                    </div>
                    <div class="form-field">
                        <label>LAST NAME</label>
                        <input type="text" id="edit_last_name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label>PHONE NUMBER</label>
                        <input type="text" id="edit_phone">
                    </div>
                    <div class="form-field">
                        <label>EMAIL ADDRESS</label>
                        <input type="email" id="edit_email" disabled title="Email cannot be changed here">
                    </div>
                </div>

                <div class="modal-section-title" style="margin-top: 8px;">&#9888; Allergies</div>

                <div class="allergy-edit-row" id="allergyEditList"></div>

                <div class="add-allergy-row">
                    <input type="text" id="newAllergyInput" placeholder="Add new allergy...">
                    <button type="button" class="btn-add-allergy" onclick="addAllergy()">Add</button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

@stop
