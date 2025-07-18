<?php
require 'vendor/autoload.php';
use Aws\S3\S3Client;
class S3_model extends CI_Model 
{

	public function upload_file_to_digital_ocean_using_path___($path, $loc, $s3_file_name='', $patient_id=null)
{
    try {
        // Initialize response array
        $response = ['check' => FALSE, 'error' => '', 'download_url' => ''];
        
        // Validate input file
        if (!file_exists($path)) {
            $response['error'] = 'File does not exist: ' . $path;
            return $response;
        }
        
        if (!is_readable($path)) {
            $response['error'] = 'File is not readable: ' . $path;
            return $response;
        }
        
        $path = str_replace('\\', '/', $path);
        
        // Create S3 client with error handling
        $client = new S3Client([
            'version' => 'latest',
            'region'  => 'fra1',
            'endpoint' => 'https://stardental.fra1.digitaloceanspaces.com',
            'use_path_style_endpoint' => false,
            'credentials' => [
                'key'    => 'allbuckets-1752049433202',
                'secret' => 'khmN7cj+LjWbhq7yaPimpENcTJwSkEEtcsh8gI+fxDk',
            ],
            'suppress_php_deprecation_warning' => true,
            'http' => [
                'verify' => false,  // Disable SSL verification for testing
                'timeout' => 60,
                'connect_timeout' => 30,
            ],
            'debug' => false,  // Set to true for debugging
        ]);

        // Build bucket folder path
        $subdomain = isset($_SERVER['HTTP_HOST']) ? explode('.', $_SERVER['HTTP_HOST'])[0] : 'default';
        
        if ($loc == 'scans' && !empty($patient_id)) {
            $bucket_folder = $subdomain . '/' . $loc . '/' . $patient_id . '/';
        } else {
            $bucket_folder = $subdomain . '/' . $loc . '/';
        }
        
        $bucket = 'dental-files';
        $file_name = $s3_file_name ?: basename($path);
        $key = $bucket_folder . $file_name;
        
        // Get file MIME type safely
        $mime_type = 'application/octet-stream'; // Default
        if (function_exists('mime_content_type')) {
            $detected_mime = mime_content_type($path);
            if ($detected_mime) {
                $mime_type = $detected_mime;
            }
        } else {
            // Fallback for common file types
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime_types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
            $mime_type = $mime_types[$extension] ?? 'application/octet-stream';
        }
        
        // Upload parameters
        $upload_params = [
            'Bucket' => $bucket,
            'Key'    => $key,
            'SourceFile' => $path,
            'ACL'    => 'public-read',
            'ContentDisposition' => 'inline; filename="' . basename($path) . '"',
            'ContentType' => $mime_type,
        ];
        
        // Remove ContentEncoding as it's likely causing issues
        // 'ContentEncoding' => 'base64' // This is wrong for file uploads
        
        // Debug information
        error_log("Uploading file: " . $path);
        error_log("Bucket: " . $bucket);
        error_log("Key: " . $key);
        error_log("MIME Type: " . $mime_type);
        
        // Perform the upload
        $result = $client->putObject($upload_params);
        
        // Check result
        if ($result && isset($result['ObjectURL'])) {
            $response['check'] = TRUE;
            $response['download_url'] = $result['ObjectURL'];
            error_log("Upload successful: " . $result['ObjectURL']);
            return $response;
        } else {
            $response['error'] = 'Upload failed - no ObjectURL returned';
            error_log("Upload failed - no ObjectURL returned");
            return $response;
        }
        
    } catch (S3Exception $e) {
        $response['check'] = FALSE;
        $response['error'] = 'S3 Error: ' . $e->getMessage();
        error_log("S3 Exception: " . $e->getMessage());
        error_log("S3 Error Code: " . $e->getAwsErrorCode());
        return $response;
        
    } catch (Exception $e) {
        $response['check'] = FALSE;
        $response['error'] = 'General Error: ' . $e->getMessage();
        error_log("General Exception: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return $response;
    }
}
	public function upload_file_to_digital_ocean_using_path($path, $loc, $s3_file_name='',$patient_id=null)
	{
		$path = str_replace('\\', '/', $path);
		$client = new S3Client([
			'version' => 'latest',
			'region'  => 'fra1',
			'endpoint' => 'https://stardental.fra1.digitaloceanspaces.com',
			'use_path_style_endpoint' => false,
			'credentials' => [
				'key'    => 'DO00LMQZ82XCHG4D7L9D',
				'secret' => 'khmN7cj+LjWbhq7yaPimpENcTJwSkEEtcsh8gI+fxDk',
			],
			'suppress_php_deprecation_warning' => true,  // Suppress PHP deprecation warnings
		]);
		// https://stardental.fra1.digitaloceanspaces.com
		$subdomain = isset($_SERVER['HTTP_HOST']) ? explode('.', $_SERVER['HTTP_HOST'])[0] : '';

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