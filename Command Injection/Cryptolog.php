<?php
include("config.php");
require_once("kontrol.php");

// Fetch POST data and sanitize
$opt = htmlspecialchars($_POST['opt'], ENT_QUOTES, 'UTF-8');
$lsid = htmlspecialchars($_POST['lsid'], ENT_QUOTES, 'UTF-8');
$sharetype = htmlspecialchars($_POST['lssharetype'], ENT_QUOTES, 'UTF-8');
$remoteaddress = htmlspecialchars($_POST['lsremoteaddress'], ENT_QUOTES, 'UTF-8');
$sharefolder = htmlspecialchars($_POST['lssharefolder'], ENT_QUOTES, 'UTF-8');
$user = htmlspecialchars($_POST['lsuser'], ENT_QUOTES, 'UTF-8');
$pass = htmlspecialchars($_POST['lspass'], ENT_QUOTES, 'UTF-8');
$domain = htmlspecialchars($_POST['lsdomain'], ENT_QUOTES, 'UTF-8');

$dbConn = mysql_connect(DB_HOST, DB_USER, DB_PASS);
if (!$dbConn) die("Out of service");
mysql_select_db(DB_DATABASE, $dbConn) or die("Out of service");
include("classes/logshares_class.php");

switch($opt) {
    case 'del':
        cLogshares::fDeleteFileshareDB($dbConn, $lsid);
        break;
    case 'add':
        cLogshares::fAddFileshareDB($dbConn, $sharetype, $remoteaddress, $sharefolder, $user, $pass, $domain);
        break;
    case 'check':
        $path = "/mnt/logsource_".$lsid."_".$sharetype;
        echo htmlspecialchars(cLogshares::fTestFileshare($path), ENT_QUOTES, 'UTF-8');
        break;
    case 'mount':
        cLogshares::fMountFileshareOnly($dbConn, $lsid, $sharetype);
        $path = "/mnt/logsource_".$lsid."_".$sharetype;
        echo htmlspecialchars(cLogshares::fTestFileshare($path), ENT_QUOTES, 'UTF-8');
        break;
}

function fTestFileshare($sharefolder)
{
    // Sanitize the input to prevent command injection
    $safe_sharefolder = escapeshellarg($sharefolder);
    $output = shell_exec('sudo /opt/cryptolog/scripts/testmountpoint.sh ' . $safe_sharefolder);
    return trim($output);
}
?>
