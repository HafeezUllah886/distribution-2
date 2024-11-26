$(document).ready(function() {
    function plus(id)
    {
        var qtyInput = $("#qty_"+id);
        var currentValue = parseInt(qtyInput.val());
        currentValue += 1;
        qtyInput.val(currentValue);
    }

    function minus(id)
    {
        var qtyInput = $("#qty_"+id);
        var currentValue = parseInt(qtyInput.val());
        if(currentValue > 1)
        {
            currentValue -= 1;
        }

        qtyInput.val(currentValue);
    }
});
