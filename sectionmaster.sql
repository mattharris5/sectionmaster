CREATE DATABASE `sm-online`;
USE `sm-online`;

/*Table structure for table `cc_processors` */

CREATE TABLE `cc_processors` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `pin` varchar(50) NOT NULL DEFAULT '',
  `event_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=973224 DEFAULT CHARSET=latin1;

/*Data for the table `cc_processors` */

insert  into `cc_processors`(`user_id`,`pin`,`event_id`) values ('123456','987654','1');

/*Table structure for table `downloads` */

CREATE TABLE `downloads` (
  `version` varchar(10) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `section` varchar(5) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `position` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `downloads` */

/*Table structure for table `email_queue` */

CREATE TABLE `email_queue` (
  `emailid` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(255) DEFAULT NULL,
  `from` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`emailid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `email_queue` */

/*Table structure for table `events` */

CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(50) NOT NULL DEFAULT '',
  `org_name` varchar(50) DEFAULT NULL,
  `type` set('event','tradingpost') NOT NULL DEFAULT 'event',
  `year` int(11) NOT NULL DEFAULT '0',
  `casual_event_name` varchar(50) NOT NULL DEFAULT '',
  `formal_event_name` varchar(100) NOT NULL DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT 'default',
  `conclave_date` varchar(50) NOT NULL DEFAULT '',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `conclave_location` varchar(50) NOT NULL DEFAULT '',
  `city_and_state` varchar(50) NOT NULL DEFAULT '',
  `reg_contact_name` varchar(50) NOT NULL DEFAULT '',
  `reg_contact_phone` varchar(50) NOT NULL DEFAULT '',
  `reg_contact_email` varchar(50) NOT NULL DEFAULT '',
  `support_contact_name` varchar(50) NOT NULL DEFAULT '',
  `support_contact_phone` varchar(50) NOT NULL DEFAULT '',
  `support_contact_email` varchar(50) NOT NULL DEFAULT '',
  `tp_contact_email` varchar(200) DEFAULT NULL,
  `slogan` varchar(100) NOT NULL DEFAULT '',
  `training_university` varchar(50) NOT NULL DEFAULT '',
  `custom_degree_text1` varchar(50) NOT NULL DEFAULT '',
  `custom_degree_text2` varchar(50) NOT NULL DEFAULT '',
  `section_chief` varchar(50) NOT NULL DEFAULT '',
  `section_adviser` varchar(50) NOT NULL DEFAULT '',
  `training_cvc` varchar(50) NOT NULL DEFAULT '',
  `training_staff_college` smallint(6) DEFAULT '6',
  `training_unrestricted_college` smallint(6) NOT NULL DEFAULT '5',
  `training_enforce_upper_level_courses` tinyint(1) NOT NULL DEFAULT '0',
  `training_required_classes_for_degree` smallint(6) NOT NULL DEFAULT '4',
  `training_allow_manual_degree_level_changes` tinyint(1) NOT NULL DEFAULT '0',
  `training_ignore_colleges` tinyint(4) NOT NULL DEFAULT '0',
  `training_ignore_degrees` tinyint(1) NOT NULL DEFAULT '0',
  `online_reg_open_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `online_reg_close_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `do_training` tinyint(1) NOT NULL DEFAULT '1',
  `do_online_payment` tinyint(1) NOT NULL DEFAULT '1',
  `do_payatdoor` tinyint(1) NOT NULL DEFAULT '1',
  `do_eventreg` tinyint(1) NOT NULL DEFAULT '0',
  `do_tradingpost` tinyint(1) NOT NULL DEFAULT '1',
  `do_eval` tinyint(1) DEFAULT '0',
  `degree_printing` tinyint(1) NOT NULL DEFAULT '0',
  `nametag_paper` tinyint(1) NOT NULL DEFAULT '0',
  `nametag_size` tinyint(1) NOT NULL DEFAULT '0',
  `creditcard_paper` tinyint(1) NOT NULL DEFAULT '0',
  `ship_calculation_method` set('by_weight','flat_amt','flat_rate','per_item') DEFAULT 'by_weight',
  `ship_calculation` double DEFAULT '0.37',
  `min_ship_cost` double DEFAULT '3.85',
  `ship_delivery_note` text,
  `allow_pre_event_shipping` tinyint(1) DEFAULT '0',
  `allow_at_event_pickup` tinyint(1) DEFAULT '0',
  `arrival_time_details` text,
  `event_cost` text,
  `detail_url` text,
  `custom_agreement_text` text,
  `extra_css` text,
  `live_remote_processing` tinyint(4) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `pin` varchar(50) NOT NULL DEFAULT '',
  `custom_payment_method1` varchar(25) DEFAULT NULL,
  `separate_talent_release_signature` tinyint(4) NOT NULL DEFAULT '0',
  `custom1_label` varchar(255) NOT NULL DEFAULT '',
  `custom2_label` varchar(255) NOT NULL DEFAULT '',
  `custom3_label` varchar(255) NOT NULL DEFAULT '',
  `recurring_event_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `events` */

insert  into `events`(`id`,`section_name`,`org_name`,`type`,`year`,`casual_event_name`,`formal_event_name`,`template`,`conclave_date`,`start_date`,`end_date`,`conclave_location`,`city_and_state`,`reg_contact_name`,`reg_contact_phone`,`reg_contact_email`,`support_contact_name`,`support_contact_phone`,`support_contact_email`,`tp_contact_email`,`slogan`,`training_university`,`custom_degree_text1`,`custom_degree_text2`,`section_chief`,`section_adviser`,`training_cvc`,`training_staff_college`,`training_unrestricted_college`,`training_enforce_upper_level_courses`,`training_required_classes_for_degree`,`training_allow_manual_degree_level_changes`,`training_ignore_colleges`,`training_ignore_degrees`,`online_reg_open_date`,`online_reg_close_date`,`do_training`,`do_online_payment`,`do_payatdoor`,`do_eventreg`,`do_tradingpost`,`do_eval`,`degree_printing`,`nametag_paper`,`nametag_size`,`creditcard_paper`,`ship_calculation_method`,`ship_calculation`,`min_ship_cost`,`ship_delivery_note`,`allow_pre_event_shipping`,`allow_at_event_pickup`,`arrival_time_details`,`event_cost`,`detail_url`,`custom_agreement_text`,`extra_css`,`live_remote_processing`,`user_id`,`pin`,`custom_payment_method1`,`separate_talent_release_signature`,`custom1_label`,`custom2_label`,`custom3_label`,`recurring_event_id`) values ('1','W-1N','Section W-1N','event','2014','Conclave','Conclave 2014','default','April 25-27, 2014','2014-04-25 00:00:00','2014-04-27 12:00:00','Camp Thunderbird','Olympia, WA','','','','','','','','Beyond the First Step','Teaquan Wask University','','','','','','1','0','0','4','0','0','0','2014-01-01 00:00:00','2014-04-24 01:00:00','1','1','0','0','1','1','1','1','1','0','by_weight','0.49','1','<b>Your ordered merchandise items will be shipped within one week of your order, with the exception of the Conclave Patches, which will be shipped after the Conclave.</b>','1','1','<H4>Check-in begins at 7:00 p.m. on Friday evening of Conclave.</H4>\r\n<a href=\\\"http://www.sectionw1n.org/\\\" target=\\\"_blank\\\">Click here for more information about Conclave</a>.','<li>$40 for New Ordeal Members (attended ordeal since 1/1/2013)</li>\r\n<li>$45 for Early Registration (registered by 4/23/2014)</li>\r\n<li>$60 for Regular Registration (registered after 4/23/2014)</li>','','','#header {\r\n	background-image: url(\'register/images/w1n/header.gif\');\r\n	padding: 0;\r\n	margin: 0;\r\n	height: 100px;\r\n}\r\ndiv#wrap {\r\n	padding: 0;\r\n	margin: 5px auto;\r\n}','1','123456','987654','','0','Ceremony Evaluation: Which ceremony and part?','First-timer: Name of person who invited you to Conclave','Youth under 21: Would you like us to send you an application for a $300 OA High Adventure Scholarship?','0');

/*Table structure for table `lodges` */

CREATE TABLE `lodges` (
  `lodge_id` int(11) NOT NULL DEFAULT '0',
  `lodge_name` varchar(50) NOT NULL DEFAULT '',
  `section` varchar(50) NOT NULL DEFAULT '',
  `council` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`lodge_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Last update: 10/30/2005';

/*Data for the table `lodges` */

insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('99','Tomo Chi-Chi','SR-5','Coastal Empire');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('414','Tsali','SR-5','Daniel Boone');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('93','Bob White','SR-5','Georgia-Carolina');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('553','Muscogee','SR-5','Indian Waters');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('552','Santee','SR-5','Pee Dee Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('551','Atta Kulla Kulla','SR-5','Blue Ridge');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('88','Osceola','SR-4','Southwest Florida');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('85','Aal-Pa-Tah','SR-4','Gulf Stream');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('89','Timuquan','SR-4','West Central Florida');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('84','O-Shot-Caw','SR-4','South Florida');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('83','Tipisa','SR-4','Central Florida');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('86','Seminole','SR-4','Gulf Ridge');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('664','Semialachee','SR-4','Suwannee River Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('87','Echockotee','SR-4','North Florida');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('583','Aina Topa Hutsi','SR2-3S','Alamo Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('775','Wewanoma','SR2-3S','Rio Grande');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('574','Wihinipa Hinsa','SR2-3S','Bay Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('564','Tonkawa','SR2-3S','Capitol Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('576','Colonneh','SR2-3S','Sam Houston Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('741','Wahinkto','SR2-3S','Concho Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('577','Karankawa','SR2-3S','South Texas');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('578','Hasinai','SR2-3S','Three Rivers');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('567','Tatanka','SR2-3S','Buffalo Trail');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('571','Mikanakawa','SR2-3N','Circle Ten');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('584','Akela Wahinapay','SR2-3N','Caddo Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('585','Tejas','SR2-3N','East Texas Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('587','Wichita','SR2-3N','Northwest Texas');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('561','Penateka','SR2-3N','Texas Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('562','Nischa Achowalogen','SR2-3N','Golden Spread');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('694','Nakona','SR2-3N','South Plains');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('662','Netopalis Sipo Schipinachk','SR2-3N','Longhorn');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('215','Caddo','SR2-3N','Norwela');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('580','Loquanne Allangwh','SR2-3N','NeTseO Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('302','Ashwanchi Kinta','SR-1B','Choctaw Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('4','Woa Cholena','SR-1B','Mobile Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('304','Ti\'ak','SR-1B','Pine Burr Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('691','Watonala','SR-1B','Pushmataha Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('773','Yustaga','SR-1B','Gulf Coast');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('3','Cowikee','SR-1B','Alabama-Florida');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('211','Quinipissa','SR-1A','Istrouma Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('212','Atchafalaya','SR-1A','Evangeline Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('213','Comanche','SR-1A','Louisiana Purchase');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('209','Quelqueshoe','SR-1A','Calcasieu Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('214','Chilantakoba','SR-1A','Southeast Louisiana');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('303','Sebooney Okasucca','SR-1A','Andrew Jackson');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('660','Wulapeju','C-7','Blackhawk Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('127','Lowaneu Allanque','C-7','Three Fires');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('118','Owasippe','C-7','Chicago Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('702','Waupecan','C-7','Rainbow');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('751','Lakota','C-7','Northwest Suburban');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('147','Pachsegink','C-7','Des Plaines Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('152','Michigamea','C-7','Calumet');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('129','Ma-Ka-Ja-Wan','C-7','Northeast Illinois');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('444','Miami','C-6B','Miami Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('145','Nischa Chuppecat','C-6B','Hoosier Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('216','Pamola','NE-1A','Katahdin Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('218','Madockawanda','NE-1A','Pine Tree');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('227','Moswetuset','NE-1A','Boston Minuteman');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('236','Nanepashemet','NE-1A','Yankee Clipper');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('244','Chippanyonk','NE-1A','Knox Trail');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('330','Passaconaway','NE-1A','Daniel Webster');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('224','Abake Mi-Sa-Na-Ki','NE-1B','Cape Cod & the Islands');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('225','Tulpe','NE-1B','Annawon');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('230','Grand Monadnock','NE-1B','Nashua Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('249','Tisquantum','NE-1B','Old Colony');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('254','Pachachaug','NE-1B','Mohegan');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('546','Abnaki','NE-1B','Narragansett');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('66','Tschitani','NE-2A','Connecticut Rivers');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('67','Achewon Netopalis','NE-2A','Greenwich');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('69','Paugassett','NE-2A','Housatonic');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('72','Owaneco','NE-2A','Connecticut Yankee');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('234','Pocumtuc','NE-2A','Western Massachusetts');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('364','Kittan','NE-2A','Twin Rivers');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('592','Ajapeu','NE-2A','Green Mountain');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('802','Black Eagle','NE-5B','Transatlantic');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('368','Otahnagon','NE-3A','Baden-Powell');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('373','Lowanne Nimat','NE-3A','Longhouse');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('375','Tkaen Dod','NE-3A','Five Rivers');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('376','Ashokwahta','NE-3A','Iroquois Trail');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('380','Ho-De-No-Sau-Nee','NE-3A','Greater Niagara Frontier');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('393','Onteroraus','NE-3A','Otschodela');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('397','Tschipey Achtu','NE-3A','Seneca Waterways');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('400','Ga-Hon-Ga','NE-3A','Revolutionary Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('382','Ho-Nan-Ne-Ho-Ont','NE-4A','Allegheny Highlands');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('500','Kuskitannee','NE-4A','Moraine Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('509','Ah\'Tic','NE-4A','Bucktail');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('532','Langundowi','NE-4A','French Creek');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('538','Gyantwachia','NE-4A','Chief Cornplanter');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('497','Monaken','NE-4B','Juniata Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('508','Nachamawat','NE-4B','Penn\'s Woods');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('512','Wagion','NE-4B','Westmoreland-Fayette');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('527','Enda Lechauhanne','NE-4B','Greater Pittsburgh');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('757','Ahtuhquog','NE-4B','Potomac');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('341','Japeechen','NE-5A','Jersey Shore');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('347','Na Tsi Hi','NE-5A','Monmouth');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('410','Arawak','','Virgin Islands');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('661','Yokahu','NE-5A','Puerto Rico');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('690','Lenape','NE-5B','Garden State');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('777','Ajapeu','NE-5B','Bucks County');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('501','Lowwapaneu','NE-5B','Northeastern Pennsylvania');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('502','Witauchsoman','NE-5B','Minsi Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('504','Wyona','','Columbia-Montour');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('525','Unami','NE-5B','Cradle of Liberty');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('533','Woapeu Sisilija','','Susquehanna');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('81','Nentego','NE-6A','Del-Mar-Va');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('82','Amangamek-Wipit','NE-6A','National Capital Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('220','Nentico','NE-6A','Baltimore Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('221','Guneukitschik','NE-6B','Mason-Dixon');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('515','Sasquesahanough','NE-6B','New Birth of Freedom');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('524','Wunita Gokhos','NE-6B','Pennsylvania Dutch');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('528','Kittatinny','NE-6B','Hawk Mountain');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('539','Octoraro','NE-6B','Chester County');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('333','Lenapehoking','NE-5A','Northern New Jersey');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('352','Sakuwit','NE-5A','Central New Jersey');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('358','Woapalanne','NE-5A','Patriots\' Path');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('640','GNYC','NE-7A','Greater New York');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('374','Nacha Nimat','NE-7B','Hudson Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('386','Buckskin','NE-7B','Theodore Roosevelt');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('388','Ktemaque','NE-7B','Westchester-Putnam');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('404','Shinnecock','NE-7B','Suffolk County');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('405','Half Moon','NE-7B','Rip Van Winkle');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('157','Kiskakon','C-6B','Anthony Wayne Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('449','Mawat Woakus','C-6B','Black Swamp Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('439','Tarhe','C-6B','Tecumseh');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('438','Ku-Ni-Eh','C-6B','Dan Beard');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('162','Takachsin','C-6A','Sagamore');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('160','Jaccos Towne','C-6A','Crossroads of America');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('156','Kiondaga','C-6A','Buffalo Trace');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('121','Woapink','C-6A','Lincoln Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('117','Illini','C-6A','Prairielands');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('165','Sakima','C-6A','La Salle');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('307','Tamegonit','C-5B','Heart of America');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('177','Mitigwa','C-5B','Mid-Iowa');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('197','Dzie-Hauk Tonga','C-5B','Jayhawk Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('653','Nampa-Tsi','C-5B','Great Rivers');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('306','Wah-Sha-She','C-5B','Ozark Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('192','Kidi Kidish','C-5A','Coronado Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('194','Mandan','C-5A','Santa Fe Trail');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('198','Kansa','C-5A','Quivira');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('326','Kit-Ke-Hak-O-Kut','C-5A','Mid-America');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('324','Golden Sun','C-5A','Cornhusker');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('322','Tatanka-Anpetu-Wi','C-5A','Overland Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('617','Chi-Hoota-Wei','C-4B','Buckskin');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('672','Thal-Coo-Zyo','C-4B','Tri-State Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('618','Nendawen','C-4B','Allohak');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('467','Netawatwees','C-4B','Muskingum Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('615','Menawngihella','C-4B','Mountaineer Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('441','Tecumseh','C-4B','Simon Kenton');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('463','Wapashuwi','C-4A','Greater Western Reserve');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('440','Cuyahoga','C-4A','Greater Cleveland');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('436','Sipp-O','C-4A','Buckeye');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('433','Marnoc','C-4A','Great Trail');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('619','Onondaga','C-4A','Ohio River Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('450','Portage','C-4A','Heart of Ohio');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('312','Anpetu-We','C-3B','Gtr. St. Louis Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('144','Illinek','C-3B','Abraham Lincoln');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('141','Black Hawk','C-3B','Mississippi Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('138','Wenasa Quenhotan','C-3B','W. D. Boyce');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('133','Konepaka Ketiwa','C-3B','Illowa');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('114','Nisha Kittan','C-3B','Lewis and Clark');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('629','Mikano','C-3A','Milwaukee County');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('172','Cho-Gun-Mun-A-Nock','C-3A','Hawkeye Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('651','Wag-O-Shag','C-3A','Potawatomi Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('634','Mascoutens','C-3A','Southeast Wisconsin');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('178','Timmeu','C-3A','Northeast Iowa');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('620','Takoda','C-3A','Glacier\'s Edge');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('266','Nacha Tindey','C-2B','Gerald R. Ford');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('274','Indian Drum','C-2B','Scenic Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('271','Gabe-Shi-Win-Gi-Ji-Kens','C-2B','Chief Okemos');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('270','Nacha-Mawat','C-2B','Southwest Michigan');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('261','Ag-Im','C-2B','Hiawathaland');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('265','Mischigonong','C-2B','Lake Huron Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('460','Tindeuchen','C-2B','Erie Shores');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('272','Noquet','C-2A','Great Lakes');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('255','Manitous','C-2A','Great Sauk Trail');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('277','Chickagami','C-2A','Blue Water');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('264','Cuwe','C-2A','Tall Pine');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('299','Blue Ox','C-1B','Gamehaven');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('173','Sac-N-Fox','C-1B','Winnebago');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('637','Otyokwa','C-1B','Chippewa Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('627','Tom Kita Chara','C-1B','Samoset');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('635','Awase','C-1B','Bay Lakes');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('624','Ni-Sanak-Tani','C-1B','Gateway Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('429','Pa-Hin','C-1A','Northern Lights');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('283','Wahpekute','C-1A','Twin Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('286','Ka\'Niss Ma\'Ingan','C-1A','Voyageurs Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('296','Naguonabe','C-1A','Central Minnesota');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('250','Totanhan Nakaha','C-1A','Northern Star');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('733','Tetonwana','C-1A','Sioux');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('60','Ha-Kin-Skay-A-Ki','W-5','Pikes Peak');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('61','Tahosa','W-5','Denver Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('62','Kola','W-5','Longs Peak');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('63','Tupwee Gudas Gov Youchigudt Soovep','W-5','Rocky Mountain');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('64','Mic-O-Say','W-5','Western Colorado');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('638','Tatokainyanka','W-5','Central Wyoming');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('695','Crazy Horse','W-5','Black Hills Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('604','Walamootkin','W-1E','Blue Mountain');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('611','Es-Kaielgu','W-1E','Inland Northwest');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('614','Tataliya','W-1E','Grand Columbia');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('606','Sikhs Mox Lamonti','W-1N','Mount Baker');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('609','T\'Kope Kwiskwis','W-1N','Chief Seattle');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('610','Nanuk','W-1N','Great Alaska');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('612','Nisqually','W-1N','Pacific Harbors');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('696','Toontuk','W-1N','Midnight Sun');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('491','Lo La\'Qam Geela','W-1S','Crater Lake');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('492','Wauna La-Mon\'Tay','W-1S','Cascade Pacific');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('697','Tsisqan','W-1S','Oregon Trail');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('106','Tukarica','W-2N','Ore-Ida');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('107','Shunkah Mahneetu','W-2N','Grand Teton');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('111','Ma-I-Shu','W-2N','Snake River');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('315','Apoxky Aio','W-2N','Montana');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('589','Awaxaawe\' Awachia','W-2S','Trapper Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('590','El-Ku-Ta','W-2S','Great Salt Lake');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('591','Tu-Cubin-Noonie','W-2S','Utah National Parks');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('23','Ut-In Selica','W-3N','Mt. Diablo Silverado');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('35','Talako','W-3N','Marin');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('41','Orca','W-3N','Redwood Empire');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('47','Amangi Nacha','W-3N','Golden Empire');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('329','Tannu','W-3N','Nevada Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('22','Kaweah','W-3S','Alameda');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('27','Tah-Heetch','W-3S','Sequoia');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('28','Achewon Nimat','W-3S','San Francisco Bay Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('31','Ohlone','W-3S','Pacific Skyline');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('42','Hungteetsepoppi','W-3S','Piedmont');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('55','Saklan','W-3S','Silicon Valley Monterey Bay');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('59','Toloma','W-3S','Greater Yosemite');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('30','Yowlumne','W-4N','Southern Sierra');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('33','Siwinis','W-4N','Los Angeles Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('51','Malibu','W-4N','Western Los Angeles County');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('53','Chumash','W-4N','Los Padres');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('57','Topa Topa','W-4N','Ventura County');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('58','Spe-Le-Yai','W-4N','Verdugo Hills');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('39','Wiatava','W-4S','Orange County');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('40','Ta Tanka','W-4S','San Gabriel Valley');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('45','Cahuilla','W-4S','California Inland Empire');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('49','Tiwahe','W-6W','San Diego-Imperial');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('412','Yah-Tah-Hey-Si-Kess','W-6E','Great Southwest');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('413','Kwahadi','W-6E','Conquistador');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('573','Gila','W-6E','Yucca');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('102','Maluhia','W-6P','Maui County');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('104','Na Mokupuni O Lawelawe','W-6P','Aloha');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('803','Achpateuny','W-6P','Far East');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('10','Wipala Wiki','W-6W','Grand Canyon');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('11','Papago','W-6W','Catalina');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('328','Nebagamon','W-6W','Las Vegas Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('0','','','');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('544','Sasquesahanough','NE-6B','New Birth of Freedom');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('32','Puvunga','W-4S','Long Beach Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('550','Un A Li\'yi','SR-5','Coastal Carolina');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('420','Eswau Huppeday','SR-5','Piedmont');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('415','Catawba','SR-5','Mecklenburg County');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('549','Skyuka','SR-5','Palmetto');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('416','Iti Bapishe Iti Hollo','SR-5','Central North Carolina');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('748','Chicksa','SR-6','Yocona Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('560','Wa-Hi-Nasa','SR-6','Middle Tennessee');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('558','Ahoalan-Nachpikin','SR-6','Chickasaw');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('556','Talidandaganu\'','SR-6','Cherokee Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('204','Kawida','SR-6','Blue Grass');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('200','White Horse','SR-6','Shawnee Trails');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('713','Sequoyah','SR-6','Sequoyah');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('559','Ittawamba','SR-6','West Tennessee Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('205','Talligewi','SR-6','Lincoln Heritage');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('557','Pellissippi','SR-6','Great Smoky Mountain');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('595','Wahunsenakah','SR-7A','Colonial Virginia');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('596','Blue Heron','SR-7A','Tidewater');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('602','Nawakwa','SR-7A','Heart of Virginia');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('599','Tutelo','SR-7A','Blue Ridge Mountains');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('598','Shenshawpotoo','SR-7A','Shenandoah Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('763','Shenandoah','SR-7A','Stonewall Jackson Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('425','Klahican','SR-7B','Cape Fear');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('424','Nayawin Rar','SR-7B','Tuscarora');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('70','Tsoiotsi Tsogalii','SR-7B','Old North State');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('421','Occoneechee','SR-7B','Occoneechee');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('427','Wahissa','SR-7B','Old Hickory');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('426','Croatan','SR-7B','East Carolina');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('13','A-Booik-Paa-Gun','SR-8','DeSoto Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('14','Wazhazee','SR-8','Ouachita Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('488','Ta Tsu Hwa','SR-8','Indian Nations');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('16','Wachtschu Mawachpo','SR-8','Westark Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('18','Quapaw','SR-8','Quapaw Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('480','Ma-Nu','SR-8','Last Frontier');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('474','Ema\'o Mahpe','SR-8','Cimarron');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('469','Washita','SR-8','Cherokee Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('468','Wisawanik','SR-8','Arbuckle Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('5','Alibamu','SR-9','Tukabatchee Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('1','Coosa','SR-9','Greater Alabama');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('6','Aracoma','SR-9','Black Warrior');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('91','Chattahoochee','SR-9','Chattahoochee');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('92','Egwa Tawa Dee','SR-9','Atlanta Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('96','Echeconnee','SR-9','Central Georgia');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('97','Immokalee','SR-9','Chehaw');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('98','Alapaha','SR-9','Alapaha Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('100','Waguli','SR-9','Northwest Georgia');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('101','Mowogo','SR-9','Northeast Georgia');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('758','Pilthlako','SR-9','Okefenokee Area');
insert  into `lodges`(`lodge_id`,`lodge_name`,`section`,`council`) values ('95','Ini-To','SR-9','Flint River');

/*Table structure for table `permissions` */

CREATE TABLE `permissions` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `superuser` tinyint(1) NOT NULL DEFAULT '0',
  `members` tinyint(1) NOT NULL DEFAULT '0',
  `orders` tinyint(1) NOT NULL DEFAULT '0',
  `users` tinyint(1) NOT NULL DEFAULT '0',
  `products` tinyint(1) NOT NULL DEFAULT '0',
  `coupons` tinyint(1) NOT NULL DEFAULT '0',
  `shipping` tinyint(1) NOT NULL DEFAULT '0',
  `mass_email` tinyint(1) NOT NULL DEFAULT '0',
  `section_info` tinyint(1) NOT NULL DEFAULT '0',
  `event_prefs` tinyint(1) NOT NULL DEFAULT '0',
  `registrations` tinyint(1) NOT NULL DEFAULT '0',
  `training` tinyint(1) NOT NULL DEFAULT '0',
  `report_summary` tinyint(1) NOT NULL DEFAULT '1',
  `report_lodge` tinyint(1) NOT NULL DEFAULT '0',
  `report_chapter` tinyint(1) NOT NULL DEFAULT '0',
  `report_tradingpost` tinyint(1) NOT NULL DEFAULT '0',
  `report_training` tinyint(1) NOT NULL DEFAULT '0',
  `report_diet` tinyint(1) NOT NULL DEFAULT '0',
  `report_medical` tinyint(1) NOT NULL DEFAULT '0',
  `report_eval` tinyint(1) NOT NULL DEFAULT '0',
  `vicarious_login` tinyint(1) NOT NULL DEFAULT '0',
  `paper_reg` tinyint(1) NOT NULL DEFAULT '0',
  `request_refund` tinyint(1) NOT NULL DEFAULT '0',
  `switch_id` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `permissions` */

insert  into `permissions`(`user_id`,`superuser`,`members`,`orders`,`users`,`products`,`coupons`,`shipping`,`mass_email`,`section_info`,`event_prefs`,`registrations`,`training`,`report_summary`,`report_lodge`,`report_chapter`,`report_tradingpost`,`report_training`,`report_diet`,`report_medical`,`report_eval`,`vicarious_login`,`paper_reg`,`request_refund`,`switch_id`) values ('1','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5');
insert  into `permissions`(`user_id`,`superuser`,`members`,`orders`,`users`,`products`,`coupons`,`shipping`,`mass_email`,`section_info`,`event_prefs`,`registrations`,`training`,`report_summary`,`report_lodge`,`report_chapter`,`report_tradingpost`,`report_training`,`report_diet`,`report_medical`,`report_eval`,`vicarious_login`,`paper_reg`,`request_refund`,`switch_id`) values ('2','0','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5','5');

/*Table structure for table `recurring_events` */

CREATE TABLE `recurring_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL DEFAULT '',
  `section_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `recurring_events` */

insert  into `recurring_events`(`id`,`title`,`section_name`) values ('2','Conclave','w1a');

/*Table structure for table `sessions` */

CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) DEFAULT NULL,
  `event_id` int(11) NOT NULL DEFAULT '0',
  `passkey` varchar(25) NOT NULL DEFAULT '',
  `timestamp` int(10) DEFAULT NULL,
  `current_reg_step` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=108848 DEFAULT CHARSET=latin1;

/*Data for the table `sessions` */

/*Table structure for table `states` */

CREATE TABLE `states` (
  `state_id` char(2) DEFAULT NULL,
  `state_name` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `states` */

insert  into `states`(`state_id`,`state_name`) values ('AL','Alabama');
insert  into `states`(`state_id`,`state_name`) values ('AK','Alaska');
insert  into `states`(`state_id`,`state_name`) values ('AZ','Arizona');
insert  into `states`(`state_id`,`state_name`) values ('AR','Arkansas');
insert  into `states`(`state_id`,`state_name`) values ('CA','California');
insert  into `states`(`state_id`,`state_name`) values ('CO','Colorado');
insert  into `states`(`state_id`,`state_name`) values ('CT','Connecticut');
insert  into `states`(`state_id`,`state_name`) values ('DE','Delaware');
insert  into `states`(`state_id`,`state_name`) values ('DC','District of Columbia');
insert  into `states`(`state_id`,`state_name`) values ('FL','Florida');
insert  into `states`(`state_id`,`state_name`) values ('GA','Georgia');
insert  into `states`(`state_id`,`state_name`) values ('HI','Hawaii');
insert  into `states`(`state_id`,`state_name`) values ('ID','Idaho');
insert  into `states`(`state_id`,`state_name`) values ('IL','Illinois');
insert  into `states`(`state_id`,`state_name`) values ('IN','Indiana');
insert  into `states`(`state_id`,`state_name`) values ('IA','Iowa');
insert  into `states`(`state_id`,`state_name`) values ('KS','Kansas');
insert  into `states`(`state_id`,`state_name`) values ('KY','Kentucky');
insert  into `states`(`state_id`,`state_name`) values ('LA','Louisiana');
insert  into `states`(`state_id`,`state_name`) values ('ME','Maine');
insert  into `states`(`state_id`,`state_name`) values ('MD','Maryland');
insert  into `states`(`state_id`,`state_name`) values ('MA','Massachusetts');
insert  into `states`(`state_id`,`state_name`) values ('MI','Michigan');
insert  into `states`(`state_id`,`state_name`) values ('MN','Minnesota');
insert  into `states`(`state_id`,`state_name`) values ('MS','Mississippi');
insert  into `states`(`state_id`,`state_name`) values ('MO','Missouri');
insert  into `states`(`state_id`,`state_name`) values ('MT','Montana');
insert  into `states`(`state_id`,`state_name`) values ('NE','Nebraska');
insert  into `states`(`state_id`,`state_name`) values ('NV','Nevada');
insert  into `states`(`state_id`,`state_name`) values ('NH','New Hampshire');
insert  into `states`(`state_id`,`state_name`) values ('NJ','New Jersey');
insert  into `states`(`state_id`,`state_name`) values ('NM','New Mexico');
insert  into `states`(`state_id`,`state_name`) values ('NY','New York');
insert  into `states`(`state_id`,`state_name`) values ('NC','North Carolina');
insert  into `states`(`state_id`,`state_name`) values ('ND','North Dakota');
insert  into `states`(`state_id`,`state_name`) values ('OH','Ohio');
insert  into `states`(`state_id`,`state_name`) values ('OK','Oklahoma');
insert  into `states`(`state_id`,`state_name`) values ('OR','Oregon');
insert  into `states`(`state_id`,`state_name`) values ('PA','Pennsylvania');
insert  into `states`(`state_id`,`state_name`) values ('RI','Rhode Island');
insert  into `states`(`state_id`,`state_name`) values ('SC','South Carolina');
insert  into `states`(`state_id`,`state_name`) values ('SD','South Dakota');
insert  into `states`(`state_id`,`state_name`) values ('TN','Tennessee');
insert  into `states`(`state_id`,`state_name`) values ('TX','Texas');
insert  into `states`(`state_id`,`state_name`) values ('UT','Utah');
insert  into `states`(`state_id`,`state_name`) values ('VT','Vermont');
insert  into `states`(`state_id`,`state_name`) values ('VA','Virginia');
insert  into `states`(`state_id`,`state_name`) values ('WA','Washington');
insert  into `states`(`state_id`,`state_name`) values ('WV','West Virginia');
insert  into `states`(`state_id`,`state_name`) values ('WI','Wisconsin');
insert  into `states`(`state_id`,`state_name`) values ('WY','Wyoming');
insert  into `states`(`state_id`,`state_name`) values ('AS','American Samoa');
insert  into `states`(`state_id`,`state_name`) values ('FM','Federated States of Micronesia');
insert  into `states`(`state_id`,`state_name`) values ('GU','Guam');
insert  into `states`(`state_id`,`state_name`) values ('MH','Marshall Islands');
insert  into `states`(`state_id`,`state_name`) values ('MP','Northern Mariana Islands');
insert  into `states`(`state_id`,`state_name`) values ('PW','Palau');
insert  into `states`(`state_id`,`state_name`) values ('PR','Puerto Rico');
insert  into `states`(`state_id`,`state_name`) values ('VI','Virgin Islands');
insert  into `states`(`state_id`,`state_name`) values ('AA','Armed Forces America');
insert  into `states`(`state_id`,`state_name`) values ('AE','Armed Forces Africa, Canada, Europe, Middle East');
insert  into `states`(`state_id`,`state_name`) values ('AP','Armed Forces Pacific');

/*Table structure for table `users` */

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '',
  `section` varchar(5) NOT NULL DEFAULT '',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `password` varchar(32) DEFAULT NULL,
  `cookie` varchar(32) NOT NULL DEFAULT '0',
  `session` varchar(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `ip` varchar(15) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `current_event` int(11) NOT NULL DEFAULT '0',
  `last_activity` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`section`,`member_id`,`password`,`cookie`,`session`,`ip`,`current_event`,`last_activity`) values ('1','superadmin','w1n','0','e19d5cd5af0378da05f63f891c7467af','0','','','1','0000-00-00 00:00:00');
insert  into `users`(`id`,`username`,`section`,`member_id`,`password`,`cookie`,`session`,`ip`,`current_event`,`last_activity`) values ('2','admin@w1n','w1n','0','e19d5cd5af0378da05f63f891c7467af','0','','','1','0000-00-00 00:00:00');

CREATE DATABASE `w1n`;
USE `w1n`;

/*Table structure for table `auction_items` */

CREATE TABLE `auction_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL DEFAULT '',
  `estimated_value` double NOT NULL DEFAULT '0',
  `buyer_id` int(11) NOT NULL DEFAULT '0',
  `donor_id` int(11) NOT NULL DEFAULT '0',
  `auction_price` double NOT NULL DEFAULT '0',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_method` varchar(50) NOT NULL DEFAULT '',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `fullinvoice` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `auction_items` */

/*Table structure for table `chapters` */

CREATE TABLE `chapters` (
  `chapter_id` int(11) NOT NULL AUTO_INCREMENT,
  `chapter_name` varchar(50) NOT NULL DEFAULT '',
  `lodge` int(11) NOT NULL DEFAULT '0',
  `active_membership` int(11) NOT NULL DEFAULT '0',
  `chapter_chief` varchar(50) NOT NULL DEFAULT '',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`chapter_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `chapters` */

/*Table structure for table `colleges` */

CREATE TABLE `colleges` (
  `college_id` int(11) NOT NULL AUTO_INCREMENT,
  `college_prefix` varchar(4) NOT NULL DEFAULT '',
  `college_name` varchar(50) NOT NULL DEFAULT '',
  `college_desc` mediumtext NOT NULL,
  `show_online` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`college_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `colleges` */

insert  into `colleges`(`college_id`,`college_prefix`,`college_name`,`college_desc`,`show_online`,`deleted`) values ('1','OPEN','STAFF -- No Classes','If you are a staff member, you should choose this option if you do not plan to attend each of the training sessions offered at Conclave. Selecting this option will bypass the rest of the training signup process.','1','0');
insert  into `colleges`(`college_id`,`college_prefix`,`college_name`,`college_desc`,`show_online`,`deleted`) values ('2','CNA','College for New Arrowmen','Are you new to the OA?  If so, this college is just for you.  It is filled with classes that will help you to better understand what the OA is all about and how you can have fun and get involved!','1','0');
insert  into `colleges`(`college_id`,`college_prefix`,`college_name`,`college_desc`,`show_online`,`deleted`) values ('3','CICE','College of Inductions and Ceremonies','Interested in Native American related aspects of the OA?  Want to learn how to better perform your part in a ceremony or how to make ceremonial regalia/costuming?  This college is for you.','1','0');
insert  into `colleges`(`college_id`,`college_prefix`,`college_name`,`college_desc`,`show_online`,`deleted`) values ('4','CLL','College of Chapter & Lodge Leadership','Wondering how you can do a better job in your servant role as a chapter or lodge leader?  Seeking new tips and hints to spice up an event you\\\'re planning?  Choose this college to learn about the leadership opportunities in the OA.','1','0');
insert  into `colleges`(`college_id`,`college_prefix`,`college_name`,`college_desc`,`show_online`,`deleted`) values ('7','COA','College of Outdoor Adventure','This college features classes designed for Arrowmen who want to gain outdoor skills, as well as learn how to plan and lead their own exciting outdoor programs.\r\n\r\n','1','0');

/*Table structure for table `conclave_attendance` */

CREATE TABLE `conclave_attendance` (
  `member_id` int(11) NOT NULL DEFAULT '0',
  `conclave_year` int(11) NOT NULL DEFAULT '0',
  `reg_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `check_in_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `check_out_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `staff_class` int(11) NOT NULL DEFAULT '1',
  `college_major` int(11) NOT NULL DEFAULT '0',
  `college_degree` int(11) NOT NULL DEFAULT '0',
  `degree_reqts_complete` tinyint(1) NOT NULL DEFAULT '0',
  `session_1` varchar(50) NOT NULL DEFAULT '',
  `session_2` varchar(50) NOT NULL DEFAULT '',
  `session_3` varchar(50) NOT NULL DEFAULT '',
  `session_4` varchar(50) NOT NULL DEFAULT '',
  `current_reg_step` varchar(50) NOT NULL DEFAULT '',
  `conclave_order_id` int(11) NOT NULL DEFAULT '0',
  `registration_complete` tinyint(1) NOT NULL DEFAULT '0',
  `housing_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `conclave_attendance` */

/*Table structure for table `contacts` */

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `contact_type` varchar(50) NOT NULL DEFAULT '',
  `org_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `contacts` */

/*Table structure for table `degrees` */

CREATE TABLE `degrees` (
  `degree_id` int(11) NOT NULL AUTO_INCREMENT,
  `degree_name` varchar(50) NOT NULL DEFAULT '',
  `sequence` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`degree_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `degrees` */

insert  into `degrees`(`degree_id`,`degree_name`,`sequence`) values ('1','Associate','1');
insert  into `degrees`(`degree_id`,`degree_name`,`sequence`) values ('2','Bachelor','2');
insert  into `degrees`(`degree_id`,`degree_name`,`sequence`) values ('3','Master','3');
insert  into `degrees`(`degree_id`,`degree_name`,`sequence`) values ('4','Doctorate','4');
insert  into `degrees`(`degree_id`,`degree_name`,`sequence`) values ('5','Honorary Doctorate','0');

/*Table structure for table `evaluation_questions` */

CREATE TABLE `evaluation_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL DEFAULT '0',
  `order` decimal(3,1) DEFAULT NULL,
  `question` varchar(255) NOT NULL DEFAULT '',
  `type` set('text','select','radio','checkbox','textarea') NOT NULL DEFAULT 'text',
  `options` text,
  `heading` set('eval','staff') DEFAULT 'eval',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`question_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `evaluation_questions` */

/*Table structure for table `evaluation_responses` */

CREATE TABLE `evaluation_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `response` text,
  `timestamp` int(11) DEFAULT '0',
  `event_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `evaluation_responses` */

/*Table structure for table `housing` */

CREATE TABLE `housing` (
  `housing_id` int(11) NOT NULL AUTO_INCREMENT,
  `housing_name` varchar(255) NOT NULL,
  `housing_description` varchar(255) NOT NULL,
  `housing_capacity` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`housing_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `housing` */

/*Table structure for table `members` */

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(50) NOT NULL DEFAULT '',
  `address2` varchar(50) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `zip` varchar(50) NOT NULL DEFAULT '',
  `latitude` float(10,6) NOT NULL DEFAULT '0.000000',
  `longitude` float(10,6) NOT NULL DEFAULT '0.000000',
  `primary_phone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `oahonor` varchar(50) NOT NULL DEFAULT '',
  `gender` set('M','F') DEFAULT NULL,
  `registered_unit_type` varchar(50) NOT NULL DEFAULT '',
  `registered_unit_number` smallint(6) NOT NULL DEFAULT '0',
  `lodge` int(11) NOT NULL DEFAULT '0',
  `chapter` int(11) NOT NULL DEFAULT '0',
  `ordeal_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `position` varchar(50) NOT NULL DEFAULT '',
  `birthdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `emer_contact_name` varchar(50) NOT NULL DEFAULT '',
  `emer_relationship` varchar(50) NOT NULL DEFAULT '',
  `emer_phone_1` varchar(50) NOT NULL DEFAULT '',
  `emer_phone_2` varchar(50) NOT NULL DEFAULT '',
  `food_allergy` tinyint(1) NOT NULL DEFAULT '0',
  `special_care` tinyint(1) NOT NULL DEFAULT '0',
  `special_diet` tinyint(1) NOT NULL DEFAULT '0',
  `special_diet_explain` mediumtext NOT NULL,
  `sleeping_device` tinyint(1) NOT NULL DEFAULT '0',
  `conditions_explanation` mediumtext NOT NULL,
  `passkey` varchar(100) NOT NULL DEFAULT '',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  `lastlogin` varchar(100) NOT NULL DEFAULT '',
  `comment` varchar(255) NOT NULL DEFAULT '',
  `mailing` tinyint(1) NOT NULL DEFAULT '0',
  `custom1` varchar(100) NOT NULL DEFAULT '',
  `custom2` varchar(100) NOT NULL DEFAULT '',
  `custom3` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `members` */

/*Table structure for table `ordered_items` */

CREATE TABLE `ordered_items` (
  `ordered_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `product_option_id` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `price` double NOT NULL DEFAULT '0',
  `delivered` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ordered_item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `ordered_items` */

/*Table structure for table `orders` */

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `order_year` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0',
  `total_collected` double(5,2) NOT NULL DEFAULT '0.00',
  `shipping` double(5,2) NOT NULL DEFAULT '0.00',
  `shipping_method` set('ship','pickup') NOT NULL DEFAULT 'pickup',
  `cc` varchar(50) NOT NULL DEFAULT '',
  `exp_month` char(2) NOT NULL DEFAULT '',
  `exp_year` char(2) NOT NULL DEFAULT '',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `cc_processed` tinyint(1) NOT NULL DEFAULT '0',
  `transaction_id` tinytext NOT NULL,
  `order_method` varchar(50) NOT NULL DEFAULT '',
  `payment_method` varchar(50) NOT NULL DEFAULT '',
  `payment_notes` varchar(50) NOT NULL DEFAULT '',
  `complete` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `orders` */

/*Table structure for table `product_categories` */

CREATE TABLE `product_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

/*Data for the table `product_categories` */

insert  into `product_categories`(`category_id`,`category`) values ('1','Conclave Fee');
insert  into `product_categories`(`category_id`,`category`) values ('2','Early Registration Discount');
insert  into `product_categories`(`category_id`,`category`) values ('3','General Discount');
insert  into `product_categories`(`category_id`,`category`) values ('4','Guest');
insert  into `product_categories`(`category_id`,`category`) values ('5','Auction');
insert  into `product_categories`(`category_id`,`category`) values ('6','Trading Post');
insert  into `product_categories`(`category_id`,`category`) values ('7','Lodge/Contingent Discount');

/*Table structure for table `product_options` */

CREATE TABLE `product_options` (
  `product_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL DEFAULT '0',
  `option_value` varchar(50) NOT NULL DEFAULT '',
  `price` double(5,2) NOT NULL DEFAULT '0.00',
  `inventory` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`product_option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `product_options` */

/*Table structure for table `products` */

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_code` varchar(25) NOT NULL DEFAULT '',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `item` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `price` double NOT NULL DEFAULT '0',
  `option_name` varchar(50) NOT NULL DEFAULT '',
  `show_in_store` tinyint(1) NOT NULL DEFAULT '0',
  `coupon` tinyint(1) NOT NULL DEFAULT '0',
  `coupon_criteria` tinytext NOT NULL,
  `auto_add_coupon` tinyint(1) NOT NULL DEFAULT '0',
  `inactive` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0',
  `inventory` int(11) NOT NULL DEFAULT '0',
  `image_thumbnail` varchar(255) NOT NULL DEFAULT '',
  `image_full` varchar(255) NOT NULL DEFAULT '',
  `ship_weight` double NOT NULL DEFAULT '0',
  `ship_cost` double NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `event_id_only` int(11) DEFAULT NULL,
  `no_ship` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `image` blob,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `products` */

insert  into `products`(`product_id`,`product_code`,`category_id`,`item`,`description`,`price`,`option_name`,`show_in_store`,`coupon`,`coupon_criteria`,`auto_add_coupon`,`inactive`,`inventory`,`image_thumbnail`,`image_full`,`ship_weight`,`ship_cost`,`ordering`,`event_id_only`,`no_ship`,`deleted`,`image`,`last_updated`) values ('1','CONCLAVE_REG','1','Registration to Conclave','','55','','0','0','','0','0','660','','','0','0','0','0','0','0','image','2013-04-24 00:53:43');

/*Table structure for table `secret_questions` */

CREATE TABLE `secret_questions` (
  `member_id` int(11) NOT NULL DEFAULT '0',
  `question1` varchar(255) NOT NULL DEFAULT '',
  `question1_answer` varchar(255) NOT NULL DEFAULT '',
  `question2` varchar(255) NOT NULL DEFAULT '',
  `question2_answer` varchar(255) NOT NULL DEFAULT '',
  `question3` varchar(255) NOT NULL DEFAULT '',
  `question3_answer` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `member_id` (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `secret_questions` */

/*Table structure for table `staff_classifications` */

CREATE TABLE `staff_classifications` (
  `staff_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_classification` varchar(50) NOT NULL DEFAULT '',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`staff_class_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `staff_classifications` */

insert  into `staff_classifications`(`staff_class_id`,`staff_classification`,`deleted`) values ('1','Participant','0');
insert  into `staff_classifications`(`staff_class_id`,`staff_classification`,`deleted`) values ('2','Staff','0');
insert  into `staff_classifications`(`staff_class_id`,`staff_classification`,`deleted`) values ('3','Professor','0');

/*Table structure for table `training_attendance` */

CREATE TABLE `training_attendance` (
  `session_id` varchar(50) NOT NULL DEFAULT '',
  `member_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `training_attendance` */

/*Table structure for table `training_locations` */

CREATE TABLE `training_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `training_location` varchar(50) NOT NULL DEFAULT '',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`location_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `training_locations` */

/*Table structure for table `training_sessions` */

CREATE TABLE `training_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `college` int(11) NOT NULL DEFAULT '0',
  `course_number` smallint(6) NOT NULL DEFAULT '0',
  `session_name` varchar(100) NOT NULL DEFAULT '',
  `trainer_name` varchar(50) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `session_length` int(6) NOT NULL DEFAULT '1',
  `location` int(11) NOT NULL DEFAULT '0',
  `offered` tinyint(1) NOT NULL DEFAULT '1',
  `max_size` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `training_sessions` */

insert  into `training_sessions`(`session_id`,`college`,`course_number`,`session_name`,`trainer_name`,`description`,`time`,`session_length`,`location`,`offered`,`max_size`,`deleted`) values ('1','1','0','(No class selected-4 sessions required for degree)','(Go to a session of your choice)','(Disregard this listing)','1','1','0','1','0','0');
insert  into `training_sessions`(`session_id`,`college`,`course_number`,`session_name`,`trainer_name`,`description`,`time`,`session_length`,`location`,`offered`,`max_size`,`deleted`) values ('2','1','0','(No class selected-4 sessions required for degree)','(Go to a session of your choice)','(Disregard this listing)','2','1','0','1','0','0');
insert  into `training_sessions`(`session_id`,`college`,`course_number`,`session_name`,`trainer_name`,`description`,`time`,`session_length`,`location`,`offered`,`max_size`,`deleted`) values ('3','1','0','(No class selected-4 sessions required for degree)','(Go to a session of your choice)','(Disregard this listing)','3','1','0','1','0','0');
insert  into `training_sessions`(`session_id`,`college`,`course_number`,`session_name`,`trainer_name`,`description`,`time`,`session_length`,`location`,`offered`,`max_size`,`deleted`) values ('4','1','0','(No class selected-4 sessions required for degree)','(Go to a session of your choice)','(Disregard this listing)','4','1','0','1','0','0');

/*Table structure for table `training_times` */

CREATE TABLE `training_times` (
  `time_id` int(11) NOT NULL AUTO_INCREMENT,
  `time_slot` char(1) NOT NULL DEFAULT '',
  `time` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`time_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `training_times` */

insert  into `training_times`(`time_id`,`time_slot`,`time`) values ('1','A','09:00 AM');
insert  into `training_times`(`time_id`,`time_slot`,`time`) values ('2','B','10:00 AM');
insert  into `training_times`(`time_id`,`time_slot`,`time`) values ('3','C','11:00 AM');
insert  into `training_times`(`time_id`,`time_slot`,`time`) values ('4','D','12:00 PM');
