<?php
/**
 * Plugin Name: Marklink
 * Plugin URI: https://github.com/nkapila6/marklink
 * Description: Export WordPress content as Markdown for LLM consumption. Provides .md endpoints for all posts/pages and generates site indexes (/llms.txt, /llms-full.txt).
 * Version: 0.0.1
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: Nikhil Kapila
 * Author URI: https://nkapila.me/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: marklink
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MARKLINK_VERSION', '0.0.1');
define('MARKLINK_FILE', __FILE__);
define('MARKLINK_DIR', plugin_dir_path(__FILE__));
define('MARKLINK_URL', plugin_dir_url(__FILE__));

class Marklink {

    private static $instance = null;
    private $options = null;

    private static $defaults = array(
        'excluded_words'  => 'copy, sample',
        'index_limit'     => 20,
        'post_types'      => array('post', 'page'),
        'enable_md'       => 1,
        'enable_llms_txt' => 1,
    );

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_options() {
        if (null === $this->options) {
            $saved = get_option('marklink_settings', array());
            $this->options = wp_parse_args($saved, self::$defaults);
        }
        return $this->options;
    }

    private function __construct() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('marklink', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function activate() {
        $this->register_rewrite_rules();
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    // -------------------------------------------------------------------------
    // Settings page
    // -------------------------------------------------------------------------

    public function add_settings_link($links) {
        $url = admin_url('options-general.php?page=marklink');
        array_unshift($links, '<a href="' . esc_url($url) . '">Settings</a>');
        return $links;
    }

    public function add_settings_page() {
        add_options_page(
            'Marklink',
            'Marklink',
            'manage_options',
            'marklink',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('marklink_settings_group', 'marklink_settings', array(
            'type'              => 'array',
            'sanitize_callback' => array($this, 'sanitize_settings'),
            'default'           => self::$defaults,
        ));

        add_settings_section(
            'marklink_general',
            'General',
            null,
            'marklink'
        );

        add_settings_field('enable_md', 'Markdown endpoints (.md)', array($this, 'render_checkbox_field'), 'marklink', 'marklink_general', array('field' => 'enable_md', 'label' => 'Enable <code>.md</code> URLs for posts and pages'));
        add_settings_field('enable_llms_txt', 'Site indexes', array($this, 'render_checkbox_field'), 'marklink', 'marklink_general', array('field' => 'enable_llms_txt', 'label' => 'Enable <code>/llms.txt</code> and <code>/llms-full.txt</code>'));

        add_settings_section(
            'marklink_index',
            'Index Settings',
            null,
            'marklink'
        );

        add_settings_field('index_limit', 'Posts in /llms.txt', array($this, 'render_number_field'), 'marklink', 'marklink_index', array('field' => 'index_limit'));
        add_settings_field('excluded_words', 'Excluded words', array($this, 'render_text_field'), 'marklink', 'marklink_index', array('field' => 'excluded_words', 'description' => 'Comma-separated. Posts with these words in the title or slug are excluded from indexes.'));
        add_settings_field('post_types', 'Post types', array($this, 'render_post_types_field'), 'marklink', 'marklink_index', array('field' => 'post_types'));
    }

    public function sanitize_settings($input) {
        $clean = array();
        $clean['enable_md']       = !empty($input['enable_md']) ? 1 : 0;
        $clean['enable_llms_txt'] = !empty($input['enable_llms_txt']) ? 1 : 0;
        $clean['index_limit']     = isset($input['index_limit']) ? absint($input['index_limit']) : 20;
        $clean['excluded_words']  = isset($input['excluded_words']) ? sanitize_text_field($input['excluded_words']) : '';

        if (!empty($input['post_types']) && is_array($input['post_types'])) {
            $clean['post_types'] = array_map('sanitize_key', $input['post_types']);
        } else {
            $clean['post_types'] = array('post', 'page');
        }

        return $clean;
    }

    public function render_checkbox_field($args) {
        $opts  = $this->get_options();
        $val   = !empty($opts[$args['field']]) ? 1 : 0;
        $name  = 'marklink_settings[' . $args['field'] . ']';
        echo '<label><input type="checkbox" name="' . esc_attr($name) . '" value="1" ' . checked(1, $val, false) . ' /> ' . wp_kses($args['label'], array('code' => array())) . '</label>';
    }

    public function render_number_field($args) {
        $opts = $this->get_options();
        $val  = isset($opts[$args['field']]) ? absint($opts[$args['field']]) : 20;
        $name = 'marklink_settings[' . $args['field'] . ']';
        echo '<input type="number" name="' . esc_attr($name) . '" value="' . esc_attr($val) . '" min="1" max="200" class="small-text" />';
    }

    public function render_text_field($args) {
        $opts = $this->get_options();
        $val  = isset($opts[$args['field']]) ? $opts[$args['field']] : '';
        $name = 'marklink_settings[' . $args['field'] . ']';
        echo '<input type="text" name="' . esc_attr($name) . '" value="' . esc_attr($val) . '" class="regular-text" />';
        if (!empty($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    public function render_post_types_field($args) {
        $opts     = $this->get_options();
        $selected = isset($opts['post_types']) ? (array) $opts['post_types'] : array('post', 'page');
        $types    = get_post_types(array('public' => true), 'objects');

        foreach ($types as $type) {
            if ($type->name === 'attachment') {
                continue;
            }
            $chk = in_array($type->name, $selected, true) ? ' checked' : '';
            echo '<label style="display:block;margin-bottom:4px;"><input type="checkbox" name="marklink_settings[post_types][]" value="' . esc_attr($type->name) . '"' . $chk . ' /> ' . esc_html($type->label) . '</label>';
        }
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>Marklink</h1>
            <p>Configure how your content is exposed as Markdown.</p>
            <form method="post" action="options.php">
                <?php
                settings_fields('marklink_settings_group');
                do_settings_sections('marklink');
                submit_button();
                ?>
            </form>
            <hr />
            <h2>Your endpoints</h2>
            <table class="widefat fixed striped" style="max-width:600px;">
                <tbody>
                    <tr><td><strong>Site index</strong></td><td><a href="<?php echo esc_url(home_url('/llms.txt')); ?>" target="_blank"><?php echo esc_html(home_url('/llms.txt')); ?></a></td></tr>
                    <tr><td><strong>Full archive</strong></td><td><a href="<?php echo esc_url(home_url('/llms-full.txt')); ?>" target="_blank"><?php echo esc_html(home_url('/llms-full.txt')); ?></a></td></tr>
                    <tr><td><strong>Homepage (.md)</strong></td><td><a href="<?php echo esc_url(home_url('/index.md')); ?>" target="_blank"><?php echo esc_html(home_url('/index.md')); ?></a></td></tr>
                </tbody>
            </table>
            <p class="description" style="margin-top:8px;">Append <code>.md</code> to any post or page URL to get its Markdown version.</p>
        </div>
        <?php
    }

    // -------------------------------------------------------------------------
    // Core functionality
    // -------------------------------------------------------------------------

    public function init() {
        $opts = $this->get_options();

        if (!empty($opts['enable_md'])) {
            $this->register_rewrite_rules();
            $this->register_query_vars();
            add_action('template_redirect', array($this, 'handle_markdown_request'), 5);
        }

        if (!empty($opts['enable_llms_txt'])) {
            $this->handle_llms_request();
        }
    }

    private function register_rewrite_rules() {
        add_rewrite_rule(
            '^(index|home)\.md$',
            'index.php?is_markdown=1&is_home=1',
            'top'
        );
        add_rewrite_rule(
            '^(.+)\.md$',
            'index.php?md_slug=$matches[1]&is_markdown=1',
            'top'
        );
    }

    private function register_query_vars() {
        add_filter('query_vars', function ($vars) {
            $vars[] = 'is_markdown';
            $vars[] = 'is_home';
            $vars[] = 'md_slug';
            return $vars;
        });
    }

    public function handle_llms_request() {
        $uri = wp_unslash($_SERVER['REQUEST_URI']);

        if ($uri !== '/llms.txt' && $uri !== '/llms-full.txt') {
            return;
        }

        $opts    = $this->get_options();
        $is_full = ($uri === '/llms-full.txt');

        header('Content-Type: text/plain; charset=UTF-8');

        echo '# ' . get_bloginfo('name') . ($is_full ? ' (Full Archive)' : '') . "\n";
        echo '> ' . get_bloginfo('description') . "\n\n";

        if (!$is_full) {
            echo "## Discover\n";
            echo '- [Full Archive](' . home_url('/llms-full.txt') . "): Complete index.\n\n";
            $limit = (int) $opts['index_limit'];
        } else {
            $limit = -1;
        }

        $post_types = !empty($opts['post_types']) ? $opts['post_types'] : array('post', 'page');

        $query = new WP_Query(array(
            'post_type'      => $post_types,
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        $forbidden = array_filter(array_map('trim', explode(',', $opts['excluded_words'])));

        echo "## Content Index\n";
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $title = get_the_title();
                $slug  = get_post_field('post_name', get_the_ID());

                $should_exclude = false;
                foreach ($forbidden as $word) {
                    if (stripos($title, $word) !== false || stripos($slug, $word) !== false) {
                        $should_exclude = true;
                        break;
                    }
                }
                if ($should_exclude) {
                    continue;
                }

                $permalink = get_permalink();
                $home_url  = trailingslashit(home_url());

                if ($permalink === $home_url || $permalink === untrailingslashit($home_url)) {
                    $md_url = home_url('/index.md');
                } else {
                    $md_url = rtrim($permalink, '/') . '.md';
                }

                echo '- [' . $title . '](' . $md_url . ")\n";
            }
        }
        wp_reset_postdata();
        exit;
    }

    public function handle_markdown_request() {
        $is_md_url  = (bool) get_query_var('is_markdown');
        $is_home_md = (bool) get_query_var('is_home');
        $md_slug    = get_query_var('md_slug');

        if (!$is_md_url && preg_match('/^\/(home|index)\.md(\?.*)?$/i', $_SERVER['REQUEST_URI'])) {
            $is_home_md = true;
            $is_md_url  = true;
        }
        if (!$is_md_url && preg_match('/^\/(.+)\.md(\?.*)?$/i', $_SERVER['REQUEST_URI'], $m)) {
            $is_md_url = true;
            $md_slug   = $m[1];
        }

        $is_md_header = (
            isset($_SERVER['HTTP_ACCEPT'])
            && strpos($_SERVER['HTTP_ACCEPT'], 'text/markdown') !== false
        );

        if (!$is_md_url && !$is_md_header) {
            return;
        }

        $post = null;

        if ($is_home_md || (is_front_page() && $is_md_header)) {
            $post_id = (int) get_option('page_on_front');

            if (!$post_id) {
                $post_id = (int) get_option('page_for_posts');
            }

            if (!$post_id) {
                $front = get_page_by_path('home');
                if (!$front) {
                    $front = get_page_by_path('index');
                }
                if ($front) {
                    $post_id = $front->ID;
                }
            }

            if ($post_id) {
                $post = get_post($post_id);
            }

        } elseif ($md_slug) {
            $opts       = $this->get_options();
            $post_types = !empty($opts['post_types']) ? $opts['post_types'] : array('post', 'page');

            foreach ($post_types as $type) {
                $post = get_page_by_path($md_slug, OBJECT, $type);
                if ($post) {
                    break;
                }
            }

        } else {
            global $post;
        }

        if (!$post || !($post instanceof WP_Post) || $post->post_status !== 'publish') {
            return;
        }

        header('Content-Type: text/markdown; charset=UTF-8');
        header('Vary: Accept');

        $content = apply_filters('the_content', $post->post_content);

        $content = preg_replace('/<(script|style|noscript)\b[^>]*>(.*?)<\/\1>/is', '', $content);
        $content = preg_replace('/<!--.*?-->/s', '', $content);

        $search = array(
            '/<h[1-3][^>]*>(.*?)<\/h[1-3]>/i',
            '/<h[4-6][^>]*>(.*?)<\/h[4-6]>/i',
            '/<strong[^>]*>(.*?)<\/strong>/i',
            '/<b[^>]*>(.*?)<\/b>/i',
            '/<em[^>]*>(.*?)<\/em>/i',
            '/<i[^>]*>(.*?)<\/i>/i',
            '/<a[^>]*href="(.*?)"[^>]*>(.*?)<\/a>/i',
            '/<li[^>]*>(.*?)<\/li>/i',
            '/<br\s*\/?>/i',
            '/<hr\s*\/?>/i',
            '/&nbsp;/i',
        );
        $replace = array(
            "\n## $1\n",
            "\n### $1\n",
            "**$1**",
            "**$1**",
            "*$1*",
            "*$1*",
            "[$2]($1)",
            "- $1",
            "\n",
            "\n---\n",
            ' ',
        );
        $markdown = preg_replace($search, $replace, $content);

        $markdown = strip_tags($markdown);
        $markdown = html_entity_decode($markdown, ENT_QUOTES, 'UTF-8');
        $markdown = str_replace(array("\t", "\r"), '', $markdown);
        $markdown = preg_replace("/\n{3,}/", "\n\n", $markdown);

        $lines = explode("\n", $markdown);
        $trimmed_lines = array_map('trim', $lines);
        $markdown = implode("\n", $trimmed_lines);

        echo '# ' . get_the_title($post) . "\n";
        echo 'URL: ' . get_permalink($post) . "\n";
        echo "---------------------------\n\n";
        echo trim($markdown);

        exit;
    }
}

Marklink::get_instance();
