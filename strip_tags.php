<?php
/*
 * Credit to  Trititaty on http://php.net/manual/en/function.strip-tags.php
 * Features:
    * allowable tags (as in strip_tags),
    * optional stripping attributes of the allowable tags,
    * optional comment preserving,
    * deleting broken and unclosed tags and comments,
    * optional callback function call for every piece processed allowing for flexible replacements.

 */
function strip_tags_with_attr($str, $allowable_tags = '', $strip_attrs = false, $preserve_comments = false, callable $callback = null) {
    $allowable_tags = array_map('strtolower', array_filter(// lowercase
                    preg_split('/(?:>|^)\\s*(?:<|$)/', $allowable_tags, -1, PREG_SPLIT_NO_EMPTY), // get tag names
                    function( $tag ) {
                return preg_match('/^[a-z][a-z0-9_]*$/i', $tag);
            } // filter broken
            ));
    $comments_and_stuff = preg_split('/(<!--.*?(?:-->|$))/', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($comments_and_stuff as $i => $comment_or_stuff) {
        if ($i % 2) { // html comment
            if (!( $preserve_comments && preg_match('/<!--.*?-->/', $comment_or_stuff) )) {
                $comments_and_stuff[$i] = '';
            }
        } else { // stuff between comments
            $tags_and_text = preg_split("/(<(?:[^>\"']++|\"[^\"]*+(?:\"|$)|'[^']*+(?:'|$))*(?:>|$))/", $comment_or_stuff, -1, PREG_SPLIT_DELIM_CAPTURE);
            foreach ($tags_and_text as $j => $tag_or_text) {
                $is_broken = false;
                $is_allowable = true;
                $result = $tag_or_text;
                if ($j % 2) { // tag
                    if (preg_match("%^(</?)([a-z][a-z0-9_]*)\\b(?:[^>\"'/]++|/+?|\"[^\"]*\"|'[^']*')*?(/?>)%i", $tag_or_text, $matches)) {
                        $tag = strtolower($matches[2]);
                        if (in_array($tag, $allowable_tags)) {
                            if ($strip_attrs) {
                                $opening = $matches[1];
                                $closing = "/>";
                                $closing = ( $opening === '</' ) ? '>' : $closing;
                                $result = $opening . $tag . $closing;
                            }
                        } else {
                            $is_allowable = false;
                            $result = '';
                        }
                    } else {
                        $is_broken = true;
                        $result = '';
                    }
                } else { // text
                    $tag = false;
                }
                if (!$is_broken && isset($callback)) {
                    // allow result modification
                    call_user_func_array($callback, array(&$result, $tag_or_text, $tag, $is_allowable));
                }
                $tags_and_text[$j] = $result;
            }
            $comments_and_stuff[$i] = implode('', $tags_and_text);
        }
    }
    $str = implode('', $comments_and_stuff);
    return $str;
}

?>