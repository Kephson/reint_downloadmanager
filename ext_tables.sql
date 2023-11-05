#
# Table structure for table 'tx_reintdownloadmanager_domain_model_download'
#
CREATE TABLE tx_reintdownloadmanager_domain_model_download
(
	sys_file_uid int(11) DEFAULT '0' NOT NULL,
	downloads    int(11) DEFAULT '0' NOT NULL,
	KEY `sys_file_uid` (`sys_file_uid`)
);


#
# add sorting field for file collections
#
CREATE TABLE sys_file_collection
(
	description          text,
	description_frontend text,
	sorting              int(11) DEFAULT '0' NOT NULL
);
