<?php 
namespace App\Libraries;

require APPPATH. "../vendor/autoload.php";

use Aws\S3\S3Client;
use Exception;

class Aws3{
	 private static $_s3Obj = NULL;
	public static $instance = NULL; 
	public function __construct(){

		$this->region = getenv('amazons3.region');
		$this->access_key = getenv('amazons3.access_key');
		$this->secret_key = getenv('amazons3.secret_key');

		self :: $_s3Obj = S3Client::factory([
			'version' => 'latest',
			'region' => $this->region,
			'credentials' => [
				'key'    => $this->access_key ,
				'secret' => $this->secret_key,
			],
		]);
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