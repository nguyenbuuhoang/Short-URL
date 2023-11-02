@extends('user.layouts.app')
@section('title', 'User Links')
@section('content')
    <div class="container-fluid py-4 px-5 mx-auto">
        <div class="row">
            <div class="col-md-10 text-center">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" name="urlInput" id="urlInput" class="form-control" placeholder="Enter short link"
                            required>
                        <div class="input-group-append">
                            <button id="shortenButton" class="btn btn-dark">Shorten</button>
                        </div>
                    </div>
                    <div id="error-message" class="alert alert-danger" style="display: none;"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="filter-icon-container">
                        <i id="filter-icon" class="fa-solid fa-filter fa-beat-fade fa-xl"
                            style="cursor: pointer; color: blue;"></i>
                    </div>
                    <button id="exportCSV" class="btn btn-primary">Export to CSV</button>
                </div>
                <div id="filter-columns" style="display: none;" class="mt-3">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nameFilter">Lọc theo tên:</label>
                            <input type="text" id="nameFilter" class="form-control" placeholder="Tìm kiếm link">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="sortBy">Sắp xếp theo:</label>
                            <select id="sortBy" class="form-control">
                                <option value="id">ID</option>
                                <option value="url">Link</option>
                                <option value="clicks">Số lượt click</option>
                                <option value="created_at">Ngày tạo</option>
                                <option value="expired_at">Ngày hết hạn</option>
                                <option value="status">Trạng thái</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="sortOrder">Thứ tự:</label>
                            <select id="sortOrder" class="form-control">
                                <option value="asc">Tăng dần</option>
                                <option value="desc">Giảm dần</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="filter-button" class="btn btn-primary btn-block">Lọc</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3 ">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="shortUrlTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Đường dẫn</th>
                                        <th>Số lần nhấp</th>
                                        <th>Ngày tạo</th>
                                        <th>Ngày hết hạn</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="shortUrl">
                                </tbody>
                            </table>
                            <nav aria-label="Page navigation">
                                <ul class="pagination" id="pagination">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 d-md-inline d-none">
                <div class="card">
                    <div class="card-header bg-dark text-white text-center">{{ Auth::user()->name }}</div>
                    <div class="card-body">
                        <table class="table table-hover mt-3">
                            <tr>
                                <th>Total Short Links: </th>
                                <td id="totalShortLinks"></td>
                            </tr>
                            <tr>
                                <th>Total Clicks: </th>
                                <td id="totalClicks"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
@endsection
