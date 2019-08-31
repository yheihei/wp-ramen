<?php
  $the_query = get_recommended_posts();
  if ($the_query->have_posts()) :
?>
<div class="pickup-contents-box-post-type <?php is_animation_style(); ?>">
  <div class="swiper-container">
    <ul class="pickup-contents swiper-wrapper">
    <?php 
      while ( $the_query->have_posts() ) :
        $the_query->the_post();

        $thumbnail_id = get_post_thumbnail_id($page_id);
        $image_attributes = wp_get_attachment_image_src($thumbnail_id,'small_size'); 
        $content = get_page($page_id);
      
      // タグ情報を取得
      if( ! empty(get_the_tags()) ){
        $tags = get_the_tags();
        $cat_id   = $tags[0]->term_id;
        $cat_name = $tags[0]->name;
        $cat_slug = $tags[0]->slug;
        $cat_link = get_tag_link($cat_id);
        
        $cat_data=get_option(intval($cat_id));
        $cat_color=esc_html($cat_data['cps_meta_category_color']);
      }
    
    ?>
      <li class="swiper-slide">
        <a href="<?php echo get_permalink($page_id); ?>">
          <div class="pickup-image">
          <?php if ( $image_attributes ): ?>
            <img src="<?php echo $image_attributes[0]; ?>" alt="" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>" />
          <?php else: ?>
            <img src="<?php echo get_jin_noimage_url(); ?>" width="480" height="270" alt="no image" />
          <?php endif; ?>
            <span class="cps-post-cat pickup-cat category-<?php echo $cat_slug; ?>" style="background-color:<?php if($cat_color){echo $cat_color;} ?>!important;" itemprop="keywords"><?php echo $cat_name ?></span>
          </div>
          <div class="pickup-title"><?php esc_html(the_title()); ?></div>
        </a>
      </li>
    <?php endwhile; ?>
    </ul>
    
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div>
</div>
<?php 
  endif;
  // 投稿データのリセット
  wp_reset_postdata();
?>