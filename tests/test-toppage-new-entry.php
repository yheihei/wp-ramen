<?php
/**
 * トップページのタブの上に最新記事2記事分へのリンクを設置
 *
 * @package Jin_Child
 */

/**
 * Sample test case.
 */
class トップページのタブの上に最新記事2記事分へのリンクを設置 extends WP_UnitTestCase {

  public function setUp() {
    update_option('jin_yhei_top_new_entry_enable', true);
    $this->factory->post->create( array( 'post_title' => '一つ目の記事' ) );
    $this->factory->post->create( array( 'post_title' => '二つ目の記事' ) );
    $this->factory->post->create( array( 'post_title' => '三つ目の記事' ) );
  }

  /**
   * @test
   */
  public function 新着記事表示の設定値がONで保存できる() {
    $this->assertEquals( true, is_enable_new_entry_show() );
  }

  /**
   * @test
   */
  public function 未設定の時はfalseになる() {
    update_option('jin_yhei_top_new_entry_enable', '');
    $this->assertEquals( false, is_enable_new_entry_show() );
  }

  /**
   * @test
   */
  public function 最新の記事を2記事取得する() {
    $the_query = get_recent_posts();
    $this->assertEquals(2, $the_query->post_count);
  }

  /**
   * @test
   */
  public function 最新の記事一覧のセクションのタイトルを取得する() {
    update_option('jin_yhei_top_new_entry_section_title', 'タイトル');
    $this->assertEquals('タイトル', get_recent_posts_section_title());
  }
}
