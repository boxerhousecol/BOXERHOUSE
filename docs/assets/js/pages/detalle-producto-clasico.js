document.addEventListener(
    "DOMContentLoaded",
    function () {


        /* =================================================
           GALERÍA
        ================================================= */

        const productImage =
            document.getElementById(
                "productImage"
            );


        const thumbnails =
            document.querySelectorAll(
                ".product-thumbnails .thumbnail"
            );


        thumbnails.forEach(
            function (
                thumbnail
            ) {


                thumbnail.addEventListener(
                    "click",
                    function () {


                        const nuevaImagen =
                            this.dataset.image;


                        if (
                            productImage
                        ) {

                            productImage.src =
                                nuevaImagen;

                        }


                        thumbnails.forEach(
                            function (
                                item
                            ) {

                                item.classList.remove(
                                    "active"
                                );

                            }
                        );


                        this.classList.add(
                            "active"
                        );

                    }
                );

            }
        );


        /* =================================================
           TALLAS
        ================================================= */

        const sizeButtons =
            document.querySelectorAll(
                ".size-option"
            );


        const selectedProductId =
            document.getElementById(
                "selectedProductId"
            );


        const selectedSize =
            document.getElementById(
                "selectedSize"
            );


        const stockStatus =
            document.getElementById(
                "stockStatus"
            );


        const stockText =
            document.getElementById(
                "stockText"
            );


        const addCartBtn =
            document.getElementById(
                "addCartBtn"
            );


        /* =================================================
           CAMBIAR TALLA
        ================================================= */

        sizeButtons.forEach(
            function (
                button
            ) {


                button.addEventListener(
                    "click",
                    function (event) {


                        event.preventDefault();


                        if (
                            this.disabled
                        ) {

                            return;

                        }


                        /* ---------------------------------
                           QUITAR SELECCIÓN
                        --------------------------------- */

                        sizeButtons.forEach(
                            function (
                                item
                            ) {

                                item.classList.remove(
                                    "active"
                                );

                            }
                        );


                        /* ---------------------------------
                           SELECCIONAR
                        --------------------------------- */

                        this.classList.add(
                            "active"
                        );


                        /* ---------------------------------
                           DATOS DE LA TALLA
                        --------------------------------- */

                        const productId =
                            this.dataset.productId;


                        const talla =
                            this.dataset.size;


                        const stock =
                            parseInt(
                                this.dataset.stock,
                                10
                            );


                        /* ---------------------------------
                           ACTUALIZAR ID
                        --------------------------------- */

                        selectedProductId.value =
                            productId;


                        /* ---------------------------------
                           ACTUALIZAR TALLA
                        --------------------------------- */

                        selectedSize.value =
                            talla;


                        /* ---------------------------------
                           STOCK
                        --------------------------------- */

                        if (
                            stock > 0
                        ) {

                            stockStatus.textContent =
                                "✓ Disponible";

                            stockStatus.className =
                                "";

                            stockText.textContent =
                                "20 unidades disponibles";

                            addCartBtn.disabled =
                                false;

                            addCartBtn.classList.remove(
                                "disabled"
                            );

                            addCartBtn.innerHTML =
                                "AGREGAR AL CARRITO <span>+</span>";


                        } else {

                            stockStatus.textContent =
                                "✕ No disponible";

                            stockStatus.className =
                                "out-stock";

                            stockText.textContent =
                                "Producto agotado";

                            addCartBtn.disabled =
                                true;

                            addCartBtn.classList.add(
                                "disabled"
                            );

                            addCartBtn.textContent =
                                "PRODUCTO AGOTADO";

                        }

                    }
                );

            }
        );


        /* =================================================
           VALIDAR CARRITO
        ================================================= */

        const cartForm =
            document.getElementById(
                "cartForm"
            );


        cartForm.addEventListener(
            "submit",
            function (event) {


                if (
                    !selectedProductId.value
                    ||
                    !selectedSize.value
                ) {

                    event.preventDefault();

                    alert(
                        "Por favor selecciona una talla."
                    );

                }

            }
        );


    }
);
