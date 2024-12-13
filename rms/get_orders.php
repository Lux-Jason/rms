<?php
// get_orders.php

include 'connectdb.php'; // Make sure this file contains your DB connection logic

$query = "SELECT order_id, order_time, order_amount, dish_image, dish_name FROM `order` WHERE user_id = ?"; // Adjust the query based on your schema
$userId = 1; // Example user ID, replace with actual user ID from session or request

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orders = array();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
?>