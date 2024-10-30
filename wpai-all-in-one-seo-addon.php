<?php
/*
Plugin Name: WP All Import - All In One SEO Add-On
Plugin URI: http://www.wpallimport.com/
Description: Import data into All In One SEO with WP All Import.
Version: 2.0.0
Author: Soflyy
*/

/**
 * Class WPAI_All_In_One_Seo_Add_On
 */
final class WPAI_All_In_One_Seo_Add_On {

	/**
	 * @var WPAI_All_In_One_Seo_Add_On $instance
	 */
	protected static $instance;

	/**
	 * @var RapidAddon
	 */
	protected $add_on;

	/**
	 * @return WPAI_All_In_One_Seo_Add_On
	 */
	static public function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * WPAI_All_In_One_Seo_Add_On constructor.
	 */
	protected function __construct() {
		$this->constants();
		$this->includes();

		$this->add_on = new RapidAddon( 'All In One SEO Add-On', 'wpai_all_in_one_seo' );

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 *  Define Add-On constants.
	 */
	public function constants() {

		if ( ! defined( 'WPAI_ALL_IN_ONE_SEO_PLUGIN_DIR_PATH' ) ) {
			// Dir path
			define( 'WPAI_ALL_IN_ONE_SEO_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'WPAI_ALL_IN_ONE_SEO_ROOT_DIR' ) ) {
			// Root directory for the plugin.
			define( 'WPAI_ALL_IN_ONE_SEO_ROOT_DIR', str_replace( '\\', '/', dirname( __FILE__ ) ) );
		}

		if ( ! defined( 'WPAI_ALL_IN_ONE_SEO_PLUGIN_PATH' ) ) {
			// Path to the main plugin file.
			define( 'WPAI_ALL_IN_ONE_SEO_PLUGIN_PATH', WPAI_ALL_IN_ONE_SEO_ROOT_DIR . '/' . basename( __FILE__ ) );
		}
	}

	/**
	 *  Include required files.
	 */
	public function includes() {
		include WPAI_ALL_IN_ONE_SEO_PLUGIN_DIR_PATH . 'rapid-addon.php';
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	/**
	 *  Init Add-On fields.
	 */
	public function init() {

		// Importing 'Advanced Reviews'
		$this->fields();

		$this->add_on->set_import_function( [ $this, 'import' ] );

		if ( is_plugin_active( "all-in-one-seo-pack-pro/all_in_one_seo_pack.php" ) ) {
			$this->add_on->run(
				array(
					"plugins" => array(
						"all-in-one-seo-pack-pro/all_in_one_seo_pack.php",
					))
			);
		} else {
			$this->add_on->run(
				array(
					"plugins" => array(
						"all-in-one-seo-pack/all_in_one_seo_pack.php",
					))
			);
		}

		/* Check dependent plugins */
		if ( !is_plugin_active( "all-in-one-seo-pack/all_in_one_seo_pack.php" ) && !is_plugin_active( "all-in-one-seo-pack-pro/all_in_one_seo_pack.php" ) ) {
			$this->add_on->admin_notice('The All In One SEO Add-On requires WP All Import <a href="http://www.wpallimport.com/order-now/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=all-in-one-seo" target="_blank">Pro</a> or <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a>, and the <a href="https://wordpress.org/plugins/all-in-one-seo-pack/">All In One SEO</a> plugin.',
				array('plugins' => array('all-in-one-seo-pack/all_in_one_seo_pack.php', 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php'))
			);
		}
	}

	/**
	 *  Define SEO fields.
	 */
	public function fields() {

		$wpai_aioseo_help = "When using XPath: use 'on' to enable or any other value to disable";

		$wpai_aioseo_radio = array(
			'_not_selected' => 'Disable',
			'_selected' => 'Enable',
		);

		$wpai_aioseo_radio_yn = array(
			'_not_selected' => 'No',
			'_selected' => 'Yes',
		);

		$this->add_on->add_field('title', 'Title', 'text');
		$this->add_on->add_field('description', 'Description', 'text');
		$this->add_on->add_field('keywords', 'Keywords (comma separated)', 'text');

		$this->add_on->add_options(false, 'Open Graph', array(
			$this->add_on->add_field('og_object_type', 'Open Graph object type', 'text'),
			$this->add_on->add_field('og_title', 'Open Graph title', 'text'),
			$this->add_on->add_field('og_description', 'Open Graph description', 'text'),
			$this->add_on->add_field('og_image_custom_url', 'Open Graph image custom URL', 'text'),
			$this->add_on->add_field('og_image_custom_fields', 'Open Graph image custom fields', 'text'),
			$this->add_on->add_field('og_image_type', 'Open Graph image type', 'text'),
			$this->add_on->add_field('og_video', 'Open Graph video', 'text'),
			$this->add_on->add_field('og_article_section', 'Open Graph article section', 'text'),
			$this->add_on->add_field('og_article_tags', 'Open Graph article tags (comma separated)', 'text')
		));

		$this->add_on->add_options(false, 'Twitter', array(
			$this->add_on->add_field('twitter_use_og', 'Twitter use open graph', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
			$this->add_on->add_field('twitter_card', 'Twitter card', 'text'),
			$this->add_on->add_field('twitter_image_custom_url', 'Twitter image custom URL', 'text'),
			$this->add_on->add_field('twitter_image_custom_fields', 'Twitter image custom fields', 'text'),
			$this->add_on->add_field('twitter_image_type', 'Twitter image type', 'text'),
			$this->add_on->add_field('twitter_title', 'Twitter title', 'text'),
			$this->add_on->add_field('twitter_description', 'Twitter description', 'text')
		));

		$this->add_on->add_options(false, 'Robots', array(
			$this->add_on->add_field('pillar_content', 'Pillar content', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
			$this->add_on->add_field('default', 'Robots Meta NOINDEX', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
			$this->add_on->add_field('noindex', 'Robots Meta NOINDEX', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
			$this->add_on->add_field('nofollow', 'Robots Meta NOFOLLOW', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
			$this->add_on->add_field('noarchive', 'Robots Meta NOARCHIVE', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
			$this->add_on->add_field('notranslate', 'Robots Meta NOTRANSLATE', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
			$this->add_on->add_field('noimageindex', 'Robots Meta NOIMAGEINDEX', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
			$this->add_on->add_field('nosnippet', 'Robots Meta NOSNIPPET', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
			$this->add_on->add_field('noodp', 'Robots Meta NOODP', 'radio', $wpai_aioseo_radio, $wpai_aioseo_help),
		));

		$this->add_on->add_options(false, 'Advanced', array(
			$this->add_on->add_field('schema_type', 'Schema type', 'text'),
			$this->add_on->add_field('schema_type_options', 'Schema type options', 'text'),
			$this->add_on->add_field('tabs', 'Tabs', 'text'),
			$this->add_on->add_field('local_seo', 'Local SEO', 'text'),
			$this->add_on->add_field('priority', 'Priority', 'text'),
			$this->add_on->add_field('frequency', 'Frequency', 'text'),
			$this->add_on->add_field('keyphrases', 'Key phrases', 'text'),
			$this->add_on->add_field('page_analysis', 'Page analysis', 'text'),
			$this->add_on->add_field('seo_score', 'Seo score', 'text'),
			$this->add_on->add_field('canonicalUrl', 'Canonical Url', 'text'),
			$this->add_on->add_field('maxSnippet', 'Max Snippet', 'text'),
			$this->add_on->add_field('maxVideoPreview', 'Max Video Preview', 'text'),
			$this->add_on->add_field('maxImagePreview', 'Max Image Preview', 'text'),
		));
	}

	/**
	 * Import SEO data.
	 *
	 * @param $post_id
	 * @param $data
	 * @param $import_options
	 * @param $article
	 */
	public function import( $post_id, $data, $import_options, $article ) {
		foreach ($data as $key => $value) {
			// Radio inputs
			if (in_array($key, array(
				'twitter_use_og',
				'pillar_content',
				'default',
				'noindex',
				'nofollow',
				'noarchive',
				'notranslate',
				'noimageindex',
				'nosnippet',
				'noodp'
			))) {
				if ( $value == '_selected' || $value == 'on') {
					$data[$key] = 1;
				} else {
					$data[$key] = 0;
				}
			}
			// Comma separated inputs
			if (in_array($key, array(
				'keywords',
				'og_article_tags',
			))) {
				$data[$key] = explode(",", $data[$key]);
				$data[$key] = array_map('trim', $data[$key]);
				$data[$key] = array_filter($data[$key]);
			}
			// Preserve existing value.
			if (in_array($key, array(
				'title',
				'description',
				'keywords',
				'og_title',
				'og_description',
				'og_article_section',
				'og_article_tags',
				'twitter_title',
				'twitter_description'
			))) {
				$meta_key = '_aioseo_' . $key;
				if (!empty($article['ID']) && !$this->add_on->can_update_meta($meta_key, $import_options)) {
					$data[$key] = get_post_meta( $post_id, $meta_key, true );
				}
			}
		}

		$saveStatus = AIOSEO\Plugin\Common\Models\Post::savePost($post_id, $data);
		if ( ! empty( $saveStatus ) ) {
			$this->add_on->log('Failed update query: ' . $saveStatus);
		}
	}
}

WPAI_All_In_One_Seo_Add_On::get_instance();
