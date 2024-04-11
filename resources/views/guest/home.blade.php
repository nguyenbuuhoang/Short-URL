@extends('guest.layouts.app')
@section('title', 'Short Link')
@section('content')
    <div class="container-fluid">
        <div class="text-center">
            <h2 class="color-text">Shorten Your Long Link ^_^</h2>
            <br>
            <p class="color-text">Linkly is an efficient and easy-to-use URL shortener.</p>
        </div>
        <div class="link-shortener">
            <i class="fa-solid fa-link"
                style="font-size: 20px; color: #ffffff; padding: 10px; margin-left:20px; margin-right: -100px;"></i>
            <input type="text" placeholder="Enter the link here" id="urlInput">
            <button id="shortenButton"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
        <div id="errorContainer" class="alert alert-danger alert-dismissible fade show"></div>
        <br>
        <div class="text-center">
            <p class="color-text">You can create up to 5 links for free with no account, and they will expire after 30
                minutes.</p>
            <p class="color-text">Advanced features (QR code generation, filtering, sorting,...) will be activated upon
                account registration. <a href="{{ route('register') }}">Register now</a></p>
        </div>
        <table id="link-table">
            <thead>
                <tr>
                    <th>Short Link</th>
                    <th>Original Link</th>
                    <th>Remaining Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="shortUrl">
            </tbody>
        </table>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlInput = $('#urlInput');
            const shortenButton = $('#shortenButton');
            const shortUrlDisplay = $('#shortUrl');
            const errorContainer = $('#errorContainer');
            let savedUrls = JSON.parse(localStorage.getItem('shortenedUrls')) || [];

            function removeExpiredURLs() {
                const now = new Date();
                const storedUrls = JSON.parse(localStorage.getItem('shortenedUrls'));

                if (storedUrls && storedUrls.length > 0) {
                    savedUrls = savedUrls.filter(data => new Date(data.expired_at) > now);
                    if (savedUrls.length < storedUrls.length) {
                        localStorage.setItem('shortenedUrls', JSON.stringify(savedUrls));
                        location.reload();
                    }
                }
            }

            function displayShortURL(data) {
                const displayedUrl = data.url.length > 30 ? data.url.substring(0, 30) + '...' : data.url;
                const newRow = createShortURLRow(data.short_url_link, displayedUrl, data.expired_at);
                shortUrlDisplay.append(newRow);
                newRow.find('.copyButton').on('click', () => copyToClipboard(data.short_url_link));
            }

            function createShortURLRow(shortURL, displayedURL, expiredAt) {
                const now = new Date();
                const expirationTime = new Date(expiredAt);
                const timeDifference = expirationTime - now;
                const timeRemaining = timeDifference > 0 ? Math.floor(timeDifference / (1000 * 60)) + ' minutes' :
                    'Expired';

                const newRow = $('<tr>').html(`
            <td title="${shortURL}">${shortURL}</td>
            <td>${displayedURL}</td>
            <td>${timeRemaining}</td>
            <td><button class="copyButton">Copy</button></td>`);
                return newRow;
            }

            async function shortenURL() {
                const url = urlInput.val();
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                if (savedUrls.length >= 5) {
                    alert('You have reached the maximum limit of saved URLs.');
                    return;
                }

                try {
                    const response = await $.ajax({
                        url: '/api/guest/create-short-url',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: JSON.stringify({
                            url
                        }),
                        contentType: 'application/json'
                    });
                    errorContainer.css('display', 'none');
                    displayShortURL(response);
                    savedUrls.push(response);
                    localStorage.setItem('shortenedUrls', JSON.stringify(savedUrls));
                    removeExpiredURLs();
                } catch (error) {
                    const errorMessage = error.responseJSON ? Object.values(error.responseJSON.errors).flat()
                        .join(' ') : 'An error occurred.';
                    errorContainer.text(errorMessage);
                    errorContainer.css('display', 'block');
                    setTimeout(() => errorContainer.css('display', 'none'), 2000);
                }
            }

            function copyToClipboard(text) {
                navigator.clipboard.writeText(text)
                    .then(() => alert('Link copied: ' + text))
                    .catch(error => console.error('Copy failed: ' + error));
            }
            savedUrls.forEach(data => displayShortURL(data));
            removeExpiredURLs();
            shortenButton.on('click', shortenURL);
        });
    </script>
@endsection
