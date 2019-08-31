<?php
/**
 * Class メインブログにラーメン、カレーブログの新着を表示する
 *
 * @package Jin_Child
 */

class メインブログにラーメン、カレーブログの新着を表示する extends WP_UnitTestCase {

  const TARGET_RSS_RAMEN_URL = "http://example.com/ramen";
  const TARGET_RSS_CURRY_URL = "http://example.com/curry";
  const TARGET_RSS_RAMEN_TITLE = "ラーメン";
  const TARGET_RSS_CURRY_TITLE = "カレー";

  const SAMPLE_RSS_URL = "https://yhei-web-design.com/";

  public function setUp() {
    
  }

  /**
   * @test
   */
  public function 管理画面からRSSを取得するURLを設定できること() {
    update_option('jin_yhei_target_rss_urls', self::TARGET_RSS_RAMEN_URL);
    $urls = get_target_rss_urls();
    $this->assertEquals( self::TARGET_RSS_RAMEN_URL, $urls[0] );
  }

  /**
   * @test
   */
  public function 管理画面からRSSを取得するURLを複数設定できること() {
    update_option('jin_yhei_target_rss_urls', self::TARGET_RSS_RAMEN_URL . "\n" . self::TARGET_RSS_CURRY_URL);
    $urls = get_target_rss_urls();
    $this->assertEquals( self::TARGET_RSS_CURRY_URL, $urls[1] );
  }

  /**
   * @test
   */
  public function 空白の行はskipすること() {
    update_option('jin_yhei_target_rss_urls', 
    self::TARGET_RSS_RAMEN_URL . "\n" . 
    "\n" . // 何もない行が一つある
    self::TARGET_RSS_CURRY_URL);
    $urls = get_target_rss_urls();
    $this->assertEquals( 2, count($urls) );
  }

  /**
   * @test
   */
  public function 管理画面からRSSを取得するタイトルを設定できること() {
    update_option('jin_yhei_target_rss_url_titles', 
    self::TARGET_RSS_RAMEN_TITLE . "\n" . 
    self::TARGET_RSS_CURRY_TITLE);
    $titles = get_target_rss_titles();
    $this->assertEquals( self::TARGET_RSS_CURRY_TITLE, $titles[1] );
  }

  /**
   * @test
   */
  public function url指定でrssを取得できる() {
    $rss_items = get_another_rss(self::SAMPLE_RSS_URL);
    foreach( $rss_items as $item ) {
      $this->assertNotNull($item->get_title());
      $this->assertNotNull($item->get_permalink());
      $this->assertNotNull($item->get_content());
    }
  }

  /**
   * @test
   */
  public function rssからアイキャッチ画像を取得する() {
    $rss_items = get_another_rss(self::SAMPLE_RSS_URL);
    foreach( $rss_items as $item ) {
      $img_url = get_eyecatch_url_from_rss($item);
      if($img_url) {
        $this->assertTrue( boolval(preg_match('/jpg|png|gif|/',$img_url)) );
      }
    }
  }

  /**
   * @test
   */
  public function 表示件数設定で設定した件数取得すること() {
    update_option('posts_per_page', 5);
    $rss_items = get_another_rss(self::SAMPLE_RSS_URL);
    $this->assertEquals(5, count($rss_items));
  }

}
