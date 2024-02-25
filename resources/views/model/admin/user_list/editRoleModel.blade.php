<div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-labelledby="editRoleModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editRoleModalLabel" style="color: black;">Edit role</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="editRoleForm">
                <div class="form-group">
                    <label for="roleSelect" style="color: black;">Chọn vai trò:</label>
                    <select class="form-control" id="roleSelect" name="role"
                        style="background-color: rgb(89, 89, 92); color: white;">
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                        <option value="user">User</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="saveRoleButton">Save</button>
        </div>
    </div>
</div>
</div>
