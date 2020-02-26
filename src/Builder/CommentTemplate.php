<?php

namespace JDD\Achachi\Builder;

use StdClass;

/**
 * Description of CommentTemplate
 *
 * @author davidcallizaya
 */
class CommentTemplate
{

    // Ejemplo de foreach:
    //      /*{FOREACH $o->methods()*/
    //      ...$e->
    //      /*}*/
    //
    // Ejemplo de un reemplazo de código:
    //      /*{$o->interfaceName()*/ActivityInterface/*}*/
    //
    // Ejemplo de remplazo dentro de bloques de documentacion:
    //    /**
    //     * 
    //     *
    //     * @return @{$e->type()}
    //     */    
    //
    // $o = Referencia al objeto que se esta renderizando
    // $e = Referencia al objeto dentro de foreach
    const OPEN_TAG = '\/\*\{[^}]+?\*\/';
    const CLOSE_TAG = '\/\*\}\*\/';
    const COMMENT_BLOCK = '\/\*([\s\S]+?)\*\/';
    const COMMENT_BLOCK_FOREACH = '^foreach\s+(.+)$';
    const COMMENT_BLOCK_INLINE_TAG = '@\{([^}]+)\}';

    /**
     *
     * @var string $template
     */
    private $template = '';

    /**
     *
     * @var StdClass $parsed
     */
    private $parsed = null;

    public function __construct($template, callable $transformer=null)
    {
        $this->template = $template;
        $this->parse();
        $this->transformer = $transformer;
    }

    private function parse()
    {
        preg_match_all(
            '/(' . self::OPEN_TAG . ')|(' . self::CLOSE_TAG . ')|(' . self::COMMENT_BLOCK . ')/',
            $this->template, $tags, PREG_OFFSET_CAPTURE
        );
        $prev = 0;
        $this->parsed = $this->createNode('root', [], null);
        $current = $this->parsed;
        foreach ($tags[0] as $i => $tag) {
            $pos = $tag[1];
            $text = substr($this->template, $prev, $pos - $prev);
            $current->content[] = $this->createNode(
                '$node->content', $text, $current
            );
            if ($tags[1][$i][1] >= 0) {
                $code = substr($tag[0], 3, -2);
                if (preg_match('/' . self::COMMENT_BLOCK_FOREACH . '/i', $code,
                        $forMatch)) {
                    $code = '$this->each(' . $forMatch[1] . ', $o, $node)';
                }
                $node = $this->createNode(
                    $code, [], $current
                );
                $current->content[] = $node;
                $current = $node;
            } elseif ($tags[3][$i]) {
                $block = $tags[3][$i][0];
                preg_match_all(
                    '/(' . self::COMMENT_BLOCK_INLINE_TAG . ')/', $block,
                    $inlineTags, PREG_OFFSET_CAPTURE
                );
                $inPrev = 0;
                foreach ($inlineTags[1] as $ii => $inTag) {
                    $inPos = $inTag[1];
                    $inText = substr($block, $inPrev, $inPos - $inPrev);
                    $current->content[] = $this->createNode(
                        '$node->content', $inText, $current
                    );
                    $current->content[] = $this->createNode(
                        $inlineTags[2][$ii][0], [], $current
                    );
                    $inPrev = $inPos + strlen($inTag[0]);
                }
                $inText = substr($block, $inPrev);
                $current->content[] = $this->createNode('$node->content',
                    $inText, $current);
            } else {
                $current = isset($current->parent) ? $current->parent : null;
            }
            $prev = $pos + strlen($tag[0]);
        }
        $text = substr($this->template, $prev);
        $current->content[] = $this->createNode('$node->content', $text,
            $current);
    }

    private function createNode($verb, $content, $parent)
    {
        $node = new StdClass;
        $node->verb = $verb;
        $node->content = $content;
        $node->parent = $parent;
        return $node;
    }

    public function evaluate($o, $e = null)
    {
        $result = [];
        foreach ($this->parsed->content as $node) {
            //if ($debug) dump($node);
            $res = $this->evaluateNode($node, $o, $e);
            if (is_array($res)) {
                foreach ($res as $r) {
                    $result[] = $r;
                }
            } else {
                $result[] = $res;
            }
        }
        return $result;
    }

    public function evaluateArray($content, $o, $array)
    {
        $res = [];
        if (is_array($array)) foreach ($array as $e) {
            foreach ($content as $node) {
                $res[] = $this->evaluateNode($node, $o, $e);
            }
        }
        return $res;
    }

    private function evaluateNode($node, $o, $e = null)
    {
        return $this->evaluateTag($node, $o, $e);
    }

    private function evaluateTag($node, $o, $e)
    {
        try {
            $transformer = $this->transformer;
            $transformer ? extract(array_merge($o, $transformer($o))) : extract($o);
            $res = eval('return ' . $node->verb . ';');
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage() . ': ' . $node->verb, 0, $ex);
        }
        return $res;
    }

    public function each($array, $object, $node)
    {
        return $this->evaluateArray($node->content, $object, $array);
    }
}
