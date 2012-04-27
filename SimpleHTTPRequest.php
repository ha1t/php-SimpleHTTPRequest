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
    private $file = array(); // ファイルやその情報をもったresult配列

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

        if (count($this->file) > 0) {
            $header = $this->file['header'];
            $data = $this->file['data'];
        }

        $context = array(
            "http" => array(
                "method"  => "POST",
                "header"  => implode("\r\n", $header),
                "content" => $data
            )
        );

        return file_get_contents($url, false, stream_context_create($context));
    }

    public function addFile($filename, $mime = '')
    {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException;
        }

        $boundary = '---------------------------'.microtime();

        $data = <<< EOD
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
EOD;

    $this->file = array(
        'header' => array(
            "Content-Type: multipart/form-data; boundary={$boundary}",
            "Content-Length: " . strlen($data)
        ),
        'data' => $data
    );
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

/*
$request = new SimpleHTTPRequest();
$request->addFile('./SimpleHTTPRequest.php');
$result = $request->post('http://project-p.jp/halt/echo.php?hoge=huga');
var_dump($result);
 */
