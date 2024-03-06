const apiDomain = "http://product-inventory.infinityfreeapp.com";

const productContainer = document.querySelector("#product-container");

async function fetchProducts(domain) {
  try {
    const response = await fetch(`${domain}/products/show`);

    console.log(`${domain}/products/show`);
    const products = await response.json();

    return products;
  } catch (error) {
    console.error(error);
    throw error;
  }
}

async function renderProductCards(products) {
  if (products.length > 0) {
    products.forEach((product) => {
      renderProductCard(product);
    });
  } else {
    productContainer.innerHTML = `
        <div class="d-flex justify-content-center">
            <div class="pt-5 pb-2 px-5 rounded d-flex flex-column w-50" style="border: 1px solid gray;border-style: dashed;">
                <img src="/public/illustrations/undraw_empty_cart_co35.svg" class="mb-2"  />
                <h5 class="mb-5">There are no products available..</h5>
            </div>
        </div>
        `;
  }
}

function renderProductCard(product) {
  const cardMarkup = `
        <div class="card p-0">
            <div>
                <img src="${apiDomain}/public/images/${
    product.image
  }" class="card-img-top object-fit-cover" alt="${
    product.image ? product.name : "No uploaded image"
  }" height="260" style="object-position: top;">
            </div>
            <div class="card-body">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex flex-wrap gap-1 align-items-center"  style="flex-basis: 100%;">
                            <h5 class="card-title mb-0">
                                <span class="fw-bold">${product.name}</span>
                                <span class="mb-0 text-secondary font-weight-bold h6">(${
                                  product.unit
                                })</span>
                            </h5>
                        </div>
                        <h6 class="font-bold mb-0 text-end" style="flex-basis: 100%;">
                            <span>Price:</span>
                            <span>&#8369;${product.price}</span>
                        </h6>
                    </div>

                    <div>
                        <h6 class="font-bold mb-1">Date of Expiry: ${
                          product.date_expiry ? product.date_expiry : "N/A"
                        }</h6>
                        <h6 class="font-bold mb-1">Available: ${
                          product.quantity
                        }</h6>
                        <h6 class="font-bold mb-1">Total Cost: &#8369;${
                          product.cost
                        }</h6>
                    </div>
                </div>

            
            </div>
            <div class="card-footer mb-0 pt-3 pb-3">
                <div class="d-flex gap-2 align-items-center">
                    <!-- Button trigger modal -->
                    <form id="product-form-edit-modal">
                        <input type="hidden" name="product" value="${
                          product.product_id
                        }">
                        <button type="submit" class="btn btn-success border edit-product" data-bs-toggle="modal" data-bs-target="#modalUpdate">
                            <div class="d-flex gap-1 align-items-center">
                                <i class="bi bi-pencil-square"></i>
                                <span>Edit</span>
                            </div>
                        </button>
                    </form>

                    <form id="product-form-delete-modal">
                        <input type="hidden" name="product" value="${
                          product.product_id
                        }" id="product-delete">
                        <button type="submit" class="btn btn-danger border edit-product" data-bs-toggle="modal" data-bs-target="#modalDelete">
                            <div class="d-flex gap-1 align-items-center">
                                <i class="bi bi-trash"></i>
                                <span>Delete</span>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    `;
  productContainer.innerHTML += cardMarkup;
}

// Edit modal and Delete modal
async function attachFormEventListeners() {
  const productFormsEditModal = document.querySelectorAll(
    "#product-form-edit-modal"
  );
  productFormsEditModal.forEach((form) => {
    form.addEventListener("submit", (event) =>
      handleFormSubmit(event, "edit-modal")
    );
  });

  const productFormsDeleteModal = document.querySelectorAll(
    "#product-form-delete-modal"
  );
  productFormsDeleteModal.forEach((form) => {
    form.addEventListener("submit", (event) =>
      handleFormSubmit(event, "delete-modal")
    );
  });
}

async function handleFormSubmit(event, action = "") {
  event.preventDefault();
  let productID = "";
  switch (action) {
    case "edit-modal":
      productID = new FormData(event.target).get("product");
      await displayFormData(productID, "product-form-update");
      break;
    case "delete-modal":
      productID = new FormData(event.target).get("product");
      await displayFormData(productID, "product-form-delete");
      break;
  }
}

async function displayFormData(productID, form) {
  const productForm = document.querySelector(`#${form}`);
  try {
    const request = await fetch(`${apiDomain}/products/show/${productID}`);
    const response = await request.json();

    // console.log(response);

    updateFormFields(productForm, response);
  } catch (error) {
    console.log(error);
  }
}

// Delete
async function handleDeleteProductEvent() {
  const productForm = document.querySelector("#product-form-delete");
  productForm.addEventListener("submit", async (event) => {
    event.preventDefault()
    try {
      const options = {
        method: "POST",
        body: new FormData(productForm)
      };
      const request = await fetch(
        `${apiDomain}/products/delete`,
        options
      );
      const response = await request.json();

      if (response.message) {
        setTimeout(() => {
          location.reload();
        }, 1000);
      }
    } catch (error) {
      console.log(error);
    }
  });
}

function updateFormFields(form, product) {
  if (form.querySelector("#product-id")) {
    form.querySelector("#product-id").value = product.product_id;
  }

  if (form.querySelector("#product-name")) {
    form.querySelector("#product-name").value = product.name;
  }

  if (form.querySelector("#product-unit")) {
    form.querySelector("#product-unit").value = product.unit;
  }

  if (form.querySelector("#product-price")) {
    form.querySelector("#product-price").value = product.price;
  }

  if (form.querySelector("#product-date-expiry")) {
    form.querySelector("#product-date-expiry").value = product.date_expiry;
  }

  if (form.querySelector("#product-quantity")) {
    form.querySelector("#product-quantity").value = product.quantity;
  }
}

// Update
async function handleUpdateProductEvent() {
  const productForm = document.querySelector("#product-form-update");

  productForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const validate = await formValidation(productForm, event);

    if (!validate) {
      const productID = new FormData(productForm).get("product");
      try {
        const options = {
          method: "POST",
          body: new FormData(productForm),
        };
        const request = await fetch(
          `${apiDomain}/products/${productID}`,
          options
        );
        const response = await request.json();

        const imageInput = productForm.querySelector("#product-image");

        if (typeof response.message === "boolean") {
          productForm.classList.add("was-validated");
          imageInput.previousElementSibling.classList.remove("is-invalid");
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else {
          imageInput.nextElementSibling.innerText = response.message;
          imageInput.classList.add("is-invalid");

          setTimeout(() => {
            imageInput.nextElementSibling.innerText = "";
            imageInput.classList.remove("is-invalid");
          }, 1000);
        }
      } catch (error) {
        console.error(error);
      }
    }
    event.stopPropagation();
  });
}

async function clearProductForm() {
  const productForm = document.querySelector("#product-form");
  const createProductButton = document.querySelector("#create-product");

  createProductButton.addEventListener("click", () => {
    clearFormFields(productForm);
  });
}

function clearFormFields(form) {
  const fields = [
    "product-name",
    "product-unit",
    "product-price",
    "product-date-expiry",
    "product-quantity",
    "product-image",
  ];

  fields.forEach((field) => {
    form.querySelector(`#${field}`).value = "";
  });
}

// Insert
async function handleStoreProductEvent() {
  const productForm = document.querySelector("#product-form");

  productForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    const validate = await formValidation(productForm, event);

    if (!validate) {
      try {
        const options = {
          method: "POST",
          body: new FormData(productForm),
          "Content-Type": "multipart/form-data",
        };
        const request = await fetch(`${apiDomain}/products/store`, options);
        const response = await request.json();

        const imageInput = productForm.querySelector("#product-image");

        if (typeof response.message === "boolean") {
          productForm.classList.add("was-validated");
          imageInput.previousElementSibling.classList.remove("is-invalid");
          setTimeout(() => {
            location.reload();
          }, 1000);
        } else {
          imageInput.nextElementSibling.innerText = response.message;
          imageInput.classList.add("is-invalid");

          setTimeout(() => {
            imageInput.nextElementSibling.innerText = "";
            imageInput.classList.remove("is-invalid");
          }, 1000);
        }
      } catch (error) {
        console.error(error);
      }
    }
  });
}

async function formValidation(form, event) {
  if (!form.checkValidity()) {
    event.preventDefault();
    form.classList.add("was-validated");

    setTimeout(() => {
      form.classList.remove("was-validated");
    }, 2000);
    event.stopPropagation();

    return true;
  }

  return false;
}

async function initializeApp() {
  await renderProductCards(await fetchProducts(apiDomain));
  await attachFormEventListeners();
  await clearProductForm();
  await handleUpdateProductEvent();
  await handleStoreProductEvent();
  await handleDeleteProductEvent();
}

initializeApp();
