<?php
$c = file_get_contents('inde.html');
preg_match('/<style>(.*?)<\/style>/s', $c, $m);
file_put_contents('resources/css/admin.css', $m[1] ?? '');
echo 'Done';
