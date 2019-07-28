<?php
/**
 * Class 催事別カテゴリー一覧ページ
 *
 * @package Jin_Child
 */

class 催事別カテゴリー一覧ページ extends WP_UnitTestCase {

	public $_category_id;
	public $_category_id_child;
	public $_category_id_mago;

	public function setUp() {
		$this->_category_id = wp_create_category( 'イベント別' );
		$this->_category_id_child = wp_create_category( '東区祭り', $this->_category_id );
		wp_create_category( '西区祭り', $this->_category_id );
		$this->_category_id_mago = wp_create_category( '美香保公園夏祭り', $this->_category_id_child );
		update_option('jin_yhei_show_only_one_category_ids', $this->_category_id);
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
		$this->assertEquals( '東区祭り', getChildCategorys($this->_category_id)[0]->name );
	}

	/**
	 * @test
	 */
	public function 設定したカテゴリーの直近の子カテゴリーが複数取得できること() {
		update_option('jin_yhei_show_only_one_category_ids', $this->_category_id);
		$this->assertEquals( '西区祭り', getChildCategorys($this->_category_id)[1]->name );
	}
}
