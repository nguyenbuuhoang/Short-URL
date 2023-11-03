@extends('admin.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card-header">Phân quyền link</div>
            <form>
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Add link</th>
                            <th>Edit link</th>
                            <th>Delete link</th>
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
                <button type="submit" class="btn btn-primary">Lưu</button>
            </form>
        </div>
    </div>
@endsection
