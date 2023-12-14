<div class="modal" id="launchLogout" style="display:none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Leaving???</h5>
        <button type="button" class="modal_close close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Do you really want to leave?</p>
      </div>
      <div class="modal-footer">
        <a class="btn-danger btn" href="/logout">Yes, I'm Going</a>
        <button class="btn-success btn modal_close" data-dismiss="modal">No, I will Stay!</button>
      </div>
    </div>
  </div>
</div>
<!-- FINAL SUMBIT ENROLLMENT MODAL -->
<div class="modal" id="finalSubmissionModal" style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 id="finalSubmissionModalTitle" class="modal-title">Ready for final Step?</h5>
            <button type="button" class="modal_close close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body text-center">
            <p id="finalSubmissionModalBody" class="bg-light text-dark">Do you really want to submit, as this step is <b class="text-uppercase">irreversible</b>.
            Once submission is done, no furhter modification possible till next year!!</p>
        </div>
        <div class="modal-footer">
            <button id="enrollmentSubmit" class="btn-primary btn-small">Yes, Made my Mind</button>
            <button id="finalSubmissionModalClose" class="btn-success btn-small modal_close" data-dismiss="modal">No, some changes left!</button>
        </div>
        </div>
    </div>
</div>