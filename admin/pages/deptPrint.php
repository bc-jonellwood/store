<?php
include('DBConn.php');
?>
<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {

    header("location: pages/sign-in.php");

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Reports</title>
    <script>
        // This function fires a request to the PHP script that marks all items in a department as `Ordered`
        async function markOrdered(deptNum) {
            await fetch('./change-all-to-ordered.php?dept_id=' + deptNum)
        }
    </script>
</head>

<?php
$DEPTID = filter_input(INPUT_GET, 'demptid', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$DEPT = $_SESSION["empNumber"];
$ROLE = $_SESSION["role_name"];
// QUERY TO FIND THE TOTAL NUMBER OF ITEMS TO BE ORDERED
if ($ROLE === "Administrator") {
    $sql = "SELECT SUM(quantity) as TOTALQNT, dr.dep_name
    FROM uniform_orders.ord_ref o
    JOIN dep_ref dr on o.department = dr.dep_num
    WHERE status = 'Approved' AND dep_num = $DEPTID";
} else {
    $sql = "SELECT SUM(quantity) as TOTALQNT, dr.dep_name
    FROM uniform_orders.ord_ref o
    JOIN dep_ref dr on o.department = dr.dep_num
WHERE (status = 'Approved' AND dr.dep_head OR dr.dep_assist = $DEPT)";
}
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $TOTALQNT = $row["TOTALQNT"];
        $DEPTNAME = $row['dep_name'];
    }
} else {
    // echo "No Pending Requests";
}
?>


<body onload='markOrdered(<?php echo $DEPTID ?>)'>
    <div id="container">
        <div id="header">
            <img src="../assets/img/bcg-hz (6).png" width="40%" alt="">
            </br>
            _______________________________________________________________________________________
        </div>
        <div id="wrapper">
            <div id="content">
                <h2><strong><?php echo $DEPTNAME ?></strong></h2>
                <h2><strong>Total Approved Items to be ordered: <?php echo $TOTALQNT ?></strong></h2>
                <?php
                echo "<p>";
                echo "<table>";
                echo "<tr>";
                echo "<th width=20%>Style Number: </th>";
                echo "<th width=20%>Color: ";
                echo "<th width=20%>Size: </th>";
                echo "<th width=20%>Quantity: ";
                echo "<th width=20%>Price Per Item: ";
                echo "</tr>";


                // <!-- QUERTY FOR PRODUCT SKUS ORDERED BY NUMBER OF ITEMS -->
                if ($ROLE === "Administrator") {
                    $sql = "SELECT o.product_code, o.size_name, o.color_id, SUM(o.quantity) as TALLY, o.product_price
                    FROM uniform_orders.ord_ref o
                    JOIN emp_ref e on o.emp_id = e.empNumber
                    JOIN dep_ref dr on o.department = dr.dep_num
                    WHERE status = 'Approved' AND e.deptNumber = $DEPTID GROUP BY product_code, size_name, color_id ORDER BY SUM(o.quantity) DESC";
                } else {
                    $sql = "SELECT o.product_code, o.size_name, o.color_id, SUM(o.quantity) as TALLY, o.product_price
                    FROM uniform_orders.ord_ref o
                    JOIN emp_ref e on o.emp_id = e.empNumber
                    JOIN dep_ref dr on o.department = dr.dep_num
                    WHERE status = 'Approved' AND e.deptNumber = 41517 AND dr.dep_head OR dr.dep_assist = 3246 GROUP BY product_code, size_name, color_id ORDER BY SUM(o.quantity) DESC";
                }
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // MY VARIABLES NEEDED TO DISPLAY REPORT
                        $PC = $row["product_code"];
                        $SN = $row["size_name"];
                        $CI = $row["color_id"];
                        $TALLY = $row["TALLY"];
                        $PRICE = $row["product_price"];
                        $PRICE = number_format($PRICE, 2);

                        echo "<p>";
                        echo "<table>";
                        echo "<td width=20%>$PC</td>";
                        echo "<td width=20%>$CI</td>";
                        echo "<td width=20%>$SN</td>";
                        echo "<td width=20%>$TALLY</td>";
                        echo "<td width=20%>$$PRICE</td>";
                    }
                }

                ?>

                </table>
                </p>
                <strong>
                    <center>__________
                        DETAILED EMPLOYEE INFORMATION
                        __________</center>
                </strong>


                <?php
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                if ($ROLE === "Administrator") {
                    $sql = "SELECT o.size_name, o.status, o.color_id, e.empName, o.emp_id, o.product_name, o.product_price, o.created, o.requested_by_name, 
                    o.quantity, o.product_name, e.email, e.deptName, e.deptNumber, e.empNumber, o.line_item_total, o.order_id, o.order_details_id, o.customer_id, o.created, o.logo, o.product_code
                    FROM uniform_orders.ord_ref o
                    join emp_ref e on o.emp_id = e.empNumber
                    JOIN dep_ref dr on o.department = dr.dep_num
                    WHERE status = 'Approved' and e.deptNumber = $DEPTID";
                } else {
                    $sql = "SELECT o.size_name, o.status, o.color_id, e.empName, o.emp_id, o.product_name, o.product_price, o.created, o.requested_by_name,
                    o.quantity, o.product_name, e.email, e.deptName, e.deptNumber, e.empNumber, o.line_item_total, o.order_id, o.order_details_id, o.customer_id, o.created, o.logo, o.product_code
                    FROM uniform_orders.ord_ref o
                    join emp_ref e on o.emp_id = e.empNumber
                    JOIN dep_ref dr on o.department = dr.dep_num
                    WHERE (status = 'Approved' AND dr.dep_head OR dr.dep_assist = $DEPT)";
                }
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // NEED THE INFORMATION FOR PRINTING
                        $SIZE = $row["size_name"];
                        $COLOR = $row["color_id"];
                        $EMPNAME = $row["empName"];
                        $EMPLOYEEID = $row["emp_id"];
                        $PRODUCTNAME = $row["product_name"];
                        $PRODUCTPRICE = $row["product_price"];
                        $REQUESTDATE = $row["created"];
                        $QNT = $row["quantity"];
                        $PRODUCTNAME = $row["product_name"];
                        $GRANDTOTAL = ($QNT * $PRODUCTPRICE);
                        $EMAIL = $REQUESTDATE = $row["email"];
                        $DEPTNAME = $row["deptName"];
                        $DEPTNUMBER = $row["deptNumber"];
                        $EMPID = $row["empNumber"];
                        $ORDERID = $row["order_id"];
                        $ORDERDETAILSID = $row['order_details_id'];
                        $TOT = $row["line_item_total"];
                        $CUSTOMERID = $row["customer_id"];
                        $DATE = $row["created"];
                        $STATUS = $row["status"];
                        $LOGO = $row["logo"];
                        $CODE = $row["product_code"];
                        $REQUESTOR = $row["requested_by_name"];

                        echo "<p>";
                        echo "<table>";
                        echo "<tr>";
                        echo "<th width=50%>$DATE</th>";
                        echo "<th width=50%>$EMPNAME";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Employee ID</td>";
                        echo "<td>$EMPLOYEEID</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Contact for Order</td>";
                        echo "<td>$REQUESTOR</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Number of Items</td>";
                        echo "<td>$QNT</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Item being Requested</td>";
                        echo "<td>$PRODUCTNAME</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Size being Requested</td>";
                        echo "<td>$SIZE</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Color being Requested</td>";
                        echo "<td>$COLOR</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Product Code</td>";
                        echo "<td>$CODE</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Logo for Item</td>";
                        echo "<td><img src='../../../$LOGO'</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Department</td>";
                        echo "<td>$DEPTNAME</td>";
                        echo "</tr>";
                        echo "<tr><td><hr></td><td><hr></td></tr>";
                    }
                }
                ?>
                </table>
                </p>
            </div>
        </div>

        <script type="text/javascript">
            //-->
        </script>
        <div id="footer">
            <center>
                <button class="hide-from-printer pulse-button" onclick="printpage()" type="submit" value="Print" role="button" id="btn">Click Here to Print Report</button>
            </center>
            <script>
                function printpage() {
                    window.print();
                }
            </script>
        </div>
    </div>
</body>

</html>

<style type="text/css">
    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
    }

    img {
        width: 100% !important;
        height: auto;
    }

    body {
        color: #292929;
        font: 90% Roboto, Arial, sans-serif;
        font-weight: 300;
    }

    p {
        padding: 0 10px;
        line-height: 1.8;
    }

    ul {
        padding-inline-start: 15px;
    }

    ul li {
        padding-right: 10px;
        line-height: 1.6;
        padding-inline-start: -25px;
    }

    h3 {
        padding: 5px 20px;
        margin: 0;
    }

    div#header {
        position: relative;
    }

    div#header h1 {
        height: 80px;
        line-height: 80px;
        margin: 0;
        padding-left: 10px;
        background: #e0e0e0;
        color: #292929;
    }

    div#header a {
        position: absolute;
        right: 0;
        top: 23px;
        padding: 10px;
        color: #006;
    }

    div#navigation {
        background: white;
    }

    div#navigation li {
        list-style: none;
    }

    div#extra {
        background: white;
    }

    div#footer {
        background: white;
    }

    div#footer p {
        padding: 20px 10px;
    }

    div#container {
        width: 700px;
        margin: 0 auto;
    }

    div#content {
        float: right;
        width: 700px;
    }

    div#navigation {
        float: left;
        width: 200px;
    }

    div#extra {
        float: left;
        clear: left;
        width: 200px;
    }

    div#footer {
        clear: both;
        width: 100%;
    }

    /* TABLE CSS */
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }

    td.no-padding {
        border: none;
        background-color: white;
    }

    th.no-padding {
        border: none;
        background-color: white;
    }

    td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    /* BUTTON CSS STARTS HERE */

    .noprint {
        display: none;
    }

    @media print {

        /* hide the print button when printing */
        .hide-from-printer {
            display: none;
        }
    }

    .print {
        visibility: visible;
    }

    /* Button resets and style */
    button {
        margin: 15px auto;
        font-family: "Montserrat";
        font-size: 28px;
        color: #ffffff;
        cursor: pointer;
        border-radius: 100px;
        padding: 15px 20px;
        border: 0px solid #000;
    }

    /* Initiate Auto-Pulse animations */
    button.pulse-button {
        animation: borderPulse 1000ms infinite ease-out, colorShift 10000ms infinite ease-in;
    }

    /* Initiate color change for pulse-on-hover */
    button.pulse-button-hover {
        animation: colorShift 10000ms infinite ease-in;
    }

    /* Continue animation and add shine on hover */
    button:hover,
    button:focus {
        animation: borderPulse 1000ms infinite ease-out, colorShift 10000ms infinite ease-in, hoverShine 200ms;
    }

    /* Declate color shifting animation */
    @keyframes colorShift {

        0%,
        100% {
            background: #0045e6;
        }

        33% {
            background: #fb3e3e;
        }

        66% {
            background: #0dcc00;
        }
    }

    /* Declare border pulse animation */
    @keyframes borderPulse {
        0% {
            box-shadow: inset 0px 0px 0px 5px rgba(255, 255, 255, .4), 0px 0px 0px 0px rgba(255, 255, 255, 1);
        }

        100% {
            box-shadow: inset 0px 0px 0px 3px rgba(117, 117, 255, .2), 0px 0px 0px 10px rgba(255, 255, 255, 0);
        }
    }

    /* Declare shine on hover animation */
    @keyframes hoverShine {
        0% {
            background-image: linear-gradient(135deg, rgba(255, 255, 255, .4) 0%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, 0) 100%);
        }

        50% {
            background-image: linear-gradient(135deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, .4) 50%, rgba(255, 255, 255, 0) 100%);
        }

        100% {
            background-image: linear-gradient(135deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0) 50%, rgba(255, 255, 255, .4) 100%);
        }
    }
</style>