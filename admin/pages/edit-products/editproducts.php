<?php
// Created: 2025/04/09 11:34:08
// Last modified: 2025/04/10 13:45:19

echo "<h1>Product Edit</h1>";

echo "<table class='table align-middle mb-0 bg-white'>";
echo "<thead class='bg-light'>";
echo "<tr>
        <th></th>
        <th>Product</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>";

echo "</thead>";
echo "<tbody>";
$icon = 'fas fa-basket-shopping';
foreach ($products as $product) {
    if ($product['product_type'] == 'Shirts') {
        $icon = 'fas fa-shirt';
    } else if ($product['product_type'] == 'Accessories') {
        $icon = 'fas fa-bag-shopping';
    } else if ($product['product_type'] == 'Boots') {
        $icon = 'fa-solid fa-shoe-prints';
    } else if ($product['product_type'] == 'Pants') {
        $icon = 'fa-solid fa-spaghetti-monster-flying';
    } else if ($product['product_type'] == 'Hats') {
        $icon = 'fas fa-hat-cowboy';
    } else if ($product['product_type'] == 'Sweatshirts') {
        $icon = 'fa-solid fa-cookie-bite';
    } else if ($product['product_type'] == 'Outwear') {
        $icon = 'fa-solid fa-fire';
    } else {
        $icon = 'fa-solid fa-location-dot';
    }
    echo "<tr>";
    echo "<td><i class='" . $icon . "'></i> ";
    echo "</td>";
    echo "<td>";
    echo "<div class='d-flex align-items-start flex-column'>";
    echo "<p class='fw-bold mb-1'>" . $product['code'] . "</p>";
    echo "<div class='ms-3 d-flex align-items-start'>
            <p class='mb-1'>" . $product['name'] . "</p>
          </div>";
    echo "</div>";
    echo "</td>";

    echo "<td class='text-muted mb-0'>" . $product['description'] . "</td>";
    echo "<td>";
    echo "<button type='button' class='btn btn-outline-secondary btn-sm ' data-mdb-ripple-color='dark' value =  '" . $product['product_id'] . "' class='btn btn-outline-info' onclick='editProduct(this.value)'>Edit</button>";
    echo "</td>";
    echo "</tr>";
}
echo "</tbody>";
echo "</table>";

?>

<div class='edit-product-popover' id='editProductPopover' name='editProductPopover' popover="manual"></div>