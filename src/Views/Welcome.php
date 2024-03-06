<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{link}">Product Inventory Application</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>

<div class="container mb-3">
    <!-- Button trigger modal -->
    <button type="button" class="btn border text-end mt-3" data-bs-toggle="modal" data-bs-target="#exampleModal" id="create-product">
        Create Product
    </button>
    <!-- Modal Create -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Product Information</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/products/store" id="product-form" class="needs-validation" novalidate>
                        <div class="mb-3 has-validation">
                            <label for="recipient-name" class="col-form-label">Product Name:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="product-name" name="name" required>
                            <div class="invalid-feedback">
                                Please provide a product name.
                            </div>
                        </div>
                        <div class="mb-3 has-validation">
                            <label for="message-text" class="col-form-label">Unit:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="product-unit" name="unit" required>
                            <div class="invalid-feedback">
                                Please provide a product unit.
                            </div>
                        </div>
                        <div class="mb-3 has-validation">
                            <label for="message-text" class="col-form-label">Price:<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="product-price" name="price" step="0.1" required>
                            <div class="invalid-feedback">
                                Please provide a product price.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Date of Expiry:</label>
                            <input type="date" class="form-control" id="product-date-expiry" name="date_exp">
                        </div>
                        <div class="mb-3 has-validation">
                            <label for="message-text" class="col-form-label">Quantity:<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="product-quantity" name="quantity" min=0 required>
                            <div class="invalid-feedback">
                                Please provide a product quantity.
                            </div>
                        </div>
                        <div class="mb-3 has-validation">
                            <label for="message-text" class="col-form-label">Product Image:</label>
                            <input type="file" class="form-control" id="product-image" name="product-image">
                            <div class="invalid-feedback">
                                Please provide a product quantity.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="product-form">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update -->
    <div class="modal fade" id="modalUpdate" tabindex="-1" aria-labelledby="modalUpdate" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalUpdate">Update Product Information</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="product-form-update" method="POST" action="/products/store" class="needs-validation" novalidate>
                        <input type="hidden" class="form-control" id="product-id" name="product">
                        <div class="mb-3 has-validation">
                            <label for="recipient-name" class="col-form-label">Product Name:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="product-name" name="name" required>
                            <div class="invalid-feedback">
                                Please provide a product name.
                            </div>
                        </div>
                        <div class="mb-3 has-validation">
                            <label for="message-text" class="col-form-label">Unit:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="product-unit" name="unit" required>
                            <div class="invalid-feedback">
                                Please provide a product unit.
                            </div>
                        </div>
                        <div class="mb-3 has-validation">
                            <label for="message-text" class="col-form-label">Price:<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="product-price" name="price" step=".01" required>
                            <div class="invalid-feedback">
                                Please provide a product price.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Date of Expiry:</label>
                            <input type="date" class="form-control" id="product-date-expiry" name="date_exp">
                        </div>
                        <div class="mb-3 has-validation">
                            <label for="message-text" class="col-form-label">Quantity:<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="product-quantity" name="quantity" min=0 required>
                            <div class="invalid-feedback">
                                Please provide a product quantity.
                            </div>
                        </div>
                        <div class="mb-3 has-validation">
                            <label for="message-text" class="col-form-label">Product Image:</label>
                            <input type="file" class="form-control" id="product-image" name="product-image">
                            <div class="invalid-feedback">
                                Please provide a product quantity.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="product-form-update">Save changes</button>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirm Product Deletion</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="product-form-delete" method="POST" action="/products/delete" class="needs-validation" novalidate>
                    <input type='hidden' name='_method' value='DELETE' />
                    <input type="hidden" class="form-control" id="product-id" name="product">
                </form>
                Do you really want to delete this product?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger" form="product-form-delete" id="product-delete-confirm">Delete Now</button>
            </div>
        </div>
    </div>
</div>

<div class="container mb-3">
    <div id="product-container"></div>
</div>

<script src="/public/js/products.js" crossorigin="anonymous" type="text/javascript"></script>