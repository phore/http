<?php



namespace Phore\Http;


use function Phore\File\load;
use function Phore\File\pe_file;
use Tester\Assert;
use Tester\Environment;

require __DIR__ . "/../vendor/autoload.php";
Environment::setup();



$h = new PhoreHttpHeader();
$h["SomeKey"] = "ABC";

Assert::equal("ABC", $h["SomeKey"]);
Assert::equal("ABC", $h["somekey"]);
Assert::equal("ABC", $h["SOMEKEY"]);
Assert::equal(null, $h["nonExistent"]);

Assert::true(isset ($h["SomeKey"]));
Assert::true(isset ($h["somekey"]));
Assert::true(isset ($h["SOMEKEY"]));

Assert::false(isset ($h["nonExistent"]));


foreach ($h as $key => $value);
Assert::equal("SomeKey", $key);
Assert::equal("ABC", $value);