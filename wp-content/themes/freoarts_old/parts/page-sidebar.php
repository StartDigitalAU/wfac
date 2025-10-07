<?php if (!empty($post->post_parent)) { ?>
    <aside class="sidebar">

        <div class="inner sticky">
	        
	        <span class="region-trigger">
	            <span class="hide-mob"><?php echo get_field('menu_heading', $post->post_parent); ?></span>
	            <span class="show-mob">View submenu</span>
	        </span>
	
	        <?php
	        $args = array(
	            'sort_order' => 'asc',
	            'sort_column' => 'menu_order',
	            'parent' => $post->post_parent,
				'hierarchical' => true,
	            'post_type' => 'page',
	            'post_status' => 'publish'
	        );
	
	        $pages = get_pages($args);
	        ?>
	        <?php if (!empty($pages)) { ?>
	            <nav class="region-wrapper">
	                <ul>
	                    <?php foreach ($pages as $page) { ?>
	                        <?php $class = ($page->ID == $post->ID) ? ' class="active"' : ''; ?>
	                        <li<?php echo $class; ?>>
	                            <a href="<?php echo get_the_permalink($page->ID); ?>" title="View <?php echo get_the_title($page->ID); ?>"><?php echo get_the_title($page->ID); ?></a>
	                        </li>
	                    <?php } ?>
	                </ul>
	            </nav>
	        <?php } ?>
        
        </div>

    </aside>
<?php } ?>