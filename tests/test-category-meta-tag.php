<?php
/**
 * Class カテゴリーにタグを設置する
 *
 * @package Jin_Child
 * @test
 * @group task_21
 */
class カテゴリーにタグを設置する extends WP_UnitTestCase {

  public $_category_id;
  public $_category_id_2;
  public $_post_id;
  const TAG_1 = 'タグ1';
  const TAG_2 = 'タグ2';
  const TAG_3 = 'タグ3';
  const SINGLE_TAG = self::TAG_1;
  const MULTIPLE_TAG = self::TAG_1 . ', ' . self::TAG_2;
  const MULTIPLE_TAG_2 = self::TAG_2 . ', ' . self::TAG_3;

  public function setUp() {
    $this->_category_id = wp_create_category( 'タグ付きカテゴリー' );
    $this->_category_id_2 = wp_create_category( 'タグ付きカテゴリー2' );
    $this->_post_id = $this->factory->post->create( array( 'post_title' => 'タグ付きカテゴリーの記事' ) );
    wp_set_post_categories( $this->_post_id, [$this->_category_id, $this->_category_id_2] );
  }

  public function tearDown() {
    update_term_meta( $this->_category_id, 'jin_yhei_category_tag_names', null );
    update_term_meta( $this->_category_id_2, 'jin_yhei_category_tag_names', null );
    update_term_meta( $this->_category_id, 'jin_yhei_category_tag_ids_string', null );
    update_term_meta( $this->_category_id_2, 'jin_yhei_category_tag_ids_string', null );
    wp_delete_post( $this->_post_id, $force_delete=true );
    $post_tags = get_tags(['hide_empty' => false]);
    foreach( $post_tags as $post_tag ) {
      wp_delete_term( $post_tag->term_id, 'post_tag' );
    }
  }

  /**
   * @test
   */
  public function カテゴリーのタグ情報をセットし、セットした値を取得できる() {
    // カテゴリーに複数タグを設定
    $term_ids = set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2] );
    $this->assertEquals( $term_ids[1], get_category_tags( $this->_category_id )[1]->term_id );
  }

  /**
   * @test
   */
  public function カテゴリーのタグ情報を複数取得しカンマ区切りのstring形式で取得できる() {
    // カテゴリーに複数タグを設定
    $term_ids = set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2] );
    $this->assertEquals( self::TAG_1 . "," . self::TAG_2, get_category_tag_names_string( $this->_category_id ) );
  }

  /**
   * @test
   */
  public function 空のカテゴリータグを設定した場合カテゴリータグがセットされないこと() {
    // カテゴリーに空のタグ情報を付与
    $term_ids = set_category_tags( $this->_category_id, ['',''] );
    $this->assertEquals( 0, count(get_category_tags( $this->_category_id )) );
  }

  /**
   * @test
   */
  public function 現在のすべてのカテゴリータグを取得する() {
    set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2] );
    set_category_tags( $this->_category_id_2, [self::TAG_2, self::TAG_3] );
    $category_tags = get_all_category_tags();
    foreach ( $category_tags as $category_tag ) {
      $this->assertEquals( true,
        ($category_tag->name === self::TAG_1) ||
        ($category_tag->name === self::TAG_2) ||
        ($category_tag->name === self::TAG_3)
      );
    }
  }

  /**
   * @test
   */
  public function カテゴリーのタグを作成した時、投稿のタグも同様に作成する() {
    set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2, self::TAG_3] );
    $post_tags = get_tags(['hide_empty' => false]);
    foreach( $post_tags as $post_tag ) {
      $this->assertTrue(
        ( $post_tag->name === self::TAG_1 ) ||
        ( $post_tag->name === self::TAG_2 ) ||
        ( $post_tag->name === self::TAG_3 )
      );
    }
  }

  /**
   * @test
   */
  public function カテゴリーのタグを作成した時、タグのIDを返却する() {
    $term_ids = set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2, self::TAG_3] );
    $this->assertContainsOnly('int', $term_ids);
  }

  /**
   * @test
   */
  public function カテゴリーのタグを作成した時、作成済みであった場合も既存のタグIDを返却する() {
    // 2回実行して既存のタグが存在する状態にする
    set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2, self::TAG_3] );
    $term_ids = set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2, self::TAG_3] );
    $this->assertContainsOnly('int', $term_ids);
  }

  /**
   * @test
   */
  public function カテゴリーのタグを作成した時、term_metaにIDの文字列が保存される() {
    $term_ids = set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2, self::TAG_3] );
    $this->assertEquals( 
      strval($term_ids[0]) . ',' . strval($term_ids[1]) . ',' . strval($term_ids[2]),
      get_term_meta( $this->_category_id, 'jin_yhei_category_tag_ids_string', true )
    );
  }

  /**
   * @test
   */
  public function 指定のタグを付与されたカテゴリー一覧を取得() {
    $term_ids = set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2] );
    $this->assertEquals( $this->_category_id , get_categorys_by_tag( $term_ids[0] )[0]->term_id );
  }

  /**
   * @test
   */
  public function タグクラウドに、カテゴリーに付与したタグが含まれる() {
    $term_ids = set_category_tags( $this->_category_id, [self::TAG_1, self::TAG_2] );
    wp_set_post_tags( $this->_post_id, self::TAG_2, false );
    wp_set_post_tags( $this->_post_id, self::TAG_3, false );
    $tag_clouds = wp_tag_cloud( 'format=array' );
    foreach( $tag_clouds as $tag_cloud ) {
      $this->assertTrue(
        preg_match('/'. self::TAG_1 . '/',$tag_cloud) ||
        preg_match('/'. self::TAG_2 . '/',$tag_cloud) ||
        preg_match('/'. self::TAG_3 . '/',$tag_cloud)
      );
    }
  }

  /**
   * @test
   */
  public function カテゴリータグが紐つけられている数を取得できること() {
    $term_ids = set_category_tags( $this->_category_id, [self::TAG_1] );
    $term_ids = set_category_tags( $this->_category_id_2, [self::TAG_1] );
    $this->assertEquals(2, get_count_category_with_tag( $term_ids[0] ) );
  }
}
