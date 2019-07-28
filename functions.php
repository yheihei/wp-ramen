<?php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' ); 
function theme_enqueue_styles() { 
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
} 


/**
 * トップページに表示する親カテゴリーを取得する
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
 * @param $term_id
 * @return カテゴリー
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
 * トップに表示するカテゴリーやタグの設定
 */
add_action('admin_menu', 'top_category_menu');
function top_category_menu() {
	add_menu_page('子テーマカスタマイズ', '子テーマカスタマイズ', 'administrator', __FILE__, 'jin_child_settings_page','',61);
	add_action( 'admin_init', 'register_jin_child_settings' );
}
function register_jin_child_settings() {
	// トップページ設定
	register_setting( 'top-category-settings-group', 'jin_yhei_top_categories' );
	register_setting( 'top-category-settings-group', 'jin_yhei_top_tag_names' );

	// カテゴリーページ設定
	register_setting( 'category-settings-group', 'jin_yhei_show_only_one_category_ids' );
}
function jin_child_settings_page() {
?>
  <div class="wrap">
    <h2>トップページ設定</h2>
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
					<tr>
            <th scope="row">
              <label for="jin_yhei_top_tag_names">スライドショーに表示したいタグ名</label>
            </th>
              <td>
								<input type="text" 
									id="jin_yhei_top_tag_names" 
									class="regular-text" 
									name="jin_yhei_top_tag_names" 
									value="<?php echo get_option('jin_yhei_top_tag_names'); ?>"
									placeholder="おすすめ,まとめ,特集 (タグ名をカンマ区切りで入力)"
								>
							</td>
          </tr>
        </tbody>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
	<div class="wrap">
    <h2>カテゴリーページ表示設定</h2>
    <form method="post" action="options.php">
      <?php 
        settings_fields( 'category-settings-group' );
        do_settings_sections( 'category-settings-group' );
      ?>
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="jin_yhei_show_only_one_category_ids">直近の子カテゴリーの一覧を表示するカテゴリーID(ex. 催事別カテゴリーページ)</label>
            </th>
              <td>
								<input type="text" 
									id="jin_yhei_show_only_one_category_ids" 
									class="regular-text" 
									name="jin_yhei_show_only_one_category_ids" 
									value="<?php echo get_option('jin_yhei_show_only_one_category_ids'); ?>"
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

/**
 * カテゴリーIDから カスタムカテゴリーのアイキャッチ画像取得
 * @param int $term_id
 */
function cps_category_eyecatch_by_term_id($term_id){
  $cat_class = get_category($term_id);
  $cat_option = get_option($term_id);

  if( isset($cat_option['cps_image_cat']) && $cat_option['cps_image_cat'] !== '' ){
    $category_eyecatch = $cat_option['cps_image_cat'];
  }
  echo '<img src="' . esc_html($category_eyecatch) . '" >';
}

/**
 * カテゴリー記事のサムネイルが設定されているか
 * @return bool
 */
function cps_has_post_thumbnail($term_id) {
	$cat_class = get_category($term_id);
  $cat_option = get_option($term_id);

  if( isset($cat_option['cps_image_cat']) && $cat_option['cps_image_cat'] !== '' ){
    return true;
	}
	return false;
}

/**
 * トップのスライドショーに表示するタグの記事を取得する
 * @return WP_Query
 */
function get_recommended_posts() {
	$tags = get_tags();
	$recommended_tag_ids = [];
	$registered_tag_names = get_registered_tag_names();
	if( empty($registered_tag_names )) {
		// 未設定の場合はありえないtag id を指定して0記事にする
		return new WP_Query( ['tag_id' => -1] );
	}
	foreach( $tags as $tag ) {
		// トップに表示するタグのIDを取得する
		if( in_array($tag->name, $registered_tag_names) ) {
			$recommended_tag_ids[] = $tag->term_id;
		}
		continue;
	}
	// トップに表示するタグIDで記事を取得する
	return new WP_Query( ['tag__in' => $recommended_tag_ids] );
}

/**
 * スライドショーに表示するタグ名を取得する
 * @return array
 */
function get_registered_tag_names() {
	$registered_tag_names = get_option('jin_yhei_top_tag_names');
	if( !$registered_tag_names ) {
		return [];
	}
	// 空白除去
	$registered_tag_names  = preg_replace("/( |　)/", "", $registered_tag_names );
	$registered_tag_names = explode(",", $registered_tag_names);
	return $registered_tag_names;
}

/**
 * 直近の子カテゴリーのみを表示するカテゴリーページのIDを取得する
 * @return array
 */
function get_show_only_one_category_ids() {
	$category_ids = get_option('jin_yhei_show_only_one_category_ids');
	if( !$category_ids ) {
		return [];
	}
	// 空白除去
	$category_ids  = preg_replace("/( |　)/", "", $category_ids );
	$category_ids = explode(",", $category_ids);
	return $category_ids;
}

/**
 * 設定したカテゴリーの直近の子カテゴリーが取得できること
 */
function getChildCategorys($category_id){
	$child_categorys = get_terms( 'category', array(
		'parent' => $category_id,
		'hide_empty' => false,
		'orderby' => 'term_order',
	));
	return $child_categorys;
}

/**
 * アーカイブページで 現在のカテゴリーを取得する
 */
function get_current_term(){
	$id;
	$tax_slug;

	if(is_category()){
			$tax_slug = "category";
			$id = get_query_var('cat'); 
	}else if(is_tag()){
			$tax_slug = "post_tag";
			$id = get_query_var('tag_id');  
	}else if(is_tax()){
			$tax_slug = get_query_var('taxonomy');  
			$term_slug = get_query_var('term'); 
			$term = get_term_by("slug",$term_slug,$tax_slug);
			$id = $term->term_id;
	}
	return get_term($id,$tax_slug);
}

function is_category_list_page() {
	// 特定のカテゴリーページの場合、直近の子カテゴリーのみ一覧表示する
	$category_list_ids = get_show_only_one_category_ids();
	//現在表示されているカテゴリーを取得
	$current_term = get_current_term();
	return !empty($category_list_ids) && in_array((string)$current_term->term_id, $category_list_ids, true);
}


?>
