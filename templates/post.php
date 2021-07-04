<?php
/*
Template Name: Default Template
*/
global $post_custom_template;
$post_custom_template = 'postTemplate';

$isCart = function_exists('wc_get_product') ? is_cart() : false;
if ($isCart) {
    global $cart_custom_template;
    $cart_custom_template = theme_template_get_option('theme_template_' . get_option('stylesheet') . '_' . 'shopping-cart-template');
    $cart_custom_template = $cart_custom_template ? $cart_custom_template : 'shoppingCartTemplate';
    add_action(
        'theme_content_styles',
        function () use ($cart_custom_template) {
            theme_cart_content_styles($cart_custom_template);
        }
    );
} else {
    add_action(
        'theme_content_styles',
        function () use ($post_custom_template) {
            theme_single_content_styles($post_custom_template);
        }
    );
}

function theme_single_body_class_filter($classes) {
    $classes[] = 'u-body';
    return $classes;
}
add_filter('body_class', 'theme_single_body_class_filter');

function theme_single_body_style_attribute() {
    return "";
}
add_filter('add_body_style_attribute', 'theme_single_body_style_attribute');

function theme_single_body_back_to_top() {
    return <<<BACKTOTOP

BACKTOTOP;
}
add_filter('add_back_to_top', 'theme_single_body_back_to_top');


function theme_single_get_local_fonts() {
    return '';
}
add_filter('get_local_fonts', 'theme_single_get_local_fonts');

ob_start();
get_header();
$header = ob_get_clean();
if (function_exists('renderTemplate')) {
    renderTemplate($header, '', 'echo', 'header');
} else {
    echo $header;
}

if (!$isCart) {
    theme_layout_before('post', '', $post_custom_template);
}

while (have_posts()) {
    $is_singular = is_singular();
    $is_archive = is_archive();
    global $post;
    if ($is_singular || $is_archive || $post->post_type !== 'post') {
        the_post();
    }
    if (isset($isWpCustomTemplate) && $isWpCustomTemplate) {
        ob_start();
        get_template_part('template-parts/' . $post_custom_template . '/single-content');
        $content = ob_get_clean();
        if (function_exists('renderTemplate')) {
            renderTemplate($content, '', 'echo', 'custom');
        } else {
            echo $content;
        }
    } else {
        get_template_part('template-parts/' . $post_custom_template . '/single-content');
    }

    if ($is_singular && (comments_open() || get_comments_number())) {
        comments_template();
    }

    get_template_part('template-parts/' . $post_custom_template . '/single-navigation');
}

if (!$isCart) {
    theme_layout_after('post');
} ?>

<?php ob_start();
get_footer();
$footer = ob_get_clean();
if (function_exists('renderTemplate')) {
    renderTemplate($footer, '', 'echo', 'footer');
} else {
    echo $footer;
}
if ($isCart) {
    remove_action('theme_content_styles', 'theme_cart_content_styles');
} else {
    remove_action('theme_content_styles', 'theme_single_content_styles');
}
remove_filter('body_class', 'theme_single_body_class_filter');