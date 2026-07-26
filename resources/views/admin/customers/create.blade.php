@extends('layouts.admin-app')

@section('title', 'Add Customer')

@section('page-css')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }
    .page-header p { font-size: 14px; color: #6c7a71; margin-top: 4px; }

    .form-layout { display: flex; gap: 24px; align-items: flex-start; }
    .form-layout .card { flex: 1; margin-bottom: 0; }

    .section-title { font-size: 18px; font-weight: 700; color: #006c49; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f3f4f6; }

    .field-group { margin-bottom: 20px; }
    .field-group label { display: block; font-size: 11px; font-weight: 700; color: #3c4a42; letter-spacing: 0.03em; margin-bottom: 8px; }
    .input-with-icon { display: flex; align-items: center; gap: 10px; border: 1px solid #d7d9dc; border-radius: 8px; padding: 12px 14px; }
    .input-with-icon .icon { color: #6c7a71; font-size: 14px; }
    .input-with-icon input {
        border: none; outline: none; flex: 1; font-size: 15px; font-family: inherit; color: #191c1e;
    }
    .input-with-icon input::placeholder { color: #9aa39d; }

    .allergy-box { border: 1px solid #d7d9dc; border-radius: 8px; padding: 16px; }
    .allergy-badges { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
    .badge-removable {
        display: inline-flex; align-items: center; gap: 6px;
        background: #ffdad6; color: #930a0a;
        font-size: 13px; padding: 6px 12px; border-radius: 20px; font-weight: 500;
    }
    .badge-removable button { background: none; border: none; cursor: pointer; color: inherit; font-size: 13px; }
    .allergy-box input {
        width: 100%; border: none; outline: none; font-size: 14px; color: #6c7a71; padding: 4px 0;
    }

    .form-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .btn-cancel {
        background: #fff; border: 1px solid #d7d9dc; border-radius: 8px;
        padding: 12px 28px; font-size: 15px; font-weight: 600; color: #191c1e;
        cursor: pointer; text-decoration: none;
    }
    .btn-save {
        background: #04281b; border: none; border-radius: 8px;
        padding: 12px 28px; font-size: 15px; font-weight: 600; color: #fff;
        cursor: pointer;
    }
</style>
@endsection

@section('content')

    <div class="page-header">
        <h1>Add Customer</h1>
        <p>Create a new customer record</p>
    </div>

    <form onsubmit="return saveNewCustomer(event)">
        <div class="form-layout">
            {{-- Personal Information --}}
            <div class="card">
                <div class="section-title">Personal Information</div>

                <div class="field-group">
                    <label>FULL NAME</label>
                    <div class="input-with-icon">
                        <input type="text" id="full_name" placeholder="e.g. John Doe" required>
                    </div>
                </div>

                <div class="field-group">
                    <label>PHONE NUMBER</label>
                    <div class="input-with-icon">
                        <span class="icon">&#128222;</span>
                        <input type="text" id="phone" placeholder="+1 (555) 000-0000" required>
                    </div>
                </div>

                <div class="field-group">
                    <label>EMAIL ADDRESS</label>
                    <div class="input-with-icon">
                        <span class="icon">&#9993;</span>
                        <input type="email" id="email" placeholder="john.doe@example.com" required>
                    </div>
                </div>

                <div class="field-group">
                    <label>DATE OF BIRTH</label>
                    <div class="input-with-icon">
                        <span class="icon">&#128197;</span>
                        <input type="text" id="dob" placeholder="mm/dd/yyyy">
                    </div>
                </div>

                <div class="field-group" style="margin-bottom:0;">
                    <label>ADDRESS</label>
                    <div class="input-with-icon">
                        <span class="icon">&#127968;</span>
                        <input type="text" id="address" placeholder="123 Pharma Way, Suite 400">
                    </div>
                </div>
            </div>

            {{-- Medical (physician field cut — no matching column in schema) --}}
            <div class="card">
                <div class="section-title">Medical</div>

                <div class="field-group">
                    <label>KNOWN ALLERGIES / ALERTS</label>
                    <div class="allergy-box">
                        <div class="allergy-badges" id="allergyList">
                            {{-- Intentionally empty: new customer has no known allergies yet --}}
                        </div>
                        <input type="text" id="newAllergyInput" placeholder="Add more..." onkeydown="if(event.key==='Enter'){event.preventDefault(); addAllergy();}">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('admin.customers') }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-save">Save Customer</button>
        </div>
    </form>

    <script>
        function addAllergy() {
            const input = document.getElementById('newAllergyInput');
            const name = input.value.trim();
            if (!name) return;
            const badge = document.createElement('span');
            badge.className = 'badge-removable';
            badge.innerHTML = name + ' <button type="button" onclick="this.parentElement.remove()">&times;</button>';
            document.getElementById('allergyList').appendChild(badge);
            input.value = '';
        }

        function saveNewCustomer(e) {
            e.preventDefault();
            // Fake data only — no backend insert yet.
            // Note: "Full Name" will need splitting into first_name/last_name
            // when this is wired to the real `user` table.
            alert('Customer added (placeholder — not yet connected to database).');
            window.location.href = "{{ route('admin.customers') }}";
            return false;
        }
    </script>

@stop
