<?php
// delete_address.php
require_once 'db.php';

if(isset($_POST['address_id'])) {
    $address_id = $_POST['address_id'];

    $db = new UniqueDatabase();
    $pdo = $db->connect();

    $stmt = $pdo->prepare('DELETE FROM delivery_addresses WHERE id = :address_id');
    $stmt->bindParam(':address_id', $address_id);

    if($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>