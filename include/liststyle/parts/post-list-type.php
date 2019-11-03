<div class="toppost-list-box-simple">

<?php 
if(is_category_list_page()) : 
  // 特定のカテゴリーページの場合、直近の子カテゴリーのみ一覧表示する
  ?>
  <div class="post-list-mag">
    <?php get_template_part('include/liststyle/parts/post-list-mag-parts-custom'); ?>
  </div>
<?php else: ?>
  <?php if( is_post_list_style() == "magazinestyle" ) : ?>

    <div class="post-list-mag">
      <?php if( is_tag() ) :
        // タグページの場合 カテゴリータグが存在すればそのカテゴリーリンク一覧を出す
        ?>
        <?php get_template_part('include/liststyle/parts/post-list-mag-parts-tag-categorys'); ?>
      <?php endif; ?>
      <?php while (have_posts()) : the_post(); ?>
      <?php get_template_part('include/liststyle/parts/post-list-mag-parts'); ?>
      <?php endwhile; ?>

      <section class="pager-top">
        <?php if( function_exists('responsive_pagination') ) { responsive_pagination( $wp_query->max_num_pages ); } ?>
      </section>
    </div>
  <?php elseif( is_post_list_style() == "magazinestyle-sp1col" ) : ?>

    <?php if( is_mobile() ) :?>
    <div class="post-list-mag-sp1col">
      <?php while (have_posts()) : the_post(); ?>
      <?php get_template_part('include/liststyle/parts/post-list-mag-parts'); ?>
      <?php endwhile; ?>

      <section class="pager-top">
        <?php if( function_exists('responsive_pagination') ) { responsive_pagination( $wp_query->max_num_pages ); } ?>
      </section>
    </div>
    <?php else: ?>
    <div class="post-list-mag">
      <?php while (have_posts()) : the_post(); ?>
      <?php get_template_part('include/liststyle/parts/post-list-mag-parts'); ?>
      <?php endwhile; ?>

      <section class="pager-top">
        <?php if( function_exists('responsive_pagination') ) { responsive_pagination( $wp_query->max_num_pages ); } ?>
      </section>
    </div>
    <?php endif; ?>

  <?php elseif( is_post_list_style() == "basicstyle" ) : ?>

    <div class="post-list <?php echo is_post_list_style() ?>">
      <?php while (have_posts()) : the_post(); ?>
      <?php get_template_part('include/liststyle/parts/post-list-parts'); ?>
      <?php endwhile; ?>

      <section class="pager-top">
        <?php if( function_exists('responsive_pagination') ) { responsive_pagination( $wp_query->max_num_pages ); } ?>
      </section>
    </div>

  <?php endif; ?>
<?php endif; ?>
</div>