<?php
/**
 * Class トップページに下記の記事を表示する
 *
 * @package Jin_Child
 */

/**
 * Sample test case.
 */
class トップページに指定のカテゴリー一覧を表示する extends WP_UnitTestCase {

  public function setUp() {
    $category_id = wp_create_category( '札幌地区別' );
    $category_id_child = wp_create_category( '東区', $category_id );
    $category_id2 = wp_create_category( 'イベント別' );
    update_option('jin_yhei_top_categories', $category_id . "," . $category_id2);
  }

  /**
   * @test
   */
  public function 札幌地区別のカテゴリー情報が取得できる() {
    $this->assertEquals( '札幌地区別', getFeaturedCategorys()[0]->name );
  }

  /**
   * @test
   */
  public function イベント別カテゴリーのカテゴリー情報が取得できる() {
    $categorys = getFeaturedCategorys();
    $this->assertEquals( 'イベント別', $categorys[1]->name );
  }

  /**
   * @test
   */
  public function 設定が空の場合カテゴリーの数がゼロとなる() {
    update_option('jin_yhei_top_categories', null);
    $this->assertTrue( empty(getFeaturedCategorys()) );
  }

  /**
   * @test
   */
  public function 設定が数値でない場合カテゴリーの数がゼロとなる() {
    update_option('jin_yhei_top_categories', 'hoge,hage,koge');
    $this->assertTrue( empty(getFeaturedCategorys()) );
  }

  /**
   * @test
   */
  public function 札幌地区別カテゴリーの子カテゴリーの情報が取得できる() {
    $categorys = getFeaturedCategorys();
    foreach( $categorys as $category ) {
      if($category->name !== '札幌地区別') continue;
      $this->assertEquals( '東区', getFeaturedCategorysChilds($category->term_id)[0]->name );
    }
  }

  /**
   * @test
   */
  public function カテゴリーのアイキャッチ画像が設定されていないことの判定() {
    $categorys = getFeaturedCategorys();
    $this->assertFalse(cps_has_post_thumbnail($categorys[0]->term_id));
  }

  /**
   * @test
   */
  public function カテゴリーのアイキャッチ画像が設定されていることの判定() {
    $categorys = getFeaturedCategorys();
    $term_id = $categorys[0]->term_id;
    // カテゴリーのアイキャッチ画像を設定
    update_option( $categorys[0]->term_id, [
      'cps_image_cat' => 'http://example.com/wp-content/uploads/2019/07/atubetuku.jpg',
    ] );
    $this->assertTrue(cps_has_post_thumbnail($categorys[0]->term_id));
  }

  /**
   * @test
   */
  public function タブの中に新着表示を含む場合設定値をtrueで返す() {
    update_option('jin_yhei_top_categories_is_involved_new_entry', true);
    $this->assertTrue( is_involved_new_entry_in_category_tabs() );
  }
}
