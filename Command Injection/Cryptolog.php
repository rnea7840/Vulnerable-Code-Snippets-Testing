<?php
include("config.php");
require_once("kontrol.php");

// Fetch POST data
$opt = $_POST['opt'];
$lsid = $_POST['lsid'];
$sharetype = $_POST['lssharetype'];
$remoteaddress = $_POST['lsremoteaddress'];
$sharefolder = $_POST['lssharefolder'];
$user = $_POST['lsuser'];
$pass = $_POST['lspass'];
$domain = $_POST['lsdomain'];

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
    case 'mount':
        if ($opt == 'mount') {
            cLogshares::fMountFileshareOnly($dbConn, $lsid, $sharetype);
        }
        $path = "/mnt/logsource_".$lsid."_".$sharetype;
        // Apply htmlspecialchars at the point of output
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
