<?php 

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AmazonS3 extends BaseConfig
{
    /*
    |--------------------------------------------------------------------------
    | Use SSL
    |--------------------------------------------------------------------------
    |
    | Run this over HTTP or HTTPS. HTTPS (SSL) is more secure but can cause problems
    | on incorrectly configured servers.
    |
    */
    public $use_ssl = TRUE;

    /*
    |--------------------------------------------------------------------------
    | usr of s3
    |--------------------------------------------------------------------------
    |
    | Enable verification of the HTTPS (SSL) certificate against the local CA
    | certificate store.
    |
    */
    public $s3_url = "";

    /*
    |--------------------------------------------------------------------------
    | bucket name
    |--------------------------------------------------------------------------
    |
    | Enable verification of the HTTPS (SSL) certificate against the local CA
    | certificate store.
    |
    */
    public $bucket = "";

    /*
    |--------------------------------------------------------------------------
    | specific directory
    |--------------------------------------------------------------------------
    |
    | Enable verification of the HTTPS (SSL) certificate against the local CA
    | certificate store.
    |
    */
    public $s3_directory = "dev";


    /*
    |--------------------------------------------------------------------------
    | Verify Peer
    |--------------------------------------------------------------------------
    |
    | Enable verification of the HTTPS (SSL) certificate against the local CA
    | certificate store.
    |
    */
    public $verify_peer = TRUE;

    /*
    |--------------------------------------------------------------------------
    | Access Key
    |--------------------------------------------------------------------------
    |
    | Your Amazon S3 access key.
    |
    */
    public $access_key = '';

    /*
    |--------------------------------------------------------------------------
    | Secret Key
    |--------------------------------------------------------------------------
    |
    | Your Amazon S3 Secret Key.
    |
    */
    public $secret_key = '';

    /*
    |--------------------------------------------------------------------------
    | Use Enviroment?
    |--------------------------------------------------------------------------
    |
    | Get Settings from enviroment instead of this file? 
    | Used as best-practice on Heroku
    |
    */
    public $get_from_enviroment = TRUE;

    /*
    |--------------------------------------------------------------------------
    | Access Key Name
    |--------------------------------------------------------------------------
    |
    | Name for access key in enviroment
    |
    */
    public $access_key_envname = '';

    
    /*
    |--------------------------------------------------------------------------
    | Access Key Name
    |--------------------------------------------------------------------------
    |
    | Name for access key in enviroment
    |
    */
    public $secret_key_envname = '';
    
    /*
    |--------------------------------------------------------------------------
    | If get from enviroment, do so and overwrite fixed vars above
    |--------------------------------------------------------------------------
    |
    */
    public function __construct()
	{
		parent::__construct();
        
        if ( $this->get_from_enviroment ){
            
            $this->access_key = getenv('amazons3.access_key');
            $this->secret_key = getenv('amazons3.secret_key');
            $this->bucket = getenv('amazons3.bucket');
            $this->s3_directory = getenv('amazons3.s3_directory');
            $this->region = getenv('amazons3.region');
            $this->s3_url = 'https://s3-'.$this->region.'.amazonaws.com/';
            
        }
    }
}