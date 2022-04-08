<?php

/*
  Plugin Name: Are You paying Attention Quiz
  Description: Give your readers a multiple choice question.
  Version 1.0
  Author: Ravi
  Author URI: www.repindia.com
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class AreYouPayingAttention
{

    function __construct()
    {
        add_action('init', array($this, 'adminAssets'));
    }

    function adminAssets()
    {
        wp_register_style('quizeditcss', plugin_dir_url(__FILE__) . 'build/index.css');
        wp_register_script('ournewblocktype', plugin_dir_url(__FILE__) . 'build/index.js', ['wp-blocks', 'wp-element', 'wp-editor']);
        register_block_type('ourplugin/are-you-paying-attention', [
            'editor_style' => 'quizeditcss',
            'editor_script' => 'ournewblocktype',
            'render_callback' => [$this, 'theHTML']
        ]);
    }

    function theHTML($attributes)
    {
        if (!is_admin()) {
            wp_enqueue_script('attentionFrontend', plugin_dir_url(__FILE__) . 'build/frontend.js', array('wp-element'));
            wp_enqueue_style('attentionfrontendstyles',  plugin_dir_url(__FILE__) . 'build/frontend.css');
        }
        ob_start();

?>
        <div class="paying-attention-update-me">
            <?php echo wp_json_encode($attributes); ?>
        </div>
<?php

        return ob_get_clean();
    }
}

$areYouPayingAttention = new AreYouPayingAttention();
