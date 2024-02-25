@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="mt-3">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card" id="totalUsersCard">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text"><i class="fa fa-user"></i> <span id="totalUsersCount"></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card" id="totalShortURLCard">
                        <div class="card-body">
                            <h5 class="card-title">Total Links</h5>
                            <p class="card-text"><i class="fa fa-link"></i> <span id="totalShortURLCount"></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card" id="totalClicksCard">
                        <div class="card-body">
                            <h5 class="card-title">Total Clicks</h5>
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
                            <div class="col-md-6 my-auto">Link Management</div>
                            <div class="col-md-6 text-right">
                                <button id="exportCSV" class="btn btn-success">Export to CSV</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="nameFilter">Filter by Name:</label>
                                <input type="text" id="nameFilter" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="urlFilter">Filter by URL:</label>
                                <input type="text" id="urlFilter" class="form-control">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="sortBy">Sort by:</label>
                                <select id="sortBy" class="form-control">
                                    <option value="id">ID</option>
                                    <option value="name">User Name</option>
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
                                    <th>URL</th>
                                    <th>Total Clicks</th>
                                    <th>Expiration</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="shortUrlTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>
    </div>
</div>
    @include('model.admin.link.editShortModal')
    @include('model.admin.link.qrcodeModel')
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script>
        $(document).ready(function() {
            const filterButton = $('#filter-button');
            const nameFilter = $('#nameFilter');
            const urlFilter = $('#urlFilter');
            const sortBy = $('#sortBy');
            const sortOrder = $('#sortOrder');
            const pagination = $('#pagination');
            const tableBody = $('#shortUrlTableBody');
            const currentTime = new Date();
            let currentPage = 1;

            function fetchDataAndDisplayTotals() {
                $.ajax({
                    url: '/api/totals',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#totalUsersCount').text(data.total_users);
                        $('#totalShortURLCount').text(data.total_short_url);
                        $('#totalClicksCount').text(data.total_clicks);
                    },
                    error: function(error) {
                        console.error('Lỗi khi tải dữ liệu:', error);
                    }
                });
            }

            function fetchDataAndPopulateTable(page) {
                currentPage = page;
                const name = nameFilter.val();
                const url = urlFilter.val();
                const sort_by = sortBy.val();
                const sort_order = sortOrder.val();

                $.ajax({
                    url: '/api/shortURL',
                    method: 'GET',
                    data: {
                        name,
                        url,
                        sort_by,
                        sort_order,
                        page,
                    },
                    dataType: 'json',
                    success: function(data) {
                        const shortUrls = data.data;
                        tableBody.empty();

                        const updatePagination = data => {
                            pagination.empty();
                            if (data.last_page > 1) {
                                for (let i = 1; i <= data.last_page; i++) {
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

                        updatePagination(data);

                        shortUrls.forEach(function(item) {
                            const userName = item.user ? item.user.name : "Guest";
                            const expiredAt = new Date(item.expired_at);
                            const daysRemaining = Math.ceil((expiredAt - currentTime) / (1000 *
                                60 * 60 * 24));
                            const expiredText = daysRemaining > 0 ? `${daysRemaining} Day` :
                                "<span class='text-danger'>Expired</span>";
                            const statusText = item.status === "active" ?
                                '<span class="badge badge-success">Activity</span>' :
                                '<span class="badge badge-danger">No activity</span>';
                            const row = $('<tr>');
                            row.html(`
                                <td>${item.id}</td>
                                <td>${userName}</td>
                                <td>
                                    <div class="mb-2">
                                        <i class="fas fa-2x mr-2 fa-copy pointer d-none d-md-inline align-middle copy-icon" onclick="copyToClipboard('${item.short_url_link}')"></i>
                                        <span class="align-middle" style="color: yellow;" id="shortUrl_${item.id}">${item.short_url_link}</span>
                                    </div>
                                    <div>
                                        <small><span style="color: yellow;">${shortenURLIfLong(item.url)}</span></small>
                                    </div>
                                </td>
                                <td>${item.clicks}</td>
                                <td>${expiredText}</td>
                                <td>${statusText}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="actionMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-gear"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="actionMenu">
                                            <a class="dropdown-item edit-action" href="#">Edit</a>
                                            <a class="dropdown-item delete-action" href="#">Delete</a>
                                            <a class="dropdown-item qr-action" href="#">QR code</a>
                                        </div>
                                    </div>
                                </td>
                            `);
                            tableBody.append(row);

                            // Edit action
                            row.find('.edit-action').on('click', function(e) {
                                e.preventDefault();
                                const shortUrlId = item.id;
                                const currentShortCode = item.short_code;
                                const currentStatus = item.status;
                                $('#shortCodeInput').val(currentShortCode);
                                $('#newStatus').val(currentStatus);

                                $('#editShortModal').modal('show');

                                $('#saveShortButton').off('click').on('click',
                                    function() {
                                        const newShortCode = $('#shortCodeInput')
                                            .val();
                                        const newStatus = $('#newStatus').val();

                                        if (newShortCode && newStatus) {
                                            $.ajax({
                                                url: `/api/shortURL/${shortUrlId}`,
                                                method: 'PUT',
                                                data: {
                                                    short_code: newShortCode,
                                                    status: newStatus,
                                                },
                                                headers: {
                                                    'X-CSRF-TOKEN': $(
                                                        'meta[name="csrf-token"]'
                                                    ).attr(
                                                        'content'),
                                                },
                                                success: function(data) {
                                                    alert(
                                                        'Update successful.'
                                                    );
                                                    location.reload();
                                                },
                                                error: function(error) {
                                                    console.log(error);
                                                },
                                            });

                                            $('#editShortModal').modal('hide');
                                        }
                                    });
                            });

                            // Delete action
                            row.find('.delete-action').on('click', function(e) {
                                e.preventDefault();
                                const shortUrlId = item.id;
                                if (confirm(
                                        "Are you sure you want to remove this short link?"
                                    )) {
                                    $.ajax({
                                        url: `/api/shortURL/${shortUrlId}`,
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': $(
                                                    'meta[name="csrf-token"]')
                                                .attr('content'),
                                        },
                                        success: function(data) {
                                            alert('Deleted successfully.');
                                            location.reload();
                                        },
                                        error: function(error) {
                                            console.log(error);
                                        },
                                    });
                                }
                            });

                            // QR Code action
                            row.find('.qr-action').on('click', function(e) {
                                e.preventDefault();
                                const shortUrl = item.short_url_link;

                                $('#qrcode').empty();

                                const qrcode = new QRCode(document.getElementById(
                                    "qrcode"), {
                                    text: shortUrl,
                                    width: 128,
                                    height: 128
                                });

                                $('#qrcodeModal').modal('show');

                                $('#downloadQRCode').on('click', function(e) {
                                    e.preventDefault();
                                    const qrCodeImg = $('#qrcode img')[0];
                                    const downloadLink = document.createElement(
                                        'a');
                                    downloadLink.href = qrCodeImg.src;
                                    downloadLink.download = 'qrcode.png';
                                    downloadLink.style.display = 'none';
                                    document.body.appendChild(downloadLink);
                                    downloadLink.click();
                                    document.body.removeChild(downloadLink);
                                });
                            });
                        });
                    },
                    error: function(error) {
                        console.error('Error loading data: ' + error);
                    }
                });
            }

            function exportCSV() {
                const name = nameFilter.val();
                const sortField = sortBy.val();
                const sortOrderValue = sortOrder.val();
                const apiUrl =
                    `/api/shortURL?name=${name}&sort_by=${sortField}&sort_order=${sortOrderValue}&export=csv`;

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
                        a.download = "data_shorts.csv";
                        document.body.appendChild(a);
                        a.click();

                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error sending AJAX request: ', error);
                    }
                });
            }

            $("#exportCSV").on("click", exportCSV);


            function shortenURLIfLong(url) {
                if (url.length > 30) {
                    return url.substring(0, 30) + '...';
                }
                return url;
            }

            function copyToClipboard(text) {
                const tempInput = document.createElement('input');
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                alert('Successfully copied the link: ' + text);
            }

            fetchDataAndDisplayTotals();
            filterButton.on('click', function() {
                fetchDataAndPopulateTable(1);
            });
            fetchDataAndPopulateTable(1);
        });
    </script>
@endsection
