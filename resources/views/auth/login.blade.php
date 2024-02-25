@extends('auth.layouts.layouts')
@section('title', 'Đăng nhập')
@section('content')
    <section class="vh-100 bg-image" style="background-image: url('{{ asset('img4.webp') }}');">
        <div class="mask d-flex align-items-center h-100 gradient-custom-3">
            <div class="container h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                        <div class="card rounded-3">
                            <div class="card-body p-5">
                                <h2 class="text-uppercase text-center mb-5">Login</h2>
                                <form id="login-form">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" id="email" name="email" class="form-control" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label">Password:</label>
                                        <input type="password" id="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit"
                                            class="btn btn-primary btn-block btn-lg gradient-custom-4 text-body">Login</button>
                                    </div>
                                    <div id="message" class="text-danger text-center mt-3"></div>
                                </form>
                                <p class="text-center text-muted mt-5 mb-0">Don't have an account? <a
                                        href="{{ route('register') }}" class="fw-bold text-body"><u>Register here</u></a>
                                </p>
                                <div class="text-center mt-4">
                                    <a href="{{ route('home') }}" class="btn btn-link">Back to Home</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const loginForm = document.getElementById('login-form');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const messageElement = document.getElementById('message');

        loginForm.addEventListener('submit', handleLogin);

        function handleLogin(event) {
            event.preventDefault();

            const email = emailInput.value;
            const password = passwordInput.value;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        email,
                        password
                    }),
                })
                .then(response => response.json())
                .then(handleResponse)
                .catch(handleError);
        }

        function handleResponse(data) {
            if (data.success) {
                setCookie('token', data.token, 1);
                alert(data.success);
                if (data.role.includes('admin') || data.role.includes('editor')) {
                    window.location.href = '/admin/index';
                } else {
                    window.location.href = '/';
                }
            }
            if (data.message === 'Account has not been verified, please go to Email to verify') {
                const confirmation = confirm(data.message + ' Click OK to verify the account.');

                if (confirmation) {
                    window.location.href = '/verify?id=' + data.id;
                }
            } else {
                showMessage(data.message);
            }
        }


        function handleError(error) {
            console.error('Error:', error);
        }

        function showMessage(message) {
            messageElement.textContent = message;
        }
        // Hàm để lưu cookie
        function setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
        }
    </script>

@endsection
