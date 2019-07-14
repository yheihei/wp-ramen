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
		$category_id2 = wp_create_category( 'イベント別' );
		update_option('jin_yhei_top_categories', $category_id . "," . $category_id2);
	}

	/**
	 * @test
	 */
	public function 札幌地区別のカテゴリー情報が取得できる() {
		// Replace this with some actual testing code.
		$this->assertEquals( '札幌地区別', getFeaturedCategorys()[0]->name );
	}

	/**
	 * @test
	 */
	public function イベント別カテゴリーのカテゴリー情報が取得できる() {
		// Replace this with some actual testing code.
		$categorys = getFeaturedCategorys();
		$this->assertEquals( 'イベント別', $categorys[1]->name );
	}

	/**
	 * @test
	 */
	public function 設定が空の場合カテゴリーの数がゼロとなる() {
		// Replace this with some actual testing code.
		update_option('jin_yhei_top_categories', null);
		$this->assertTrue( empty(getFeaturedCategorys()) );
	}

	/**
	 * @test
	 */
	public function 設定が数値でない場合カテゴリーの数がゼロとなる() {
		// Replace this with some actual testing code.
		update_option('jin_yhei_top_categories', 'hoge,hage,koge');
		$this->assertTrue( empty(getFeaturedCategorys()) );
	}
}
