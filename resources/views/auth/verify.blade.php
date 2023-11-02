@extends('auth.layouts.layouts')
@section('title', 'Xác minh tài khoản')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ __('Xác minh tài khoản') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <p>Nhập mã xác minh để hoàn tất quá trình xác minh tài khoản.</p>
                        <div class="form-group">
                            <label for="verification_code">Mã xác minh</label>
                            <input type="text" id="verification_code" class="form-control">
                        </div>
                        <div id="message" class="text-danger mt-3 mb-3"></div>
                        <button id="verifyButton" class="btn btn-primary">Xác minh</button>
                        <button id="resendVerificationButton" class="btn btn-secondary">Gửi lại mã xác minh</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            const verifyButton = document.getElementById('verifyButton');
            const resendVerificationButton = document.getElementById('resendVerificationButton');
            const verificationCodeInput = document.getElementById('verification_code');
            const messageDiv = document.getElementById('message');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            resendVerificationButton.addEventListener('click', function() {
                fetch('/api/resend-verification/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                    })
                    .then(response => response.json())
                    .then(data => handleResendVerificationResponse(data))
                    .catch(error => handleError(error));
            });

            verifyButton.addEventListener('click', function() {
                const verificationCode = verificationCodeInput.value;

                if (!verificationCode) {
                    messageDiv.textContent = 'Vui lòng nhập mã xác minh.';
                    return;
                }
                fetch('/api/verify/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            verification_code: verificationCode
                        })
                    })
                    .then(response => response.json())
                    .then(data => handleVerificationResponse(data))
                    .catch(error => handleError(error));
            });

            function handleResendVerificationResponse(data) {
                if (data.message === 'Đã gửi lại email xác minh với mã xác minh mới.') {
                    alert(data.message);
                } else {
                    messageDiv.textContent = 'Lỗi: ' + data.message;
                }
            }

            function handleVerificationResponse(data) {
                if (data.message === 'Xác minh thành công.') {
                    setCookie('token', data.token, 1);
                    alert(data.message);
                    window.location.href = '/';
                } else {
                    messageDiv.textContent = 'Xác minh không thành công: ' + data.message;
                }
            }

            function handleError(error) {
                messageDiv.textContent = 'Lỗi: ' + error;
            }
            // Hàm để lưu cookie
            function setCookie(name, value, days) {
                const expires = new Date();
                expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
                document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
            }
        });
    </script>
@endsection
