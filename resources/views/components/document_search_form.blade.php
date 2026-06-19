<form method="GET" action="{{ $action }}" class="form-inline" style="margin-top: 10px;">
    <input type="hidden" name="type" value="{{ request('type') }}">
    <div class="form-group mr-2">
        <input type="text" name="title" class="form-control" placeholder="Search by Title" value="{{ request('title') }}">
    </div>
    <div class="form-group mr-2">
        <select name="section" class="form-control">
            <option value="">Search by Section</option>
            <option value="Finance" {{ request('section') == 'Finance' ? 'selected' : '' }}>Finance</option>
            <option value="HR" {{ request('section') == 'HR' ? 'selected' : '' }}>HR</option>
            <option value="ICT" {{ request('section') == 'IT' ? 'selected' : '' }}>IT</option>
            <option value="Admission" {{ request('section') == 'Admission' ? 'selected' : '' }}>Admission</option>
        </select>
    </div>
    <div class="form-group mr-2">
        <input type="date" name="date_from" class="form-control" placeholder="Date From" value="{{ request('date_from') }}">
    </div>
    <div class="form-group mr-2">
        <input type="date" name="date_to" class="form-control" placeholder="Date To" value="{{ request('date_to') }}">
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
    <a href="{{ $resetUrl }}" class="btn btn-secondary ml-2">Reset</a>
</form>