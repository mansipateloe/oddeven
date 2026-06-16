<script>
$(function() {
    $("#myModal").modal();
});
</script>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form role="form" method="POST">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Why being late ? Reason</h4>
                </div>
                <div class="modal-body form-group">
                    <textarea class="form-control" rows="5" minlength="20" name="reason" required></textarea>
                    <span>*Minimum 20 characters required.</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" name="latesignIn" class="btn btn-primary" value="Sign in">
                </div>
            </div>
        </div>
    </form>
</div>