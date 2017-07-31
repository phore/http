<?php



namespace Phore\Http;


use function Phore\File\load;
use function Phore\File\pe_file;
use Tester\Assert;
use Tester\Environment;

require __DIR__ . "/../vendor/autoload.php";
Environment::setup();


request("https://google.de")->into($result, $header)->wait(50);




//pe_url("https://google.de")->intoFile($result, $header)->run(50);
//url("http://google.de")->
print_r ($header);
//print_r ($header);
echo $result;

echo "\n=================";
