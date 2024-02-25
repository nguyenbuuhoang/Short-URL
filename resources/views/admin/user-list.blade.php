@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6 my-auto">User List</div>
                            <div class="col-md-6 text-right">
                                <button id="exportCSV" class="btn btn-success">Export to CSV</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label for="nameFilter">Filter by Name:</label>
                                <input type="text" id="nameFilter" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sortBy">Sort by:</label>
                                <select id="sortBy" class="form-control">
                                    <option value="id">ID</option>
                                    <option value="name">User Name</option>
                                    <option value="roles">Role</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sortOrder">Order:</label>
                                <select id="sortOrder" class="form-control">
                                    <option value="asc">Ascending</option>
                                    <option value="desc">Descending</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1 mb-2">
                                <div class="form-group">
                                    <button type="button" id="filter-button" class="btn btn-primary btn-block" style="width: 100px">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush table-borderless">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Total URLs</th>
                                    <th>Registration Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>
    </div>
</div>

    @include('model.admin.user_list.editRoleModel')
    <script>
        $(document).ready(function() {
            const tableBody = $('tbody');
            const filterButton = $('#filter-button');
            const nameFilter = $('#nameFilter');
            const sortBy = $('#sortBy');
            const sortOrder = $('#sortOrder');
            const pagination = $('#pagination');
            let currentPage = 1;

            function formatDateString(dateString) {
                const createdDate = new Date(dateString);
                const formattedDate =
                    `${createdDate.getDate()}/${createdDate.getMonth() + 1}/${createdDate.getFullYear()}`;
                return formattedDate;
            }

            function createUserRow(user) {
                const formattedDate = formatDateString(user.created_at);
                const isVerified = user.is_verified ? 'Đã xác thực' : 'Chưa xác thực';
                const role = user.roles.map(role => role.name).join(', ');
                const totalUrls = user.total_urls.length > 0 ? user.total_urls[0].total_url : 0;

                const row = `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${role}</td>
                        <td>${totalUrls}</td>
                        <td>${formattedDate}</td>
                        <td>${isVerified}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button" id="actionMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-gear"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="actionMenu">
                                    <a class="dropdown-item edit-action" href="#">Sửa Role</a>
                                    <a class="dropdown-item delete-action" href="#">Xóa</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
                return row;
            }
            $.ajax({
                url: '/api/users-list',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    const users = data.users.data;

                    if (users.length > 0) {
                        const userRows = users.map(createUserRow).join('');
                        tableBody.html(userRows);
                    } else {
                        tableBody.html('<tr><td colspan="8">Không tìm thấy người dùng</td></tr>');
                    }
                },
                error: function(error) {
                    console.error('Lỗi:', error);
                }
            });


            tableBody.on('click', '.edit-action', function(e) {
                e.preventDefault();

                const row = $(this).closest('tr');
                const userId = row.find('td:first')
                    .text();
                $('#roleSelect').val('user');
                $('#editRoleModal').modal('show');
                $('#saveRoleButton').data('userId', userId);
            });

            $('#saveRoleButton').click(function() {
                const userId = $(this).data('userId');
                const selectedRole = $('#roleSelect').val();
                $.ajax({
                    url: `/api/users/${userId}`,
                    method: 'PUT',
                    data: {
                        role: selectedRole
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content'),
                    },
                    dataType: 'json',
                    success: function(response) {
                        alert('Cập nhật thành công.');
                        location.reload();
                    },
                    error: function(error) {
                        console.error('Lỗi khi cập nhật vai trò:', error);
                    }
                });
                $('#editRoleModal').modal('hide');
            });
            tableBody.on('click', '.delete-action', function(e) {
                e.preventDefault();
                const userId = $(this).closest('tr').find('td:first').text();
                const confirmation = confirm('Bạn có chắc chắn muốn xóa người dùng này?');
                if (confirmation) {
                    $.ajax({
                        url: `/api/users/${userId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        dataType: 'json',
                        success: function(data) {
                            alert(data.message);
                            $(e.target).closest('tr').remove();
                        },
                        error: function(error) {
                            console.error('Lỗi:', error);
                            alert('Lỗi xóa người dùng');
                        }
                    });
                }
            });

            function fetchDataAndPopulateTable(page) {
                currentPage = page;
                const name = nameFilter.val();
                const sort_by = sortBy.val();
                const sort_order = sortOrder.val();

                $.ajax({
                    url: '/api/users-list',
                    method: 'GET',
                    data: {
                        name,
                        sort_by,
                        sort_order,
                        page,
                    },
                    dataType: 'json',
                    success: function(data) {
                        const users = data.users.data;

                        if (users.length > 0) {
                            const userRows = users.map(createUserRow).join('');
                            tableBody.html(userRows);
                        } else {
                            tableBody.html('<tr><td colspan="8">Không tìm thấy người dùng</td></tr>');
                        }

                        updatePagination(data);
                    },
                    error: function(error) {
                        console.error('Lỗi:', error);
                    }
                });
            }

            function exportCSV() {
                const name = nameFilter.val();
                const sortField = sortBy.val();
                const sortOrderValue = sortOrder.val();
                const apiUrl =
                    `/api/users-list?name=${name}&sort_by=${sortField}&sort_order=${sortOrderValue}&export=csv`;

                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    dataType: 'text',
                    success: function(data) {
                        const blob = new Blob([data], {
                            type: 'text/csv'
                        });
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement("a");
                        a.href = url;
                        a.download = "data_users_list.csv";
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    },
                    error: function(xhr, status, error) {
                        console.error('Lỗi khi gửi yêu cầu AJAX: ', error);
                    }
                });
            }

            $("#exportCSV").on("click", exportCSV);

            const updatePagination = data => {
                pagination.empty();
                if (data.users.total > data.users.per_page) {
                    for (let i = 1; i <= data.users.last_page; i++) {
                        const pageLink = $('<a>').addClass('page-link').text(i);
                        pageLink.on('click', () => fetchDataAndPopulateTable(i));

                        const listItem = $('<li>').addClass('page-item');
                        if (i === currentPage) {
                            listItem.addClass('active');
                        }
                        listItem.append(pageLink);

                        pagination.append(listItem);
                    }
                }
            };

            filterButton.click(function() {
                fetchDataAndPopulateTable(1);
            });

            fetchDataAndPopulateTable(1);
        });
    </script>
@endsection
