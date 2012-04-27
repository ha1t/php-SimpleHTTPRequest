<?php
/**
 *
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

    public function setBasicAuth($username, $password)
    {
        $this->basic_auth = urlencode($username) . ':' . urlencode($password);
    }

    public function setQueryString()
    {

    }

    public function get($url)
    {
        if ($this->basic_auth) {
            $url = str_replace('http://', "http://{$this->basic_auth}@", $url);
        }

        return file_get_contents($url);
    }

    public function post($url)
    {
        if ($this->basic_auth) {
            $url = str_replace('http://', "http://{$this->basic_auth}@", $url);
        }
    }

    public static function addFile($filename, $data)
    {
        $boundary = '---------------------------'.time();

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
$ctx = stream_context_create($context);
$url = 'http://example.com/upload_url';
var_dump(file_get_contents($url,false,$ctx));

    }
}

