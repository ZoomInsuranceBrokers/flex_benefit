<!-- Example split danger button -->
<div class="d-flex justify-content-between align-items-center">
    <div>
        <button type="button" class="btn btn-primary" id="downloadPdf">Download Pdf</button>
    </div>
    <div class="btn-group mb-4">
        <button type="button" class="btn btn-primary" id="enrollmentHistoryYear">Policy Year</button>
        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">FY</span>
        </button>
        <ul class="dropdown-menu">
            @foreach($data['fyData'] as $fyRow)
            <li class="{{ $fyRow['is_active'] ? 'bg-secondary text-white' : 'bg-light' }}">
                <a class="dropdown-item" data-id="{{ base64_encode($fyRow['id']) }}" id="fyClick{{ base64_encode($fyRow['id']) }}" href="#">
                    {{ $fyRow['name'] . ($fyRow['is_active'] ? '(Current Year)' : '') }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</div>



<div id="enrollment_history_content" style="clear:both;">
    <div class="bg-info p-3 text-white text-center" style="font-size:16px;">Please select finanical year to load enrollment data</div>
</div>


