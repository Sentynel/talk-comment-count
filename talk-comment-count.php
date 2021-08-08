<?php
/**
 * Plugin Name: Talk Comment Count
 * Version: 0.0.2
 * Author: Sam Lade
 */
function amg_talk_comment_count ($count, $post_id) {
    try {
        $mng = new MongoDB\Driver\Manager("mongodb://localhost");
        $url = get_permalink($post_id);
        $url = preg_replace_callback(
            "/%[a-f0-9]{2}/",
            function ($matches) {
                return strtoupper($matches[0]);
            },
            $url
        );
        $assetq = new MongoDB\Driver\Query(["url"=>$url], ["limit" => 1, "projection" => ["commentCounts.status" => ""]]);
        $assets = $mng->executeQuery("coral.stories", $assetq)->toArray();
        $newcount = $assets[0]->commentCounts->status->APPROVED + $assets[0]->commentCounts->status->NONE;
        return $newcount;
    } catch (exception $e) {
        return $count;
    }
}

add_filter('get_comments_number', 'amg_talk_comment_count', 10, 2);
