<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa url</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="newShortCode">Nhập URL:</label>
                <div class="input-group">
                    <span class="input-group-text" id="baseURL"></span>
                    <input type="text" id="newShortCode" class="form-control">
                </div>
                <label for="newStatus">Trạng thái:</label>
                <select id="newStatus" class="form-control">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Không hoạt động</option>
                </select>
                <div id="passwordShortField" style="display: none;">
                    <label for="passwordShort">Mật khẩu:</label>
                    <input type="password" id="passwordShort" class="form-control">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="saveShortCode">Lưu</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        var currentURL = window.location.origin;
        document.getElementById("baseURL").textContent = currentURL + "/";
    });
</script>
