<?php

add_filter('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Only interfere if it's a product preview link with our key
        if (isset($_GET['preview_key']) && $_GET['preview_key'] === 'tutor_preview') {
            $query->set('post_status', ['publish', 'pending', 'draft', 'future', 'private']);
        }
    }
});

add_action('template_redirect', function () {
    if (is_singular('product') && !is_user_logged_in()) {
        global $post;
        $status = get_post_status($post);
        if (in_array($status, ['draft', 'pending', 'future', 'private'])) {
            if (!isset($_GET['preview_key']) || $_GET['preview_key'] !== 'tutor_preview') {
                wp_redirect(home_url());
                exit;
            }
        }
    }
});

add_action('add_meta_boxes', function () {
    add_meta_box(
        'product_preview_link',
        'Tutor Preview Link',
        'render_product_preview_link_box',
        'product',
        'side',
        'high'
    );
});

function render_product_preview_link_box($post)
{
    $slug = $post->post_name;
    $link = home_url('/product/' . $slug . '/?preview_key=tutor_preview');
?>
    <p>
        <input type="text" id="preview-link" value="<?php echo esc_url($link); ?>" readonly style="width:100%; font-size: 13px;">
    </p>
    <p>
        <button type="button" class="button button-secondary" onclick="copyPreviewLink()">Copy Preview Link</button>
    </p>
    <script>
        function copyPreviewLink() {
            const input = document.getElementById('preview-link');
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            alert('Preview link copied to clipboard!');
        }
    </script>
<?php
}
