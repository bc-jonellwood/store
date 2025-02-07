<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {

    header("location: ../signin/signin.php");

    exit;
}
$uid = $_GET['uid'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Report</title>
</head>
<script>
function formatMoney(amt) {
    const dollars = new Intl.NumberFormat('us-EN', {
        style: 'currency',
        currency: 'USD'
    }).format(amt, )
}
async function getOrder(uid) {
    console.log(uid);
    await fetch('vendorReportGet.php?uid=' + uid)
        .then((response) => response.json())
        .then((data) => {
            console.log(data);
            const splitObjs = splitByVendorID(data);
            console.log(splitObjs);
            var container = document.getElementById('tables-container');
            container.innerHTML = ''; // Clear the container before appending new tables
            var html = '';
            const date = new Date();
            let day = date.getDate();
            let month = date.getMonth() + 1;
            let year = date.getFullYear();

            for (var i = 0; i < splitObjs.length; i++) {
                var html = '';
                html += `
                    <div id="container">
                        <div id="header">
                            <img src="../assets/img/bcg-hz (6).png" width="25%" alt="bcg logo">
                            <div class="deets">
                                <span>Department: ${splitObjs[i][0].dep_name}</span><br>
                                <span class='ponumber'> PO#: ${splitObjs[i][0].po_number} </span><br>
                                <span>Report Date: ${month}/${day}/${year} </span><br>
                                <span>Vendor: ${splitObjs[i][0].vendor}</span><br>
                                <span>Item Count: ${splitObjs[i].length} </span>
                            </div>
                        </div>
                    </div>            
                    <table>
                        <tr>
                            <th width=10%>Style #</th>
                            <th width=10%>Color</th>
                            <th width=10%>Size</th>
                            <th width=5%>Qty</th>
                            <th width=5%>Unit Price</th>
                            <th width=5%>Logo Fee</th>
                            <th width=5%>Tax</th>
                            <th width=10%>Total x/Tax</th>
                            <th width=20%>Logo</th>
                            <th>Employee Name</th>
                        </tr>`;

                for (var j = 0; j < splitObjs[i].length; j++) {
                    html += `<tr>    
                                <td>${splitObjs[i][j].product_code}</td>
                                <td>${splitObjs[i][j].color_id}</td>
                                <td>${splitObjs[i][j].size_name}</td>
                                <td>${splitObjs[i][j].quantity}</td>
                                <td>${new Intl.NumberFormat('us-EN', {style: 'currency', currency: 'USD'}).format(splitObjs[i][j].pre_tax_price)}</td>
                                <td>${new Intl.NumberFormat('us-EN', {style: 'currency', currency: 'USD'}).format(splitObjs[i][j].logo_fee)}</td>
                                <td>${new Intl.NumberFormat('us-EN', {style: 'currency', currency: 'USD'}).format(splitObjs[i][j].tax)}</td>
                                <td>${new Intl.NumberFormat('us-EN', {style: 'currency', currency: 'USD'}).format(splitObjs[i][j].line_item_total)}</td>
                                <td class='logo-img'><img src="../../${splitObjs[i][j].logo}" alt='logo' /></td>
                                
                                <td>${splitObjs[i][j].rf_first_name} ${splitObjs[i][j].rf_last_name} </td>
                                </tr>
                                <tr>
                                <td colspan='3'>${splitObjs[i][j].product_name}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>Department Name Placement: ${splitObjs[i][j].dept_patch_place}</td>
                            </tr>
                            `;
                }

                html += `</table>`;
                html += `<div class="pageBreak"> </div>`
                container.innerHTML += html; // Append the HTML for each table
            }
        });
}
getOrder('<?php echo $uid ?>');

function splitByVendorID(data) {
    const result = {};
    data.forEach(item => {
        const vendorID = item.vendor_id;

        if (!result[vendorID]) {
            // make an object IF we dont have one already
            result[vendorID] = [];
        }
        // push if we do
        result[vendorID].push(item)
    });
    return Object.values(result);
}

function createTable(tableData) {
    const table = document.createElement("table");
    const headers = Object.keys(tableData[0]);
    // Create a table header
    const headerRow = table.insertRow();
    headers.forEach(header => {
        const th = document.createElement('th');
        th.textContent = header;
        headerRow.appendChild(th);
    });

    // Create the table rows
    tableData.forEach(dataItem => {
        const row = table.insertRow();
        headers.forEach(header => {
            const cell = row.insertCell();
            cell.textContent = dataItem[header];
        });
    });
    console.log(table);
    return table;
}
</script>

<body>
    <!-- <div id="container"> -->
    <!-- <div id="header"> -->
    <!-- <img src="../assets/img/bcg-hz (6).png" width="50%" alt="bcg logo"> -->
    </br>

    <div id="tables-container"></div>
    <!-- </div> -->
</body>



</html>


<style>
table {
    margin-top: 30px;
    margin-bottom: 30px;
    margin-left: 20px;
    margin-right: 20px;
    border-collapse: collapse;
}

table,
th,
td {
    border: 1px solid black;
}

th,
td {
    padding: 10px;
}

th {
    background-color: #FDDF9530;
    /* background-color: #808080; */
}

colgroup {

    width: 225px;
}

#header {
    text-align: center;
}

.logo-img {
    background-color: #808080;
}

.logo-img img {
    display: flex;
    max-width: 150px;
    margin-left: auto;
    margin-right: auto;
}

.ponumber {
    font-family: monospace;

}

.pageBreak {
    clear: both;
    page-break-after: right;

}

caption {
    font-size: x-large;
    font-weight: bold;
    padding: 20px;
}

.test-hr {
    /* color: hotpink; */
    /* border: 10px solid blue; */
    height: 20px;

    --dot-bg: lightslategrey;
    --dot-color: white;
    --dot-size: 2px;
    --dot-space: 11px;
    background:
        linear-gradient(90deg, var(--dot-bg) calc(var(--dot-space) - var(--dot-size)), transparent 1%) center / var(--dot-space) var(--dot-space),
        linear-gradient(var(--dot-bg) calc(var(--dot-space) - var(--dot-size)), transparent 1%) center / var(--dot-space) var(--dot-space),
        var(--dot-color);

}

h2 {
    text-align: center;
}

#header {
    background-color: lightblue;
    padding: 10px;
    display: flex;
    justify-content: space-evenly;
    font-size: larger;
    font-weight: bold;
    border: 1px solid darkslateblue;
    margin-left: 20px;
    margin-right: 20px;
}

.deets {
    text-align: left;
}
</style>