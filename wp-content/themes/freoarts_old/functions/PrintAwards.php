<?php

$print_award_year = get_field('print_award_year', 'option');

define( 'CURRENT_AWARDS_YEAR', $print_award_year);

include_once( 'PrintAwardsSubmission.php' );
include_once( 'PrintAwardsSubmissionImage.php' );

$print_awards = new PrintAwards();
$print_awards->init();

class PrintAwards {


	function __construct(){
		//..
	}

	public function init(){
		$this->initDB();
	}

    public function initDB(){

        $this->initSubmissionsDB();
        $this->initImagesDB();

    }

	public function initSubmissionsDB(){

		global $wpdb;

		$table_name = $wpdb->prefix . PrintAwardsSubmission::$table_name;
		
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

			$sql = "
				CREATE TABLE $table_name (
					id mediumint(9) not null AUTO_INCREMENT,
					user_id mediumint(9) not null,
					awards_year varchar(12) not null,
					status enum('draft','submitted') not null default 'draft',
					created_at datetime default '0000-00-00 00:00:00' not null,
					submitted datetime null,
					order_id mediumint(9) null,
					paid enum('yes', 'no') not null default 'no',
					terms_accepted enum('yes', 'no') not null default 'no',
					artist_first_name varchar(255) not null default '',
					artist_surname varchar(255) not null default '',
					artist_agent varchar(255) null,
					artist_email varchar(255) not null default '',
					address text not null default '',
					suburb varchar(255) not null default '',
					state varchar(32) not null default '',
					postcode varchar(32) not null default '',
					mobile varchar(255) not null default '',
					home_phone varchar(32) null,
					work_phone varchar(32) null,
					title_of_work varchar(255) not null default '',
					year_made varchar(32) not null,
					height varchar(32) not null,
					width varchar(32) not null,
					depth varchar(32) not null,
					medium varchar(255) not null,
					printer varchar(255) not null,
					number_of_works_in_edition varchar(32) not null,
					edition_number_of_work varchar(32) not null,
					number_of_works_for_sale varchar(32) not null,
					price varchar(32) not null,
					gst enum('yes', 'no') not null default 'no',
					abn varchar(32) null,
					notes_about_work text not null default '',
					trashed tinyint(1) not null default 0,
					UNIQUE KEY id (id)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			";

			$wpdb->query( $sql );

		}

	}

    public function initImagesDB(){

        global $wpdb;

        $table_name = $wpdb->prefix . PrintAwardsSubmissionImage::$table_name;
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

            $sql = "
                CREATE TABLE $table_name (
                    id mediumint(9) not null AUTO_INCREMENT,
                    user_id mediumint(9) not null default 0,
                    submission_id mediumint(9) null,
                    awards_year varchar(12) not null,
                    type varchar(255) null,
                    token varchar(255) not null,
                    filename text not null,
                    uploaded int(11) not null,
                    file_path varchar(255) not null,
                    UNIQUE KEY id (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ";

            $wpdb->query( $sql );

        }

    }

    /*


    public function initAdmin(){

    	add_action( 'admin_menu', array( $this, 'initMenuItems' ) );

    }


    public function initMenuItems(){

    	//Toplevel
    	$hook = add_menu_page( 'Print Awards', 'Print Awards', 'edit_posts', 'fa_pw_submissions_list', array( $this, 'listSubmissions') );

    }

    public function listSubmissions(){

    	?>

    	<div class="wrap">
	
			<div class="icon32" id="icon-tools">
				<br />
			</div>
			
			<h2>Print Awards</h2>
			
			
			
		</div><!-- .wrap -->

    	<?php


    }

	*/
    
}