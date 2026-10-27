// ==============================
// MENÚ DE 3 RAYAS
// ==============================

document.addEventListener("DOMContentLoaded", function () {

    const menuBtn = document.getElementById("menuBtn");
    const mobileMenu = document.getElementById("mobileMenu");
    const closeMenu = document.getElementById("closeMenu");


    // ==============================
    // ABRIR MENÚ
    // ==============================

    if (menuBtn && mobileMenu) {

        menuBtn.addEventListener("click", function () {

            mobileMenu.classList.add("active");

        });

    }


    // ==============================
    // CERRAR MENÚ CON X
    // ==============================

    if (closeMenu && mobileMenu) {

        closeMenu.addEventListener("click", function () {

            mobileMenu.classList.remove("active");

        });

    }


    // ==============================
    // CERRAR AL ELEGIR UNA OPCIÓN
    // ==============================

    const menuLinks =
        document.querySelectorAll("#mobileMenu a");

    menuLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            mobileMenu.classList.remove("active");

        });

    });


    // ==============================
    // CARRITO
    // ==============================

    const cartBtn =
        document.getElementById("cartBtn");

    if (cartBtn) {

        cartBtn.addEventListener("click", function () {

            window.location.href = "carrito.php";

        });

    }

});


