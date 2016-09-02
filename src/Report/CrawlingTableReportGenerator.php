<?php

namespace Ascendens\Igcrawler\Report;

use ArrayObject;
use Closure;

class CrawlingTableReportGenerator implements ReportGeneratorInterface
{
    const SORT_ASC = 1;
    const SORT_DESC = 2;

    /**
     * @var ArrayObject
     */
    private $data = null;

    /**
     * @var array
     */
    private $th;

    /**
     * @var string
     */
    private $sortBy;

    /**
     * @var string
     */
    private $sortDirection;

    /**
     * @var string
     */
    private $attributes;

    /**
     * @param array $th Headers for HTML table in next format: ["id" => "caption"]
     * @param string $sortBy Id of header to sort by
     * @param int $sortDirection One of SORT_* constants
     * @param string $attributes Raw HTML attributes string for table
     */
    public function __construct(array $th, $sortBy = '', $sortDirection = self::SORT_ASC, $attributes = '')
    {
        $this->th = $th;
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;
        $this->attributes = $attributes;
        $this->sortDirection = $sortDirection;
        $this->data = new ArrayObject();
    }

    /**
     * @inheritdoc
     */
    public function add($data)
    {
        $this->data->append($data);

        return $this;
    }

    /**
     * @return ArrayObject
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $parameters May have "template" with "{body}" where generated table would be posted
     * @return string
     */
    public function generate(array $parameters = [])
    {
        $template = isset($parameters['template']) ? $parameters['template'] : '{body}';
        $result = str_replace('{body}', $this->generateTable(), $template);

        return $result;
    }

    /**
     * @return string
     */
    private function generateTable()
    {
        $result = sprintf('<table %s>', $this->attributes);
        // THs
        $result .= '<tr>';
        foreach ($this->th as $caption) {
            $result .= sprintf('<th>%s</th>', $caption);
        }
        $result .= '</tr>';
        // Sorting
        if (!empty($this->sortBy)) {
            $this->data->uasort(Closure::bind(function ($a, $b) {
                return $this->sortCallback($a, $b);
            }, $this));
        }
        // TDs
        foreach ($this->data as $dataSet) {
            $result .= '<tr>';
            foreach (array_keys($this->th) as $id) {
                $content = array_key_exists($id, $dataSet) ? $dataSet[$id] : '';
                $result .= "<td>$content</td>";
            }
            $result .= '</tr>';
        }
        $result .= '</table>';

        return $result;
    }

    /**
     * @param array $a
     * @param array $b
     * @return mixed
     */
    private function sortCallback(array $a, array $b)
    {
        switch ($this->sortDirection) {
            case self::SORT_DESC:
                return $b[$this->sortBy] - $a[$this->sortBy];
                break;

            case self::SORT_ASC:
                return $a[$this->sortBy] - $b[$this->sortBy];
                break;

            default:
                return 0;
        }
    }
}
