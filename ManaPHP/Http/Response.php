<?php
namespace ManaPHP\Http;

class Response extends \Phalcon\Http\Response
{
    public function setJsonContent($content, $jsonOptions = null)
    {
        $this->setContentType('application/json', 'utf-8');

        if ($jsonOptions === null) {
            $jsonOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;
        }

        $this->_content = json_encode($content, $jsonOptions, 512);

        return $this;
    }
}