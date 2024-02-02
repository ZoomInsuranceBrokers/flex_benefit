<div class="modal" id="launchEnrollmentModal" style="display:none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="modal_close close" data-dismiss="modal" aria-label="Close"  onclick="updatePoints()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        {{-- <a class="btn-danger btn" href="/logout"></a> --}}
        <button class="btn-info btn modal_close" data-dismiss="modal" onclick="updatePoints()"></button>
      </div>
    </div>
  </div>
</div>