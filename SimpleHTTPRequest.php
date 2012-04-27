<?php
/**
 *
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
    private $file_boundary = '';

    public function setBasicAuth($username, $password)
    {
        $this->basic_auth = urlencode($username) . ':' . urlencode($password);
        $this->file_boundary = '';
    }

    // 第二引数でarrayに入れてもいいけどurlにそのままQueryStringを入れても良い感じになっている
    public function get($url, $query_string = array())
    {
        if ($this->basic_auth) {
            $url = str_replace('http://', "http://{$this->basic_auth}@", $url);
        }

        $query = '';
        if (count($query_string) != 0) {
            $query = '?' . http_build_query($query_string);
        }
        return file_get_contents($url . $query);
    }

    public function post($url, $data_string = array())
    {
        if ($this->basic_auth) {
            $url = str_replace('http://', "http://{$this->basic_auth}@", $url);
        }

        $data = '';
        if (count($data_string) != 0) {
            $data = '?' . http_build_query($data_string);
        }

        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: ".strlen($data)
        );

        $context = array(
            "http" => array(
                "method"  => "POST",
                "header"  => implode("\r\n", $header),
                "content" => $data
            )
        );

        return file_get_contents($url, false, stream_context_create($context));
    }

    public static function addFile($filename, $data)
    {
        $boundary = '---------------------------'.microtime();

        $data = <<< __data
            --{$boundary}
            Content-Disposition: form-data; name="test"

hogehoge
--{$boundary}
Content-Disposition: form-data; name="test2"

foobar
--{$boundary}
Content-Disposition: form-data; name="file"; filename="test.txt"
Content-Type: text/plain

value2
--{$boundary}--
__data;

$header = array(
    "Content-Type: multipart/form-data; boundary=".$boundary,
    "Content-Length: ".strlen($data)
);

$context = array(
    "http" => array(
        "method"  => "POST",
        "header"  => implode("\r\n", $header),
        "content" => $data
    )
);
        return file_get_contents($url, false, stream_context_create($context));
    }
}

/*
$request = new SimpleHTTPRequest();
$result = $request->get('http://project-p.jp/halt/echo.php', array('hoge' => 'huga', 'moge' => 'pole'));
var_dump($result);

$request = new SimpleHTTPRequest();
$result = $request->get('http://project-p.jp/halt/echo.php?hoge=huga');
var_dump($result);
 */

