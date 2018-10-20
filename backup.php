<?php
	// Includes
	require 'vendor/autoload.php';


	$dotenv = new Dotenv\Dotenv(__DIR__);
	$dotenv->load();

	use Aws\S3\S3Client;
	use Aws\S3\MultipartUploader;
	use Aws\Exception\MultipartUploadException;

	// Config

	$auth = base64_encode(getenv('CPANEL_USERNAME') . ':' . getenv('CPANEL_PASSWORD'));
	$domain = getenv('CPANEL_DOMAIN');

	$homeDirecotryDomainName = getenv('HOMEDIRECTORY_DOMAINNAME');
	$mysqlDatabase = getenv('MYSQL_DATABASE');

   	$s3Client = new Aws\S3\S3Client([
		'version' => 'latest',
		'region' => getenv('S3_REGION')
	]);

	$s3Bucket = getenv('S3_BUCKET');
	$s3KeyPrefix = getenv('S3_KEY_PREFIX');

	$backups = array(
		array(
			'filename' => 'home_directory.tar.gz',
			'url' => $domain . '/getbackup/backup-' . $homeDirecotryDomainName . '.tar.gz'
		),
		array(
			'filename' => 'database.sql.gz',
			'url' => $domain . '/getsqlbackup/' . $mysqlDatabase . '.sql.gz'
		)
	);


	$options = array(
	  'http' => array(
	    'header'  => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Basic $auth\r\n",
	    'method'  => 'GET'
	  ),
	  'ssl' => array(
	    'verify_peer' => false,
	    'verify_peer_name' => false,
	    'allow_self_signed' => true
	  )
	);

	$context = stream_context_create($options);

	$timestamp = time();

	// Start downloading and uploading each backup file
	foreach ($backups as $backup) {
		$resource = fopen($backup['url'], 'r', false, $context);
		$key = $s3KeyPrefix . $timestamp . '/' . $backup['filename'];

		$uploader = new MultipartUploader($s3Client, $resource, [
			'bucket' => $s3Bucket,
			'key'    => $key,
		]);

		try {
			$result = $uploader->upload();
			echo "Upload complete, key: {$key}\n";

		} catch (MultipartUploadException $e) {
		    echo $e->getMessage() . "\n";
		}
	}

?>