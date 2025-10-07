<div class="mob-nav">

    <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
        <label class="u-vis-hide" for="mob-search">Search</label>
        <input type="text" class="mob-nav-search" id="mob-search" placeholder="Search" name="s" />
        <button type="submit"><span>Search</span></button>
    </form>
		
	<nav>
		<ul class="primary-nav">
			<li class="parent">
                <?php echo get_link_html('/about/', 'About', 'Go to the About page'); ?>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'about-mobile_menu',
                    'fallback_cb' => false,
                    'container' => false,
                    'menu_class' => 'sub-menu'
                    // 'walker' => new Better_Walker_Nav_Menu
                ));
                ?>
			</li>
			<li class="parent whats-on">
                <?php echo get_link_html('/whats-on/', 'What\'s On', 'Go to the What\'s On page'); ?>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'whats_on-mobile_menu',
                    'fallback_cb' => false,
                    'container' => false,
                    'menu_class' => 'sub-menu'
                    // 'walker' => new Better_Walker_Nav_Menu
                ));
                ?>
			</li>
			<li class="parent for-artists">
                <?php echo get_link_html('/for-artists/', 'For Artists', 'Go to the For Artists page'); ?>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'for_artists-mobile_menu',
                    'fallback_cb' => false,
                    'container' => false,
                    'menu_class' => 'sub-menu'
                    // 'walker' => new Better_Walker_Nav_Menu
                ));
                ?>
            </li>
			<li class="parent courses">
                <?php echo get_link_html('/courses/', 'Courses', 'Go to the Courses On page'); ?>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'courses-mobile_menu',
                    'fallback_cb' => false,
                    'container' => false,
                    'menu_class' => 'sub-menu'
                    // 'walker' => new Better_Walker_Nav_Menu
                ));
                ?>
			</li>
			<li class="parent news">
                <?php echo get_link_html('/news/', 'News', 'Go to the News page'); ?>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'news-mobile_menu',
                    'fallback_cb' => false,
                    'container' => false,
                    'menu_class' => 'sub-menu'
                    // 'walker' => new Better_Walker_Nav_Menu
                ));
                ?>
			</li>
            <li><a href="https://shop.fac.org.au" title="Shop">Shop</a></li>
			<li><?php echo get_link_html('/contact-us/', 'Contact', 'Go to the Contact Us page'); ?></li>
			<li><?php echo get_link_html('/my-account/', 'Login', 'Go to the Login page'); ?></li>
		</ul>
		<ul class="secondary-nav">
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><?php echo get_link_html('/membership/', 'Memberships', 'Go to the Memberships page'); ?></li>
			<li><?php echo get_link_html('/terms/', 'Terms', 'Go to the Terms page'); ?></li>
			<li><?php echo get_link_html('/privacy/', 'Privacy', 'Go to the Privacy page'); ?></li>
		</ul>
	</nav>
 	
 	<div class="contact-meta">
	 	<h4 class="title">Location</h4>
	 	<p><?php echo ifne($GLOBALS['theme_options'], 'street_address'); ?></p>
 	</div>
 	
 	<div class="contact-meta">
	 	<h4 class="title">Phone Number</h4>
	 	<p><a href="tel:<?php echo telephone_link(ifne($GLOBALS['theme_options'], 'phone_number')); ?>"><?php echo ifne($GLOBALS['theme_options'], 'phone_number'); ?></a></p>
 	</div>
 	
 	<div class="contact-meta">
	 	<h4 class="title">Opening & Admission</h4>
	 	<p><?php echo ifne($GLOBALS['theme_options'], 'footer_opening_times'); ?></p>
 	</div>
 	
	<div class="social-links">
        <?php get_template_part('parts/social-links'); ?>
 	</div>

</div>