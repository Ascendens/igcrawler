<?php

namespace Ascendens\Igcrawler\WebPageProcessor\Decorator;

use InvalidArgumentException;

/**
 * Extracts content of link "href" attribute. Returns "null" for links to files in common formats
 */
class Href extends AbstractDecorator
{
    /**
     * @inheritdoc
     */
    public function doProcess($data)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('Input must be an array');
        }

        return array_map([$this, 'extractUrl'], $data);
    }

    /**
     * File formats to exclude. This part must be improved
     */
    protected function excludeFormats()
    {
        return [
            'jpeg', 'jpg', 'png', 'gif',
            'js', 'json',
            'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'epub', 'odt', 'odp', 'ods', 'txt', 'rtf',
            'swf', 'flv',
        ];
    }

    /**
     * @param string $tag
     * @return null|string
     */
    private function extractUrl($tag)
    {
        if (
            preg_match('/href=("|\')([^"\']+)("|\')/i', $tag, $result)
            && !preg_match('/\.(' . join('|', $this->excludeFormats()) . ')/i', $result[2])
        ) {
            return $result[2];
        }

        return null;
    }
}
