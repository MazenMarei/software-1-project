    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/customer/dashboard">
                <img src="../../assets/images/artshelf-logo.png" alt="ArtShelf Logo" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/customer/dashboard">Discover</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/artists">Artists</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/collections">Collections</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/customer/fairs">Art Fairs</a>
                    </li>
                </ul>
                <div class="navbar-right d-flex align-items-center">
                    <div class="dropdown me-3">
                        <button class="btn btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-shopping-cart"></i>
                            <?php echo isset($cartItems) && count($cartItems) > 0 ? '<span class="badge bg-accent">' . count($cartItems) . '</span>' : ''; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header">Shopping Cart</h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-center" href="/customer/cart">View Cart</a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-icon profile-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/uploads/profiles/<?php echo isset($customer) ? $customer->getProfilePic() : 'default.jpg'; ?>" alt="Profile Picture" class="profile-picture">
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <h6 class="dropdown-header"><?php echo isset($customer) ? $customer->getUserName() : 'User'; ?></h6>
                            <a class="dropdown-item" href="/customer/profile">
                                <i class="fa fa-user me-2"></i> My Profile
                            </a>
                            <a class="dropdown-item" href="/customer/wallet">
                                <i class="fa-solid fa-wallet"></i> My Wallet
                            </a>
                            <a class="dropdown-item" href="/customer/orders">
                                <i class="fa fa-shopping-bag me-2"></i> My Orders
                            </a>
                            <a class="dropdown-item" href="/customer/following">
                                <i class="fa fa-users me-2"></i> Following
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/logout">
                                <i class="fa fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>