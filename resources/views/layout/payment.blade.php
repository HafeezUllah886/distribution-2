<table class="w-100">
    <tbody>
        <tr>
            <td>Method</td>
            <td>
                <select name="method" id="method" onchange="check_method()" class="form-control form-control-sm">
                    <option value="Cash">Cash</option>
                    <option value="Online">Online</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Other">Other</option>
                </select>
            </td>
        </tr>
        @foreach ($currencies as $currency)
            <tr class="cash">
                <td>{{$currency->title}}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" data-value="{{$currency->value}}" id="currency_{{$currency->id}}" oninput="updateTotal()" name="qty[]" value="0">
                    <input type="hidden" class="form-control" id="currencyID_{{$currency->id}}" name="currencyID[]" value="{{$currency->id}}">
                </td>
            </tr>
        @endforeach

        <tr>
            <td>Amount</td>
            <td>
                <input type="number" class="form-control form-control-sm" min="1" readonly id="amount" name="amount" value="0">
            </td>
        </tr>
        <tr class="non-cash d-none">
            <td>No.</td>
            <td>
                <input type="text" class="form-control form-control-sm" name="number">
            </td>
        </tr>
        <tr class="non-cash d-none">
            <td>Bank</td>
            <td>
                <input type="text" class="form-control form-control-sm" name="bank">
            </td>
        </tr>
        <tr class="non-cash d-none">
            <td>Remarks</td>
            <td>
                <input type="text" class="form-control form-control-sm" name="remarks">
            </td>
        </tr>
        <tr>
            <td>Attachement</td>
            <td>
                <input type="file" class="form-control form-control-sm" name="file">
            </td>
        </tr>
    </tbody>

</table>

<script>
     function updateTotal() {
        var total = 0;
        $("input[id^='currency_']").each(function() {
            var inputId = $(this).attr('id');
            var inputVal = $(this).val();
            var inputValue = $(this).data('value');
            var value = inputVal * inputValue;
            total += parseFloat(value);
        });
        $("#amount").val(total.toFixed(2));
    }

    function check_method() {
        $("#amount").val(0);
        if ($("#method").val() == "Cash") {
            $(".non-cash").addClass("d-none");
            $(".cash").removeClass("d-none");
            $("#amount").attr("readonly", true);
        } else {
            $(".non-cash").removeClass("d-none");
            $(".cash").addClass("d-none");
            $("#amount").attr("readonly", false);
        }
    }
    

    </script>