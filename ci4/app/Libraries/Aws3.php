<?php 
namespace App\Libraries;

require APPPATH. "../vendor/autoload.php";

use Aws\S3\S3Client;
use Exception;

class Aws3{
	 private static $_s3Obj = NULL;
	public static $instance = NULL;
	public function __construct(){

		// Get S3/MinIO config from CodeIgniter config
		$s3config = config("AmazonS3");

		$this->region = $s3config->region ?? 'us-east-1';
		$this->access_key = $s3config->access_key;
		$this->secret_key = $s3config->secret_key;
		$this->endpoint = $s3config->endpoint ?? '';
		$this->use_path_style = $s3config->use_path_style ?? false;

		$config = [
			'version' => 'latest',
			'region' => $this->region,
			'credentials' => [
				'key'    => $this->access_key ,
				'secret' => $this->secret_key,
			],
		];

		// Add MinIO/S3-compatible endpoint if configured
		if (!empty($this->endpoint)) {
			$config['endpoint'] = $this->endpoint;
			$config['use_path_style_endpoint'] = $this->use_path_style;
		}

		self :: $_s3Obj = S3Client::factory($config);
	}	

	public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance =  new self();
        }

        return self::$instance;
    }
	
	public function addBucket($bucketName){
		$result = self :: $_s3Obj->createBucket(array(
			'Bucket'=>$bucketName,
			'LocationConstraint'=> $this->region));
		return $result;	
	}
	public static function sendFile($bucketName, $filename){


		self::getInstance();
		try{
			$result = self :: $_s3Obj->putObject(array(
				'Bucket' => $bucketName,
				'Key' => $filename['name'],
				'SourceFile' => $filename['tmp_name'],
				'ACL' => 'public-read'
		));
		}catch (Exception $ex) {
			echo $ex->getMessage(); die;
		}
		
	
		return $result['ObjectURL']."\n";
	}

	public static function deleteObject($bucket, $url ){
		self::getInstance();
		return self :: $_s3Obj->deleteObject([
			'Bucket' => $bucket,
			'Key'    => $url
		]);
	}
	public static function getObjectInfo($bucket, $url){
		try{
			self :: getInstance();
			$result = self :: $_s3Obj->getObject([
				'Bucket' => $bucket,
				'Key'    => $url
			]);
			 return $result['ContentType'];
		}catch (Exception  $e) {
			return false;
		}
	}	
 }