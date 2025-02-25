<?php
/**
 * Plugin Name: Talk Comment Count
 * Version: 0.0.2
 * Author: Sam Lade
 */
function amg_talk_comment_count ($count, $post_id) {
    static $mng = false;

    $key = "comment_count_{$post_id}";
    $res = wp_cache_get($key, '');
    if ($res !== false) {
        return $res;
    }
    try {
        if ($mng === false) {
            // cache Mongo connection
            $mng = new MongoDB\Driver\Manager("mongodb://%2frun%2fmongodb%2fmongodb-27017.sock");
        }
        $url = get_permalink($post_id);
        $url = preg_replace_callback(
            "/%[a-f0-9]{2}/",
            function ($matches) {
                return strtoupper($matches[0]);
            },
            $url
        );
        $assetq = new MongoDB\Driver\Query(["url"=>$url], ["limit" => 1, "projection" => ["commentCounts.status" => 1]]);
        $assets = $mng->executeQuery("coral.stories", $assetq)->toArray();
        if (count($assets) === 0) {
            return 0;
        };
        $newcount = $assets[0]->commentCounts->status->APPROVED + $assets[0]->commentCounts->status->NONE;
        wp_cache_set($key, $newcount, '', 300);
        return $newcount;
    } catch (exception $e) {
        return $count;
    }
}

add_filter('get_comments_number', 'amg_talk_comment_count', 10, 2);
