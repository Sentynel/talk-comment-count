<?php
/**
 * Plugin Name: Talk Comment Count
 * Version: 0.0.1
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
        $assetq = new MongoDB\Driver\Query(["url"=>$url], ["limit" => 1, "projection" => ["id" => ""]]);
        $assets = $mng->executeQuery("talk.assets", $assetq)->toArray();
        // error handle
        if (count($assets) == 0) {
            return 0;
        }
        $assetid = $assets[0]->id;
        $countq = new MongoDB\Driver\Command(['count'=>'comments', 'query'=>['asset_id'=>$assetid, 'status'=>['$ne'=>'REJECTED']]]);
        $countres = $mng->executeCommand('talk', $countq)->toArray();
        return $countres[0]->n;
    } catch (exception $e) {
        return $count;
    }
}

add_filter('get_comments_number', 'amg_talk_comment_count', 10, 2);
