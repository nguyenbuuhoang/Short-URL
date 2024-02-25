@extends('admin.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card-header">Link Permissions</div>
            <form>
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Add Link</th>
                            <th>Edit Link</th>
                            <th>Delete Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Admin</td>
                            <td><input type="checkbox" name="admin_add_link" value="1"></td>
                            <td><input type="checkbox" name="admin_edit_link" value="1"></td>
                            <td><input type="checkbox" name="admin_delete_link" value="1"></td>
                        </tr>
                        <tr>
                            <td>Editor</td>
                            <td><input type="checkbox" name="editor_add_link" value="1"></td>
                            <td><input type="checkbox" name="editor_edit_link" value="1"></td>
                            <td><input type="checkbox" name="editor_delete_link" value="1"></td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
@endsection
