<?php

use App\Core\Security;

?>
<?php
$flashMessages = [];
if (!empty($flash)) {
    $flashMessages = is_array($flash) && isset($flash[0]) ? $flash : [$flash];
}

include __DIR__ . '/../../partials/ui/toasts.php';
?>
