<?php
try {
    $stacks = exec('/home/debian/docker/applications/deck/get_stacks.sh' . ' 27');
} catch (Exception $e) {
    exit('Exception reçue : ' . $e->getMessage());
}
$stacks = json_decode(substr($stacks, strlen('@json')), true);
foreach ($stacks as $stack) {
    $col[] = $stack['id'] . "(" . $stack['title'] . ")";
}
$cols = implode(', ', $col);
