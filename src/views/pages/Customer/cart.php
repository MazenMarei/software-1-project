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
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="row mb-3">
                                                <div class="col">
                                                    <label for="city" class="form-label">City</label>
                                                    <select
                                                        class="form-control"
                                                        id="city"
                                                        name="city"
                                                        required>
                                                        <option value="" disabled selected>Select your city</option>
                                                        <?php foreach ($governments as $government): ?>
                                                            <option value="<?php echo htmlspecialchars($government); ?>" <?php echo ($customer->getCity() == $government) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($government); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col">
                                                    <label for="postal_code">Postal Code</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        id="postal_code"
                                                        name="postal_code"
                                                        required
                                                        pattern="^\d{5}$"
                                                        value="<?php echo $customer->getPostalCode(); ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Address</label>
                                                <textarea
                                                    class="form-control"
                                                    id="address"
                                                    name="address"
                                                    rows="3"
                                                    required><?php echo $customer->getAddress(); ?></textarea>
                                                <div class="invalid-feedback">
                                                    Please enter your address
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row text-end text-black fs-5 fw-semibold heading-font">
                            <p>Total Price: $<span id="cartTotal"><?php echo number_format($cart->getTotalPrice(), 2); ?></span></p>
                            <input type="hidden" id="originalTotalValue" value="<?php echo $cart->getTotalPrice(); ?>">
                            <input type="hidden" name="appliedBalance" id="appliedBalance" value="0">
                        </div>
                        <div class="row justify-content-center">
                            <?php if (count($cartItems) > 0): ?>
                                <button
                                    class="btn col-5 col-md-2 bg-black fw-semibold text-white"
                                    type="submit"
                                    form="checkoutForm">
                                    Checkout
                                </button>
                            <?php else: ?>
                                <p class="text-danger">Your cart is empty. Please add items to your cart before checking out.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ---------------------------- error message ---------------------------- -->
            <div
                class="row fs-4 text-black fw-semibold justify-content-center"
                id="checkoutError"></div>
        </div>
    </main> <!-- footer -->
    <?php require_once VIEWS . 'components/customer_footer.php'; ?> <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../../../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../../../assets/js/popper.min.js"></script>
    <script src="../../../assets/js/bootstrap.min.js"></script>
    <!-- Custom Scripts -->
    <script src="../../../assets/js/customer-dropdown.js"></script>
    <script>
        // Handle the balance checkbox
        $(document).ready(function() {
            const useBalanceCheckbox = $('#useBalanceCheckbox');
            const customerBalance = parseFloat($('#customerBalanceValue').val()) || 0;
            const originalTotal = parseFloat($('#originalTotalValue').val()) || 0;
            const cartTotalElement = $('#cartTotal');
            const appliedBalanceInput = $('#appliedBalance');

            useBalanceCheckbox.on('change', function() {
                let newTotal = originalTotal;
                let appliedBalance = 0;

                if (this.checked) {
                    // If customer has sufficient balance to cover the entire amount
                    if (customerBalance >= originalTotal) {
                        appliedBalance = originalTotal;
                        newTotal = 0;
                    } else {
                        // If customer balance is less than the total, deduct what they have
                        appliedBalance = customerBalance;
                        newTotal = originalTotal - customerBalance;
                    }
                }

                // Update the displayed total and the hidden input
                cartTotalElement.text(newTotal.toFixed(2));
                appliedBalanceInput.val(appliedBalance);
            });
        });
    </script>
</body>

</html>