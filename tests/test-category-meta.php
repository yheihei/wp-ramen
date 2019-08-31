<?php
/**
 * Class カテゴリーページの優先度設定
 *
 * @package Jin_Child
 */

class カテゴリーページの優先度設定 extends WP_UnitTestCase {

  public $_category_id;
  public $_category_id_child1;
  public $_category_id_child2;
  public $_category_id_child3;
  public $_category_id_child4_priority_none;
  public $_category_id_child5_priority_none;
  public $_category_id_priority_none;

  const HIGHEST_PRIORITY = 128;
  const MIDDLE_PRIORITY = 64;
  const LOWEST_PRIORITY = 32;
  const ZERO_PRIORITY = 0;

  public function setUp() {
    $this->_category_id = wp_create_category( 'イベント別' );
    $this->_category_id_child1 = wp_create_category( '東区祭り', $this->_category_id );
    $this->_category_id_child2 = wp_create_category( '西区祭り', $this->_category_id );
    $this->_category_id_child3 = wp_create_category( '中央区祭り', $this->_category_id );
    $this->_category_id_child4_priority_none = wp_create_category( '優先度未設定祭り', $this->_category_id );
    $this->_category_id_child5_priority_none = wp_create_category( '優先度未設定祭り2', $this->_category_id );
    $this->_category_id_priority_none = wp_create_category( '優先度未設定' );
    $this->_category_id_priority_zero = wp_create_category( '優先度0' );
    $this->_category_id_priority_not_number = wp_create_category( '数値じゃない' );
    // カテゴリーの優先度を設定
    update_term_meta( $this->_category_id_child1, 'jin_yhei_category_priority', self::LOWEST_PRIORITY );
    update_term_meta( $this->_category_id_child2, 'jin_yhei_category_priority', self::MIDDLE_PRIORITY );
    update_term_meta( $this->_category_id_child3, 'jin_yhei_category_priority', self::HIGHEST_PRIORITY );
    update_term_meta( $this->_category_id_priority_zero, 'jin_yhei_category_priority', self::ZERO_PRIORITY );
    update_term_meta( $this->_category_id_priority_not_number, 'jin_yhei_category_priority', '数値じゃない' );
  }

  /**
   * @test
   */
  public function 対象のカテゴリーの優先度が取得できる() {
    $this->assertEquals( self::LOWEST_PRIORITY, get_category_priority( $this->_category_id_child1 ) );
  }

  /**
   * @test
   */
  public function カテゴリーの優先度が未設定の場合デフォルト値が返ってくる() {
    $this->assertEquals( JIN_YHEI_CATEGORY_PRIORITY_VALUE_DEFAULT, get_category_priority( $this->_category_id_priority_none ) );
  }

  /**
   * @test
   */
  public function カテゴリーの優先度が0で保存できること() {
    $this->assertEquals( self::ZERO_PRIORITY, get_category_priority( $this->_category_id_priority_zero ) );
  }

  /**
   * @test
   */
  public function カテゴリーの優先度が数値じゃない場合デフォルト値が返ってくること() {
    $this->assertEquals( JIN_YHEI_CATEGORY_PRIORITY_VALUE_DEFAULT, get_category_priority( $this->_category_id_priority_not_number ) );
  }

  /**
   * @test
   */
  public function 優先度の一番高い子カテゴリーが最初に返ってくる() {
    $categorys = getFeaturedCategorysChilds( $this->_category_id );
    $sorted_categorys = [];
    foreach( $categorys as $category ) {
      $sorted_categorys[] = $category;
    }
    $this->assertEquals( self::HIGHEST_PRIORITY, get_category_priority( $sorted_categorys[0]->term_id ) );
  }

  /**
   * @test
   */
  public function 優先度が中間の子カテゴリーが2番目に返ってくる() {
    $categorys = getFeaturedCategorysChilds( $this->_category_id );
    $sorted_categorys = [];
    foreach( $categorys as $category ) {
      $sorted_categorys[] = $category;
    }
    $this->assertEquals( self::MIDDLE_PRIORITY, get_category_priority( $sorted_categorys[1]->term_id ) );
  }

  /**
   * @test
   */
  public function 優先度が未設定の子カテゴリーが最後に返ってくる() {
    $categorys = getFeaturedCategorysChilds( $this->_category_id );
    $sorted_categorys = [];
    foreach( $categorys as $category ) {
      $sorted_categorys[] = $category;
    }
    $this->assertEquals( JIN_YHEI_CATEGORY_PRIORITY_VALUE_DEFAULT, get_category_priority( $sorted_categorys[3]->term_id ) );
  }

  /**
   * @test
   */
  public function 優先度が未設定の子カテゴリーが二つあっても取得できている() {
    $categorys = getFeaturedCategorysChilds( $this->_category_id );
    $this->assertEquals( 5, count($categorys) );
  }
}
