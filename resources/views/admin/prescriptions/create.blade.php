@extends('layouts.admin-app')

@section('title', 'New Prescription')

@section('page-css')
<style>
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
    .modal-box { background: #fff; border-radius: 8px; width: 100%; max-width: 820px; padding: 32px; max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h2 { font-size: 22px; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; text-decoration: none; color: #191c1e; }

    .form-row { display: flex; gap: 20px; margin-bottom: 18px; }
    .form-group { flex: 1; }
    .form-group label { display: block; font-size: 12px; font-weight: 600; color: #3c4a42; letter-spacing: 0.02em; margin-bottom: 6px; }
    .form-group input, .form-group textarea {
        width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; font-family: inherit;
    }
    .field-error { color: #ba1a1a; font-size: 12px; margin-top: 4px; }

    .med-section-header { display: flex; justify-content: space-between; align-items: center; margin: 20px 0 12px; }
    .med-section-header h3 { font-size: 16px; }
    .add-row-link { color: #006c49; background: none; border: none; font-size: 13px; font-weight: 600; cursor: pointer; }

    .med-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
    .med-row input { flex: 1; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; }
    .med-row input.qty { flex: 0 0 70px; }
    .remove-row-btn { background: none; border: none; color: #ba1a1a; font-size: 16px; cursor: pointer; padding: 6px; }

    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; color: #191c1e; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
</style>
@endsection

@section('content')

    <div class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>New Prescription</h2>
                <a href="{{ route('admin.prescriptions') }}" class="modal-close">&times;</a>
            </div>

            <form method="POST" action="{{ route('admin.prescriptions.store') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label>CUSTOMER</label>
                        <input type="text" name="customer" placeholder="Search customer name or ID..." value="{{ old('customer') }}">
                        @error('customer') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>PRESCRIBING DOCTOR</label>
                        <input type="text" name="doctor" placeholder="Dr. Name / License No." value="{{ old('doctor') }}">
                        @error('doctor') <div class="field-error">{{ $message }}</div> @enderror
                        {{-- Combined field: schema splits this into doctor_first_name /
                             doctor_last_name / doctor_license separately — will need
                             parsing when this connects to a real INSERT. --}}
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>DATE ISSUED</label>
                        <input type="date" name="issue_date" value="{{ old('issue_date') }}">
                        @error('issue_date') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>VALID UNTIL</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>PRESCRIPTION NOTES</label>
                    <textarea name="notes" rows="2" placeholder="Any additional notes or warnings...">{{ old('notes') }}</textarea>
                </div>

                <div class="med-section-header">
                    <h3>Medicines</h3>
                    <button type="button" class="add-row-link" onclick="addMedRow()">+ Add Row</button>
                </div>

                <div id="medRows">
                    <div class="med-row">
                        <input type="text" name="medicine_name[]" placeholder="Search medicine...">
                        <input type="text" name="dosage[]" placeholder="e.g. 500mg">
                        <input type="number" name="quantity[]" class="qty" placeholder="0">
                        <input type="text" name="instructions[]" placeholder="e.g. 1x Daily">
                        <button type="button" class="remove-row-btn" onclick="removeMedRow(this)">&#128465;</button>
                    </div>
                    <div class="med-row">
                        <input type="text" name="medicine_name[]" placeholder="Search medicine...">
                        <input type="text" name="dosage[]" placeholder="e.g. 500mg">
                        <input type="number" name="quantity[]" class="qty" placeholder="0">
                        <input type="text" name="instructions[]" placeholder="e.g. 1x Daily">
                        <button type="button" class="remove-row-btn" onclick="removeMedRow(this)">&#128465;</button>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('admin.prescriptions') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save">Save Prescription</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addMedRow() {
            const rows = document.getElementById('medRows');
            const row = document.createElement('div');
            row.className = 'med-row';
            row.innerHTML = `
                <input type="text" name="medicine_name[]" placeholder="Search medicine...">
                <input type="text" name="dosage[]" placeholder="e.g. 500mg">
                <input type="number" name="quantity[]" class="qty" placeholder="0">
                <input type="text" name="instructions[]" placeholder="e.g. 1x Daily">
                <button type="button" class="remove-row-btn" onclick="removeMedRow(this)">&#128465;</button>
            `;
            rows.appendChild(row);
        }
        function removeMedRow(btn) {
            const rows = document.getElementById('medRows');
            if (rows.children.length > 1) {
                btn.closest('.med-row').remove();
            }
        }
    </script>

@stop