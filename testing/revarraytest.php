<?php
$input  = array("php", 4.0, array("green", "red"));
print_r($input);
echo "<br />";
$result = array_reverse($input);

$result_keyed = array_reverse($input, true);

print_r($result_keyed);
?>