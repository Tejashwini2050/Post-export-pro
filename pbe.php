<?php
require_once('tcpdf/tcpdf.php');

include 'components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

$check_orders = $conn->prepare("SELECT * FROM `orders` WHERE pid IN (SELECT pid FROM `products` WHERE adminid=?)");
$check_orders->execute([$admin_id]);

if($check_orders->rowCount() > 0) {
    // Create a new PDF instance
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Your Name');
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('Order Details PDF');

    // Add a page
    $pdf->AddPage();

    // Define CSS styles for the table
    $html = '<style type="text/css">
                table {
                    border-collapse: collapse;
                    width: 100%;
                    margin-bottom: 20px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                    font-size: 12px;
                }
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #f7f7f7;
                }
                tr:hover {
                    background-color: #e0e0e0;
                }
                table th:first-child,
                table td:first-child {
                    width: 5%;
                }
                table th:nth-child(2),
                table td:nth-child(2) {
                    width: 15%;
                }
                table th:nth-child(3),
                table td:nth-child(3) {
                    width: 20%;
                }
                table th:nth-child(4),
                table td:nth-child(4) {
                    width: 15%;
                }
                table th:nth-child(5),
                table td:nth-child(5),
                table th:nth-child(6),
                table td:nth-child(6),
                table th:nth-child(7),
                table td:nth-child(7) {
                    width: 10%;
                }
                table th:last-child,
                table td:last-child {
                    width: 10%;
                }
            </style>';

    $html .= '<table>
                <tr>
                    <th>Bill ID</th>
                    <th>Exporter Name</th>
                    <th>Exporter Address</th>
                    <th>Consignee Name</th>
                    <th>Consignee Address</th>
                    
                    <th>Description of Goods</th>
                    <th>Quantity</th>
                    <th>Value</th>
                    <th>Total Amount</th>
                </tr>';

    // Fetch and display data in the PDF
    while($row = $check_orders->fetch(PDO::FETCH_ASSOC)) {
        
        $select_product = $conn->prepare("SELECT details FROM products WHERE pid =?");
        $select_product->execute([$row['pid']]);
        $product = $select_product->fetch(PDO::FETCH_ASSOC);

        $select_bp = $conn->prepare("SELECT fname, lname, address FROM business_profile WHERE adminid IN (SELECT adminid FROM products WHERE pid = ?)");
        $select_bp->execute([$row['pid']]);
        $business_profile = $select_bp->fetch(PDO::FETCH_ASSOC);

        // Add data to the table
        $html .= '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $business_profile['fname'] . ' ' . $business_profile['lname'] . '</td>                     
                    <td>' . $business_profile['address'] . '</td>
                    <td>' . $row['name'] . '</td>
                    <td>' . $row['address'] . '</td>
                   
                    <td>' . $product['details'] . '</td>
                    <td>' . $row['quantity'] . '</td>
                    <td>' . $row['price'] . '</td>
                    <td>' . $row['total_price'] . '</td>
                  </tr>';
    }

    // Close the HTML table
    $html .= '</table>';

    // Add HTML content to the PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Output the PDF to the browser
    $pdf->Output('order_details.pdf', 'I');
} else {
    // No orders found message
    echo '<p>No postal bills of exports found.</p>';
}
?>