<?php

	Class Extension_S3_Image_Upload extends Extension{

		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/system/preferences/',
					'delegate' => 'CustomActions',
					'callback' => 'savePreferences'
				),
				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'appendPreferences'
				),
			);
		}
		public function appendPreferences($context){
			$group = new XMLElement('fieldset');
			$group->setAttribute('class', 'settings');
			$group->appendChild(new XMLElement('legend', 'Amazon S3 Security Credentials'));
			$div = new XMLElement('div', NULL, array('class' => 'group'));
			$label = Widget::Label('Access Key ID');
			$label->appendChild(Widget::Input('settings[s3_image_upload][access-key-id]', General::Sanitize($this->getAmazonS3AccessKeyId())));
			$div->appendChild($label);
			$label = Widget::Label('Secret Access Key');
			$label->appendChild(Widget::Input('settings[s3_image_upload][secret-access-key]', General::Sanitize($this->getAmazonS3SecretAccessKey()), 'password'));
			$div->appendChild($label);
			$group->appendChild($div);
			$group->appendChild(new XMLElement('p', 'Get a Access Key ID and Secret Access Key from the <a href="http://aws.amazon.com">Amazon Web Services site</a>.', array('class' => 'help')));
			$context['wrapper']->appendChild($group);
		}

		public function getAmazonS3AccessKeyId(){
			return Symphony::Configuration()->get('access-key-id', 's3_image_upload');
		}

		public function getAmazonS3SecretAccessKey(){
			return Symphony::Configuration()->get('secret-access-key', 's3_image_upload');
		}


		public function install(){
			return Symphony::Database()->query("
				CREATE TABLE `tbl_fields_s3_image_upload` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					`bucket` varchar(50) NOT NULL,
					`key_prefix` varchar(50) NOT NULL,
					`min_width` int(11) unsigned NOT NULL,
					`min_height` int(11) unsigned NOT NULL,
					`crop_dimensions` varchar(255) NOT NULL,
					`crop_ui` enum('yes','no'),
					PRIMARY KEY  (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
		}


		public function update(){

			if(version_compare($previousVersion, '1.1', '<')) {
				$status[] = Symphony::Database()->query("
					ALTER TABLE `tbl_fields_s3_image_upload`
					ADD `crop_ui` enum('yes','no') DEFAULT 'yes'
				");
			}
		}
		
		public function uninstall() {
			Symphony::Database()->query("DROP TABLE `tbl_fields_s3_image_upload`");
		}
		
	}