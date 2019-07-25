<?php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' ); 
function theme_enqueue_styles() { 
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
} 


/**
 * トップページに表示するカテゴリーを取得する
 */
function getFeaturedCategorys(){
	$category_ids = explode(",", get_option('jin_yhei_top_categories'));
	if(isset($category_ids[0]) && !$category_ids[0]) {
		return [];
	}
	if(!is_numeric($category_ids[0])) {
		return [];
	}

	$featuredCategorys = [];
	foreach($category_ids as $category_id) {
		$featuredCategory = get_term_by('id', $category_id, 'category');
		$featuredCategorys[] = $featuredCategory;
	}
	return $featuredCategorys;
}

/**
 * トップページのカテゴリー一覧に表示するカテゴリーを取得する
 */
function getFeaturedCategorysChilds($term_id) {
	// $child_category_ids = get_term_children($term_id, 'category');
	$featured_child_categorys = get_terms( 'category', array(
		'parent' => $term_id,
		'hide_empty' => false,
		'orderby' => 'term_order',
	));
	return $featured_child_categorys;
}

/**
 * トップに表示するカテゴリーの設定
 */
add_action('admin_menu', 'top_category_menu');
function top_category_menu() {
	add_menu_page('子カテゴリーカスタマイズ', '子カテゴリーカスタマイズ', 'administrator', __FILE__, 'jin_child_settings_page','',61);
	add_action( 'admin_init', 'register_top_category_settings' );
}
function register_top_category_settings() {
	register_setting( 'top-category-settings-group', 'jin_yhei_top_categories' );
}
function jin_child_settings_page() {
?>
  <div class="wrap">
    <h2>トップに表示するカテゴリー</h2>
    <form method="post" action="options.php">
      <?php 
        settings_fields( 'top-category-settings-group' );
        do_settings_sections( 'top-category-settings-group' );
      ?>
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="jin_yhei_top_categories">トップに表示したいカテゴリーID</label>
            </th>
              <td>
								<input type="text" 
									id="jin_yhei_top_categories" 
									class="regular-text" 
									name="jin_yhei_top_categories" 
									value="<?php echo get_option('jin_yhei_top_categories'); ?>"
									placeholder="2,8,10,12 (カテゴリーIDをカンマ区切りで入力)"
								>
							</td>
          </tr>
        </tbody>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
<?php
}

// カテゴリーIDから カスタムカテゴリーのアイキャッチ画像取得
function cps_category_eyecatch_by_term_id($term_id){
  $cat_class = get_category($term_id);
  $cat_option = get_option($term_id);

  if( isset($cat_option['cps_image_cat']) && $cat_option['cps_image_cat'] !== '' ){
    $category_eyecatch = $cat_option['cps_image_cat'];
  }
  echo '<img src="' . esc_html($category_eyecatch) . '" >';
}

function cps_has_post_thumbnail($term_id) {
	$cat_class = get_category($term_id);
  $cat_option = get_option($term_id);

  if( isset($cat_option['cps_image_cat']) && $cat_option['cps_image_cat'] !== '' ){
    return true;
	}
	return false;
}

function get_recommended_posts() {
	$tags = get_tags();
	$recommended_tag_id = 0;
	foreach( $tags as $tag ) {
		if( $tag->name === 'おすすめ' ) {
			$recommended_tag_id = $tag->term_id;
			break;
		}
		continue;
	}
	return new WP_Query( "tag_id={$recommended_tag_id}" );
}

?>
