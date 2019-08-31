<?php
/**
 * Class まとめ記事、固定記事、特集記事欄※「札幌市西区のおすすめラーメンまとめ」など
 *
 * @package Jin_Child
 */

class まとめ記事、固定記事、特集記事欄※「札幌市西区のおすすめラーメンまとめ」など extends WP_UnitTestCase {

  public function setUp() {
    update_option('jin_yhei_top_tag_names', 'おすすめ,まとめ記事');
    $post_id = $this->factory->post->create( array( 'post_title' => 'おすすめタグがついた記事' ) );
    wp_set_post_tags( $post_id, 'おすすめ', true );
    $post_id2 = $this->factory->post->create( array( 'post_title' => 'まとめ記事タグがついた記事' ) );
    wp_set_post_tags( $post_id2, 'まとめ記事', true );
  }

  public function tearDown() {
    wp_reset_postdata();
  }
  

  /**
   * @test
   */
  public function スライドショーに表示するタグ名を登録できる() {
    update_option('jin_yhei_top_tag_names', 'おすすめ');
    $registered_tag_names = get_registered_tag_names();
    $this->assertEquals('おすすめ', $registered_tag_names[0]);
  }

  /**
   * @test
   */
  public function スライドショーに表示するタグ名を複数登録できる() {
    update_option('jin_yhei_top_tag_names', 'おすすめ,まとめ記事');
    $registered_tag_names = get_registered_tag_names();
    $this->assertEquals('まとめ記事', $registered_tag_names[1]);
  }

  /**
   * @test
   */
  public function スライドショーに表示するタグ名に空白が混じってもきちんと登録できる() {
    update_option('jin_yhei_top_tag_names', 'おすすめ,     まとめ記事');
    $registered_tag_names = get_registered_tag_names();
    $this->assertEquals('まとめ記事', $registered_tag_names[1]);
  }

  /**
   * @test
   */
  public function スライドショーに表示するタグ名が未設定の場合空で取得できること() {
    update_option('jin_yhei_top_tag_names', null);
    $registered_tag_names = get_registered_tag_names();
    $this->assertTrue(empty($registered_tag_names));
  }

  /**
   * @test
   */
  public function 設定されたタグの記事が一つ取得できる() {
    update_option('jin_yhei_top_tag_names', 'おすすめ');
    $the_query = get_recommended_posts();
    $the_query->the_post();
    $tags = get_the_tags();
    $this->assertEquals('おすすめ',$tags[0]->name);
  }

  /**
   * @test
   */
  public function 設定されたタグが一つもない場合記事が取得できないこと() {
    update_option('jin_yhei_top_tag_names', null);
    $the_query = get_recommended_posts();
    $this->assertFalse($the_query->have_posts());
  }
}
