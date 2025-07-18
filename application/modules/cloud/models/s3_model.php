<?php
require 'vendor/autoload.php';
use Aws\S3\S3Client;
class S3_model extends CI_Model 
{

	
	public function upload_file_to_digital_ocean_using_path($path, $loc, $s3_file_name='',$patient_id=null)
	{
		$path = str_replace('\\', '/', $path);
		$client = new S3Client([
			'version' => 'latest',
			'region'  => 'fra1',
			'endpoint' => 'https://fra1.digitaloceanspaces.com',
			'use_path_style_endpoint' => false,
			'credentials' => [
				'key'    => 'allbuckets-1752049433202',
				'secret' => 'khmN7cj+LjWbhq7yaPimpENcTJwSkEEtcsh8gI+fxDk',
			],
			'suppress_php_deprecation_warning' => true,  // Suppress PHP deprecation warnings
		]);
// https://stardental.fra1.digitaloceanspaces.com
		$subdomain = isset($_SERVER['HTTP_HOST']) ? explode('.', $_SERVER['HTTP_HOST'])[0] : '';
		var_dump($subdomain);
		$bucket_folder = $subdomain . '/' . $loc . '/';

		if ($loc == 'scans' AND !empty($patient_id)) {
			$bucket_folder = $subdomain . '/' . $loc . '/' . $patient_id . '/';
		} else {
			$bucket_folder = $subdomain . '/' . $loc . '/';
		}
		$bucket = 'dental-files';

		$result = $client->putObject([
			'Bucket' => $bucket,
			'Key'    => $bucket_folder . ($s3_file_name ?: basename($path)),
			'SourceFile' => $path,
			'ACL'    => 'public-read',
			'ContentDisposition' => 'inline; filename=' . basename($path),
			'ContentType' => mime_content_type($path),
			'ContentEncoding' => 'base64'
		]);

		if($result)
		{
			$response['check'] = TRUE;
			$dowload_url = $result['ObjectURL'];
			$response['download_url'] = $dowload_url;
			return $response;
		}
		else
		{
			$response['check'] = FALSE;
			$response['error'] =  'Please upload an image that is at least 300px by 300px';
			return $response;
		}

		// unlink($path);
	}
}
?>