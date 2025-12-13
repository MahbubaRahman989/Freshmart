<?php
include "admin_auth.php";
include "db.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    mysqli_query($conn, "DELETE FROM contact_messages WHERE id = $id");
}

header("Location: admin_view_messages.php");
exit();
?>