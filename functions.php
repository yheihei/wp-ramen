<?php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' ); 
function theme_enqueue_styles() { 
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
} 

define("JIN_YHEI_CATEGORY_PRIORITY_VALUE_DEFAULT", 1);
define('JIN_YHEI_CATEGORY_POSTS_COUNT_FORMAT', '訪問 %s 回');

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
    'hierarchical' => false,
    'orderby' => 'term_order',
  ));

  return sortCategorysByPriority( $featured_child_categorys );
}

/**
 * カテゴリーリストを優先度でソートする
 * @param object[] $target_categorys
 * @return object[] $categorys_sorted_by_priority
 */
function sortCategorysByPriority( $target_categorys ) {
  $categorys = [];
  $sorted_ids_to_priority = [];
  foreach( $target_categorys as $target_category ) {
    $categorys[$target_category->term_id]
      = $target_category;
    $sorted_ids_to_priority[$target_category->term_id] = get_category_priority($target_category->term_id);
  }
  // Priorityを使って降順でソート
  arsort($sorted_ids_to_priority);
  
  // Priorityでソートした結果を返す
  $categorys_sorted_by_priority = [];
  foreach( $sorted_ids_to_priority as $sorted_id => $priority ) {
    $categorys_sorted_by_priority[] = $categorys[$sorted_id];
  }
  return $categorys_sorted_by_priority;
}

/**
 * タブの中に新着表示を含む場合設定値をtrueで返す
 */
function is_involved_new_entry_in_category_tabs() {
  return boolval( get_option('jin_yhei_top_categories_is_involved_new_entry') );
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
  // トップのタブに新着記事を含める
  register_setting( 'top-category-settings-group', 'jin_yhei_top_categories_is_involved_new_entry' );
  // トップに表示する他ブログの新着RSS設定
  register_setting( 'top-category-settings-group', 'jin_yhei_target_rss_url_titles' );
  register_setting( 'top-category-settings-group', 'jin_yhei_target_rss_urls' );
  // トップに最新記事を表示
  register_setting( 'top-settings-group', 'jin_yhei_top_new_entry_enable' );
  register_setting( 'top-settings-group', 'jin_yhei_top_new_entry_section_title' );

  // カテゴリーページ設定
  register_setting( 'category-settings-group', 'jin_yhei_show_only_one_category_ids' );
  register_setting( 'category-settings-group', 'jin_yhei_excluded_count_category_ids' );
  register_setting( 'category-settings-group', 'jin_yhei_category_posts_count_format' );
}
function jin_child_settings_page() {
?>
  <div class="wrap">
    <h2>トップページ設定</h2>
    <h3>タブ設定</h3>
    <form method="post" action="options.php">
      <?php 
        settings_fields( 'top-category-settings-group' );
        do_settings_sections( 'top-category-settings-group' );
      ?>
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="jin_yhei_top_categories">タブに表示したいカテゴリーID</label>
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
              <label for="jin_yhei_top_categories_is_involved_new_entry">タブに新着記事を含める</label>
            </th>
              <td>
                <input type="checkbox"
                  id="jin_yhei_top_categories_is_involved_new_entry"
                  name="jin_yhei_top_categories_is_involved_new_entry"
                  <?php checked(get_option('jin_yhei_top_categories_is_involved_new_entry'), 1); ?>
                  value="1">
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
          <tr>
            <th scope="row">
              <label for="jin_yhei_target_rss_url_titles">トップに新着表示したい他ブログのタイトル</label>
            </th>
            <td>
              <textarea
                id="jin_yhei_target_rss_url_titles" 
                class="regular-text" 
                name="jin_yhei_target_rss_url_titles" 
                placeholder="タイトルを改行区切りで入力"
                rows="4"
              ><?php echo get_option('jin_yhei_target_rss_url_titles'); ?></textarea>
            </td>
            <th scope="row">
              <label for="jin_yhei_target_rss_urls">トップに新着表示したい他ブログのURL</label>
            </th>
            <td>
              <textarea
                id="jin_yhei_target_rss_urls" 
                class="regular-text" 
                name="jin_yhei_target_rss_urls" 
                placeholder="URLを改行区切りで入力"
                rows="4"
              ><?php echo get_option('jin_yhei_target_rss_urls'); ?></textarea>
            </td>
          </tr>
        </tbody>
      </table>
      <?php submit_button(); ?>
    </form>
    <h3>その他</h3>
    <form method="post" action="options.php">
      <?php 
        settings_fields( 'top-settings-group' );
        do_settings_sections( 'top-settings-group' );
      ?>
      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="jin_yhei_top_new_entry_enable">トップページの上部に最新記事リンクを設置</label>
            </th>
            <td>
              <input type="checkbox"
                id="jin_yhei_top_new_entry_enable"
                name="jin_yhei_top_new_entry_enable"
                <?php checked(get_option('jin_yhei_top_new_entry_enable'), 1); ?>
                value="1">
            </td>
            <th scope="row">
              <label for="jin_yhei_top_new_entry_section_title">最新記事リンクのセクションタイトル</label>
            </th>
            <td>
            <input type="text" 
              id="jin_yhei_top_new_entry_section_title" 
              class="regular-text" 
              name="jin_yhei_top_new_entry_section_title" 
              value="<?php echo get_option('jin_yhei_top_new_entry_section_title'); ?>"
              placeholder="最新の記事一覧"
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
          <tr>
            <th scope="row">
              <label for="jin_yhei_excluded_count_category_ids">記事数表記をしないカテゴリーID</label>
            </th>
            <td>
              <input type="text" 
                id="jin_yhei_excluded_count_category_ids" 
                class="regular-text" 
                name="jin_yhei_excluded_count_category_ids" 
                value="<?php echo get_option('jin_yhei_excluded_count_category_ids'); ?>"
                placeholder="2,8,10,12 (カテゴリーIDをカンマ区切りで入力)"
              >
            </td>
            <th scope="row">
              <label for="jin_yhei_category_posts_count_format">記事数表記フォーマット</label>
            </th>
            <td>
              <input type="text" 
                id="jin_yhei_category_posts_count_format" 
                class="regular-text" 
                name="jin_yhei_category_posts_count_format" 
                value="<?php echo get_option('jin_yhei_category_posts_count_format'); ?>"
                placeholder="訪問 %s 回"
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
    'hierarchical' => false,
    'orderby' => 'term_order',
  ));
  return sortCategorysByPriority( $child_categorys );
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

function get_target_rss_urls() {
  $rss_urls = get_option('jin_yhei_target_rss_urls');
  if( !$rss_urls ) {
    return [];
  }
  // 空白除去
  $rss_urls  = preg_replace("/( |　)/", "", $rss_urls );
  $rss_urls = explode("\n", $rss_urls); // 改行区切り
  foreach($rss_urls as $key => $rss_url) {
    // 空白の行はskipすること
    if( !$rss_url ) {
      unset($rss_urls[$key]);
    }
  }
  return $rss_urls;
}

function get_target_rss_titles() {
  $rss_titles = get_option('jin_yhei_target_rss_url_titles');
  if( !$rss_titles ) {
    return [];
  }
  // 空白除去
  $rss_titles  = preg_replace("/( |　)/", "", $rss_titles );
  $rss_titles = explode("\n", $rss_titles); // 改行区切り
  foreach($rss_titles as $key => $rss_title) {
    // 空白の行はskipすること
    if( !$rss_title ) {
      unset($rss_titles[$key]);
    }
  }
  return $rss_titles;
}

function get_another_rss($url) {
  $rss = fetch_feed($url);
  $rss_items = [];
  $maxitems = 0;
  if ( ! is_wp_error( $rss ) ) { // ちゃんとフィードが生成されているかをチェックします。
      // すべてのフィードから最新を出力します。
      $maxitems = $rss->get_item_quantity( get_option('posts_per_page') ); 
      // 0件から始めて指定した件数までの配列を生成します。
      $rss_items = $rss->get_items( 0, $maxitems );
  }
  return $rss_items;
}

/**
 * rss の item から アイキャッチ画像のURLを取得する
 * @param SimplePie $rss_item
 * @see http://simplepie.org/wiki/reference/start#simplepie_item
 */
function get_eyecatch_url_from_rss($rss_item) {
  $first_img_url = '';
  if ( preg_match( '/<img.+?src=[\'"]([^\'"]+?)[\'"].*?>/msi', $rss_item->get_content(), $matches ) ) {
      $first_img_url = $matches[1];
  }
  return $first_img_url;
}

/**
 * RSSにアイキャッチ画像を挿入する
 */
function rss_post_thumbnail($content) {
  global $post;
  if(has_post_thumbnail($post->ID)) {
    $content = '<p>' . get_the_post_thumbnail($post->ID) . '</p>' . $content;
  }
  return $content;
}
add_filter('the_excerpt_rss', 'rss_post_thumbnail');
add_filter('the_content_feed', 'rss_post_thumbnail');

/**
 * 対象のカテゴリーの優先度を取得
 * @param int $term_id カテゴリーID
 * @return int $priority
 */
function get_category_priority($term_id) {
  $priority = get_term_meta( $term_id, 'jin_yhei_category_priority', true );
  if( is_null($priority) || $priority === '' || !ctype_digit($priority) ) {
    $priority = JIN_YHEI_CATEGORY_PRIORITY_VALUE_DEFAULT;
  }
  return $priority;
}

/**
 * カテゴリー編集画面にカテゴリ優先度欄を追加
 * */
add_action ( 'category_add_form_fields', 'jin_yhei_category_priority' );
add_action ( 'edit_category_form_fields', 'jin_yhei_category_priority');
function jin_yhei_category_priority( $tag ) {
  $term_id = isset($tag->term_id) ? $tag->term_id : 0;
  $priority = get_category_priority($term_id);
?>
<tr class="form-field">
    <th><label for="jin_yhei_category_priority">表示優先度(大きい順に表示)</label></th>
    <td><input type="text" name="Cat_meta[jin_yhei_category_priority]" id="extra_text" value=<?php echo $priority ?> /></td>
</tr>
<?php
}
/**
 * カテゴリー編集画面のカテゴリー優先度保存処理
 * */
add_action ( 'edited_term', 'save_jin_yhei_category_priority');
function save_jin_yhei_category_priority( $term_id ) {
  if ( isset( $_POST['Cat_meta'] ) ) {
     $t_id = $term_id;
     $cat_keys = array_keys($_POST['Cat_meta']);
        foreach ($cat_keys as $key){
        if (isset($_POST['Cat_meta'][$key])){
           update_term_meta($t_id, $key, $_POST['Cat_meta'][$key]);
        }
     }
  }
}

/**
 * トップページに最新記事リンクを出すかどうか
 */
function is_enable_new_entry_show() {
  return get_option('jin_yhei_top_new_entry_enable');
}

/**
 * トップの最新記事2つを取得
 * @return WP_Query
 */
function get_recent_posts() {
  return new WP_Query( ['posts_per_page' => 2] );
}

/**
 * 最新の記事一覧のセクションのタイトルを取得する
 */
function get_recent_posts_section_title() {
  return get_option('jin_yhei_top_new_entry_section_title');
}

/**
 * 指定のカテゴリーを持つ記事数を取得する
 */
function getPostCountsByTermId($term_id) {
  $category = get_category($term_id);
  return $category->count;
}

/**
 * 記事数表示の対象外カテゴリーかどうか
 */
function is_excluded_count_category($term_id) {
  $category_ids_string = get_option('jin_yhei_excluded_count_category_ids');
  if(!$category_ids_string) {
    // 未設定なら全てのカテゴリーが記事数表示の対象
    return false;
  }
  $excluded_count_category_ids = explode(',', preg_replace("/( |　)/", "", $category_ids_string ));
  if( in_array(strval($term_id), $excluded_count_category_ids, true) ) {
    return true;
  }
  return false;
}

/**
 * 記事数表示のフォーマットを取得する
 */
function get_category_posts_count_format() {
  $format = get_option('jin_yhei_category_posts_count_format', "");
  if( !$format ) {
    return JIN_YHEI_CATEGORY_POSTS_COUNT_FORMAT;
  }
  return $format;
}

?>
