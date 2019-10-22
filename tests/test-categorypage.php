<?php
/**
 * Class 催事別カテゴリー一覧ページ
 *
 * @package Jin_Child
 */

class カテゴリー一覧ページ extends WP_UnitTestCase {

  public $_category_id;
  public $_category_id_child;
  public $_category_id_child2;
  public $_category_id_mago;

  public $_post_id1;
  public $_post_id2;
  public $_post_id3;
  public $_post_id4;

  public function setUp() {
    $this->_category_id = wp_create_category( 'イベント別' );
    $this->_category_id_child = wp_create_category( '東区祭り_task_19', $this->_category_id );
    $this->_category_id_child2 = wp_create_category( '西区祭り_task_19', $this->_category_id );
    $this->_category_id_mago = wp_create_category( '美香保公園夏祭り', $this->_category_id_child );
    update_option('jin_yhei_show_only_one_category_ids', $this->_category_id);

    // 東区カテゴリーの記事3つと 西区カテゴリーの子カテゴリー記事を1つ作成する
    $this->_post_id1 = $this->factory->post->create();
    $this->_post_id2 = $this->factory->post->create();
    $this->_post_id3 = $this->factory->post->create();
    $this->_post_id4 = $this->factory->post->create();
    wp_set_post_categories($this->_post_id1, $this->_category_id_child, false);
    wp_set_post_categories($this->_post_id2, $this->_category_id_child, false);
    wp_set_post_categories($this->_post_id3, $this->_category_id_child, false);
    wp_set_post_categories($this->_post_id4, $this->_category_id_mago, false);
  }

  public function tearDown() {
    wp_delete_post( $this->_post_id1, true );
    wp_delete_post( $this->_post_id2, true );
    wp_delete_post( $this->_post_id3, true );
    wp_delete_post( $this->_post_id4, true );
    update_option('jin_yhei_category_posts_count_format', NULL);
  }

  /**
   * @test
   */
  public function 対象のカテゴリーを管理画面から指定できること() {
    $category_ids = get_show_only_one_category_ids();
    $this->assertEquals( $this->_category_id, get_show_only_one_category_ids()[0] );
  }

  /**
   * @test
   */
  public function 対象のカテゴリーを管理画面から複数指定できること() {
    update_option('jin_yhei_show_only_one_category_ids', $this->_category_id . ',' . $this->_category_id_child);
    $category_ids = get_show_only_one_category_ids();
    $this->assertEquals( $this->_category_id, get_show_only_one_category_ids()[0] );
  }

  /**
   * @test
   */
  public function 設定したカテゴリーの直近の子カテゴリーが取得できること() {
    update_option('jin_yhei_show_only_one_category_ids', $this->_category_id);
    $this->assertEquals( '東区祭り_task_19', getChildCategorys($this->_category_id)[0]->name );
  }

  /**
   * @test
   */
  public function 設定したカテゴリーの直近の子カテゴリーが複数取得できること() {
    update_option('jin_yhei_show_only_one_category_ids', $this->_category_id);
    $this->assertEquals( '西区祭り_task_19', getChildCategorys($this->_category_id)[1]->name );
  }

  /**
   * @test
   * @group task_19
   */
  public function 東区カテゴリーの記事数が取得できる() {
    $this->assertEquals(3, getPostCountsByTermId($this->_category_id_child));
  }

  /**
   * @test
   * @group task_19
   */
  public function 孫カテゴリーの記事数まではカウントしないこと() {
    $this->assertEquals(0, getPostCountsByTermId($this->_category_id_child2));
  }

  /**
   * @test
   * @group task_19
   */
  public function 記事数表示の対象外term_idである() {
    update_option('jin_yhei_excluded_count_category_ids', $this->_category_id);
    $this->assertTrue( is_excluded_count_category($this->_category_id) );
  }

  /**
   * @test
   * @group task_19
   */
  public function 複数の対象外を設定した時に、記事数表示の対象外term_idである() {
    update_option('jin_yhei_excluded_count_category_ids', "{$this->_category_id}, 0");
    $this->assertTrue( is_excluded_count_category($this->_category_id) );
  }

  /**
   * @test
   * @group task_19
   */
  public function 記事数表示の表記フォーマットがデフォルト値で取得できる() {
    $this->assertEquals( '訪問 %s 回', get_category_posts_count_format() );
  }

  /**
   * @test
   * @group task_19
   */
  public function 記事数表示の表記フォーマットが設定された値で取得できる() {
    update_option('jin_yhei_category_posts_count_format', "記事数 %s 記事");
    $this->assertEquals( '記事数 %s 記事', get_category_posts_count_format() );
  }
}
