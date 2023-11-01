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
        $(document).ready(function() {
            $('#login-form').submit(function(event) {
                event.preventDefault();

                const email = $('#email').val();
                const password = $('#password').val();
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                const requestData = {
                    email: email,
                    password: password
                };

                $.ajax({
                    url: '/api/login',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: JSON.stringify(requestData),
                    success: function(response) {
                        if (response.token) {
                            const expiryDate = new Date();
                            expiryDate.setDate(expiryDate.getDate() + 1);
                            document.cookie = "token=" + response.token + "; expires=" +
                                expiryDate.toUTCString() + "; path=/";
                            alert('Đăng nhập thành công');
                            window.location.href = '/';
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        $('#message').text('Sai tên đăng nhập hoặc mật khẩu');
                    }
                });
            });
        });
    </script>


@endsection
