<?php

/*
Author: Jon Ellwood
Organization: Berkeley County IT Department
Last Updated: 07/12/2024
Purpose: View the items with type mens-shirts.
Includes:    slider.php, viewHead.php, cartSlideout.php, footer.php
*/
session_start();
if ($_SESSION['GOBACK'] == '') {
    $_SESSION['GOBACK'] = $_SERVER['HTTP_REFERER'];
}
include_once "config.php";
$conn = new mysqli($host, $user, $password, $dbname, $port, $socket)
    or die('Could not connect to the database server' . mysqli_connect_error());

// check if users isComm. If so redirect to comm only products
if (isset($_SESSION["isComm"]) && ($_SESSION["isComm"] === true)) {
    header("location: products-by-communications.php");
    exit;
}
// init shopping cart class
include_once 'Cart.class.php';
$cart = new Cart;

?>

<?php include "./components/viewHead.php" ?>
<div class="container">
    <div class="btn-container" id="btn-container">
        <button class="btn js-toggle-filter" popovertarget="filters-popover">filter <code>products</code></button>

        <div id="filters-popover" class="filters-popover" popover>
            <button popovertarget="filters-popover" popovertargetaction="hide" class="btn-close ms-2 mb-1"
                role="button">
                <span aria-hidden="true"></span>
            </button>
            <div class="popover-description">
                <span>Uncheck items you don't want to see in results</span>
            </div>
            <div id="filters"></div>
            <button class="button" onclick="resetFilters()">Reset Filters</button>
        </div>
    </div>
</div>
<script>
let sizesToRemove = [];
let typesToRemove = [];
let sleevesToRemove = [];

function resetFilters() {
    location.reload();
}

function getFilterData() {
    fetch('fetchFilters.php')
        .then((response) => response.json())
        .then((data) => {
            console.log(data);
            // console.log(data[3].type_filters[0].filter);
            var sizes = data[1].size_filters;
            var types = data[3].type_filters;
            var sleeves = data[2].sleeve_filters;
            var html = '';
            html += `<div class='filters-holder'>`
            html += `<div class="filter-group" id="size-filter-group">`
            html += `<span>Size Filters:</span>`
            for (var i = 0; i < sizes.length; i++) {
                var prefix = 's'
                html += `<label for="${prefix + sizes[i].id}">${sizes[i].filter}`
                html +=
                    `<input type="checkbox" name="${sizes[i].filter}" value="${sizes[i].id}" id="${prefix + sizes[i].id}" checked onchange="sendToSizesToRemoveArray(this.value)"/>`
                html += `</label>`
            }
            html += `</div>`
            html += `<div class="filter-group" id="type-filter-group">`
            html += `<span>Type Filters:</span>`
            for (var j = 0; j < types.length; j++) {
                var prefix = 't'
                html += `<label for="${prefix + types[j].id}">${types[j].filter}`
                html +=
                    `<input type="checkbox" name="${types[j].filter}" value="${types[j].id}" id="${prefix + types[j].id}" checked onchange="sendToTypesToRemoveArray(this.value)"/>`
                html += `</label>`
            }
            html += `</div>`
            html += `<div class="filter-group" id="sleeve-filter-group">`
            html += `<span>Sleeve Filters:</span>`
            for (var k = 0; k < sleeves.length; k++) {
                var prefix = 'sl'
                html += `<label for="${prefix + sleeves[k].id}">${sleeves[k].filter}`
                html +=
                    `<input type="checkbox" name="${sleeves[k].filter}" value="${sleeves[k].id}" id="${prefix + sleeves[k].id}" checked onchange="sendToSleevesToRemoveArray(this.value)"/>`
                html += `</label>`
            }
            html += `</div>`
            html += `</div>`
            document.getElementById('filters').innerHTML = html;
        })
}


function getFilteredProducts(genderFilter) {
    fetch('fetchFilteredProductsBySize.php?gender=' + genderFilter)
        .then((response) => response.json())
        .then((data) => {
            var html = '';
            for (var i = 0; i < data.length; i++) {
                html += renderProduct(data[i]);
            }
            document.getElementById('products-target').innerHTML = html;

        })
    getFilterData();
}
</script>


<script src="functions/renderProduct.js"></script>
<script src="functions/renderFiltersBtnAndPopover.js"></script>
<script src="functions/sendToSizesToRemoveArray.js"></script>
<script src="functions/sendToTypesToRemoveArray.js"></script>
<script src="functions/sendToSleevesToRemoveArray.js"></script>
<div id="filters-target" class="products-target"></div>
<div id="products-target" class="d-grid-4 gap-3 m-4"></div>

<script>
getFilteredProducts('1');
// renderFiltersBtnAndPopover()
</script>
<?php include "footer.php" ?>

</body>

</html>