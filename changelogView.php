<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changelog View</title>
    <!-- <script type="module" src="https://md-block.verou.me/md-block.js"></script> -->
    <!-- <link rel="stylesheet" id='test' href="berkstrap-dark.css" defer async>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" defer async> -->


</head>

<!-- <body>
    <h1>County Store Application Changelog</h1>
    <h2>Version 1.2.2 - (2024-04-10) 📷</h2>
    <h3><strong>UI Update</strong></h3>
    <ul>
        <li>Admin users in the the admin portal will now see an image of the product for each line item in the order.
        </li>
        <li>Mouseover the image to see a larger version of the image.</li>
        <li>This should help managers when making approval decisions as well as comparing the product the received
            against what was ordered.</li>
        <li>Many thanks to &lt;mark&gt; <em>Lauren Willis</em> &lt;/mark&gt; for the suggestion for this feature!</li>
        <li>Added a &quot;Receive&quot; button to the UI, hopefully 🤞 only for orders in the &quot;Ordered&quot;
            status. This will allows managers to mark the items as Received which should allow them to populate in the
            Inventory Management System.
            : TODO: The feature can be enhanced by passing the employee the item is being assinged into the function and
            assigning it to the employee in the Inventory System in addition to changing the status of the item.</li>
        <li>All requests with the status of &quot;Pending&quot; after 90 days from being submitted will automatically be
            &quot;Expired&quot;. This should reduce the chance of old requests being approved in error, reduce the
            clutter managers see on their dashboard, and may improve load times as well.</li>
        <li>The main UI for Store Administration (Approve, Deny, Receive items, etc..) will not display Denied requests.
            This has been requested by several managers.
            : TODO: Add a method for managers to find denied request and change the status to approved (in the event it
            was denied in error, etc....)</li>
    </ul>
    <h3><strong>Bug's Squished</strong></h3>
    <ul>
        <li>Fixed a bug that allowed users to &quot;Approve&quot; orders that were already ordered; causing them to
            reneter the ordering pipeline. Approve buttons that were not intended to be displayed were displayed to the
            user next the the disabled approve button and the warning 'Orders in this status can not be edited.' ...
            which clearly was not accurate.</li>
        <li>Fixed an issue when viweing requests for employees who have seperated from Employement that caused an index
            error in retreiving their fiscal year spending.</li>
    </ul>
    <h3><strong>Database Maintenance</strong></h3>
    <ul>
        <li>Several older orders were moved to 'Expired' status to be excluded from current queries. These were early
            orders that will never be received.</li>
        <li>Requests 90 days old with the status of 'Pending' were moved to 'Expired' status. This will be a weekly
            event (see above).</li>
    </ul>
    <h2>Version 1.2.1 - (2024-03-7) 🐞</h2>
    <h3><strong>Bug Squished</strong></h3>
    <ul>
        <li>Fixed an error in the Inventory Management System (<em>yes we have one of those and it's pretty okay if you
                ask me</em>) lookup where employees with a seperation date greater than todays date were excluded from
            lookup. Thanks again <em>Kelly Herrin</em> .</li>
        <li>Updated vendor report query to include only items with status of 'Ordered' (3/13/2024)</li>
    </ul>
    <h2>Version 1.2.0 - (2024-03-4) 📰</h2>
    <h3><strong>Major UI Update</strong></h3>
    <ul>
        <li>
            <p>Product details page changed from &lt;code&gt;'product-details-onefee'&lt;/code&gt; to
                &lt;code&gt;'product-details'&lt;/code&gt;.</p>
        </li>
        <li>
            <p>This change is in preparation for product updates and changes coming soon.</p>
        </li>
        <li>
            <p>The products page will no longer display a color swatch for the selected item.</p>
        </li>
        <li>
            <p>🔥 Each product image updates to show the product model wearing the selected color. This has been one of
                the most requested features. 🔥</p>
        </li>
        <li>
            <p>If we missed one, shoot us an email at store.berkeleycountysc.gov and let us know! 🙏</p>
        </li>
        <li>
            <p>The logo selection dropdown now also updates a small logo image near the &quot;Add to Cart&quot; button.
            </p>
        </li>
        <li>
            <p>Logo options of &quot;black&quot; or &quot;white&quot; have been consolidated as they are no longer
                needed. The color of the stitched logo is based on the color of the shirt. For a specific stitching
                color request please add in a comment.</p>
        </li>
        <li>
            <p>Product 185 (Gildan - Heavy Blend Hooded Sweatshirt) product view updated to reflect minimum order
                requirements and Department restrictions.</p>
        </li>
    </ul>
    <h3><strong>Bug's Squished</strong></h3>
    <ul>
        <li>Fixed error in fiscal year date calculations for Employee Requests page for manager approvals.</li>
        <li>Changed query structure for new orders / customer information to exclude separated employess.</li>
    </ul>
    <h2>Version 1.1.5 - (2024-02-01) 🧢</h2>
    <h3><strong>Product Added</strong></h3>
    <ul>
        <li>🧢 🧢 HATS! 🧢 🧢</li>
        <li>New hat options available</li>
        <li>Hat product view customized - see each hat live in full HD Color.</li>
    </ul>
    <h3><strong>Admin Features Added</strong></h3>
    <ul>
        <li><em>Department Admin Page</em></li>
        <li>Assign, Reassign, or reset Department Head, Department Assistant, and Department Asset Managers from UI.
        </li>
        <li>Page is still slow to load but I know why. Added to TODO list.</li>
        <li>Return from API is sorted by those departments with assignments already, those with none are near the
            bottom.</li>
        <li><em>TODO:</em> Sort / Filter function to make it easier to find the one you seek among the multitudes.</li>
        <li>When assigning an employee to a role within a department the &lt;code&gt;users&lt;/code&gt; table will be
            checked and a user account will be created if one does not exist.</li>
    </ul>
    <h2>Version 1.1.4 - (2024-01-29) 🧩</h2>
    <h3><strong>Minor Feature Added</strong></h3>
    <ul>
        <li>Link added to product details page above logo selector to open a new tab in .../logos.php</li>
        <li>Logos page shows users all the available logos in high res images. Clicking on a logo opens a nice popover
            style element to see the image in greater detail.</li>
        <li>Removed all Communications Logos from the query so they will not be displayed.</li>
    </ul>
    <h2>Version 1.1.3 - (2024-01-24) 🗓</h2>
    <h3><strong>Bug's Squished</strong></h3>
    <ul>
        <li>Fixed an issue causing a fee to be applied twice.</li>
        <li>Searching from nav bar actually searches for text entered into the search bar, not just directs to you the
            search page to type it all in again. 🎇</li>
        <li>Just fixed about 1,300 typos on this page.</li>
    </ul>
    <h3><strong>Minor changes to database</strong></h3>
    <ul>
        <li>Base cost, logo fee, and tax are captured individually. This will be valuable in the future when tax rates
            change allowing us to accurately report on amounts spent.</li>
    </ul>
    <h3><strong>New Additions</strong></h3>
    <ul>
        <li>About 10 new items were added to the store for your enjoyment; including a 7x, 8x, 9x, and 10x option for
            shirts.</li>
        <li>A new vendor was added to the store. While is not reflected in the users UI, it will allow for more diverse
            options down the road.</li>
    </ul>
    <h3><strong>Feature added</strong></h3>
    <ul>
        <li>At checkout users will see, after selecting who the order is for, a total of orders &quot;Approved&quot;,
            &quot;Ordered&quot;, and &quot;Received&quot; for the current fiscal year.</li>
        <li>In the admin section any line items that have the order status or &quot;Ordered&quot; the buttons for
            &quot;Approve&quot; and &quot;Deny&quot; are now disabled to prevent changes in status. Thanks to
            &lt;mark&gt;<em>Kelly Herrin</em>&lt;/mark&gt; for the suggestion on this one.</li>
    </ul>
    <h2>Version 1.1.2 - (2023-12-14) 🎅</h2>
    <h3><strong>Minor Bugs Squished</strong></h3>
    <ul>
        <li>Fixed an issue where a specific product price would not update properly based on size when writing order to
            database.</li>
        <li>Updated vendor report template to tables and pages separated by vendor. This is helpful when one order has
            items from multiple vendors. The design should allow for one report that can be sent to both vendors easily,
            or each vendor can receive their specific page only. This leaves that decision up to the user.</li>
        <li>Added two new hats to the product line up!</li>
        <li>Updated several product images to render better in the product details view.</li>
        <li>Moved &quot;Reports / Past Reports&quot; from a side module on the admin page to it's own page. It can be
            found under &quot;Reports&quot; from the top or side nav depending on where you are.</li>
        <li>Added additional data fields to the &quot;Reports&quot; page to make finding the exact vendor report easier.
        </li>
        <li>Employee Requests page is now default landing page for Admin Login.</li>
        <li>Employee Requests page refactored to only display requests that are NOT status received. All received items
            should be managed through Inventory Management System. This should increase page load times and reduce the
            time it takes to find specific orders.</li>
        <li>Items requiring a Purchase Order (per the vendor) are flagged as such in the 'Employee Requests' section in
            admin backend.</li>
        <li>A 'Generate PO Request' button will be displayed if any of the items in that order require a Purchase Order.
            The PO Request will list only the items on the order that require a PO. This printable item is intended make
            it as simple as possible to provide the person(s) entering the PO request the information they need.</li>
    </ul>
    <h2>Version 1.1.1 - (2023-10-05) 📰</h2>
    <h3><strong>Preparing for the Future</strong></h3>
    <ul>
        <li>In preparation for some exciting changes coming to the store some database format changes were in order:
        </li>
        <li>Previous versions of pricing recorded in the database per order included the product price, logo fees, and
            stitching fees combined into one value. Each value will now be recorded individually in the database. End
            users should only see minor changes related to this.</li>
        <li>The &quot;Order Success&quot; screen after an order is placed now reflects the lower base unit price
            (without the embedded logo fees) as well as a line item for the logo fee under it.</li>
        <li>The email confirmation sent upon a successful request being placed also reflects the lower base price and a
            line item for the logo fees.</li>
        <li>The email confirmation section with the historical data for the employee was removed due to lack of
            interest. Actually if it was not noted in this changelog it is doubtful anyone would even have noticed.</li>
        <li>Admin reporting and vendor reporting have been updated to reflect this changes as well a base price and logo
            fee displayed in their own respective columns.</li>
        <li><strong>All previous orders</strong> have had the base price of each line item reduced by $5.00 (<em>except
                for specific products that did not have a logo fee added to it</em>). Historical data and reporting
            should continue to be as accurate as it was before - however the two values will not be displayed as one.
        </li>
    </ul>
    <h2>Version 1.1.0 - (2023-09-27) 🆕</h2>
    <h3><strong>New Admin Features</strong></h3>
    <ul>
        <li>Reporting for previous orders and invoice amounts has been updated to reflect both pre tax and post tax
            numbers for ease of use.</li>
        <li>When creating an order instance the person entering the order will now be prompted for a PO#. Leaving the
            input blank when there is no PO is the expected action.</li>
        <li>This feature is currently not supported in Firefox, but is in all other modern browsers. It is only
            available behind a flag in Firefox as of this update. <a href="https://developer.mozilla.org/en-US/docs/Web/API/Popover_API">MDN Docs</a></li>
        <li>The vendor reports now reflect the item pricing both with tax and without &amp; the dev team doesn't care if
            vendors want to see both numbers or not. ☕</li>
        <li>The vendor report now shows the PO Number associated with the order instance on the top of the report. If
            there is not PO the value of 'N/A' is displayed.</li>
    </ul>
    <h2>Version 1.0.9 - (2023-09-22) 🧩</h2>
    <h3><strong>Minor UI changes</strong></h3>
    <ul>
        <li>Created and updated logo for product #185.</li>
        <li>Update product #185 to post correct logo value in database when ordered.</li>
    </ul>
    <h2>Version 1.0.8 - (2023-09-01) 🐞</h2>
    <h3><strong>Minor Bugs Squished</strong></h3>
    <ul>
        <li>Fixed an issue with &quot;Continue Shopping&quot; button on product view and cart view where adding item to
            cart or changing quantity in cart caused button to become a link to current page.</li>
        <li>Fixed an issue when in the event there are two entries in BIC with the same employee ID, but different
            names, the one without a separation date wins. This impacted a modal in the requests.php page and the
            managers ability to view/approve/deny the request.</li>
    </ul>
    <h3><strong>UI tweaks</strong></h3>
    <ul>
        <li>Added size chart under photo for shirts.</li>
    </ul>
    <h2>Version 1.0.7 - (2023-08-21) 🐞</h2>
    <h3><strong>Minor Bugs Squished</strong></h3>
    <ul>
        <li>Fixed an issue where gender filter = N/A was not included in any results.</li>
    </ul>
    <h3><strong>Product Added</strong></h3>
    <ul>
        <li>New Product 🔥 added.</li>
    </ul>
    <h3><strong>Admin Updates</strong></h3>
    <ul>
        <li><em>Making changes to admin backend to prep for adding additional vendors</em></li>
        <li>Added vendor name and &quot;requested for&quot; name to viewOrdersByDept report. Currently items are sorted
            during query.</li>
        <li>TODO: Create tables for each employee with sub tables for different vendors.</li>
    </ul>
    <h2>Version 1.0.6 - (2023-08-18) 🧩</h2>
    <h3><strong>UI tweaks</strong></h3>
    <ul>
        <li>Added department name placement value to display in approval screen under the logo</li>
        <li>Added department name placement value to display in confirmation email to employee</li>
    </ul>
    <h2>Version 1.0.5 - (2023-08-08) 🐞</h2>
    <h3><strong>Minor Bugs Squished</strong></h3>
    <ul>
        <li>Fixed broken link in Nav</li>
        <li>Fixed bug that caused inactive products to display on front page</li>
    </ul>
    <h3>Version 1.0.4 - (2023-07-31) 🧩</h3>
    <h3><strong>UI tweaks</strong></h3>
    <ul>
        <li>Set product specific CSS for logs images over layed on hats.</li>
        <li>Pushed new index page layout to production</li>
        <li>Added 4th top selling product to page solely for layout purposes.</li>
        <li>Support page links on right updated, nav removed to better direct traffic.</li>
    </ul>
    <h2>Version 1.0.3 - (2023-07-27)</h2>
    <h3>New Features 🤯</h3>
    <h4><strong>New products details page</strong></h4>
    <ul>
        <li>Added ability to select quantity of items before putting in the cart.</li>
        <li>Added Cart View Slide Out feature with interactive cart to view items in cart and delete items.</li>
        <li>Slideout feature is accessed using the cart icon in Nav that previously linked to the cart.</li>
        <li>Go To Cart button in slide out now takes users to cart.</li>
        <li>Logos were consolidated to reflect approved, department specific, logos.</li>
        <li>All other logos are shown in both black and white with &quot;Department Name&quot; under them to reflect
            what product will more closely resemble.</li>
        <li>Selecting the No Dept Name or Left Sleeve option will remove the Department Name from image.</li>
        <li>Logo Image now displays overlaid on the product.</li>
        <li>New Nav bar allows for quickly locating Mens and Women's products, with sub menus sorting by type to allow
            for locating products faster as well as faster page loads.</li>
    </ul>
    <h4><strong>TODO:</strong></h4>
    <ul>
        <li><s>Logo Images and Product Images are not uniform so not all render great. ie a few logos end up in an
                armpit. This was &lt;mark&gt;not intentional&lt;/mark&gt; as a comment on product quality. Need to work
                on standard image sizes.</s></li>
        <li>New versions of main page with nav, and new version of search page need tested and pushed to production.
        </li>
        <li>Media Queries for mobile for all pages need refined based on new layout.</li>
    </ul>
    <h2>Version 1.0.2 - (2023-07-24)</h2>
    <h3>New Features 👏</h3>
    <h4><strong>New Email format and functionality for unavailable products</strong></h4>
    <ul>
        <li>Updated emails sent to users when size or color is not available. (55ae7bf1)</li>
        <li>Email script was updated to include requested for and requested by addresses. (55ae7bf1)</li>
        <li>Email now provides users a list of options available based on the specific product. (55ae7bf1)</li>
        <li>List of options has the unavailable option removed from the list to avoid confusion. (55ae7bf1)</li>
    </ul>
    <h2>Version 1.0.1 - (2023-07-24)</h2>
    <h3>Other Changes</h3>
    <ul>
        <li><strong>other:</strong> description[fixed typo in script] (69abd7ee)</li>
        <li><strong>test:</strong> description [updated changelog func] (63daa23c)</li>
        <li>//github.com/Berkeley-County/county-store (fd94eeae)</li>
        <li>//github.com/Berkeley-County/county-store (395f8ac8)</li>
    </ul>
    <h2>Version 1.0.0 - (2023-07-13)</h2>
    <h3>General Changes</h3>
    <ul>
        <li>Added cart viewer slideout feature</li>
        <li>Fixed UI bug that caused shift in rendering in certain cases</li>
        <li>Changed product photo in &quot;View Details&quot; to the version without model</li>
        <li>Added the logo to the product image and allows the user to update the image from logo dropdown</li>
    </ul>

</body> -->

<body>
    <?php
    $changelogHtml = file_get_contents('./changelog.html');
    echo $changelogHtml

    ?>
</body>

</html>
<style>
    @font-face {
        font-family: inter;
        src: url(./fonts/whitneysemibold.otf);
    }

    body {
        margin: 0px;
        padding: 40px;
        color: #2f3941;
        font-family: inter;
        /* background-color: #FFFFFF; */
        background-color: #ffffff;
        background-image: linear-gradient(312deg, #ffffff 0%, #fffff0 100%);

        /* background-image: linear-gradient(0deg, #0d0e0e 27%, #5e5e6a 100%); */
        background-size: cover;
        background-position: center center;
        background-attachment: fixed;
    }

    /* li {
        font-family: monospace;
    } */

    code {
        font-family: monospace;
        /* font-size: xx-large; */
        background-color: #FFDEE9;
    }

    mark {
        background-color: #B5FFFC;
    }
</style>