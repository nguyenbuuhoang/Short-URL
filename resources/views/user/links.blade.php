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
    @include('model.user.editModal')
    @include('model.user.qrcodeModal')
    <script>
        $(document).ready(function() {
            const urlInput = $('#urlInput');
            const tableBody = $('#shortUrlTable tbody');
            const userId = {{ Auth::check() ? Auth::user()->id : 'null' }};
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const nameFilter = $('#nameFilter');
            const sortBy = $('#sortBy');
            const sortOrder = $('#sortOrder');
            const filterButton = $('#filter-button');
            const pagination = $('#pagination');

            const updatePagination = data => {
                pagination.empty();
                if (data.shortUrls.total > data.shortUrls.per_page) {
                    for (let i = 1; i <= data.shortUrls.last_page; i++) {
                        const pageLink = $('<a>').addClass('page-link').text(i);
                        pageLink.on('click', () => fetchDataAndPopulateTable(i));

                        const listItem = $('<li>').addClass('page-item');
                        listItem.append(pageLink);

                        pagination.append(listItem);
                    }
                }
            };

            function createShortURLRow(shortUrl) {
                const row = $('<tr>');
                row.append($('<td>').text(shortUrl.id));

                const shortUrlContent = $('<div class="mb-2">');
                shortUrlContent.append(
                    '<i class="fas fa-2x mr-2 fa-copy pointer d-none d-md-inline align-middle copy-icon"></i>');
                shortUrlContent.append(
                    `<span class="align-middle" id="shortUrl_${shortUrl.id}">${shortUrl.short_url_link}</span>`);

                const urlDescription = $('<div>');
                urlDescription.append('<small><span class="text-muted"></span></small>');
                urlDescription.find('span').text(shortenURLIfLong(shortUrl.url));

                row.append($('<td>').append(shortUrlContent).append(urlDescription));
                row.append($('<td>').text(shortUrl.clicks));
                const createdAtDate = new Date(shortUrl.created_at);
                row.append($('<td>').text(createdAtDate.toLocaleString()));

                const now = new Date();
                const expiredAtDate = new Date(shortUrl.expired_at);

                if (now > expiredAtDate) {
                    const expiredCell = $('<td>').text('Hết hạn');
                    expiredCell.addClass('text-danger');
                    row.append(expiredCell);
                } else {
                    const timeRemaining = Math.max(0, expiredAtDate - now);
                    const daysRemaining = Math.ceil(timeRemaining / (1000 * 60 * 60 * 24));
                    const remainingCell = $('<td>').text(`${daysRemaining} ngày`);
                    row.append(remainingCell);
                }

                const statusBadge = shortUrl.status === 'active' ?
                    '<span class="badge badge-success">Công khai</span>' :
                    '<span class="badge badge-danger">Riêng tư</span>';
                row.append($('<td>').html(statusBadge));

                const dropdownMenu = $(`
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton_${shortUrl.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-gear"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_${shortUrl.id}">
                            <a class="dropdown-item edit-action" href="#"><i class="fas fa-edit"></i> Edit</a>
                            <a class="dropdown-item delete-action" href="#"><i class="fas fa-trash-alt"></i> Delete</a>
                            <a class="dropdown-item qr-action" href="#"><i class="fas fa-qrcode"></i> QR</a>
                        </div>
                    </div>
                `);

                row.append($('<td>').append(dropdownMenu));
                tableBody.append(row);

                row.find('.fa-copy').on('click', function() {
                    const shortUrlText = $(this).next('span').text();
                    copyToClipboard(shortUrlText);
                });

                row.find('.edit-action').on('click', function(e) {
                    e.preventDefault();
                    const shortUrlId = shortUrl.id;
                    const currentShortCode = shortUrl.short_code;
                    $('#editModal').modal('show');
                    $('#newShortCode').val(currentShortCode);
                    $('#saveShortCode').on('click', function() {
                        const newShortCode = $('#newShortCode').val();
                        const newStatus = $('#newStatus').val();

                        if (newShortCode && newStatus) {
                            $(this).off('click');

                            $.ajax({
                                url: `api/short-urls/${shortUrlId}`,
                                method: 'PUT',
                                data: {
                                    short_code: newShortCode,
                                    status: newStatus
                                },
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content'),
                                },
                                success: function(data) {
                                    alert('Cập nhật thành công.');
                                    location.reload();
                                },
                                error: function(error) {
                                    console.log(error);
                                }
                            });

                            $('#editModal').modal('hide');
                        }
                    });
                });

                row.find('.delete-action').on('click', function(e) {
                    e.preventDefault();
                    const shortUrlId = shortUrl.id;

                    if (confirm("Bạn có chắc chắn muốn xóa URL này?")) {
                        $.ajax({
                            url: `api/short-urls/${shortUrlId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function(data) {
                                row.remove();
                                alert('URL đã được xóa thành công.');
                                location.reload();
                            },
                            error: function(error) {
                                console.log(error);
                            }
                        });
                    }
                });

                row.find('.qr-action').on('click', function(e) {
                    e.preventDefault();
                    const url = shortUrl.short_url_link;
                    $('#qrcode').empty();

                    const qrcode = new QRCode(document.getElementById("qrcode"), {
                        text: url,
                        width: 128,
                        height: 128
                    });

                    $('#qrcodeModal').modal('show');

                    $('#downloadQRCode').on('click', function(e) {
                        e.preventDefault();
                        const qrCodeImg = $('#qrcode img')[0];
                        const downloadLink = document.createElement('a');
                        downloadLink.href = qrCodeImg.src;
                        downloadLink.download = 'qrcode.png';
                        downloadLink.style.display = 'none';
                        document.body.appendChild(downloadLink);
                        downloadLink.click();
                        document.body.removeChild(downloadLink);
                    });
                });
            }

            function copyToClipboard(text) {
                const tempInput = document.createElement('input');
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                alert('Đã sao chép đường dẫn thành công: ' + text);
            }

            function shortenURLIfLong(url) {
                if (url.length > 30) {
                    return url.substring(0, 30) + '...';
                }
                return url;
            }

            function fetchDataAndPopulateTable(page) {
                const name = nameFilter.val();
                const sortField = sortBy.val();
                const sortOrderValue = sortOrder.val();
                const apiUrl =
                    `/api/short-urls/${userId}?url=${name}&sort_by=${sortField}&sort_order=${sortOrderValue}&page=${page}`;

                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        tableBody.empty();
                        data.shortUrls.data.forEach(function(shortUrl) {
                            createShortURLRow(shortUrl);
                        });

                        updatePagination(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Lỗi khi gửi yêu cầu AJAX: ', error);
                    }
                });
            }
            $("#shortenButton").on("click", shortenURL);

            function exportCSV() {
                const name = nameFilter.val();
                const sortField = sortBy.val();
                const sortOrderValue = sortOrder.val();
                const apiUrl =
                    `/api/short-urls/${userId}?url=${name}&sort_by=${sortField}&sort_order=${sortOrderValue}&export=csv`;

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
                        a.download = "data_short_urls.csv";
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

            function loadUserShortURLs(userId) {
                $.ajax({
                    url: `/api/short-urls/${userId}`,
                    method: 'GET',
                    success: function(response) {
                        tableBody.empty();
                        response.shortUrls.data.forEach(function(shortUrl) {
                            createShortURLRow(shortUrl);
                        });
                        updatePagination(response);
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }

            if (userId) {
                loadUserShortURLs(userId);
            }

            filterButton.on('click', function() {
                fetchDataAndPopulateTable(1);
            });

            function updateTotals(userId) {
                $.ajax({
                    url: `/api/short-urls/${userId}/totals`,
                    method: 'GET',
                    success: function(response) {
                        $('#totalShortLinks').text(response.totalShortLinks);
                        $('#totalClicks').text(response.totalClicks);
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }
            updateTotals(userId);

            function shortenURL() {
                const url = urlInput.val();
                const errorContainer = $('#error-message');
                $.ajax({
                    url: '/api/user/create-short-url',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    data: JSON.stringify({
                        url: url,
                        user_id: userId,
                    }),
                    contentType: 'application/json',
                    success: function(data) {
                        loadUserShortURLs(userId);
                        urlInput.val('');
                        errorContainer.hide();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        if (xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = '';

                            for (const key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errorMessages += errors[key].join(', ') + '<br>';
                                }
                            }
                            errorContainer.html(errorMessages);
                            errorContainer.show();
                        } else {}
                    }
                });
            }

            $('#filter-icon').on('click', function() {
                const filterColumns = $('#filter-columns');
                if (filterColumns.css('display') === 'none') {
                    filterColumns.css('display', 'block');
                } else {
                    filterColumns.css('display', 'none');
                }
            });
        });
    </script>
@endsection
