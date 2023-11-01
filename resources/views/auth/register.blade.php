@extends('auth.layouts.layouts')
@section('title','Đăng ký')
@section('content')
    <section class="vh-100 bg-image" style="background-image: url('{{ asset('img4.webp') }}');">
        <div class="mask d-flex align-items-center h-100 gradient-custom-3">
            <div class="container h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-9 col-lg-7 col-xl-6">
                        <div class="card" style="border-radius: 15px;">
                            <div class="card-body p-5">
                                <h2 class="text-uppercase text-center mb-5">Create an account</h2>
                                <form id="registerForm">
                                    @csrf
                                    <div class="form-outline mb-4">
                                        <label for="name">Tên</label>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                        <div id="name-error" class="text-danger"></div>
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" required>
                                        <div id="email-error" class="text-danger"></div>
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label for="password">Mật khẩu</label>
                                        <input type="password" name="password" id="password" class="form-control" required>
                                        <div id="password-error" class="text-danger"></div>
                                    </div>

                                    <div class="d-flex justify-content-center">
                                        <button type="submit"
                                            class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">Register</button>
                                    </div>
                                    <p class="text-center text-muted mt-5 mb-0">Have already an account? <a
                                            href="{{ route('login') }}" class="fw-bold text-body"><u>Login here</u></a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const registerForm = document.getElementById('registerForm');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const nameError = document.getElementById('name-error');
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');

        registerForm.addEventListener('submit', handleRegistration);

        function handleRegistration(event) {
            event.preventDefault();

            const name = nameInput.value;
            const email = emailInput.value;
            const password = passwordInput.value;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/api/register', {
                method: 'POST',
                body: JSON.stringify({ name, email, password }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                return response.json().then(data => {
                    handleErrors(data);
                });
            })
            .then(data => {
                handleSuccess(data);
            })
            .catch(error => {
                console.error(error);
            });
        }

        function handleErrors(data) {
            if (data.errors) {
                nameError.textContent = data.errors.name ? data.errors.name[0] : '';
                emailError.textContent = data.errors.email ? data.errors.email[0] : '';
                passwordError.textContent = data.errors.password ? data.errors.password[0] : '';
            } else {
            }
        }

        function handleSuccess(data) {
            alert(data.message);
            window.location.href = '/login';
        }
    </script>

@endsection
