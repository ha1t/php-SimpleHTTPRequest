# php-SimpleHTTPRequest

PEARを使わずにpostしたりgetしたりするクラス

## このライブラリの目的

- 依存関係をできるだけ少なくしたシンプルなGET/POSTできるクラスの作成
- クラス自体の呼び出しも簡単にできるように(static method)

## Example

### GET

```php
<?php
$url = 'http://www.yahoo.co.jp/';
$response_body = SimpleHTTPRequest::get($url);
echo $response_body;
```

### POST

```php
<?php
$url = 'http://www.yahoo.co.jp/';
$params = array('name' => 'hoge', 'body' => 'hello');
$response_body = SimpleHTTPRequest::post($url, $params);
echo $response_body;
```

## TODO

- 戻り値の返し方。headerが403で、bodyにエラー文がついたレスポンスが来た時に、どのように値を返すか。Resultクラスはちょっと重くないか？
