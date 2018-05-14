/**
 * Created by Mark on 2/28/18.
 */

$(document).ready(function(){
    checkboxSliderEvent();
    checkboxEvent();
    selectionEvent();
    selectionFuelUxEvent();
    spinBoxFuelUxEvent();
    checkAllCheckboxEvent();
    deleteModalFormEvent();
    iFrameDynamicSize();
});


function iFrameDynamicSize(){
    // See: https://stackoverflow.com/questions/838137/jquery-change-height-based-on-browser-size-resize

    //jQuery.event.add(window, "load", resizeFrame);
    //jQuery.event.add(window, "resize", resizeFrame);
    $(window).resize(resizeFrame).resize();

    function resizeFrame()
    {
        var h = $(window).height();
        var w = $(window).width();
        $(".myIframe").css('height',(h < 768 || w < 1024) ? 500 : 400);
    }

    // See: https://stackoverflow.com/questions/7317781/how-to-set-iframe-size-dynamically
    //$('.myIframe').css('height', $(window).height()+'px');
}

/**
 * This function is for gluing the switch-checkbox slider from the page to the back end.
 */
function checkboxSliderEvent(){
    $('.approvalSwitch-checkbox').change(function() {
        var checkedValue = $(this).attr('data-on');
        var uncheckedValue = $(this).attr('data-off');
        var currentValue = $(this).prop('checked')? checkedValue : uncheckedValue;

        // The control records the value under the format of {$postTarget}&{$id}
        var controlValue = (this.value).split('&');
        var url = controlValue[0];
        var targetId = controlValue[1];

        $.ajax({
            url: url,
            type: 'post',
            data: {"currentValue": currentValue,
                   "targetId" : targetId}
        });
    })
}

function checkboxEvent(){
    $('.checkBoxes-dynamic').change(function() {
        var currentValue = $(this).prop('checked');

        // The control records the value under the format of {$postTarget}&{$id}
        var controlValue = (this.value).split('&');
        var url = controlValue[0];
        var targetId = controlValue[1];

        $.ajax({
            url: url,
            type: 'post',
            data: {"currentValue": currentValue,
                   "targetId" : targetId}
        });
    })
}

/**
 * This function toggles the checkbox selection for all rows in 'view_all_posts.php'.
 */
function checkAllCheckboxEvent(){
    $('#selectAllBoxes').click(function(){
        // this vs. $(this)
        if(this.checked){
            $('.checkBoxes-child').each(function(){
                this.checked = true;
            });
        } else {
            $('.checkBoxes-child').each(function(){
                this.checked = false;
            });
        }
    });
}

function selectionEvent(){
    $('.selection-dynamic').change(function() {
        // The control records the value under the format of {$postTarget}&{$id}
        var controlValue = (this.value).split('&');

        var url = controlValue[0];
        var targetId = controlValue[1];
        var targetValue = controlValue[2];

        $.ajax({
            url: url,
            type: 'post',
            data: {"currentValue": targetValue,
                   "targetId" : targetId}
        });
    })
}

function selectionFuelUxEvent(){
    $('.selectlist').on('changed.fu.selectlist', function () {
        var selectedItem = $(this).selectlist('selectedItem');

        // The control records the value under the format of {$postTarget}&{$id}
        var controlValue = (selectedItem.value).split('&');

        var url = controlValue[0];
        var targetId = controlValue[1];
        var targetValue = controlValue[2];

        $.ajax({
            url: url,
            type: 'post',
            data: {"currentValue": targetValue,
                "targetId" : targetId}
        });
    })
}

function spinBoxFuelUxEvent(){
    $('.spinbox').on('changed.fu.spinbox', function () {
        var currentValue = $(this).spinbox('getValue');

        var controlName = $(this)[0].getElementsByClassName('spinbox-input')[0].getAttribute('name').split('&');
        var url = controlName[1];
        var targetId = controlName[2];

        $.ajax({
            url: url,
            type: 'post',
            data: {"currentValue": currentValue,
                "targetId" : targetId}
        });
    })
}


function deleteModalFormEvent(){
    $('.btn-danger').click(function(){
        if ($(this).attr('name') === 'delete'){
            // Get data from button input control
            // Comes from data-item-id attribute given to the input control.
            // These can be custom defined as data-{name1}-[name2...] which is referenced in JS as dataset.name1[Name2]
            var targetId = this.dataset.itemId;
            var itemType = this.dataset.itemType;
            var postUrl = this.dataset.postUrl;
            var currentValue = true;

            // Set values in the modal form
            $('#itemType').text(itemType);
            $('.modal-content form#deleteId').attr('action', postUrl);
            $('.modal-footer #targetId').attr('value', targetId);
            $('.modal-footer #currentValue').attr('value', currentValue);

            // Display the modal form
            $('#myModal').modal('show');
        }
    });
}