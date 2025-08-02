<?php
$logData = file_get_contents("php://input");
file_put_contents("mpesa_callback_log.txt", $logData, FILE_APPEND);
?>