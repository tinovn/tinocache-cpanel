<?php

namespace tinocachePlugin\Model\Tool;

use SimpleXMLElement;

class XmlTool
{

    public function getXml($data, $mode = false)
    {
        $root    = "";
        $rootKey = "";

        foreach ($data as $key => $d)
        {
            $rootKey = $key;
            $root    = "<$key/>";
        }

        $xml = new SimpleXMLElement($root);
        $this->convertToXML($xml, $data[$rootKey]);

        if ($mode == true)
        {
            return str_replace('<?xml version="1.0"?>', '', $xml->asXML());
        }

        return $xml->asXML();
    }

    private function convertToXML(SimpleXMLElement $object, array $data)
    {
        foreach ($data as $key => $value)
        {
            if (is_array($value))
            {
                $newObject = $object->addChild($key);
                $this->convertToXML($newObject, $value);
            }
            else
            {
                if ($key == (int)$key)
                {
                    $key = "$key";
                }

                $object->addChild($key, $value);
            }
        }

        return $object;
    }

    public function xmlToArray($xml)
    {
        return simplexml_load_string(trim($xml));
    }

    public function xmlToJson($xml)
    {
        return json_encode(simplexml_load_string(trim($xml)));
    }
}
