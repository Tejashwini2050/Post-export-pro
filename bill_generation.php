<?php
require_once('tcpdf/tcpdf.php');

$mysqli = new mysqli('localhost', 'root', '', 'shop_db');

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

// Create a new PDF instance
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator('Your Name');
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Order Details PDF');

// Add a page
$pdf->AddPage();

// Query to fetch data from the 'orders' table
$query = "SELECT * FROM orders WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

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
        $html .= '<h1 style="text-align: center;">POST EXPORT PRO</h1>';
        $html .= '<h2 style="text-align: center;">Order Details</h2>';

$html .= '<table>
            <tr>
                <th>Order ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Product Name</th>   
                <th>Item Price</th>
                <th>Delivery Charge</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>Placed On</th>
            </tr>';

// Fetch and display data in the PDF
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderID = $row['id'];
        $name = $row['name'];
        $address = $row['address'];
        $pname = $row['pname'];
        $price = $row['price'];
        $tariff = $row['delivery_charge'];
        $total_price = $row['total_price'];
        $method = $row['method'];
        $placed_on = $row['placed_on'];

        // Add data to the table
        $html .= '<tr>
                    <td>' . $orderID . '</td>
                    <td>' . $name . '</td>                     
                    <td>' . $address . '</td>
                    <td>' . $pname . '</td>
                    <td>' . $price. '</td>
                    <td>' . $tariff . '</td>
                    <td>' . $total_price . '</td>
                    <td>' . $method . '</td>
                    <td>' . $placed_on . '</td>
                  </tr>';
    }
}

// Close the HTML table
$html .= '</table>';

// Add HTML content to the PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF to the browser
$pdf->Output('order_details.pdf', 'I');

// Close the database connection
$mysqli->close();
?>