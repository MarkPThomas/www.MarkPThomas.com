<?php namespace markpthomas\main; ?>
<!-- Modal -->
<div id="myModal" class="modal fade" aria-hidden="true" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Delete Box</h4>
            </div>
            <div class="modal-body">
                <h3 class="text-center">Are you sure that you want to delete this <span id="itemType"></span>?</h3>
            </div>
            <form method="post" action="" id="deleteId">
                <div class="modal-footer">
<!--                    <a class="btn btn-danger modal_delete_link" href="">Delete</a>-->
                    <input type='hidden' id='targetId' name='targetId' value='deleteId'>
                    <input type='hidden' id='currentValue' name='currentValue' value='true'>
                    <input class='btn btn-danger' type='submit' name='delete' data-id="1" value='Delete'>
<!--                    <button type="button" class='btn btn-danger' name='delete' id='deleteId' data-id="1" value='deleteId'>Delete</button>-->
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>

    </div>
</div>