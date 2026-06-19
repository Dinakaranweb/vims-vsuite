<!-- Purchase Order Modal for Purchase Head -->
<form class="modal-part" id="modal-purchase-order-part" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label>Purchase Order File<span style="color:Red"> *</span></label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <input type="file" name="purchase_order" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                <small class="text-muted">Upload Purchase Order (PDF, JPG, PNG - Max 5MB)</small>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Message (Optional)</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
    </div>
    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
    <input type="hidden" name="status" value="Create Purchase Order">
</form>

<!-- Work Order Modal for Purchase Head -->
<form class="modal-part" id="modal-work-order-part" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label>Work Order File<span style="color:Red"> *</span></label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <input type="file" name="work_order" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                <small class="text-muted">Upload Work Order (PDF, JPG, PNG - Max 5MB)</small>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Message (Optional)</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control"></textarea>
            </div>
        </div>
    </div>
    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
    <input type="hidden" name="status" value="Create Work Order">
</form>

<!-- Finance Sanction Modal -->
<!-- Finance Sanction Modal -->
<form class="modal-part" id="modal-sanction-part">
    @csrf
    <div class="form-group">
        <label>Sanctioned Amount <span style="color:Red">*</span></label>
        <div class="input-group">
            <input type="number" step="0.01" name="sanctioned_amount" class="form-control" placeholder="Enter sanctioned amount" required>
            <div class="input-group-append">
                <span class="input-group-text">{{ $doc->currency ?? 'INR' }}</span>
            </div>
        </div>
        <small class="text-muted">Requested Amount: {{ isset($doc->currency) ? $doc->currency : '₹' }} {{ number_format($doc->amount, 2) }}</small>
    </div>
    <div class="form-group">
        <label>Recommended Amount</label>
        <div class="input-group">
            <input type="number" step="0.01" name="recommended_amount" class="form-control" placeholder="Enter recommended amount">
            <div class="input-group-append">
                <span class="input-group-text">{{ $doc->currency ?? 'INR' }}</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Remarks</label>
        <div class="input-group">
            <div class="col-sm-12 col-md-12">
                <textarea name="message" class="summernote form-control" placeholder="Add remarks about sanction"></textarea>
            </div>
        </div>
    </div>
    <input type="hidden" name="doc_id" value="{{ $doc->id }}">
    <input type="hidden" name="status" value="Sanction">
</form>