<?php
/**
 * Class Response - for response to geckoboard API - provided by Paul Joyce at Geckoboard
 */
class Response {
    /**
     * Response format - xml or json
     */
    private $format = 'xml';

    /**
     * Set response format
     *
     * @access public
     * @param string $format xml or json
     * @return void
     */
    public function setFormat($format) {
        $this->format = $format;
    }

    /**
     * Create response string
     *
     * @access public
     * @param array $data
     * @param bool $cdata
     * @return string
     */
    public function getResponse($data, $cdata = false) {
        switch ($this->format) {
            case 'xml':
                $response = $this->getXmlResponse($data, $cdata);
                break;
            case 'json':
                $response = $this->getJsonResponse($data);
                break;
        }
        return $response;
    }

    /**
     * Create response in xml format
     *
     * @access private
     * @param array $data
     * @param bool $cdata
     * @return string
     */
    private function getXmlResponse($data, $cdata) {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $root = $dom->createElement('root');
        $dom->appendChild($root);

        foreach ($data as $k => $v) {
            if (is_array($v)) {
                for ($i = 0; $i < sizeof($v); $i++) {
                    $item = $dom->createElement($k);
                    foreach ($v[$i] as $k1 => $v1) {
                        $cdata = $dom->createCDATASection($v1);
                        $node = $dom->createElement($k1);
                        $node->appendChild($cdata);
                        $item->appendChild($node);
                    }
                    $root->appendChild($item);
                }
            }
            else {
                $item = $dom->createElement($k);
                $cdata = $dom->createCDATASection($v);
                //$node->appendChild($cdata);
                $item->appendChild($cdata);
                $root->appendChild($item);
            }
        }

        $response = $dom->saveXML();
        return $response;
    }

    /**
     * Create response in json format
     *
     * @access private
     * @param array $data
     * @return string
     */
    private function getJsonResponse($data) {
        $response = json_encode($data);
        return $response;
    }

}
