<header class="topbar-nav">
    <nav class="navbar navbar-expand fixed-top">
        <ul class="navbar-nav mr-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link toggle-menu" href="javascript:void();">
                    <i class="icon-menu menu-icon"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <div id="user-info" style="display: none;">
            </div>
        </ul>
    </nav>
</header>
<script src="{{ asset('template/assets/js/cookie.js') }}"></script>
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
                        Chào ${username}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" id="logout">Đăng xuất</a>
                    </div>
                </li>
            `;
                $("#user-info").html(dropdownMenuHtml);
                $("#user-info").show();
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
    }
</script>
