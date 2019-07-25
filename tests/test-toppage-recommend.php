<?php
/**
 * Class トップページに下記の記事を表示する
 *
 * @package Jin_Child
 */

/**
 * Sample test case.
 */
class 最上部におすすめ記事リンク extends WP_UnitTestCase {

	public function setUp() {
		$post_id = $this->factory->post->create( array( 'post_title' => 'おすすめタグがついた記事' ) );
		wp_set_post_tags( $post_id, 'おすすめ', true );
		$post_id2 = $this->factory->post->create( array( 'post_title' => 'おすすめタグなし記事' ) );
	}

	/**
	 * @test
	 */
	public function タグの名前が「おすすめ」の記事を取得する() {
		$the_query = get_recommended_posts();
		$the_query->have_posts();
		$the_query->the_post();
		$tags = get_the_tags();
		$this->assertEquals('おすすめ', $tags[0]->name);
	}
}
