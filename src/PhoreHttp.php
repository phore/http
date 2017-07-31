<?php


namespace Phore\Http;

use Symfony\Component\Yaml\Yaml;

/**
 * Class PhoreUrl
 * @package Phore\File
 *
 * @property $GET self
 * @property $POST self
 * @property $DELETE self
 * @property $HEADER self
 * @property $PUT self
 */
class PhoreHttp
{

    /**
     * @var array
     */
    private $url;

    private $req = [
        "method" => "GET",
        "postfields" => null,
        "postbody" => null,
        "requestHeader" => null
    ];

    /**
     * @var PhoreStreamReceiver[]
     */
    private $receiver = [];

    private $onHeader = null;

    public function __construct(string $url)
    {
        $this->url = $url;
        $this->req["requestHeader"] = new PhoreHttpHeader();
    }


    public function xpath($subpath) : self {
        if (is_array($subpath)) {
            for ($i=0; $i<count ($subpath); $i++) {
                $subpath[$i] = urlencode($subpath[$i]);
            }
            $subpath = implode("/", $subpath);
        }

        $abs = false;
        if (substr ($subpath, 0, 1) == "/")
            $abs = true;

        $path = $this->url;
        if (substr($path,-1) == "/" && $abs === true)
            $subpath = substr($subpath, 1); // strip one slash
        if (substr($path,-1) != "/" && $abs === false)
            $subpath = "/" . $subpath; // Add one slash

        $path .= $subpath;
        return new self($path);
    }


    public function GET(array $params = null) : self {


    }

    public function POST(array $params = null) : self {
        $this->req["method"] = "POST";
        $this->req["postfields"] = $params;
        return $this;
    }


    public function with(string $data=null) : self {
        if ($this->req["postfields"] !== null)
            throw new \Exception("You cannot combine POST() with with() requests: Both are being sent in request body");
        $this->req["postfields"] = $data;
        return $this;
    }

    public function withHeaders(array $headers) {

    }

    public function withYaml($data) : self {
        $yaml = Yaml::dump($data);

    }

    public function withJSON($data) : self {
        $json = json_encode($data);

    }

    public function intoYaml() : self {

    }

    public function intoJSON() : self {

    }

    public function intoFile($filename) : self {

    }

    public function into(&$ref, PhoreHttpHeader &$header = null) : self {
        $header = new PhoreHttpHeader();
        $this->onHeader = function ($data) use ($header) {
            foreach (explode("\n", $data) as $lineNo => $line) {
                if ($lineNo == 0) {
                    continue;
                }
                list($headerKey, $headerValue) = explode(";", $line, 2);
                $header[$headerKey] = $headerValue;
            }
        };


        $ref = "";
        $this->addHandler(new class ($ref) implements PhoreStreamReceiver {

            private $ref;

            public function __construct(&$ref)
            {
                $this->ref =& $ref;
            }

            public function onData($data)
            {
                $this->ref .= $data;
            }
        });
        return $this;
    }


    public function addHandler (PhoreStreamReceiver $receiver) : self {
        $this->receiver[] = $receiver;
        return $this;
    }


    public function run($timeLimit=30) : self {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeLimit);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        if ($this->req["method"] == "POST") {
            curl_setopt($ch, CURLOPT_POSTFIELDS, 1);
            if ($this->req["postfields"] !== null)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->req["postfields"]);
        }

        $onBody = false;
        $buf = "";
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $str) use (&$buf, &$onBody) {
            if ($onBody === false) {
                echo ".";
                $buf .= $str;
                if (($pos = strpos($buf, "\r\n\r\n") ) !== false) {
                    //echo "END OF HEADER: $str";

                    $onBody = true;
                    $headerStr = substr($buf, 0, $pos);
                    if ($this->onHeader !== null)
                        ($this->onHeader)($headerStr);


                    if (strpos(strtoupper($buf), "\nLOCATION:")) {
                        $onBody = false;
                        $buf = "";
                        return strlen($str);
                    }
                    $body = substr($buf, $pos+4);
                    $buf = "";
                    if ($body != "") {
                        foreach ($this->receiver as $curRec) {
                            $curRec->onData($body);
                        }
                    }
                }
            } else {
                foreach ($this->receiver as $curRec)
                    $curRec->onData($str);
            }
            return strlen($str);
        });
        if ( ! curl_exec($ch)) {
            throw new \Exception("Error loading '$this->url': " . curl_error($ch));
        }
        return $this;
    }


    public function new () {

    }

    public function wait($timeLimit=30, $parallel=5) {

    }

}