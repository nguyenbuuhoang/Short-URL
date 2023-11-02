@extends('admin.layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="mt-3">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card" id="totalUsersCard">
                            <div class="card-body">
                                <h5 class="card-title">Tổng User</h5>
                                <p class="card-text"><i class="fa fa-user"></i> <span id="totalUsersCount"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card" id="totalShortURLCard">
                            <div class="card-body">
                                <h5 class="card-title">Tổng Số Link</h5>
                                <p class="card-text"><i class="fa fa-link"></i> <span id="totalShortURLCount"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card" id="totalClicksCard">
                            <div class="card-body">
                                <h5 class="card-title">Tổng Số Lần Click</h5>
                                <p class="card-text"><i class="fa fa-eye"></i> <span id="totalClicksCount"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6 my-auto">Quản lý links</div>
                                <div class="col-md-6 text-right">
                                    <button id="exportCSV" class="btn btn-success">Export to CSV</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <label for="nameFilter">Tìm theo Tên:</label>
                                    <input type="text" id="nameFilter" class="form-control">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="urlFilter">Tìm theo Url gốc:</label>
                                    <input type="text" id="urlFilter" class="form-control">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="sortBy">Sắp xếp theo:</label>
                                    <select id="sortBy" class="form-control">
                                        <option value="id">ID</option>
                                        <option value="name">Tên User</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="sortOrder">Thứ tự:</label>
                                    <select id="sortOrder" class="form-control">
                                        <option value="asc">Tăng dần</option>
                                        <option value="desc">Giảm dần</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1 mb-2">
                                    <div class="form-group">
                                        <button type="button" id="filter-button"
                                            class="btn btn-primary btn-block">Lọc</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush table-borderless">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên User</th>
                                        <th>Đường dẫn</th>
                                        <th>Tổng click</th>
                                        <th>Thời hạn</th>
                                        <th>Trạng thái</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody id="shortUrlTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="pagination" id="pagination">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script></script>
@endsection
