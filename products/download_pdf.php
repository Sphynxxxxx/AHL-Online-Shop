<?php
session_start();
include '../connections/config.php';
require_once('../connections/vendor/tcpdf/tcpdf.php');

if (!isset($_SESSION['email'])) {
    header("Location: customer.php");
    exit();
}


if (!isset($_GET['order_reference'])) {
    header("Location: customer.php");
    exit();
}

$referenceNumber = $_GET['order_reference'];


$query = "
    SELECT 
        o.id AS order_id,
        o.reference_number,
        o.order_date,
        o.delivery_method,
        c.customer_name,
        c.email,
        c.contact_number,
        c.address,
        p.product_name,
        p.image AS product_image,
        od.quantity,
        od.price,
        o.total_price
    FROM 
        orders o
    JOIN 
        customers c ON o.customer_id = c.id
    JOIN 
        order_details od ON o.id = od.order_id
    JOIN 
        products p ON od.product_id = p.product_id
    WHERE 
        o.reference_number = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $referenceNumber);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows == 0) {
    header("Location: customer.php");
    exit();
}


$orderDetails = [];
$orderInfo = null;
while ($row = $result->fetch_assoc()) {
    if ($orderInfo === null) {
        $orderInfo = [
            'reference_number' => $row['reference_number'],
            'customer_name' => $row['customer_name'],
            'email' => $row['email'],
            'contact_number' => $row['contact_number'],
            'address' => $row['address'],
            'order_date' => $row['order_date'],
            'delivery_method' => $row['delivery_method'],
            'total_price' => $row['total_price']
        ];
    }
    
    $orderDetails[] = [
        'product_name' => $row['product_name'],
        'product_image' => $row['product_image'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}


$pdf = new TCPDF();
$pdf->AddPage();


$pdf->SetFont('helvetica', '', 12);


$pdf->Cell(0, 10, 'Order Confirmation', 0, 1, 'C');
$pdf->Ln(10);


$pdf->Cell(0, 10, 'Reference Number: ' . htmlspecialchars($orderInfo['reference_number']), 0, 1);
$pdf->Cell(0, 10, 'Order Date: ' . htmlspecialchars($orderInfo['order_date']), 0, 1);
$pdf->Cell(0, 10, 'Customer Name: ' . htmlspecialchars($orderInfo['customer_name']), 0, 1);
$pdf->Cell(0, 10, 'Email: ' . htmlspecialchars($orderInfo['email']), 0, 1);
$pdf->Cell(0, 10, 'Contact Number: ' . htmlspecialchars($orderInfo['contact_number']), 0, 1);
$pdf->Cell(0, 10, 'Address: ' . htmlspecialchars($orderInfo['address']), 0, 1);
$pdf->Cell(0, 10, 'Delivery Method: ' . ucfirst(htmlspecialchars($orderInfo['delivery_method'])), 0, 1);
$pdf->Ln(10);

// Order Items
$pdf->Cell(0, 10, 'Order Items:', 0, 1);
foreach ($orderDetails as $item) {
  
    $productImagePath = 'product_pics/' . $item['product_image'];
    if (file_exists($productImagePath)) {
        
        $pdf->Image($productImagePath, 10, $pdf->GetY(), 40, 40, '', '', '', true, 300, '', false, false, 0, false, false, false);
    } else {
        
        $pdf->Cell(40, 40, 'No Image', 1, 0, 'C');
    }

  
    $pdf->SetX(60); 

    $pdf->Cell(0, 10, 'Product: ' . htmlspecialchars($item['product_name']), 0, 1);
    $pdf->Cell(0, 10, 'Quantity: ' . htmlspecialchars($item['quantity']), 0, 1);
    $pdf->Cell(0, 10, 'Price: ₱' . number_format($item['price'], 2), 0, 1);
    $pdf->Cell(0, 10, 'Subtotal: ₱' . number_format($item['quantity'] * $item['price'], 2), 0, 1);
    $pdf->Ln(5);
}


$pdf->Cell(0, 10, 'Total Price: ₱' . number_format($orderInfo['total_price'], 2), 0, 1);


$pdf->Output('order_details.pdf', 'D');

$stmt->close();
$conn->close();
?>
