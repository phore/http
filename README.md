# http
cURL http wrapper


## Example

To load the Data

```php
request("http://google.de")->into($content)->wait(5);

echo $content;
```


## Access Web-Services (RESTful Services)

Use `xpath` to expand the url of the original request:

```
// String xpath:
request("http://google.de/api")->xpath("/U4711/get/1234")->into($jsonData, $headers)->wait(5);

// Array xpath (with auto-escaping)
request("http://google.de/api")->xpath([$userId, "get", $documentId"])->into($jsonData, $headers)->wait(5);
```

> xpath array values will be `urlencoded()' twice! (To prevent slashes being interpreted as part of the directory)



