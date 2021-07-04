<?php
global $blog_custom_template;
$blog_custom_template = 'blogTemplate';

add_action(
    'theme_content_styles',
    function () use ($blog_custom_template) {
        theme_blog_content_styles($blog_custom_template);
    }
);

function theme_index_body_class_filter($classes) {
    $classes[] = 'u-body';
    return $classes;
}
add_filter('body_class', 'theme_index_body_class_filter');

function theme_index_body_style_attribute() {
    return "";
}
add_filter('add_body_style_attribute', 'theme_index_body_style_attribute');

function theme_index_body_back_to_top() {
    return <<<BACKTOTOP

BACKTOTOP;
}
add_filter('add_back_to_top', 'theme_index_body_back_to_top');

function theme_index_get_local_fonts() {
    return '';
}
add_filter('get_local_fonts', 'theme_index_get_local_fonts');

ob_start();
get_header();
$header = ob_get_clean();
if (function_exists('renderTemplate')) {
    renderTemplate($header, '', 'echo', 'header');
} else {
    echo $header;
}
theme_layout_before('blog', '', $blog_custom_template);
?>

<?php if (is_home() && ! is_front_page()) : ?>
    <header>
        <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
    </header>
<?php endif; ?>

<?php if (is_search()) : ?>
    <header class="page-header">
        <h1 class="page-title"><?php printf(__('Search results for %s'), '<span>' . esc_html(get_search_query()) . '</span>'); ?></h1>
    </header>
<?php endif; ?>

<?php
if (have_posts()) {

    global $wp_query;
    $first_repeatable = 0;
    $last_repeatable = 0;

    $template_used = array();
    $templates_count = 1;

    $blog_sections_count = $last_repeatable + 1;

    if ($blog_sections_count) {
        for ($template_idx = 0; $template_idx < $blog_sections_count; $template_idx++) {
            if ($template_idx < $first_repeatable && !empty($template_used[$template_idx])) {
                if ($blog_sections_count == $first_repeatable) {
                    break;
                } else {
                    continue;
                }
            }
            $template_used[$template_idx] = true;

            $is_singular = is_singular();
            if ($is_singular) {
                the_post();
            }
            if (isset($isWpCustomTemplate) && $isWpCustomTemplate) {
                ob_start();
                get_template_part('template-parts/'. $blog_custom_template .'/post-content-' . ($template_idx + 1));
                $content = ob_get_clean();
                if (function_exists('renderTemplate')) {
                    renderTemplate($content, '', 'echo', 'custom');
                } else {
                    echo $content;
                }
            } else {
                get_template_part('template-parts/'. $blog_custom_template .'/post-content-' . ($template_idx + 1));
            }

            if ($is_singular && (comments_open() || get_comments_number())) {
                comments_template();
            }
        }
    }
    // If no content, include the "No posts found" template.
} else {
    get_template_part('template-parts/'. $blog_custom_template . '/content', 'none');
}

theme_layout_after('blog'); ?>

<?php ob_start();
get_footer();
$footer = ob_get_clean();
if (function_exists('renderTemplate')) {
    renderTemplate($footer, '', 'echo', 'footer');
} else {
    echo $footer;
}
remove_action('theme_content_styles', 'theme_blog_content_styles');
remove_filter('body_class', 'theme_index_body_class_filter');
