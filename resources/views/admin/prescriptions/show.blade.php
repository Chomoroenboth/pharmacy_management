@extends('layouts.admin-app')

@section('title', 'Prescription Details')

@section('page-css')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }
    .page-header .rx-code { color: #006c49; }

    .info-grid { display: flex; gap: 24px; margin-bottom: 24px; }
    .info-grid .card { flex: 1; margin-bottom: 0; }
    .section-label { font-size: 12px; color: #6c7a71; font-weight: 700; letter-spacing: 0.03em; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f3f4f6; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 11px; font-weight: 600; color: #3c4a42; margin-bottom: 6px; }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; font-family: inherit;
    }
    .form-row { display: flex; gap: 12px; }
    .form-row .form-group { flex: 1; }

    .med-table { width: 100%; border-collapse: collapse; }
    .med-table th { background: #f3f4f6; text-align: left; padding: 12px 16px; font-size: 12px; color: #3c4a42; font-weight: 600; }
    .med-table td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .med-name { font-weight: 600; }
    .row-actions { display: flex; gap: 12px; }
    .row-actions button { background: none; border: none; cursor: pointer; font-size: 14px; }
    .row-actions .edit-icon { color: #006c49; }
    .row-actions .delete-icon { color: #ba1a1a; }

    .add-med-link { color: #006c49; background: none; border: 1px solid #10b981; border-radius: 6px; padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer; margin-top: 16px; }

    .bottom-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 24px; }
    .btn-delete-outline { background: #fff; border: 1px solid #ba1a1a; color: #ba1a1a; border-radius: 6px; padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer; }
    .bottom-right { display: flex; gap: 12px; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; color: #191c1e; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }

    /* Modals */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #fff; border-radius: 8px; width: 100%; max-width: 460px; padding: 32px; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h2 { font-size: 20px; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px; }
    .locked-input { background: #f3f4f6; display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; color: #3c4a42; }
    .delete-body { font-size: 14px; color: #3c4a42; line-height: 1.5; margin: 12px 0 0; }
    .delete-body strong { color: #191c1e; }
    .warn-icon-circle { width: 56px; height: 56px; border-radius: 50%; background: #ffdad6; color: #ba1a1a; font-size: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .btn-remove-solid { background: #ba1a1a; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
</style>
@endsection

@section('content')

    @if (session('message'))
        <div style="background:#d1fae5; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
            {{ session('message') }}
        </div>
    @endif

    <div class="page-header">
        <h1>Prescription Details - <span class="rx-code">{{ $prescription->code }}</span></h1>
    </div>

    <form method="POST" action="{{ route('admin.prescriptions.update', $prescription->id) }}">
        @csrf
        @method('PUT')

        <div class="info-grid">
            <div class="card">
                <div class="section-label">DOCTOR INFORMATION</div>
                <div class="form-group">
                    <label>DOCTOR NAME</label>
                    <input type="text" name="doctor" value="{{ $prescription->doctor }}">
                </div>
                <div class="form-group">
                    <label>CLINIC</label>
                    <input type="text" name="clinic" value="{{ $prescription->clinic }}">
                </div>
                {{-- Doctor phone was on the Figma but has no matching column
                     on `prescription` — cut. --}}
            </div>

            <div class="card">
                <div class="section-label">PRESCRIPTION DETAILS</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>STATUS</label>
                        <select name="status">
                            <option value="active" {{ $prescription->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="filled" {{ $prescription->status === 'filled' ? 'selected' : '' }}>Filled</option>
                            <option value="expired" {{ $prescription->status === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>EXPIRY DATE</label>
                        <input type="date" name="expiry_date" value="{{ $prescription->expiry_date }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>NOTES</label>
                    <textarea name="notes" rows="3">{{ $prescription->notes }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="section-label">PRESCRIBED MEDICATIONS</div>
            <table class="med-table">
                <thead>
                    <tr><th>MEDICINE</th><th>DOSAGE</th><th>QTY</th><th>INSTRUCTIONS</th><th>ACTION</th></tr>
                </thead>
                <tbody>
                    @foreach($medicines as $med)
                    <tr>
                        <td class="med-name">{{ $med->name }}</td>
                        <td>{{ $med->dosage }}</td>
                        <td>{{ $med->quantity }}</td>
                        <td>{{ $med->instructions }}</td>
                        <td>
                            <div class="row-actions">
                                <button type="button" class="edit-icon" onclick="openEditMedicine({{ $med->id }}, '{{ addslashes($med->name) }}', '{{ addslashes($med->dosage) }}', {{ $med->quantity }}, '{{ addslashes($med->instructions) }}')">&#9998;</button>
                                <button type="button" class="delete-icon" onclick="openRemoveMedicine({{ $med->id }}, '{{ addslashes($med->name) }}')">&#128465;</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="add-med-link" onclick="openModal('addMedicineModal')">+ Add Medicine</button>
        </div>

        <div class="bottom-actions">
            <button type="button" class="btn-delete-outline" onclick="openModal('deleteRxModal')">&#128465; Delete Prescription</button>
            <div class="bottom-right">
                <a href="{{ route('admin.prescriptions') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </div>
    </form>

    {{-- Add Medicine modal --}}
    <div class="modal-overlay" id="addMedicineModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Add Medicine</h2>
                <button class="modal-close" onclick="closeModal('addMedicineModal')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.prescriptions.medicines.store', $prescription->id) }}">
                @csrf

                <div class="form-group">
                    <label>MEDICINE NAME</label>
                    <input type="text" name="medicine_name" placeholder="Search medicine...">
                </div>
                <div class="form-group">
                    <label>DOSAGE</label>
                    <input type="text" name="dosage" placeholder="e.g. 500mg">
                </div>
                <div class="form-group">
                    <label>QUANTITY</label>
                    <input type="number" name="quantity" placeholder="0">
                </div>
                <div class="form-group">
                    <label>INSTRUCTIONS</label>
                    <textarea name="instructions" rows="2" placeholder="e.g. Take 1 tablet daily"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('addMedicineModal')">Cancel</button>
                    <button type="submit" class="btn-save">Add Medicine</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Medicine modal --}}
    <div class="modal-overlay" id="editMedicineModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Edit Medicine</h2>
                <button class="modal-close" onclick="closeModal('editMedicineModal')">&times;</button>
            </div>
            <form method="POST" id="editMedicineForm" action="">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>MEDICINE NAME</label>
                    <div class="locked-input"><span id="editMedName"></span> &#128274;</div>
                </div>
                <div class="form-group">
                    <label>DOSAGE</label>
                    <input type="text" name="dosage" id="editMedDosage">
                </div>
                <div class="form-group">
                    <label>QUANTITY</label>
                    <input type="number" name="quantity" id="editMedQuantity">
                </div>
                <div class="form-group">
                    <label>INSTRUCTIONS</label>
                    <textarea name="instructions" id="editMedInstructions" rows="2"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('editMedicineModal')">Cancel</button>
                    <button type="submit" class="btn-save">&#10003; Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Remove Medicine modal --}}
    <div class="modal-overlay" id="removeMedicineModal">
        <div class="modal-box" style="text-align:center;">
            <div class="warn-icon-circle">&#9888;</div>
            <h2>Remove Medicine?</h2>
            <div class="delete-body">
                Are you sure you want to remove <strong id="removeMedName"></strong> from this prescription? This action cannot be undone.
            </div>
            <form method="POST" id="removeMedicineForm" action="">
                @csrf
                @method('DELETE')
                <div class="modal-footer" style="justify-content:center;">
                    <button type="button" class="btn-cancel" onclick="closeModal('removeMedicineModal')">Cancel</button>
                    <button type="submit" class="btn-remove-solid">Remove</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Prescription modal --}}
    <div class="modal-overlay" id="deleteRxModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2><span style="color:#ba1a1a;">&#9888;</span> Delete Prescription</h2>
            </div>
            <div class="delete-body">
                Are you sure you want to delete this prescription? This action cannot be undone and will remove the record from the patient's active treatment history.
            </div>
            <form method="POST" action="{{ route('admin.prescriptions.destroy', $prescription->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteRxModal')">Cancel</button>
                    <button type="submit" class="btn-remove-solid">Delete Prescription</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        function openEditMedicine(medId, name, dosage, quantity, instructions) {
            document.getElementById('editMedName').textContent = name;
            document.getElementById('editMedDosage').value = dosage;
            document.getElementById('editMedQuantity').value = quantity;
            document.getElementById('editMedInstructions').value = instructions;
            document.getElementById('editMedicineForm').action =
                "{{ url('admin/prescriptions/' . $prescription->id . '/medicines') }}/" + medId;
            openModal('editMedicineModal');
        }

        function openRemoveMedicine(medId, name) {
            document.getElementById('removeMedName').textContent = name;
            document.getElementById('removeMedicineForm').action =
                "{{ url('admin/prescriptions/' . $prescription->id . '/medicines') }}/" + medId;
            openModal('removeMedicineModal');
        }
    </script>

@stop