<div class="col-md-2 col-sm-12  mt-3">
    <label for=""><strong>From Date</strong></label>
    <input type="date" class="form-control" value="{{ now()->startOfMonth()->format('Y-m-d') }}" name="from_date"
        id="filterFromdate" required>
    {{-- <input type="date" class="form-control" name="from_date"
        id="filterFromdate" required> --}}
</div>

<div class="col-md-2 col-sm-12  mt-3">
    <label for=""><strong>To Date</strong></label>
    <input type="date" class="form-control" value="{{ now()->endOfMonth()->format('Y-m-d') }}" name="to_date"
        id="filterTodate" required>
</div>
