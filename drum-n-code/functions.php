<?php


add_action( 'wp_enqueue_scripts', function (){

    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', ['_s-style'] );
    wp_enqueue_style('news', get_stylesheet_directory_uri() . '/assets/css/news.css', ['_s-style']);

});

 add_theme_support('post-thumbnails');
 add_theme_support('title-tag');
 add_theme_support('custom-logo');
 add_theme_support( 'align-wide' );


add_theme_support( 'html5', array(
    'comment-list',
    'comment-form',
    'search-form',
    'gallery',
    'caption',
    'script',
    'style',
) );


 register_nav_menus(
     array(
         'menu-header' => esc_html__( 'Меню в шапке', 'menu'),
     )
 );

add_action('init', 'my_custom_init');
function my_custom_init(){
    register_post_type('news', array(
        'labels'             => array(
            'name'               => 'News',
            'singular_name'      => 'News',
            'parent_item_colon'  => '',
            'menu_name'          => 'News'
        ),
        'public'             => true,
        'taxonomies'         => array('category'),
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title','editor','thumbnail','excerpt')
    ) );
}

function news_shortcode($atts) {
    $atts = shortcode_atts(array(
        'number'    => 3,
        'category'  => '',
    ), $atts, 'news');

    $args = array(
        'post_type'      => 'news',
        'posts_per_page' => $atts['number'],
        'category_name'  => $atts['category'],
    );

    $query = new WP_Query($args);
    $output = '';

    if ($query->have_posts()) {
        $output .= '<div class="news-container">';

        while ($query->have_posts()) {
            $query->the_post();

            $thumbnail = get_the_post_thumbnail(get_the_ID(), array(490, 328));
            $title = get_the_title();
            $excerpt = get_the_excerpt();

            if (empty($excerpt)) {
                $excerpt = wp_trim_words(get_the_content(), 20);
            }

            $permalink = get_permalink();

            $output .= '
                <div class="news-item">
                    <div class="news-thumbnail">' . $thumbnail . '</div>
                    <h2 class="news-title"><a href="' . $permalink . '">' . $title . '</a></h2>
                    <div class="news-content">
                        <p class="news-excerpt">' . $excerpt . '</p>
                        <a href="' . $permalink . '" >Read more</a>
                    </div>
                </div>';
        }

        $output .= '</div>';
        wp_reset_postdata();
    } else {
        $output .= '<p>No news found.</p>';
    }
    return $output;
}
add_shortcode('news', 'news_shortcode');


?>
