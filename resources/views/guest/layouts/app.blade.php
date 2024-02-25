<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/reponsive.css') }}">
</head>

<body style="background-image: url('{{ asset('backgroud.jpg') }}');">
    <header>
        <a class="logo" href="#">
            <div class="logo">SHORT-URL</div>
        </a>
        <i class="icon fas fa-bars" id="icon"></i>
        <div class="navbar">
            <button class="close-btn" id="close-btn"><i class="fa-solid fa-xmark"></i></button>
            <ul>
                <li>
                    <a href="{{route('home')}}" class="color-text">Short Url</i></a>
                    {{-- <a href="#" class="color-text">Web Tool &nbsp;<i class="fa-solid fa-chevron-down"></i></a>
                    <ul class="sub-menu">
                        <li><a href="#" class="color-text">Count Character</a></li>
                        <li><a href="#" class="color-text">Short Url</a></li>
                        <li><a href="#" class="color-text">Json Beauty</a></li>
                        <li><a href="#" class="color-text">Resize Img</a></li>
                    </ul> --}}
                </li>
                <li><a href="{{ route('login') }}" class="color-text" id="login-link">Login</a></li>
                <li><a href="{{ route('register') }}" class="color-text" id="register-link">Register</a></li>
                <div id="user-info"></div>
            </ul>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('template/assets/js/cookie.js') }}"></script>
    <!-- Script -->
    <script src="{{ asset('template/assets/js/script.js') }}"></script>
    <script>
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        if (token) {
            $.ajax({
                type: "GET",
                url: "/api/user",
                headers: {
                    "Authorization": "Bearer " + token,
                    "X-CSRF-TOKEN": csrfToken
                },
                success: function(data) {
                    const username = data.name;
                    const dropdownMenuHtml = `
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Hi ${username}
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" id="logout">Logout</a>
                            </div>
                        </li>
                    `;
                    $("#user-info").html(dropdownMenuHtml);
                    $("#user-info").show();
                    $("#login-link").hide();
                    $("#register-link").hide();
                }
            });

            $("#user-info").on("click", "#logout", function() {
                $.ajax({
                    type: "POST",
                    url: "/api/logout",
                    headers: {
                        "Authorization": "Bearer " + token,
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function() {
                        deleteCookie('token');
                        location.reload();
                    }
                });
            });
        } else {
            $("#login-link").show();
            $("#register-link").show();
        }
    </script>
</body>

</html>
