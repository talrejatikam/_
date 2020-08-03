<?php
if (! defined( 'ABSPATH' )) exit;
GFForms::include_feed_addon_framework();

class GF_SMS_Alert extends GFFeedAddOn {

	protected $_version = "2.0.0";
	protected $_min_gravityforms_version = "1.8.20";
	protected $_slug = "gravity-forms-sms-alert";
	protected $_full_path = __FILE__;
	protected $_title = "SMS Alert";
	protected $_short_title = "SMS Alert";
	protected $_multiple_feeds = false;

	private static $_instance = null;

	public static function get_instance() {
		
		
		
		if ( self::$_instance == null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	

	public function feed_settings_title() {
		return __( 'SMS ALERT', 'smsalert-gravity-forms' );
	}
	
	public function feed_settings_fields() {
    	return array(
        array(
            'title'  => 'Customer SMS Settings',
            'fields' => array(                
				array(
                    'label'             => 'Customer Numbers',
                    'type'              => 'text',
                    'name'              => 'smsalert_gForm_cstmer_nos',
                    'tooltip'           => 'Enter Customer Numbers',
                    'class'             => 'medium merge-tag-support mt-position-right',
                    'feedback_callback' => array( $this, 'is_valid_setting' )
                ),
                array(
                    'label'   => 'Customer Templates',
                    'type'    => 'textarea',
                    'name'    => 'smsalert_gForm_cstmer_text',
                    'tooltip' => 'Enter your Customer SMS Content',
                    'class'   => 'medium merge-tag-support mt-position-right'
                ),
            )
        ),
        array(
            'title'  => 'Admin SMS Settings',
            'fields' => array(
               array(
                    'label'             => 'Admin Numbers',
                    'type'              => 'text',
                    'name'              => 'smsalert_gForm_admin_nos',
                    'tooltip'           => 'Enter admin Numbers',
                    'class'             => 'medium merge-tag-support mt-position-right',
                    'feedback_callback' => array( $this, 'is_valid_setting' )
                ),
                array(
                    'label'   => 'Admin Templates',
                    'type'    => 'textarea',
                    'name'    => 'smsalert_gForm_admin_text',
                    'tooltip' => 'Enter your admin SMS Content',
                    'class'   => 'medium merge-tag-support mt-position-right'
                ),
            )
        ),
	  );
	}
	
	/**gravity form submission frontend*/
	public static function do_gForm_processing( $entry, $form )
	{
		$meta = RGFormsModel::get_form_meta( $entry['form_id'] );
		$feeds = GFAPI::get_feeds(null,$entry['form_id'],'gravity-forms-sms-alert');
		$message = $cstmer_nos = $admin_nos = $admin_msg ='';
		foreach($feeds as $feed)
		{
			if(sizeof($feed)>0 && array_key_exists('meta',$feed))
			{
				$admin_msg = $feed['meta']['smsalert_gForm_admin_text'];
				$admin_nos = $feed['meta']['smsalert_gForm_admin_nos'];
				$cstmer_nos_pattern = $feed['meta']['smsalert_gForm_cstmer_nos'];
				$message =$feed['meta']['smsalert_gForm_cstmer_text'];
			}
		}

		foreach($meta['fields'] as $meta_field)
		{
			if(is_object($meta_field))
			{
				$field_id = $meta_field->id;
				if(isset($entry[$field_id]))
				{
					$label = $meta_field->label;
					$search = '{'.$label.':'.$field_id.'}';
					$replace=$entry[$field_id];
					$message = str_replace($search,$replace,$message);
					$admin_msg = str_replace($search,$replace,$admin_msg);

					if($cstmer_nos_pattern==$search)
					{
					$cstmer_nos=$replace;
					}
				}
			}
		}
		if($cstmer_nos!='' && $message!='')
		{
			do_action('sa_send_sms', $cstmer_nos, $message);
		}
		if($admin_nos!='' && $admin_msg!='')
		{
			do_action('sa_send_sms', $admin_nos, $admin_msg);
		}
	}
	/**gravity form submission frontend ends*/
}
new GF_SMS_Alert();

add_action( 'gform_after_submission', array( 'GF_SMS_Alert', 'do_gForm_processing' ), 10, 2 );