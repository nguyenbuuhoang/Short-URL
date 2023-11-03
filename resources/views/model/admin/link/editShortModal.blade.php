<div class="modal fade" id="editShortModal" tabindex="-1" role="dialog" aria-labelledby="editShortModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editShortModalLabel" style="color: black;">Chỉnh sửa</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="editShortForm">
                <div class="form-group">
                    <label for="shortCodeInput" style="color: black;">Short Code</label>
                    <input type="text" class="form-control" id="shortCodeInput"
                        style="background-color: rgb(89, 89, 92); color: white;">
                </div>
                <div class="form-group">
                    <label for="newStatus" style="color: black;">Trạng thái:</label>
                    <select id="newStatus" class="form-control"
                        style="background-color: rgb(89, 89, 92); color: white;">
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Không hoạt động</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-primary" id="saveShortButton">Lưu</button>
        </div>
    </div>
</div>
</div>
