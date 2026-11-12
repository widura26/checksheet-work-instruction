<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="st-select-label d-block">Pilih Proyek</label>
        <select id="select-proyek" class="form-select st-select">
            <option value="Semua" selected>Semua</option>
            @foreach ($options['wbs'] as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="st-select-label d-block">Pilih Group</label>
        <select id="select-group" class="form-select st-select">
            <option value="Semua" selected>Semua</option>
            @foreach ($options['group'] as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="st-select-label d-block">Pilih Trainset / Batch</label>
        <select id="select-trainset" class="form-select st-select">
            <option value="Semua" @selected($defaultTrainset === 'Semua')>Semua</option>
            @foreach ($options['trainset'] as $o)
                <option value="{{ $o }}" @selected($defaultTrainset === $o)>{{ $o }}</option>
            @endforeach
        </select>
    </div>
</div>
