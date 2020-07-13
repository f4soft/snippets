<?php

/**
 * Get ids of random posts from category or globally
 *
 * @param int $count
 * @param array|int $category_id
 * @param array $post__not_in
 * @param integer $max_count_cache
 * @return void
 */
function getRandPostIds($count, $category_id = null, $post__not_in = [], $max_count_cache = 1000)
{
    $cache_key = 'rand-' . (is_array($category_id) ? implode(',', $category_id) : (int)$category_id);
    if (false === ($ids = get_transient($cache_key))) {
        $args = array(
            'showposts' => $max_count_cache,
            'orderby' => 'rand',
            'no_found_rows' => true,
            'fields' => 'ids',
        );

        if ($category_id) {
            $args['category__in'] = $category_id;
        }

        $result = new WP_Query($args);
        $ids = $result->posts;
        set_transient($cache_key, $ids, HOUR_IN_SECONDS);
    }

    if (!is_array($post__not_in)) {
        $post__not_in = (array)$post__not_in;
    }

    $return = [];
    $rand_keys = array_rand($ids, $count + count($post__not_in));
    $rand_keys = (array)$rand_keys;

    foreach ($rand_keys as $key) {
        if (!in_array($ids[$key],  $post__not_in)) {
            $return[] = $ids[$key];
        }
        if (count($return) >= $count) {
            break;
        }
    }
    return $return;
}
