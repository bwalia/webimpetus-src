<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Libraries\Aws3;

/*
 * @author Ashrafuzzaman Sujan
 */


class Amazon_s3_model extends Model
{


    protected $s3config;

    public function __construct()
    {
        parent::__construct();
        $this->s3config = config("AmazonS3");
    }  
    
    /*
     * function to upload
     * image at amazon s3
     */
    public function uploadFileToS3($tableName, $fileName, $fieldName)
    {

        $filePath = "";
        $returArray = array(
            "status" => false
        );

        $accessKey = $this->s3config->access_key;
        $secretKey = $this->s3config->secret_key;
        $s3Url = $this->s3config->s3_url;
        $bucket = $this->s3config->bucket;
        $directory = $this->s3config->s3_directory;
        if(!empty($directory)){
            $fileName = $directory."/".$fileName;
        }
        if (strlen($_FILES[$fieldName]['tmp_name']) > 0) {
            $file = $_FILES[$fieldName]['tmp_name'];
    
            // set authetication

            $newfilename = array(
                'name' => $fileName,
                'tmp_name'=> $_FILES[$fieldName]['tmp_name']
            );
            // $contentType = $this->getMimeType($file);
            $result =  Aws3::sendFile($bucket, $newfilename);
            if($result){
                $returArray['filePath'] = $result;
                $returArray['status'] = TRUE;
            }else{

                $returArray['status'] = FALSE;
            }
        }

        return $returArray;
    }

    public function doUpload ( $fieldName, $subDirectoryName)
    {
	    // all special character will be removed from file
        $fileinfo = $this->getFileExtension( $_FILES[$fieldName]['name'] );
        $filename = $fileinfo['filename'].'.'.$fileinfo['extension'];
        $time= time();
        $fileName = "{$subDirectoryName}/{$time}/" . preg_replace( '/[^A-Za-z0-9\-.]/', '', $filename );
	    
	    $data = $this->uploadFileToS3 ( '', $fileName, $fieldName );
	    
	    return $data;
    }

    function getFileExtension ( $url )
	{
	    $pieces = explode('/', $url);
	    $file['basename'] = $pieces[count($pieces)-1];
	    $pieces = explode('.', $file['basename']);
	    $file['extension'] = $pieces[count($pieces)-1];
	    $file['filename'] = str_replace('.'.$file['extension'], '', $file['basename']);
	
	    return $file;
	}
    /*
     * function to delete file from amazon s3
     *
     */

    public function sendLocalFileToAWS($tempFile, $fileName, $fieldName)
    {

        $filePath = "";
        $returArray = array(
            "status" => false
        );

        $bucket = $this->s3config->bucket;
        $directory = $this->s3config->s3_directory;

        if(!empty($directory)){
            $fileName = $directory."/".$fileName;
        }
            // set authetication

            $newfilename = array(
                'name' => $fileName,
                'tmp_name'=> $tempFile
            );
            // $contentType = $this->getMimeType($file);
            $result =  Aws3::sendFile($bucket, $newfilename);
            if($result){
                $returArray['filePath'] = $result;
                $returArray['status'] = TRUE;
            }else{

                $returArray['status'] = FALSE;
            }
        

        return $returArray;
    }

    public function deleteFileFromS3($tableName, $fieldName, $id = 0)
    {
        $returArray = array(
            "status" => false
        );
        $s3Url = $this->s3config->s3_url;
        $bucket = $this->s3config->bucket;
        $directory = $this->s3config->s3_directory;
        
        $builder = $this->db->table($tableName);
        $builder->select($fieldName);
        $builder->where('id', $id);
        $dataObj = $builder->get()->getRowObject();
        if (is_object($dataObj)) {
            
            if (! empty($dataObj->$fieldName)) {
                $fullFilePath = $dataObj->$fieldName;
                $returArray['filename'] = pathinfo($fullFilePath, PATHINFO_BASENAME);
                $returArray['filepath'] = $fullFilePath;
                $filePath = str_replace($s3Url.$bucket.'/', '', $fullFilePath);
                $returArray['status'] = Aws3 :: deleteObject($bucket, $filePath);
            }
        }
    
        return $returArray;
    }
    
    /*
     * function to delete file from amazon s3 using s3 uri
     */
    public function getS3DomainUrl ($s3Url, $bucket)
    {
        $s3Domain = str_replace('https://', '', $s3Url);
        $pieces = explode( '-' , $s3Domain );
        $s3 = $pieces[0];
        unset( $pieces[0] );
        $regionalDomain = implode('-', $pieces);
        $s3DomainUrl = 'https://' . $bucket . '.' . $s3 . '.' . $regionalDomain;
        return $s3DomainUrl;
    }

    public function deleteFileFromS3UsingUri ( $uri )
    {
        $retArr = array('status' => 0, 'msg' => dashboard_lang('_FILE_COULD_NOT_BE_DELETED_PLEASE_TRY_AGAIN'));
        $s3Url = $this->s3config->s3_url;
        $bucket = $this->s3config->bucket;
        $uri = trim($uri);

        $s3DomainUrl = $this->getS3DomainUrl($s3Url, $bucket);
        
        if ( stripos( $uri, $s3Url.$bucket ) !== false )
            $filePath = explode($s3Url.$bucket."/", $uri)[1];
        else if ( stripos( $uri, $s3DomainUrl ) !== false )
            $filePath = str_replace($s3DomainUrl , '', $uri);
        
        $result = Aws3 :: deleteObject($bucket, $filePath);
       
        if( $result ){
            $retArr = array('status' => 1, 'msg' => '');
            return $retArr;
        }
        return $retArr;
    }
    
    public function checkS3FileExists ( $uri ) 
    {
        $s3Url = $this->s3config->s3_url;
        $bucket = $this->s3config->bucket;
        $uri = trim($uri);
        $s3DomainUrl = $this->getS3DomainUrl($s3Url, $bucket);
        $directory = $this->s3config->s3_directory;
        if ( stripos( $uri, $s3Url.$bucket ) !== false )
            $uri = explode($s3Url.$bucket."/", $uri)[1];
        else if ( stripos( $uri, $s3DomainUrl ) !== false )
            $uri = str_replace( $s3DomainUrl, '', $uri);
        
        return Aws3::getObjectInfo($bucket, $uri);
    }

    public function  getMimeType($file) {
        // MIME types array
        $mimeTypes = array(
            "323"       => "text/h323",
            "acx"       => "application/internet-property-stream",
            "ai"        => "application/postscript",
            "aif"       => "audio/x-aiff",
            "aifc"      => "audio/x-aiff",
            "aiff"      => "audio/x-aiff",
            "asf"       => "video/x-ms-asf",
            "asr"       => "video/x-ms-asf",
            "asx"       => "video/x-ms-asf",
            "au"        => "audio/basic",
            "avi"       => "video/x-msvideo",
            "axs"       => "application/olescript",
            "bas"       => "text/plain",
            "bcpio"     => "application/x-bcpio",
            "bin"       => "application/octet-stream",
            "bmp"       => "image/bmp",
            "c"         => "text/plain",
            "cat"       => "application/vnd.ms-pkiseccat",
            "cdf"       => "application/x-cdf",
            "cer"       => "application/x-x509-ca-cert",
            "class"     => "application/octet-stream",
            "clp"       => "application/x-msclip",
            "cmx"       => "image/x-cmx",
            "cod"       => "image/cis-cod",
            "cpio"      => "application/x-cpio",
            "crd"       => "application/x-mscardfile",
            "crl"       => "application/pkix-crl",
            "crt"       => "application/x-x509-ca-cert",
            "csh"       => "application/x-csh",
            "css"       => "text/css",
            "dcr"       => "application/x-director",
            "der"       => "application/x-x509-ca-cert",
            "dir"       => "application/x-director",
            "dll"       => "application/x-msdownload",
            "dms"       => "application/octet-stream",
            "doc"       => "application/msword",
            "dot"       => "application/msword",
            "dvi"       => "application/x-dvi",
            "dxr"       => "application/x-director",
            "eps"       => "application/postscript",
            "etx"       => "text/x-setext",
            "evy"       => "application/envoy",
            "exe"       => "application/octet-stream",
            "fif"       => "application/fractals",
            "flr"       => "x-world/x-vrml",
            "gif"       => "image/gif",
            "gtar"      => "application/x-gtar",
            "gz"        => "application/x-gzip",
            "h"         => "text/plain",
            "hdf"       => "application/x-hdf",
            "hlp"       => "application/winhlp",
            "hqx"       => "application/mac-binhex40",
            "hta"       => "application/hta",
            "htc"       => "text/x-component",
            "htm"       => "text/html",
            "html"      => "text/html",
            "htt"       => "text/webviewhtml",
            "ico"       => "image/x-icon",
            "ief"       => "image/ief",
            "iii"       => "application/x-iphone",
            "ins"       => "application/x-internet-signup",
            "isp"       => "application/x-internet-signup",
            "jfif"      => "image/pipeg",
            "jpe"       => "image/jpeg",
            "jpeg"      => "image/jpeg",
            "jpg"       => "image/jpeg",
            "js"        => "application/x-javascript",
            "latex"     => "application/x-latex",
            "lha"       => "application/octet-stream",
            "lsf"       => "video/x-la-asf",
            "lsx"       => "video/x-la-asf",
            "lzh"       => "application/octet-stream",
            "m13"       => "application/x-msmediaview",
            "m14"       => "application/x-msmediaview",
            "m3u"       => "audio/x-mpegurl",
            "man"       => "application/x-troff-man",
            "mdb"       => "application/x-msaccess",
            "me"        => "application/x-troff-me",
            "mht"       => "message/rfc822",
            "mhtml"     => "message/rfc822",
            "mid"       => "audio/mid",
            "mny"       => "application/x-msmoney",
            "mov"       => "video/quicktime",
            "movie"     => "video/x-sgi-movie",
            "mp2"       => "video/mpeg",
            "mp3"       => "audio/mpeg",
            "mpa"       => "video/mpeg",
            "mpe"       => "video/mpeg",
            "mpeg"      => "video/mpeg",
            "mpg"       => "video/mpeg",
            "mpp"       => "application/vnd.ms-project",
            "mpv2"      => "video/mpeg",
            "ms"        => "application/x-troff-ms",
            "mvb"       => "application/x-msmediaview",
            "nws"       => "message/rfc822",
            "oda"       => "application/oda",
            "p10"       => "application/pkcs10",
            "p12"       => "application/x-pkcs12",
            "p7b"       => "application/x-pkcs7-certificates",
            "p7c"       => "application/x-pkcs7-mime",
            "p7m"       => "application/x-pkcs7-mime",
            "p7r"       => "application/x-pkcs7-certreqresp",
            "p7s"       => "application/x-pkcs7-signature",
            "pbm"       => "image/x-portable-bitmap",
            "pdf"       => "application/pdf",
            "pfx"       => "application/x-pkcs12",
            "pgm"       => "image/x-portable-graymap",
            "pko"       => "application/ynd.ms-pkipko",
            "pma"       => "application/x-perfmon",
            "pmc"       => "application/x-perfmon",
            "pml"       => "application/x-perfmon",
            "pmr"       => "application/x-perfmon",
            "pmw"       => "application/x-perfmon",
            "pnm"       => "image/x-portable-anymap",
            "pot"       => "application/vnd.ms-powerpoint",
            "ppm"       => "image/x-portable-pixmap",
            "pps"       => "application/vnd.ms-powerpoint",
            "ppt"       => "application/vnd.ms-powerpoint",
            "prf"       => "application/pics-rules",
            "ps"        => "application/postscript",
            "pub"       => "application/x-mspublisher",
            "qt"        => "video/quicktime",
            "ra"        => "audio/x-pn-realaudio",
            "ram"       => "audio/x-pn-realaudio",
            "ras"       => "image/x-cmu-raster",
            "rgb"       => "image/x-rgb",
            "rmi"       => "audio/mid",
            "roff"      => "application/x-troff",
            "rtf"       => "application/rtf",
            "rtx"       => "text/richtext",
            "scd"       => "application/x-msschedule",
            "sct"       => "text/scriptlet",
            "setpay"    => "application/set-payment-initiation",
            "setreg"    => "application/set-registration-initiation",
            "sh"        => "application/x-sh",
            "shar"      => "application/x-shar",
            "sit"       => "application/x-stuffit",
            "snd"       => "audio/basic",
            "spc"       => "application/x-pkcs7-certificates",
            "spl"       => "application/futuresplash",
            "src"       => "application/x-wais-source",
            "sst"       => "application/vnd.ms-pkicertstore",
            "stl"       => "application/vnd.ms-pkistl",
            "stm"       => "text/html",
            "svg"       => "image/svg+xml",
            "sv4cpio"   => "application/x-sv4cpio",
            "sv4crc"    => "application/x-sv4crc",
            "t"         => "application/x-troff",
            "tar"       => "application/x-tar",
            "tcl"       => "application/x-tcl",
            "tex"       => "application/x-tex",
            "texi"      => "application/x-texinfo",
            "texinfo"   => "application/x-texinfo",
            "tgz"       => "application/x-compressed",
            "tif"       => "image/tiff",
            "tiff"      => "image/tiff",
            "tr"        => "application/x-troff",
            "trm"       => "application/x-msterminal",
            "tsv"       => "text/tab-separated-values",
            "txt"       => "text/plain",
            "uls"       => "text/iuls",
            "ustar"     => "application/x-ustar",
            "vcf"       => "text/x-vcard",
            "vrml"      => "x-world/x-vrml",
            "wav"       => "audio/x-wav",
            "wcm"       => "application/vnd.ms-works",
            "wdb"       => "application/vnd.ms-works",
            "wks"       => "application/vnd.ms-works",
            "wmf"       => "application/x-msmetafile",
            "wps"       => "application/vnd.ms-works",
            "wri"       => "application/x-mswrite",
            "wrl"       => "x-world/x-vrml",
            "wrz"       => "x-world/x-vrml",
            "xaf"       => "x-world/x-vrml",
            "xbm"       => "image/x-xbitmap",
            "xla"       => "application/vnd.ms-excel",
            "xlc"       => "application/vnd.ms-excel",
            "xlm"       => "application/vnd.ms-excel",
            "xls"       => "application/vnd.ms-excel",
            "xlsx"      => "vnd.ms-excel",
            "xlt"       => "application/vnd.ms-excel",
            "xlw"       => "application/vnd.ms-excel",
            "xof"       => "x-world/x-vrml",
            "xpm"       => "image/x-xpixmap",
            "xwd"       => "image/x-xwindowdump",
            "z"         => "application/x-compress",
            "zip"       => "application/zip"
        );
    
        $fileArr = explode('.', $file);
        $extension = end($fileArr);
        if(isset($mimeTypes[$extension])){
            return $mimeTypes[$extension];
        }else{
            return "text/plain";
        }
         // return the array value
    }

    public function doUploadMultiple ( $fieldName, $subDirectoryName, $tempFIle, $fileName)
    {
	    // all special character will be removed from file
        $fileinfo = $this->getFileExtension( $fileName );
        $filename = $fileinfo['filename'].'.'.$fileinfo['extension'];
        $time= time();
        $fileName = "{$subDirectoryName}/{$time}/" . preg_replace( '/[^A-Za-z0-9\-.]/', '', $filename );
	    
	    $data = $this->uploadMultipleFileToS3 ( '', $fileName, $fieldName, $tempFIle );
	    
	    return $data;
    }

    public function uploadMultipleFileToS3( $tableName, $fileName, $fieldName, $tempFIle) 
    {

        $filePath = "";
        $returArray = array(
            "status" => false
        );

        $accessKey = $this->s3config->access_key;
        $secretKey = $this->s3config->secret_key;
        $s3Url = $this->s3config->s3_url;
        $bucket = $this->s3config->bucket;
        $directory = $this->s3config->s3_directory;
        if(!empty($directory)){
            $fileName = $directory."/".$fileName;
        }
        if (strlen($tempFIle) > 0) {
            $file = $tempFIle;
    
            // set authetication

            $newfilename = array(
                'name' => $fileName,
                'tmp_name'=> $tempFIle
            );
            // $contentType = $this->getMimeType($file);
            $result =  Aws3::sendFile($bucket, $newfilename);
            if($result){
                $returArray['filePath'] = $result;
                $returArray['status'] = TRUE;
            }else{

                $returArray['status'] = FALSE;
            }
        }

        return $returArray;
    }
    
}
