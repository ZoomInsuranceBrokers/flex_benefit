<!-- Example split danger button -->
<div>
    {{-- <div class="alert alert-primary col-4" role="alert" style="">
        <strong>Financial Year:</strong><span id="enrollment_history_year"></span>
    </div> --}}
    <div class="btn-group mb-4" style="float:right;clear:both;">
        <button type="button" class="btn btn-primary" id="enrollment_history_year">Financial Year</button>
        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">FY</span>
        </button>
        <ul class="dropdown-menu">
            @foreach($data['fyData'] as $fyRow)
                <li class="{{ $fyRow['is_active'] ? 'bg-secondary text-white' : 'bg-light' }}">
                    <a class="dropdown-item" data-id="{{ base64_encode($fyRow['id']) }}" 
                    id="fyClick{{ base64_encode($fyRow['id']) }}" href="#">
                        {{ $fyRow['name'] . ($fyRow['is_active'] ? '(Current Year)' : '') }}
                    </a>
                </li>
            @endforeach
            <!-- <li><hr class="dropdown-divider"></li> -->
        </ul>
    </div>
</div>
    
        
<div id="enrollment_history_content" style="clear:both;">
    <div class="bg-info p-3 text-white text-center" style="font-size:16px;">Please select finanical year to load enrollment data</div>
</div>
