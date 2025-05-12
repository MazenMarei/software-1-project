<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Cart | ArtShelf</title>
    <!-- Bootstrap CSS -->
    <link href="../../../assets/css/main.css" rel="stylesheet">
    <link href="../../../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../../assets/css/main.css" rel="stylesheet">
    <link href="../../../assets/css/customer.dashboard.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../../assets/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <?php require_once VIEWS . 'components/customer_navbard.php'; ?>

    <main id="chartSection" class="my-5">

        <div class="container rounded-1 ">
            <div class="advisor-header">
                <h1 class="page-title">Shopping Cart</h1>
                <p class="page-description">
                    Review and checkout your selected artworks.
                </p>
            </div>
            <!-- ------------------------ heading and title row ------------------------ -->
            <div class="row">
                <div class="col">
                    <h3 class="text-black heading-font fw-semibold">Checkout</h3>
                </div>
            </div>

            <!-- ------------------------ chart items row ------------------------ -->
            <div class="row mt-4 light-dark-bg rounded-3 p-4">
                <form
                    action="/customer/checkout"
                    method="post"
                    id="checkoutForm">

                    <div class="accordion" id="accordionChart">
                        <!-- ------------------------ chart accordion-item ------------------------ -->
                        <div class="accordion-item">
                            <!-- ------------------------ accordion-item header ------------------------ -->
                            <div class="accordion-header">
                                <button
                                    class="accordion-button custom-accordion-button fw-semibold"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#chartItem"
                                    aria-expanded="true"
                                    aria-controls="collapseOne">
                                    Chart Items
                                </button>
                            </div>
                            <!-- ------------------------ accordion-item body ------------------------ -->
                            <div
                                id="chartItem"
                                class="accordion-collapse collapse show"
                                data-bs-parent="#accordionChart">
                                <div class="accordion-body">
                                    <!-- -------------------------------- table -------------------------------- -->
                                    <div class="container">
                                        <!-- ------------------------------ table row ------------------------------ -->
                                        <div class="row justify-content-center">
                                            <!-- ---------------------------- table header ----------------------------- -->
                                            <div
                                                class="row border border-1 rounded-3 p-3 accent-bg text-white fw-semibold fs-5">
                                                <div class="col-5 px-md-auto px-0">
                                                    <span class="d-none d-md-inline">Item</span> Name
                                                </div>
                                                <div class="col-4">
                                                    <span class="d-none d-md-inline">Item</span> Price
                                                </div>
                                                <div class="col-3 text-end px-md-3 px-0">Actions</div>
                                            </div>
                                            <!-- ---------------------------- table body ----------------------------- -->
                                            <div class="row p-0 mx-0 my-5" id="cartTableBody">
                                                <!-- Cart items will be dynamically inserted here -->
                                                <?php if (isset($cartItems) && count($cartItems) > 0): ?>
                                                    <?php foreach ($cartItems as $item): ?>
                                                        <div class="row border border-1 rounded-3 p-3 mb-3 align-items-center">
                                                            <div class="col-5 px-md-auto px-0">
                                                                <div class="d-flex align-items-center">
                                                                    <img
                                                                        src="../uploads/artworks/<?php echo htmlspecialchars($item->getImages()); ?>"
                                                                        alt="Artwork Image"
                                                                        class="img-fluid rounded-3 me-3"
                                                                        style="width: 100px; height: 100px;" />
                                                                    <span class="fw-semibold">
                                                                        <?php echo htmlspecialchars($item->getTitle()); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-4">
                                                                <span class="fw-semibold">
                                                                    $<?php echo number_format($item->getPrice(), 2); ?>
                                                                </span>
                                                            </div>
                                                            <div class="col-3 text-end px-md-3 px-0">
                                                                <form action="/customer/remove-from-cart/<?php echo $item->getArtworkId(); ?>" method="post">
                                                                    <button
                                                                        class="btn btn-danger btn-sm"
                                                                        type="submit">
                                                                        Remove
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ------------------------ shipping accordion-item ------------------------ -->
                        <div class="accordion-item">
                            <!-- ------------------------ accordion-item header ------------------------ -->
                            <div class="accordion-header">
                                <button
                                    class="accordion-button custom-accordion-button fw-semibold collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#shippingDetails"
                                    aria-expanded="false"
                                    aria-controls="collapseOne"
                                    id="shippingDetailsButton">
                                    Shipping Details
                                </button>
                            </div>
                            <!-- ------------------------ accordion-item body ------------------------ -->
                            <div
                                id="shippingDetails"
                                class="accordion-collapse collapse"
                                data-bs-parent="#accordionChart">
                                <div class="row p-4">
                                    <!-- ------------------------ shipping form ------------------------ -->

                                    <!-- ------------------------ name field ------------------------ -->
                                    <div class="mb-3">
                                        <label for="shippingName" class="form-label">Name</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="shippingName"
                                            placeholder="Enter your name"
                                            minlength="5"
                                            required />
                                        <div class="invalid-feedback">Please Type your name.</div>
                                    </div>
                                    <!-- --------------------------- address 1 field --------------------------- -->
                                    <div class="mb-3">
                                        <label for="shippingAddress1" class="form-label">Address 1</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="shippingAddress1"
                                            placeholder="Enter your address"
                                            minlength="20"
                                            required />
                                        <div class="invalid-feedback">
                                            Please Type your address.
                                        </div>
                                    </div>
                                    <!-- --------------------------- address 2 field --------------------------- -->
                                    <div class="mb-3">
                                        <label for="shippingAddress2" class="form-label">Address 2
                                            <span class="light-text-color">(optional)</span></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="shippingAddress2"
                                            placeholder="Enter your address" />
                                        <div class="invalid-feedback">
                                            Please Type your address.
                                        </div>
                                    </div>
                                    <!-- --------------------------- city field --------------------------- -->
                                    <div class="mb-3">
                                        <label for="shippingCity" class="form-label">City</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="shippingCity"
                                            placeholder="Enter your city"
                                            minlength="4"
                                            required />
                                        <div class="invalid-feedback">Please Type your city.</div>
                                    </div>
                                    <!-- --------------------------- country field --------------------------- -->

                                    <div class="mb-3">
                                        <label for="shippingCountry" class="form-label">Country</label>
                                        <select class="form-select" id="shippingCountry" required>
                                            <option value="" selected disabled>
                                                Select your country
                                            </option>
                                            <option value="egypt">Egypt</option>
                                            <option value="usa">USA</option>
                                            <option value="uk">UK</option>
                                            <option value="germany">Germany</option>
                                            <option value="france">France</option>
                                            <option value="italy">Italy</option>
                                            <option value="spain">Spain</option>
                                            <option value="japan">Japan</option>
                                            <option value="china">China</option>
                                            <option value="russia">Russia</option>
                                            <option value="brazil">Brazil</option>
                                            <option value="australia">Australia</option>
                                            <option value="canada">Canada</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please Type your country.
                                        </div>
                                    </div>

                                    <!-- --------------------------- postal code field --------------------------- -->
                                    <div class="mb-3">
                                        <label for="shippingPostalCode" class="form-label">Postal Code</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="shippingPostalCode"
                                            placeholder="Enter your postal code"
                                            minlength="5"
                                            required />
                                        <div class="invalid-feedback">
                                            Please Type your postal code.
                                        </div>
                                    </div>
                                    <!-- --------------------------- phone field --------------------------- -->
                                    <div class="mb-3">
                                        <label for="shippingPhone" class="form-label">Phone</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="shippingPhone"
                                            placeholder="Enter your phone"
                                            minlength="11"
                                            required />
                                        <div class="invalid-feedback">
                                            Please Type your phone.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ------------------------ payment accordion-item ------------------------ -->
                        <div class="accordion-item">
                            <!-- ------------------------ accordion-item header ------------------------ -->
                            <div class="accordion-header">
                                <button
                                    class="accordion-button custom-accordion-button fw-semibold collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#paymentMethod"
                                    aria-expanded="false"
                                    aria-controls="collapseOne">
                                    Payment Method
                                </button>
                            </div>
                            <!-- ------------------------ accordion-item body ------------------------ -->
                            <div
                                id="paymentMethod"
                                class="accordion-collapse collapse"
                                data-bs-parent="#accordionChart">
                                <div class="row p-4">
                                    <!-- ------------------------ payment form ------------------------ -->
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="flexRadioDefault"
                                            id="flexRadioDefault1"
                                            checked />
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            Cash on delivery
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="flexRadioDefault"
                                            id="flexRadioDefault2" />
                                        <label class="form-check-label" for="flexRadioDefault2">
                                            Paypal
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="flexRadioDefault"
                                            id="flexRadioDefault3" />
                                        <label class="form-check-label" for="flexRadioDefault3">
                                            Credit Card
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ------------------------ checkout and total row ------------------------ -->
                    <div class="row mt-5">
                        <div class="row text-end text-black fs-5 fw-semibold heading-font">
                            <p>Total Price: <span id="cartTotal"><?php echo number_format($cart->getTotalPrice(), 2); ?></span></p>
                        </div>
                        <div class="row justify-content-center">
                            <button
                                class="btn col-5 col-md-2 bg-black fw-semibold text-white"
                                type="submit"
                                form="checkoutForm">
                                Checkout
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ---------------------------- error message ---------------------------- -->
            <div
                class="row fs-4 text-black fw-semibold justify-content-center"
                id="checkoutError"></div>
        </div>
    </main>
    <!-- footer -->
    <?php require_once VIEWS . 'components/customer_footer.php'; ?> <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../../assets/js/popper.min.js"></script>
    <script src="../../../assets/js/bootstrap.min.js"></script>
    <!-- Custom Scripts -->
    <script src="../../../assets/js/customer-dropdown.js"></script>
</body>

</html>