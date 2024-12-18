document.addEventListener("DOMContentLoaded", function () {
    const mobileMenuToggle = document.getElementById("mobile-menu-toggle");
    const mobileMenu = document.getElementById("mobile-menu");
    const mobileMenuClose = document.getElementById("menu-toggle-close");
    const mainContent = document.getElementById("main-content");
    const desktopMenuToggle = document.getElementById("desktop-menu-toggle");

    mobileMenuToggle.addEventListener("click", function () {
        mobileMenu.classList.toggle("hidden");
        mainContent.classList.toggle("opacity-25");
    });
    mobileMenuClose.addEventListener("click", function () {
        mobileMenu.classList.toggle("hidden");
        mainContent.classList.toggle("opacity-25")
    });

    desktopMenuToggle.addEventListener("click", function () {
        mobileMenu.classList.toggle("md:block");
    });

    document.addEventListener("click", function (event) {
        if (
            !mobileMenu.contains(event.target) &&
            !mobileMenuToggle.contains(event.target)
        ) {
            mobileMenu.classList.add("hidden");
            mainContent.classList.remove("opacity-25")
        }
    });
});


