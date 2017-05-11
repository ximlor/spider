<?php

namespace Core;

use DOMDocument;
use DOMNode;
use DOMXPath;
use DOMNodeList;

class Selector
{
    /**
     * @var DOMDocument
     */
    protected $dom = null;

    public function __construct(string $html)
    {
        $this->dom = new DOMDocument();

        // remove redundant white space
        $this->dom->preserveWhiteSpace = false;

        $this->dom->loadHTML($html);
    }

    public function xPathSelector(string $selector)
    {
        $xpathObj = new DOMXPath($this->dom);
        $result = $xpathObj->query($selector);
        if (!$result instanceof DOMNodeList) {
            throw new \Exception('xpath query error');
        }
        return $result;
    }

    public function queryByNode(string $selector, DOMNode $contextnode, bool $registerNodeNS = true)
    {
        $xpathObj = new DOMXPath($this->dom);
        $result = $xpathObj->query($selector, $contextnode);
        if (!$result instanceof DOMNodeList) {
            throw new \Exception('xpath query error');
        }

        return $this->getNodeContent($result->item(0));
    }

    protected function getNodeContent(DOMNode $node)
    {
        switch ($node->nodeType) {
            case XML_ATTRIBUTE_NODE:
                $result = $node->value;
                break;
            default:
                $result = $node->nodeValue;
                break;
        }
        return preg_replace('/\s(?=)/', '', $result);
    }
}