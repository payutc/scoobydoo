<?php

// Restriction de l'accéssibilité des cookies
$sessionPath = parse_url($CONF["scoobydoo_url"], PHP_URL_PATH);
session_set_cookie_params(0, $sessionPath);
session_start();

if(isset($_GET["logout"]))
{
    session_destroy();
    header("Location: ".$_SESSION['cas_url']."/logout?url=".$CONF['scoobydoo_url']);
    exit();
}
    
