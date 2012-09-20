<?php

$json['apc_enabled'] = function_exists('apc_fetch'); 

if(isset($_GET['APC_UPLOAD_PROGRESS']) && !empty($json['apc_enabled']))
{
    $status = apc_fetch('upload_' . $_GET['APC_UPLOAD_PROGRESS']);
    
    if ($status)
    {
        $json['percent'] = (!empty($status['total']))
            ? $status['current'] * 100 / $status['total']
            : 0;
    }
}

echo json_encode($json);

?>