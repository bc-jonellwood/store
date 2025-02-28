<?php
/*
Author: Jon Ellwood
Organization: Berkeley County IT Department
Last Updated: 06/28/2024
Purpose: View the items in the users cart, add comments if needed, and make changes to the quantity if needed. 
Includes:    slider.php, viewHead.php, cartSlideout.php, footer.php
*/

session_start();

if ($_SESSION['GOBACK'] == '') {
    $_SESSION['GOBACK'] = $_SERVER['HTTP_REFERER'];
}
require_once 'config.php';
$conn = new mysqli($host, $user, $password, $dbname, $port, $socket)
    or die('Could not connect to the database server' . mysqli_connect_error());
// init cart class
include_once 'Cart.class.php';
$cart = new Cart;
include "./components/viewHead.php"
?>

<script>
    function getCartItem(id) {
        const cart = <?php echo $cart->serializeCart(); ?>;
        const cartItem = cart[id];
        if (cartItem) {
            return cartItem;
        } else {
            console.error("Cart can not be retrieved")
        }
    }

    function updateSizeAndPriceData(event) {
        var selectElement = event.target;
        var selectedOption = selectElement.options[selectElement.selectedIndex];

        var hiddenPriceIdInput = document.getElementById('price_id')
        hiddenPriceIdInput.value = selectedOption.getAttribute('data-priceid');

        var hiddenPriceInput = document.getElementById('price')
        hiddenPriceInput.value = selectedOption.getAttribute('data-price');

        var hiddenSizeNameInput = document.getElementById('size_name')
        hiddenSizeNameInput.value = selectedOption.getAttribute('data-sizename');


    }

    function updateColorData(event) {
        var selectElement = event.target;
        var selectedOption = selectElement.options[selectElement.selectedIndex];

        var hiddenColorIdInput = document.getElementById('color_id')
        hiddenColorIdInput.value = selectedOption.getAttribute('data-colorid');

        var hiddenColorNameInput = document.getElementById('color_name')
        hiddenColorNameInput.value = selectedOption.getAttribute('data-colorname');
    }

    function updateLogo(val) {
        var hiddenLogoInput = document.getElementById('logo')
        hiddenLogoInput.value = val;
    }

    function updateDeptPatch(val) {
        var hiddenDeptInput = document.getElementById('deptPatchPlace')
        var hiddenLogoFeeInput = document.getElementById('logoFee')
        hiddenDeptInput.value = val;
        if (val == 'Left Sleeve') {
            hiddenLogoFeeInput.value = parseFloat(10.00)
        } else {
            hiddenLogoFeeInput.value = parseFloat(5.00)
        }
    }

    function formatColorValueForUrl(str) {
        var noSpaces = str.replace(/[\s/]/g, '');
        var lowercaseString = noSpaces.toLowerCase();
        return lowercaseString;
    }

    function getCartItemOptions(id, cartItem) {
        fetch(`./fetchProductDetails.php?id=${id}`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error status: ${response.status}`);
                }
                return response.json();

            })
            .then((data) => {
                console.log('product-data-for-cart-item');
                console.log(data);
                //return data;


                var html = '';
                html += `
                    <form action='cartAction.php' method='post' id='editCartItem'>
                    <div class="editCartItemDiv">
                        <input type='hidden' name='id' value='${cartItem.rowid}' />
                        <input type='hidden' name='action' value='updateCartItem' />
                        <input type='hidden' name='color_id' id='color_id' value='${cartItem.color_id}' />
                        <input type='hidden' name='color_name' id='color_name' value='${cartItem.color_name}' />
                        <input type='hidden' name='size_id' id='size_id' value='${cartItem.size_id}' />
                        <input type='hidden' name='size_name' id='size_name' value='${cartItem.size_name}' />
                        <input type='hidden' name='logo' id='logo' value='${cartItem.logo}' />
                        <input type='hidden' name='deptPatchPlace' id='deptPatchPlace' value='${cartItem.deptPatchPlace}' />
                        <input type='hidden' name='price_id' id='price_id' value='${cartItem.price_id}' />
                        <input type='hidden' name='price' id='price' value='${cartItem.price}' />
                        <input type='hidden' name='logoFee' id='logoFee' value=${cartItem.logoFee} /> 

                        <fieldset>
                        <label for='editSize'>Edit Size</label>
                        <select name='size_id' id='editSize' onchange='updateSizeAndPriceData(event)'>
                            `;
                for (var i = 0; i < data['price_data'].length; i++) {
                    var isSelected = cartItem.size_id == data['price_data'][i].size_id ? 'selected' : '';

                    html += `
                                <option value="${data['price_data'][i].size_id}" ${isSelected === 'selected'? 'selected' : ''} data-priceid=${data['price_data'][i].price_id} data-sizename="${data['price_data'][i].size_name}" data-price=${data['price_data'][i].price}>
                                    ${data['price_data'][i].size_name}
                                </option>
                            `;
                }
                html += `
                        </select>
                        </fieldset>
                        <fieldset>
                        <label for='editSize'>Edit color</label>
                        <select name='color_id' id='editColor' onchange='updateColorData(event)'>
                        `;
                for (var j = 0; j < data['color_data'].length; j++) {
                    var isSelected = cartItem.color_id == data['color_data'][j].color ? 'selected' : '';

                    html += `
                                <option value="${data['color_data'][j].color_id}" ${isSelected === 'selected'? 'selected' : ''} data-colorid=${data['color_data'][j].color_id} data-colorname="${data['color_data'][j].color}" data-colorid="${data['color_data'][j].color_id}">
                                    ${data['color_data'][j].color}
                                </option>
                            `;
                }
                html += `
                        </select>
                        </fieldset>
                        <fieldset>
                        <label for='editLogo'>Edit Logo</label>
                        <select name='logo' id='editLogo' onchange='updateLogo(this.value)'>
                    `;
                for (var k = 0; k < data['logo_data'].length; k++) {
                    // var isSelected = cartItem.logo.replace(/_NO\.png$/, '.png') == data['logo_data'][k].image ?
                    //     'selected' : '';
                    var isSelected = cartItem.logo.replace(/_NO\.png$/, '.png').toLowerCase() === data['logo_data'][k]
                        .image.toLowerCase() ? 'selected' : '';

                    html += `
                            <option value="${data['logo_data'][k].image}" ${isSelected === 'selected'? 'selected' : ''}>
                                ${data['logo_data'][k].logo_name}
                            </option>
                            `;
                }
                html += `
                        </select>
                        </fieldset>
                        <fieldset>
                            <label for='deptPatchPlace'>Edit Dept Name</label>
                            <select name='deptPatchPlace' id='deptPatchPlace' onchange='updateDeptPatch(this.value)'>
                                <option value='No Dept Name' id='p1' ${cartItem.deptPatchPlace === 'No Dept Name'? 'selected' : ''}>No Dept Name</option>
                                <option value='Below Logo' id='p2' ${cartItem.deptPatchPlace === 'Below Logo'? 'selected' : ''}>Below Logo</option>
                                <option value='Left Sleeve' id='p3' ${cartItem.deptPatchPlace === 'Left Sleeve'? 'selected' : ''}>Left Sleeve</option>
                            </select>
                        </fieldset>

                        <fieldset>
                        <label for='editQty'>Quantity</label>
                        <input name='qty' id='editQty' type='number' value='${cartItem.qty}' min='1' max='100'>
                        </fieldset>
                        </div>
                        <div class='edit-cart-item-submit-btn-holder'>
                        <button class='btn cart-item-submit-btn' type='b'>
                        <span aria-hidden=”true”></span> 
                        <span class="sr-only">Submit</span>
                        </button>
                        </div>
                        </form>
                        `;
                document.getElementById('popover-edit-form-holder').innerHTML = html;



            })
            .catch((error) => {
                console.error('Error fetching product details:', error);
            });
    }


    function updateCartItem(obj, id) {
        // Construct the URL with parameters
        const url = new URL("cartAction.php");
        url.searchParams.append("action", "updateCartItem");
        url.searchParams.append("id", id);
        url.searchParams.append("qty", obj.value);


        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                if (data === 'ok') {
                    // If data is ok, reload the page
                    location.reload();
                } else {
                    // Else alert user that the update failed
                    alert('Cart update failed, please try again.');
                }
            })
            .catch(error => {
                console.error('There has been a problem with your fetch operation:', error);
                alert('An error occurred, please try again later.');
            });
    }

    function updateCartItemComment(obj, id) {

        const url = new URL("cartAction.php");
        url.searchParams.append("action", "updateCartItemComment");
        url.searchParams.append("id", id);
        url.searchParams.append("comment", obj.value);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text(); // Parse the response body as text
            })
            .then(data => {
                if (data === 'ok') {
                    // If data is ok, reload the page or perform another action
                    location.reload(); // Comment this line we decide we don't want it. I hate reloads but...
                } else {
                    alert('Comment update failed, please try again');
                }
            })
            .catch(error => {
                console.error('There has been a problem with your fetch operation:', error);
                alert('An error occurred, please try again later.');
            });
    }

    function convertObjectToArray(obj) {
        let resultArray = [];
        for (let key in obj) {
            if (typeof obj[key] === "object") {
                resultArray.push([key, convertObjectToArray(obj[key])]);
            } else {
                resultArray.push([key, obj[key]]);
            }
        }
        return resultArray;
    }

    function makeDollar(str) {
        let amount = parseFloat(str);
        return `$${amount.toFixed(2)}`;
    }

    function renderEdit(cartId) {
        console.log('CartId is ', cartId)
        const cartItem = getCartItem(cartId)
        console.log(cartItem);
        const itemOptions = getCartItemOptions(cartItem.id, cartItem)
    }

    function renderComment(cartId) {
        console.log('Comment id is :', cartId)
        var target = document.getElementById('add-comment-item-popover');
        var html = '';
        // <input type="hidden" name="action" value="updateCartItemComment">
        // <label for="comment"></label>
        html +=
            // <form class="comment-form" onsubmit=updateCartItemComment(this, ${cartId})>
            `
            <form class="comment-form" action="cartAction.php" method="post">
            <input type="hidden" name="action" value="updateCartItemComment">
            <input type="hidden" name="id" value="${cartId}">
            <textarea name="comment[]" id="comment" placeholder="Comment" rows="5" cols="50"></textarea>
            <br>
            <button class="button comment-form-btn" type="submit">Submit</button>
            </form>`;
        target.innerHTML = html;
    }

    function removeItem(cartId) {
        if (confirm('Are you sure you want to delete this item?')) {
            fetch('cartAction.php?action=removeCartItem&id=' + cartId)
                .then(response => {
                    if (response.ok) {
                        localStorage.removeItem('store-cart');
                        window.location.reload();
                    } else {
                        alert("Remove item failed!!!! 😲 ");
                    }
                })
                .catch(error => {
                    console.error('Error removing item:', error);
                    alert('An error occurred while removing the item')
                })
        }
    }


    function renderCheckout(cart) {
        // console.log('cart');
        // console.log(cart);
        cartArray = convertObjectToArray(cart);
        // console.log('cartArray.length')
        // console.log(cartArray.length)
        if (cartArray.length <= 3) {
            var html = '<img src="cart_empty.jpg" alt="Cart is empty" class="empty-cart-img" />';
            document.getElementById('items').innerHTML += html;
            return
        }
        // console.log(cartArray)
        // let accumulatedHtml = '';
        // let selectQuantities = {};
        var html = '';
        for (var i = 3; i < cartArray.length; i++) {
            const itemEntry = cartArray[i][1];
            console.log("Item Entry # ", i)
            console.log(itemEntry)
            var html = '';
            html += `
                <div>
                    <div class="active-items">
                        <div class="active-item" data-cartId=${itemEntry[4][1]}>
                            <div class="item-content">
                                <div class="item-content-inner">
                                    <div class="image-wrapper">
                                        <img src="${itemEntry[5][1]}" alt="${itemEntry[1][1]}" class="product-image" />
                                    </div>
                                    <div class="item-content-inner-inner">
                                        <ul class="unordered-list">
                                            <li class="product-title">${itemEntry[6][1]} - ${itemEntry[1][1]}</li>
                                            <div class="price-block">${makeDollar(itemEntry[2][1])}</div>
                                            <div class="content-tail">
                                                <div> 
                                                    <li>Size: ${itemEntry[13][1]}</li>
                                                    <li>Color: ${itemEntry[11][1]}</li>
                                                    <li>Qty: ${itemEntry[3][1]}</li>
                                                    <li>Dept Name: ${itemEntry[16][1]} </li>
                                                </div>
                                                <div class="logo-holder">
                                                ${itemEntry[0][1] !== 105 ? `<img src=${itemEntry[15][1]} alt="logo" class="logo-pict">` : ''}
                                                    
                                                </div>
                                            </div>
                                            
                                            <div class="item-content-footer">
                                            <button class='btn btn-danger' value="${itemEntry[4][1]}" onclick="removeItem(this.value)">Delete</button>
                                            <button class='btn btn-info' value="${itemEntry[4][1]}" onclick="renderEdit(this.value)" popovertarget="edit-cart-item" popovertargetaction="show">Edit</button>
                                            <button class='btn btn-warning' value="${itemEntry[4][1]}" onclick="renderComment(this.value)" popovertarget="add-comment" popovertargetaction="show">Comment</button>
                                            </div>
                                            </ul>
                                            </div>
                                            </div>
                                            </div>
                                            </div>
                                            </div>
                                            </div>
                                            
                                            `
            var chtml = `
                    ${itemEntry[14][1] ? `<p>${itemEntry[6][1]}</p> <span> ${itemEntry[14][1]}</span>` : `<span class='no-comment'>no comment</span>`}
                `;

            document.getElementById('items').innerHTML += html;
            document.getElementById('comments-stream').innerHTML += chtml;
        }
    }

    function logCart() {
        let cartItems = JSON.parse(localStorage.getItem('store-cart')) || {};
        console.log('cartItems');
        console.log(cartItems);
    }
    // logCart();
</script>
</head>

<body>

    <div class="container">
        <div id="items" class="items"></div>
        <div class="items">
            <table class="checkout-summary-table">
                <tbody>
                    <tr>
                        <th>Total Items</th>
                        <td class="amount-column"><?php echo $cart->total_items() ?></td>
                    </tr>
                    <tr>
                        <th>Sub Total</th>
                        <td class="amount-column"><?php echo CURRENCY_SYMBOL . (number_format(($cart->total()), 2))  ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Logo Fees</th>
                        <td class="amount-column">
                            <?php echo CURRENCY_SYMBOL . (number_format(($cart->total_logo_fees()), 2))  ?></td>
                    </tr>
                    <tr>
                        <th>Total Tax</th>
                        <td class="amount-column"><?php
                                                    $totalWithFees = $cart->total() + $cart->total_logo_fees();
                                                    $taxes = $totalWithFees * 0.09;
                                                    echo CURRENCY_SYMBOL . (number_format(($taxes), 2))
                                                    ?></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td class="amount-column">
                            <?php echo CURRENCY_SYMBOL . (number_format(($cart->total() + $cart->total_logo_fees() + $taxes), 2))  ?>
                        </td>
                    </tr>
                </tbody>
            </table>


            <?php if ($cart->total_items() > 0) { ?>
                <div class="button-holder">
                    <a href="checkout.php" class="btn btn-success">
                        Checkout
                    </a>
                </div>
            <?php } ?>
            <div class="comments-stream" id="comments-stream">Comment:</div>
        </div>
        </?php include "viewCartDump.php" ?>

        <div class="bottom-buttons-holder d-flex justify-content-between mx-5">
            <div>
                <a href="<?php echo $_SESSION['GOBACK'] ?>"><button class="btn btn-primary" type="button"> Continue
                        Shopping
                    </button></a>
            </div>
            <div>
                <?php if ($cart->total_items() > 0) { ?>
                    <a href="checkout.php"><button class="btn btn-success" type="button"> Proceed to Checkout </button></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div id="edit-cart-item" popover>
        <div id="cart-item-edit-details" class="cart-item-edit-details">
            <div class="popover-btn-holder">
                <button popovertarget="edit-cart-item" popovertargetaction="hide" class="btn-close ms-2 mb-1"
                    role="button">
                    <span aria-hidden="true"></span>
                </button>

            </div>
            <div class="popover-desc-text-holder">
                <p class="popover-desc-text">Make changes to the color , size, quantity, logo, or dept name for this
                    cart item.</p>

            </div>
            <div class="popover-edit-form-holder" id="popover-edit-form-holder">

            </div>

            <!-- This is where the details for the cart item will render -->
            <div id="edit-cart-item-popover"></div>
        </div>
    </div>
    <div id="add-comment" popover=manual>
        <div class="popover-btn-holder">
            <button class="btn-close ms-2 mb-1" popovertarget="add-comment" popovertargetaction="hide">
                <span aria-hidden=”true”></span>

            </button>
        </div>
        <div class="popover-desc-text-holder">
            <p class="popover-desc-text">Add your comment.</p>
            <!-- This is where the form to add the comment will render -->
            <div id="add-comment-item-popover"></div>
        </div>
    </div>


    <?php include "footer.php" ?>
    <script>
        renderCheckout(<?php echo $cart->serializeCart() ?>);
    </script>

</body>


</html>
<style>
    #cart-logo-img {
        width: 50px;
        transition: all .2s ease-in-out;
    }

    .container {
        max-width: unset !important;
        width: auto;
        display: grid;
        grid-template-columns: 6fr 2fr;
        box-shadow: 0 0 25px -5px #000000;
        margin: 2%;
        padding: 2%;
        border-radius: 5px;
    }

    .cart-display {
        display: grid;
        grid-template-columns: 5fr 1fr;
    }

    .items {

        border-radius: 5px;
        padding: 10px;
        margin: 10px;
        color: #000;
    }

    .active-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, 100%);
        gap: 12px;
        margin-bottom: 20px;
    }

    .active-item {
        max-width: none;
        padding-bottom: 12px;
        background-color: #FFF;
        box-shadow: 0 0 25px -5px #000000;
        border-radius: 5px;
    }

    .item-content {
        position: relative;
        margin-top: 12px;
    }

    .item-content-inner {
        width: 100%;
        display: flex !important;
        flex-direction: row;
        table-layout: fixed;
        zoom: 1;
        border-collapse: collapse;
    }

    .item-content-inner-inner {
        width: 100%;
        display: flex !important;
        flex-direction: row;
        table-layout: fixed;
        zoom: 1;
        border-collapse: collapse;
    }

    .image-wrapper {
        margin-right: 12px;
        margin-inline-end: 12px;
        flex-shrink: 0;
        margin-bottom: 4px;
    }

    .product-image {
        vertical-align: top;
        max-width: 100%;
        border: 0;
        height: 180px;

    }

    .item-content {
        min-width: 0;
        flex: auto;
        margin-inline-end: 0;
        margin-right: 12px;
    }

    .item-content-footer {
        display: flex;
        justify-content: space-between;
        padding-top: 12px;
    }


    .unordered-list {
        display: grid;
        column-gap: 12px;
        grid-template-areas: "head price" "tail price";
        grid-template-rows: auto 1fr;
        grid-template-columns: 1fr minmax(13ch, 20%);
        list-style: none;
        word-wrap: break-word;
        margin: 0;
        width: 100%
    }

    .product-title {
        grid-area: head;
        line-height: 1.3rem;
        max-height: 2.6em;
        font-size: x-large;
        word-break: normal;
        padding: 3px;
    }

    .price-block {
        grid-area: price;
        display: flex;
        flex-flow: column;
        align-items: end;
        text-align: end;
        line-height: 1.3rem;
        max-height: 2.6em;
        font-size: x-large;
        word-break: normal;
        padding: 3px;
    }

    .content-tail {
        grid-area: tail;
        display: grid;
        grid-template-columns: 1fr 3fr;
        margin-top: 5px;
        margin-bottom: 5px;
    }

    .logo-pict {
        height: 100px;
        margin-left: 50px;
        padding-top: 12px;
        margin-right: auto;

    }

    .logo-holder {
        display: flex;

        grid-row-start: 1;
        grid-row-end: 4;
    }


    .checkout {
        background-color: #FFFFFF;
        height: fit-content;

        border-radius: 5px;
        /* padding: 12px; */

        color: #000;
        /* margin-left: 10px; */
        /* padding-right: 20px; */
        text-align: end;
        display: flex;
        flex-direction: column;
        padding: 2%;
        margin: 2%;
        box-shadow: 0 0 25px -5px #000000;
    }


    .amount-column {
        min-width: 25px;
        text-align: right;
    }

    .checkout-summary-table {
        width: 100%;
    }

    .checkout p {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
        word-break: normal;
    }

    .checkout p span {
        margin-right: 5px;
    }

    #add-comment,
    #edit-cart-item {
        width: 45%;
        height: 37%;

        background-color: #ffffff;
        color: hsl(224, 10%, 23%);
        border: 7px solid #BF1722;

        mark {
            border-radius: 5px;
            padding: 2px;
            font-weight: bold;
        }
    }

    .cart-item-edit-details {
        background-color: #ffffff50;
        padding: 20px
    }

    .popover-btn-holder {
        display: flex;
        justify-content: flex-end;
        width: 100%;
    }

    .popover-close-btn {
        background-color: #000000;
        padding: 5px;
        margin-bottom: 5px;
    }

    .popover-desc-text-holder {
        margin: 5px;
        padding: 5px;

        p {
            font-size: larger;
        }
    }


    .editCartItemDiv {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    ::backdrop {
        backdrop-filter: blur(3px);
    }

    .edit-cart-item-submit-btn-holder {
        display: flex;
        padding: 5px;
        justify-content: flex-end;
    }

    .cart-item-submit-btn {
        padding: 5px;
        background-color: limegreen;
        color: #000
    }


    .empty-cart-img {
        max-height: 50dvh;
        margin-left: auto;
        margin-right: auto;
    }

    .comments-stream {
        margin-top: 5px;
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: #A09D9C;
        color: #000000;
        padding: 5px;

        p {
            text-align: left;
            padding: 0;
            margin: 0;
            margin-bottom: -10px;
            width: 100%;
            text-transform: uppercase;
            font-size: medium;
        }

        span {
            display: flex;
            justify-content: flex-start;
            font-size: medium;
            font-weight: bold;
            font-family: monospace;
            text-align: left;
            padding: 5px;
            border-bottom: 1px solid #808080;
            width: 100%;
        }

        span:before {
            content: '👉🏼 '
        }
    }

    .no-comment {
        visibility: hidden;
    }

    .button-holder {
        margin-top: 5px;
        display: flex;

        justify-content: space-around;

        margin-left: 5em;
        margin-right: 5em;
    }


    @view-transition {
        navigation: auto;
    }
</style>