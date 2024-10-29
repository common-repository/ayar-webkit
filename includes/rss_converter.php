<?php
$rss_method= $awk_options['converter_options'];
if ($rss_method == 'ayar2zg'){
add_filter("the_title_rss",$rss_method);
add_filter("wp_title_rss",$rss_method);
add_filter("bloginfo_rss",$rss_method);
add_filter("the_excerpt_rss",$rss_method);
add_filter("the_content_feed",$rss_method);
add_filter("comment_text_rss",$rss_method);
add_filter("comment_text",$rss_method);
}
//remove_all_actions( 'do_feed_rss2' );
//add_action( 'do_feed_rss2', 'ayar_feed_rss2', 10, 1 );

//function ayar_feed_rss2() {
//    $rss_template = ARSS_PLUGIN_PATH . '/templates/ayar-feed-rss2.php';
//    load_template( $rss_template );
//}
