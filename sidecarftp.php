<?php
use Magento\Framework\App\Bootstrap;
require __DIR__.'/app/bootstrap.php';

/* FTP Account */
$ftp_host = 'ftp.getsidecar.com'; /* host */
$ftp_user_name = 'jjcommerce'; /* username */
$ftp_user_pass = 'AoOjzGp40bVX'; /* password */

/* Source File Name and Path */
$local_filerayallen = __DIR__.'/googlefeed/rayallen.txt';
$remote_filerayallen = 'rayallen.txt';

/* Source File Name and Path */
$local_filejjdog = __DIR__.'/googlefeed/jjdog.txt';
$remote_filejjdog = 'jjdog.txt';

/* Connect using basic FTP */
$connect_it = ftp_connect( $ftp_host );

/* Login to FTP */
$login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
$contents = ftp_nlist($connect_it, ".");

/* Download $remote_file and save to $local_file */

ftp_put( $connect_it,$remote_filerayallen ,$local_filerayallen , FTP_BINARY);
ftp_put( $connect_it,$remote_filejjdog ,$local_filejjdog , FTP_BINARY);
?>
