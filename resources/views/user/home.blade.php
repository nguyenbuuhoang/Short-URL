@extends('user.layouts.app')
@section('title', 'Short Link')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mt-3">
                <h2>URL Shortener</h2>
                <p>Đây là giao diện trang Short-Url Link cơ bản, để có các chức năng cao cấp hơn, vui lòng đăng nhập và xác
                    thực</p>
                <p>Lưu ý: Link URL test chỉ có thời hạn là 30 phút do người dùng chưa đăng nhập</p>
                <p> Giới hạn chỉ hiển thị 3 Url gần nhất </p>
                <div class="input-group mb-4" style="max-width: 600px; margin: 0 auto;">
                    <input type="text" id="urlInput" class="form-control" name="urlInput"
                        placeholder="Enter a link to shorten it">
                    <button id="shortenButton" class="btn btn-primary">Shorten URL</button>
                </div>
            </div>
        </div>
        <div class="alert alert-danger" id="errorContainer" style="display: none;"></div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Link Short</th>
                                <th>Short URL</th>
                                <th>Thời hạn</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="shortUrl">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            const urlInput = $('#urlInput');
            const shortenButton = $('#shortenButton');
            const shortUrlDisplay = $('#shortUrl');
            const errorContainer = $('#errorContainer');
            let savedUrls = JSON.parse(localStorage.getItem('shortenedUrls')) || [];

            function removeExpiredURLs() {
                const now = new Date();
                const updatedSavedUrls = savedUrls.filter(data => {
                    const expirationTime = new Date(data.expired_at);
                    return expirationTime > now;
                });

                if (updatedSavedUrls.length < savedUrls.length) {
                    savedUrls = updatedSavedUrls;
                    localStorage.setItem('shortenedUrls', JSON.stringify(savedUrls));
                    location.reload();
                }
            }

            // Populate the table with saved URLs
            savedUrls.forEach(data => {
                displayShortURL(data);
            });

            // Check and remove expired URLs
            removeExpiredURLs();

            shortenButton.on('click', shortenURL);

            function shortenURL() {
                const url = urlInput.val();
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '/api/guest/create-short-url',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    data: JSON.stringify({
                        url
                    }),
                    contentType: 'application/json',
                    success: function(data) {
                        errorContainer.css('display', 'none');
                        displayShortURL(data);
                        savedUrls.push(data);
                        if (savedUrls.length > 3) {
                            savedUrls.shift();
                        }
                        localStorage.setItem('shortenedUrls', JSON.stringify(savedUrls));
                        removeExpiredURLs();
                    },
                    error: function(xhr) {
                        const errorMessages = xhr.responseJSON.errors;
                        let errorMessage = '';
                        for (let key in errorMessages) {
                            errorMessage += errorMessages[key][0] + ' ';
                        }
                        errorContainer.text(errorMessage);
                        errorContainer.css('display', 'block');
                    }
                });
            }

            function displayShortURL(data) {
                const displayedUrl = data.url.length > 30 ? data.url.substring(0, 30) + '...' : data.url;
                const newRow = createShortURLRow(data.short_url_link, displayedUrl, data.expired_at);
                shortUrlDisplay.append(newRow);

                const copyButton = newRow.find('.copyButton');
                copyButton.on('click', function() {
                    copyToClipboard(data.short_url_link);
                });
            }

            function createShortURLRow(shortURL, displayedURL, expiredAt) {
                const now = new Date();
                const expirationTime = new Date(expiredAt);
                const timeDifference = expirationTime - now;

                let timeRemaining = '';
                if (timeDifference > 0) {
                    const minutesRemaining = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
                    if (minutesRemaining > 0) {
                        timeRemaining = `${minutesRemaining} phút`;
                    }
                } else {
                    timeRemaining = 'Hết hạn';
                }

                const newRow = $('<tr>');
                newRow.html(`
                    <td title="${shortURL}">${displayedURL}</td>
                    <td>${shortURL}</td>
                    <td>${timeRemaining}</td>
                    <td><button class="copyButton">Copy</button></td>`);
                return newRow;
            }

            function copyToClipboard(text) {
                navigator.clipboard.writeText(text)
                    .then(() => {
                        alert('Link đã được sao chép: ' + text);
                    })
                    .catch(error => {
                        console.error('Sao chép thất bại: ' + error);
                    });
            }
        });
    </script>
@endsection
