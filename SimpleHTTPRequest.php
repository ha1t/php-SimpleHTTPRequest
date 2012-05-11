<?php
/**
 *
 * @note http://php.net/manual/ja/context.http.php#context.http.ignore-errors
 * とりあえず単一のファイルをアップロードできればよし！
 * BasicAuthができる
 * Fileが追加できる
 * getができる
 * postができる
 * QueryStringがつけられる
 */

class SimpleHTTPRequest
{
    const METHOD_GET  = 'get';
    const METHOD_POST = 'post';

    private $method = 'get';
    private $basic_auth = '';
    private $query_string = array();
    private $files = array(); // ファイルやその情報をもったresult配列

    private static function createBasicAuth($username, $password)
    {
        return urlencode($username) . ':' . urlencode($password);
    }

    // 第二引数でarrayに入れてもいいけどurlにそのままQueryStringを入れても良い感じになっている
    public static function get($url, $query_string = array(), $basic_auth = array())
    {
        if ($basic_auth) {
            $url = str_replace('http://', 'http://' . self::createBasicAuth($basic_auth['username'], $basic_auth['password']) . '@', $url);
        }

        $query = '';
        if (count($query_string) != 0) {
            $query = '?' . http_build_query($query_string);
        }
        return file_get_contents($url . $query);
    }

    public static function post($url, $data_string = array(), $basic_auth = array(), $files = array())
    {
        if ($basic_auth) {
            $url = str_replace('http://', 'http://' . self::createBasicAuth($basic_auth['username'], $basic_auth['password']) . '@', $url);
        }

        $data = '';
        if (count($data_string) != 0) {
            $data = http_build_query($data_string, '', '&');
        }

        $header = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($data),
        );

        /*
        if (count($this->files) > 0) {
            foreach ($this->files as $file) {
                $header = $file['header'];
                $data = $file['data'];
            }
        }
         */

        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", $header),
                'content' => $data,
                'ignore_errors' => true,
            ),
        );

        $result = file_get_contents($url, false, stream_context_create($context));

        if ($result === false) {
            throw new ErrorException(print_r($http_response_header, true));
        }

        // とりあえずいかなる時もレスポンスを返す感じにした
        // はぁーっ。Resultクラス作りたくねーしなー
        /*
        $parts = explode(' ', $http_response_header[0]);
        $http_status_code = $parts[1];

        if ($http_status_code{0} == '4') {
            throw new ErrorException('400');
        }
         */

        return $result;
    }

    public function addFile($name, $filename, $mime = 'text/plain')
    {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException;
        }

        $file_data = file_get_contents($filename);
        $base_filename = basename($filename);

        $boundary = '---------------------------'.microtime();

        $data = <<< EOD
--{$boundary}
Content-Disposition: form-data; name="{$name}"; filename="{$base_filename}"
Content-Type: {$mime}

{$file_data}
--{$boundary}--
EOD;

        $this->files[$name] = array(
            'header' => array(
                "Content-Type: multipart/form-data; boundary={$boundary}",
                "Content-Length: " . strlen($data)
            ),
            'data' => $data
        );
    }
}

// http://memorva.jp/memo/php/fsockopen.php
/*
$request = new SimpleHTTPRequest();
$result = $request->get('http://project-p.jp/halt/echo.php', array('hoge' => 'huga', 'moge' => 'pole'));
var_dump($result);

$request = new SimpleHTTPRequest();
$result = $request->get('http://project-p.jp/halt/echo.php?hoge=huga');
var_dump($result);
 */

$request = new SimpleHTTPRequest();
//$request->addFile('photo', './SimpleHTTPRequest.php');
$result = $request->post('http://project-p.jp/halt/echo.php', array('hoge' => 'huga'));
var_dump($result);
