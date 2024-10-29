<?php
function zawgyifont(){ ?>
<style type="text/css">
@font-face {
font-family:'Zawgyi-One';
src: url('http://webfont.myanmapress.com/fonts/zawgyi-one.ttf') format('truetype');
}
</style>
<?php
}
$mobile_method= $awk_options['converter_options'];
if ($mobile == 'yes' && $browser->isMobile() == true){
if ($mobile_method == 'ayar2zg'){
//add_filter("the_title",$mobile_method);
//add_filter("wp_title",$mobile_method);
//add_filter("the_excerpt",$mobile_method);
//add_filter("the_content",$mobile_method);
//add_filter("get_the_excerpt",$mobile_method);
//add_filter("get_the_content",$mobile_method);
//add_filter("the_title_rss",$mobile_method);
//add_filter("wp_title_rss",$mobile_method);
//add_filter("bloginfo_rss",$mobile_method);
//add_filter("the_excerpt_rss",$mobile_method);
//add_filter("the_content_feed",$mobile_method);
//add_filter("comment_text_rss",$mobile_method);
//add_filter("comment_text",$mobile_method);
//add_filter("the_tags",$mobile_method);
//add_filter("wp_list_pages",$mobile_method);
//add_filter("wp_dropdown_cats",$mobile_method);
//add_filter("wp_list_categories",$mobile_method);
//add_filter("widget_title",$mobile_method);
//add_filter("widget_text",$mobile_method);
if ( ! is_admin() ) {
     //add_filter("gettext",$mobile_method);
}
add_action("wp_head","zawgyifont");

add_action('wp', 'mobile_html_set_up_buffer', 10, 0);
}
}
