<?php

use Aws\Common\Aws;
use Aws\S3\Exception\S3Exception;

class S3Helper
{

    private $_s3;
    private $_cdn;

    public static function objectExist($key)
    {
        $_cdn = Config::get("site.cdn.aws");

        $_s3 = Aws::factory(Config::get("aws"))->get('S3');
        $yeah = $_s3->doesObjectExist($_cdn['bucket'], $key);
        dd($yeah);

    }

    public static function putRemote($remote, $path = 'serve/', $name = 'tmp')
    {
        $remote = trim($remote);
        if ($remote == '') return null;

        $junk = explode('.', $remote);
        $ext = strtolower(end($junk));

        $uName = $name . ".{$ext}";
        try {
            copy($remote, "/tmp/{$uName}");

        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }

        $types = ['jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
        $type = $types[$ext];
        // echo $remote."   ===> ";

        $key = $path . $uName;
        try {
            S3Helper::put("/tmp/{$uName}", $key, $type);
            $fullpath = S3Helper::get_path($key);
            unlink("/tmp/{$uName}");
            return $fullpath;
        } catch (S3Exception $e) {
            echo $e->getMessage();
            return null;

        }
        return null;
    }

    public static function put($localFile, $key, $type = 'binary/octet-stream', $acl = 'public-read')
    {

        $_cdn = Config::get("site.cdn.aws");
        if (File::exists($localFile)) {
            try {
                $_s3 = Aws::factory(Config::get("aws"))->get('S3');
                $result = $_s3->putObject([
                    'Bucket' => $_cdn['bucket'],
                    'Key' => $key,
                    'ACL' => $acl,
                    'ContentType' => $type,
                    'SourceFile' => $localFile
                ]);
                return $result;
            } catch (S3Exception $e) {
                Log::warning("Failed on CDN UPLOAD ");
                return null;
            }
        } else {
            echo "ERR";
            return null;
        }
    }

    public static function get_path($key = null)
    {
        if ($key) return Config::get("site.cdn.aws.path") . "/" . Config::get("site.cdn.aws.bucket") . "/" . $key;
        return Config::get("site.cdn.aws.path") . "/" . Config::get("site.cdn.aws.bucket");
    }

    public static function delete($key = null)
    {
        if (strlen($key)):
            $_cdn = Config::get("site.cdn.aws");

            try {
                $_s3 = Aws::factory(Config::get("aws"))->get('S3');
                $result = $_s3->deleteObject([
                    'Bucket' => $_cdn['bucket'],
                    'Key' => $key
                ]);

                return $result;

            } catch (S3Exception $e) {
                Log::warning("Failed to delete file on S3");
            }

        else:
            return false;
        endif;
    }
}

?>