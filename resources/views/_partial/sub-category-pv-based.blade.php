<div class="col-4 offset-4">
    <form id="subCategoryForm{{ $subCatId }}" data-ispv="{{ $is_point_value_based }}">
    <table class="tab-content-table table-responsive mb-3 fs-11 col-3">
        <tbody class="col-6">
            <tr>
                <th>Benefit Name</th>
                <th>Points</th>
            </tr>
            @foreach($activePolicyForSubCategoryFY as $key => $item)
                @php //$subCatId = $item['ins_subcategory_id_fk']; 
                    $currenySymbol = html_entity_decode($item['policy']['currency']['symbol']);
                    if (!$item['policy']['show_value_column']) {
                @endphp
                <tr>
                    <td>
                        <input @php 
                        if ($item['policy']["points"] <= Auth::user()->points_available ) { @endphp
                            name="plan{{ $subCatId }}" data-sc-id="{{ $subCatId }}" data-plan-id="{{  $item['policy']["id"] }}"
                            id='planId{{ $item['policy']["id"] }}' value="{{ $item['policy']['id'] }}"
                        @php } else {
                            echo 'disabled';
                        }
                        @endphp 
                        type="checkbox"
                        @php
                            if (count($userPolData)) {
                                foreach($userPolData as $upRow) {
                                    if($item['policy']['id']==$upRow->ip_id){
                                        echo 'checked';
                                    }
                                }
                            } else if ($item['policy']['is_default_selection']) {
                                echo 'checked';
                            }
                        @endphp 
                         />
                        <label @php 
                        if ($item['policy']["points"] > Auth::user()->points_available ) {
                            echo 'class="text-danger"';
                        } else { @endphp
                            for='planId{{ $item['policy']["id"] }}'
                        @php } @endphp >
                            {{ $item['policy']["name"] }}
                        </label>
                    </td>
                    <td @php echo $item['policy']["points"] <= Auth::user()->points_available ? 
                     '' : 'class="text-danger"'; @endphp >{{ $formatter->formatCurrency($item['policy']["points"], 'INR') }}</td>
                </tr>
                @php
                    }
            @endphp
            @endforeach
            @foreach($activePolicyForSubCategoryFY as $key => $item)
                @php
                if($item['policy']['show_value_column']) {
                @endphp
                <tr>
                    <td>
                        <input name="plan{{ $subCatId }}" data-sc-id="{{ $subCatId }}" data-plan-id="{{  $item['policy']["id"] }}"
                        type="checkbox" {{ $item['policy']['is_default_selection'] ? 'checked' : '' }}
                        id='chkValuePlanId{{ $item['policy']["id"] }}' name='chkValuePlanName{{ $item['policy']["id"] }}' 
                        value="{{ $item['policy']['id'] }}"
                        @php
                        $txtValue = '';
                            if (count($userPolData)) {
                                foreach($userPolData as $upRow) {
                                    if($item['policy']['id']==$upRow->ip_id){
                                        echo 'checked';
                                        $txtValue = $upRow->points_used;
                                    }
                                }
                            } else if ($item['policy']['is_default_selection']) {
                                echo 'checked';

                            }
                        @endphp  />
                        <label for='planId{{ $item['policy']["id"] }}'>
                            {{ $item['policy']["name"] }}
                        </label>
                    </td>
                    <td><input type="number" pattern="[0-9]" onkeyup="checkPoints({{ $item['policy']["id"] }})" class="w-100" value="{{ $txtValue }}" data-plan-id="{{ $item['policy']["id"] }}" disabled name='txtValuePlanName{{ $item['policy']["id"] }}' 
                    id='txtValuePlanId{{ $item['policy']["id"] }}' /></td>
                </tr>        @php
                    }
                @endphp
            @endforeach 
        </tbody>
    </table>
    </form>
    <script>
    if(@php echo $is_point_value_based; @endphp) {
        $('#currSelectionHeadCol' + {{ $subCatId }} + ',#currSelectionDataCol' + {{ $subCatId }}).remove();
    }
    </script>
</div>