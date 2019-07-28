<?php if( ! get_theme_mod('toppost_list_cat') ) :?>
<div class="toppost-list-box-simple">
	<div class="post-list-mag">
		
		<?php
			$ad_infeed_pc_num = get_option('ad_infeed_pc_num');
			$ad_infeed_sp_num = get_option('ad_infeed_sp_num');
		?>
		<?php if( isset($ad_infeed_pc_num) || isset($ad_infeed_sp_num) ) :?>
			<?php get_template_part('include/liststyle/parts/post-list-mag-parts-infeed'); ?>
		<?php else: ?>
			<?php while (have_posts()) : the_post(); ?>
			<?php get_template_part('include/liststyle/parts/post-list-mag-parts'); ?>
			<?php endwhile; ?>
		<?php endif; ?>

		<section class="pager-top">
			<?php if( function_exists('responsive_pagination') ) { responsive_pagination( $wp_query->max_num_pages ); } ?>
		</section>
	</div>
</div>
<?php else: ?>
<div class="toppost-list-box">
	<?php
		$toppost_list_cat = get_theme_mod('toppost_list_cat');
		$list_cat_id = explode(",", $toppost_list_cat);
		$list_cat_num = 0;
		$list_cat_num2 = 0;
	?>
	<input type="radio" name="switch" id="tab-1" checked>
	<input type="radio" name="switch" id="tab-2">
	<input type="radio" name="switch" id="tab-3">
	<input type="radio" name="switch" id="tab-4">
	<input type="radio" name="switch" id="tab-5">
	
	<?php
		$featured_categorys = getFeaturedCategorys();
	?>
	<ul class="tabBtn-mag">
	<?php 
		$global_tab_index_count = 0;
		// カテゴリー一覧表示が未設定の場合通常表示
		if( empty($featured_categorys) ) : 
		?>
		<li><label for="tab-1">最新記事</label></li>
		<?php while( isset($list_cat_id[$list_cat_num2]) ): ?>
		<?php
			// カテゴリー情報を取得
			$category_tab = get_category($list_cat_id[$list_cat_num2]);
			$cat_name_tab = $category_tab->cat_name;
		?>
		<li><label for="tab-<?php echo $list_cat_num2+2; ?>"><?php echo $cat_name_tab; ?></label></li>
		<?php $list_cat_num2++; ?>
		<?php endwhile; ?>
		<?php else:
			// トップページに指定のカテゴリー一覧を表示する 場合
			?>
		<?php foreach($featured_categorys as $index => $featured_category) :
			$global_tab_index_count = $index+1;
			?>
			<li><label for="tab-<?= $index+1 ?>"><?= $featured_category->name ?></label></li>
		<?php endforeach; ?>
		<?php // メインブログにラーメン、カレーブログの新着を表示する
			$other_blog_titles = get_target_rss_titles();
			foreach($other_blog_titles as $index => $other_blog_title) :
		?>
			<li><label for="tab-<?= $global_tab_index_count+$index+1 ?>"><?= $other_blog_title ?></label></li>
			<?php endforeach; ?>
	<?php endif; ?>
	</ul>
	<div class="toppost-list-box-inner">
		<?php foreach($featured_categorys as $featured_category) : ?>
			<div class="post-list-mag autoheight">
					<?php
						$ad_infeed_pc_num = get_option('ad_infeed_pc_num');
						$ad_infeed_sp_num = get_option('ad_infeed_sp_num');
						$infeed_ad_pc = explode(",", $ad_infeed_pc_num);
						$infeed_ad_sp = explode(",", $ad_infeed_sp_num);
						$infeed_ad_count = 1;
						$infeed_ad_sp_num = 0;
						$infeed_ad_num = 0;
					?>
					<?php 
					$featured_child_categorys = getFeaturedCategorysChilds($featured_category->term_id);
					foreach($featured_child_categorys as $featured_child_category) : ?>
					<?php
						// カテゴリー情報を取得
						// var_dump($featured_child_category);
						// $category = get_the_category();
						// if(isset($category[0])){
						$cat_id   = $featured_child_category->term_id;
						$cat_name = $featured_child_category->name;
						$cat_slug = $featured_child_category->slug;
						$cat_url = get_category_link($featured_child_category->term_id);
						// }else{
						// 	$cat_name = "";
						// }
					?>

					<?php if( ! is_mobile() && isset($infeed_ad_pc[$infeed_ad_num]) && $infeed_ad_pc[$infeed_ad_num] == $infeed_ad_count ): ?>

						<?php if( ! get_option('ad_infeed_magazine') == null ) : ?>
							<div class="post-list-item pconly">
								<div class="post-list-inner-infeed">
									<?php echo get_option('ad_infeed_magazine'); ?>
								</div>
							</div>

						<?php endif; ?>

						<?php $infeed_ad_num++; $infeed_ad_count++;  ?>

						<?php if( isset($infeed_ad_pc[$infeed_ad_num]) && $infeed_ad_pc[$infeed_ad_num] == $infeed_ad_count ): ?>

							<?php if( ! get_option('ad_infeed_magazine') == null ) : ?>
								<div class="post-list-item pconly">
									<div class="post-list-inner-infeed">
										<?php echo get_option('ad_infeed_magazine'); ?>
									</div>
								</div>
							<?php endif; ?>
							<?php $infeed_ad_num++; $infeed_ad_count++;?>
						<?php endif; ?>

					<?php endif; ?>

					<?php
						$cat_class = get_category($featured_child_category->term_id);
						$cat_option = get_option($featured_child_category->term_id);
		
						if( is_array($cat_option) ){
						$cat_option = array_merge(array('cont'=>''),$cat_option);
						}
						if( ! empty($cat_option['cps_image_cat']) ){
							$cat_eyecatch = $cat_option['cps_image_cat'];
						}
						$cat_desc = $cat_option['cps_meta_content'];
					?>
					<article class="post-list-item" itemscope itemtype="https://schema.org/BlogPosting">
						<a class="post-list-link" rel="bookmark" href="<?php echo get_category_link( $featured_child_category->term_id ); ?>" itemprop='mainEntityOfPage'>
							<div class="post-list-inner">
								<div class="post-list-thumb" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
									<?php if ( ! is_mobile() ): ?>
										<?php if ( cps_has_post_thumbnail( $featured_child_category->term_id ) ): ?>
											<?php cps_category_eyecatch_by_term_id($featured_child_category->term_id); ?>
											<meta itemprop="url" content="<?php cps_thumb_info('url'); ?>">
											<meta itemprop="width" content="640">
											<meta itemprop="height" content="360">
										<?php else: ?>
											<img src="<?php echo get_jin_noimage_url(); ?>" width="480" height="270" alt="no image" />
											<meta itemprop="url" content="<?php bloginfo('template_url'); ?>/img/noimg320.png">
											<meta itemprop="width" content="480">
											<meta itemprop="height" content="270">
										<?php endif; ?>
									<?php else: ?>
										<?php if( is_post_list_style() == "magazinestyle-sp1col" ): ?>
											<?php if ( cps_has_post_thumbnail( $featured_child_category->term_id ) ): ?>
												<?php cps_category_eyecatch_by_term_id($featured_child_category->term_id); ?>
												<meta itemprop="url" content="<?php cps_thumb_info('url'); ?>">
												<meta itemprop="width" content="640">
												<meta itemprop="height" content="360">
											<?php else: ?>
												<img src="<?php echo get_jin_noimage_url(); ?>" width="480" height="270" alt="no image" />
												<meta itemprop="url" content="<?php bloginfo('template_url'); ?>/img/noimg320.png">
												<meta itemprop="width" content="480">
												<meta itemprop="height" content="270">
											<?php endif; ?>
										<?php else: ?>
											<?php if ( cps_has_post_thumbnail( $featured_child_category->term_id ) ): ?>
												<?php cps_category_eyecatch_by_term_id($featured_child_category->term_id); ?>
												<meta itemprop="url" content="<?php cps_thumb_info('url'); ?>">
												<meta itemprop="width" content="320">
												<meta itemprop="height" content="180">
											<?php else: ?>
												<img src="<?php echo get_jin_noimage_url(); ?>" width="480" height="270" alt="no image" />
												<meta itemprop="url" content="<?php bloginfo('template_url'); ?>/img/noimg320.png">
												<meta itemprop="width" content="320">
												<meta itemprop="height" content="180">
											<?php endif; ?>
										<?php endif; ?>
									<?php endif; ?>
								</div>
								<div class="post-list-meta vcard">

									<h2 class="post-list-title entry-title" itemprop="headline"><?php echo $featured_child_category->name; ?></h2>

									<span class="writer fn" itemprop="author" itemscope itemtype="http://schema.org/Person"><span itemprop="name"><?php the_author(); ?></span></span>

									<div class="post-list-publisher" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
										<span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
											<span itemprop="url"><?php echo get_topnavi_logo_image_url(); ?></span>
										</span>
										<span itemprop="name"><?php bloginfo('name'); ?></span>
									</div>
								</div>
							</div>
						</a>
					</article>
					<?php $infeed_ad_count++;?>
					<?php endforeach; ?>
		</div>
		<?php endforeach; ?>

		<?php 
		// メインブログにラーメン、カレーブログの新着を表示する
		$rss_urls = get_target_rss_urls();
		foreach($rss_urls as $rss_url) : ?>
			<div class="post-list-mag autoheight">
					<?php
						$ad_infeed_pc_num = get_option('ad_infeed_pc_num');
						$ad_infeed_sp_num = get_option('ad_infeed_sp_num');
						$infeed_ad_pc = explode(",", $ad_infeed_pc_num);
						$infeed_ad_sp = explode(",", $ad_infeed_sp_num);
						$infeed_ad_count = 1;
						$infeed_ad_sp_num = 0;
						$infeed_ad_num = 0;
					?>
					<?php 
					$rss_items = get_another_rss($rss_url);
					foreach($rss_items as $rss_item) : 
						$rss_item_img_url = get_eyecatch_url_from_rss($rss_item);
					?>

					<?php if( ! is_mobile() && isset($infeed_ad_pc[$infeed_ad_num]) && $infeed_ad_pc[$infeed_ad_num] == $infeed_ad_count ): ?>

						<?php if( ! get_option('ad_infeed_magazine') == null ) : ?>
							<div class="post-list-item pconly">
								<div class="post-list-inner-infeed">
									<?php echo get_option('ad_infeed_magazine'); ?>
								</div>
							</div>

						<?php endif; ?>

						<?php $infeed_ad_num++; $infeed_ad_count++;  ?>

						<?php if( isset($infeed_ad_pc[$infeed_ad_num]) && $infeed_ad_pc[$infeed_ad_num] == $infeed_ad_count ): ?>

							<?php if( ! get_option('ad_infeed_magazine') == null ) : ?>
								<div class="post-list-item pconly">
									<div class="post-list-inner-infeed">
										<?php echo get_option('ad_infeed_magazine'); ?>
									</div>
								</div>
							<?php endif; ?>
							<?php $infeed_ad_num++; $infeed_ad_count++;?>
						<?php endif; ?>

					<?php endif; ?>
					
					<article class="post-list-item" itemscope itemtype="https://schema.org/BlogPosting">
						<a class="post-list-link" rel="bookmark" href="<?php echo $rss_item->get_permalink(); ?>" itemprop='mainEntityOfPage'>
							<div class="post-list-inner">
								<div class="post-list-thumb" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
									<?php if ( ! is_mobile() ): ?>
										<?php if ( $rss_item_img_url ): ?>
											<img src="<?php echo $rss_item_img_url ?>" >
											<meta itemprop="url" content="<?php echo $rss_item_img_url ?>">
											<meta itemprop="width" content="640">
											<meta itemprop="height" content="360">
										<?php else: ?>
											<img src="<?php echo get_jin_noimage_url(); ?>" width="480" height="270" alt="no image" />
											<meta itemprop="url" content="<?php bloginfo('template_url'); ?>/img/noimg320.png">
											<meta itemprop="width" content="480">
											<meta itemprop="height" content="270">
										<?php endif; ?>
									<?php else: ?>
										<?php if( is_post_list_style() == "magazinestyle-sp1col" ): ?>
											<?php if ( $rss_item_img_url ): ?>
												<img src="<?php echo $rss_item_img_url ?>" >
												<meta itemprop="url" content="<?php echo $rss_item_img_url ?>">
												<meta itemprop="width" content="640">
												<meta itemprop="height" content="360">
											<?php else: ?>
												<img src="<?php echo get_jin_noimage_url(); ?>" width="480" height="270" alt="no image" />
												<meta itemprop="url" content="<?php bloginfo('template_url'); ?>/img/noimg320.png">
												<meta itemprop="width" content="480">
												<meta itemprop="height" content="270">
											<?php endif; ?>
										<?php else: ?>
											<?php if ( $rss_item_img_url ): ?>
												<img src="<?php echo $rss_item_img_url ?>" >
												<meta itemprop="url" content="<?php echo $rss_item_img_url ?>">
												<meta itemprop="width" content="320">
												<meta itemprop="height" content="180">
											<?php else: ?>
												<img src="<?php echo get_jin_noimage_url(); ?>" width="480" height="270" alt="no image" />
												<meta itemprop="url" content="<?php bloginfo('template_url'); ?>/img/noimg320.png">
												<meta itemprop="width" content="320">
												<meta itemprop="height" content="180">
											<?php endif; ?>
										<?php endif; ?>
									<?php endif; ?>
								</div>
								<div class="post-list-meta vcard">

									<h2 class="post-list-title entry-title" itemprop="headline"><?php echo $rss_item->get_title(); ?></h2>

									<span class="writer fn" itemprop="author" itemscope itemtype="http://schema.org/Person"><span itemprop="name"><?php the_author(); ?></span></span>

									<div class="post-list-publisher" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
										<span itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
											<span itemprop="url"><?php echo get_topnavi_logo_image_url(); ?></span>
										</span>
										<span itemprop="name"><?php bloginfo('name'); ?></span>
									</div>
								</div>
							</div>
						</a>
					</article>
					<?php $infeed_ad_count++;?>
					<?php endforeach; ?>
		</div>
		<?php endforeach; ?>
		
		
		<?php while( isset($list_cat_id[$list_cat_num]) ): ?>

		<div class="post-list-mag autoheight">
			<?php
				$child_categories = get_term_children($list_cat_id[$list_cat_num], 'category');
				$cat_url = get_category_link($list_cat_id[$list_cat_num]);
				$cat_url = rtrim($cat_url, '/');
				$parent_cat_id = $list_cat_id[$list_cat_num];
			
				if( $child_categories ){// 子カテゴリーがあるとき
					$all_cat_id = $list_cat_id[$list_cat_num];
					$child_categories_total = count($child_categories);
					
					if( get_category($all_cat_id)->parent == 0 ){
						foreach ($child_categories as $key => $value) {
							$all_cat_id .= ','.$value;
							$child_cat_count[$key] = get_category($value)->count;
						}
						$cat_count_child = 0;
						for( $i = 0 ; $i < $child_categories_total; $i++){
							$cat_count_child += $child_cat_count[$i];
						}
						$cat_count = $cat_count_child + get_category($parent_cat_id)->count;
					}else{
						foreach ($child_categories as $key => $value) {
							$all_cat_id .= ','.$value;
							$child_cat_count[$key] = get_category($value)->count;
						}
						$all_cat_id_each = explode(",", $all_cat_id);
						$all_cat_id2 = count($all_cat_id_each);
						$cat_count_child = 0;
						for( $i = 0 ; $i < $all_cat_id2 - 1; $i++){
							$cat_count_child += $child_cat_count[$i];
						}
						$cat_count = $cat_count_child + get_category($parent_cat_id)->count;
					}
				}
				else{// 子カテゴリーがないとき
					$cat_count = get_category($parent_cat_id)->count;
				}
				$args = array(
					'cat' => array($parent_cat_id),
					'posts_per_page' => get_option('posts_per_page'),
				);
				$the_query = new WP_Query( $args );
			?>
			<?php while ( $the_query->have_posts() ) : $the_query->the_post() ?>
			<?php
				// カテゴリー情報を取得
				$category = get_the_category();
				$cat_id   = $category[0]->cat_ID;
				$cat_name = $category[0]->cat_name;
			?>

			<?php get_template_part('include/liststyle/parts/post-list-mag-parts'); ?>

			<?php endwhile; ?>

			<?php if( isset( $cat_url ) && $cat_count > get_option('posts_per_page') ) :?>
			
				<?php if( strstr( $cat_url,'/?' ) == false ): ?>
				<div class="more-cat">
					<div class="more-cat-button ef"><a href="<?php echo $cat_url; ?>/page/2/"><span>next</span></a></div>
				</div>
				<?php else: ?>
				<div class="more-cat">
					<div class="more-cat-button ef"><a href="<?php echo $cat_url; ?>&paged=2"><span>next</span></a></div>
				</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	
	<?php $list_cat_num++; ?>
	<?php endwhile; ?>
	
	</div>

</div>
<?php endif; ?>