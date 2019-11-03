<?php
// 現在表示されているタグを取得
$current_term = get_current_term();

// 表示されているカテゴリーの直近1つの子カテゴリーの情報を取得
$tag_categorys = get_categorys_by_tag($current_term->term_id);
foreach( $tag_categorys as $tag_category ) : ?>

<?php
  $cat_id   = $tag_category->term_id;
  $cat_name = $tag_category->name;
  $cat_slug = $tag_category->slug;
  $cat_url = get_category_link($cat_id);
?>
<article class="post-list-item">
  <a class="post-list-link visit-count" rel="bookmark" href="<?php echo $cat_url; ?>" itemprop='mainEntityOfPage'>
    <div class="post-list-inner">
      <div class="post-list-thumb" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
        <?php if ( ! is_mobile() ): ?>
          <?php if (cps_has_post_thumbnail($cat_id)): ?>
            <?php cps_category_eyecatch_by_term_id($cat_id); ?>
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
            <?php if (cps_has_post_thumbnail($cat_id)): ?>
              <?php cps_category_eyecatch_by_term_id($cat_id); ?>
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
            <?php if (cps_has_post_thumbnail($cat_id)): ?>
              <?php cps_category_eyecatch_by_term_id($cat_id); ?>
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
        <?php if( ! $cat_name == "") :?>
        <span class="post-list-cat category-<?php echo $cat_slug; ?>" style="background-color:<?php cps_category_color(); ?>!important;" itemprop="keywords"><?php echo $cat_name ?></span>
        <?php endif; ?>

        <span class="writer fn" itemprop="author" itemscope itemtype="http://schema.org/Person"><span itemprop="name"><?php the_author(); ?></span></span>

        <h2 class="post-list-title entry-title visit-count" itemprop="headline"><?php echo $cat_name; ?></h2>
        <?php 
        if( !is_excluded_count_category($cat_id) ) :
          // 記事数表示
          $category_post_count = getPostCountsByTermId($cat_id);
        ?>
        <span class="visit-count"><?php echo sprintf(get_category_posts_count_format(), $category_post_count); ?></span>
        <?php endif; ?>
      </div>
    </div>
  </a>
</article>

<?php endforeach; ?>