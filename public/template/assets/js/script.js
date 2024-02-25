document.addEventListener("DOMContentLoaded", function () {
    // Lấy các phần tử DOM cần sử dụng
    const icon = document.getElementById("icon");
    const closeBtn = document.getElementById("close-btn");
    const menuUl = document.querySelector(".navbar");

    // Kiểm tra kích thước màn hình và ẩn/hiện các phần tử tương ứng
    function checkScreenSize() {
        if (window.innerWidth <= 768) {
            icon.style.display = "block";
            if (!menuUl.classList.contains("show")) {
                closeBtn.style.display = "none";
            }
        } else {
            icon.style.display = "none";
            closeBtn.style.display = "none";
            menuUl.classList.remove("show");
        }
    }

    // Kiểm tra trạng thái hiển thị của menu và cập nhật biểu tượng tương ứng
    function checkMenuVisibility() {
        if (window.innerWidth <= 768 && menuUl.classList.contains("show")) {
            icon.style.display = "none";
            closeBtn.style.display = "block";
        }
    }

    // Đóng menu nếu click bên ngoài vùng navbar
    function closeMenuIfClickedOutside(event) {
        if (window.innerWidth <= 768 && !menuUl.contains(event.target) && event.target !== icon && event.target !== closeBtn) {
            menuUl.classList.remove("show");
            closeBtn.style.display = "none";
            icon.style.display = "block";
        }
    }

    // Kiểm tra kích thước màn hình khi trang được tải
    checkScreenSize();

    // Sự kiện click để mở/đóng menu
    icon.addEventListener("click", function () {
        menuUl.classList.toggle("show");
        icon.style.display = "none";
        closeBtn.style.display = "block";
    });

    // Sự kiện click để đóng menu
    closeBtn.addEventListener("click", function () {
        menuUl.classList.remove("show");
        closeBtn.style.display = "none";
        icon.style.display = "block";
    });

    // Sự kiện resize để xử lý thay đổi kích thước màn hình
    window.addEventListener("resize", function () {
        console.clear();
        checkScreenSize();
        checkMenuVisibility();
    });

    // Sự kiện scroll để xử lý cuộn trang
    window.addEventListener("scroll", function () {
        checkMenuVisibility();
    });

    // Thêm sự kiện click cho document để đóng menu khi click ngoài vùng navbar
    document.addEventListener("click", closeMenuIfClickedOutside);
});
