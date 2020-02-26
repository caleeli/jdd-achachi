<?php

namespace JDD\Achachi\Builder;

/* {foreach($sequence in $sequences): */
/* {$sequence *//* } */

/**
 * 
 *
 * @return @{$e->type()}
 */
/* endforeach} */

class NewCommentTemplate
{

    private $contentParser;
    private $template;
    private $parsed = '';

    function __construct($template)
    {
        $this->expressions = [
            '\/\*[\s\S]+?\*\/' => [
                '\/\*\{[\s\S]+?\}\*\/' => function ($match) {
                    return '<?php ' . substr($match, 3, -3) . ' ?>';
                },
                '\/\*\{[\s\S]+?\*\/' => function ($match) {
                    $this->contentParser = [$this, 'dropContent'];
                    return '<?= ' . substr($match, 3, -2) . ' ?>';
                },
                '\/\*\}\*\/' => function ($match) {
                    $this->contentParser = [$this, 'escapeContent'];
                    return '';
                },
                '@\{([^}]+)\}' => function ($match) {
                    return '<?= ' . substr($match, 2, -1) . ' ?>';
                },
            ],
        ];
        $this->template = $template;
    }

    public function evaluate(array $__data__)
    {
        ob_start();
        extract($__data__);
        $this->contentParser = [$this, 'escapeContent'];
        $parsed = $this->parse($this->template, $this->expressions);
        eval('?>' . $parsed);
        $response = ob_get_contents();
        ob_end_clean();
        return $response;
    }

    private function parse($template, array $expressions)
    {
        $parsed = '';
        $reg = [];
        foreach ($expressions as $exp => $callback) {
            $reg[] = '(' . $exp . ')';
        }
        $regexp = '/' . implode('|', $reg) . '/';
        preg_match_all($regexp, $template, $tags, PREG_OFFSET_CAPTURE);
        $prev = 0;
        $callbacks = array_values($expressions);
        foreach ($tags[0] as $i => $tag) {
            $pos = $tag[1];
            $content = call_user_func($this->contentParser,
                substr($template, $prev, $pos - $prev));
            $parsed .= $content;
            foreach ($tags as $j => $match) {
                if ($j > 0) {
                    $callback = $callbacks[$j - 1];
                    //dump([$j => $callback, $match[$i][0], count($tags)]);
                    $parsed .= $match[$i][0] ? is_callable($callback) ? $callback($match[$i][0]) : $this->parse($match[$i][0],
                            $callback) : '';
                }
            }
            $prev = $pos + strlen($tag[0]);
        }
        $content = call_user_func($this->contentParser, substr($template, $prev));
        $parsed .= $content;
        return $parsed;
    }

    private function escapeContent($content)
    {
        return str_replace('<?', '<?= "<?" ?>', $content);
    }

    private function dropContent()
    {
        return '';
    }
}

// <?php foreach($sequence as $sequences): ?/>
//         <?= $sequence ?/>
//     /**
//      * 
//      *
//      * @return <?= $e->type() ?/>
//      */    
// <?php endforeach; ?/>
// 


