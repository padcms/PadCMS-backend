INSERT INTO old_device_token SELECT * FROM device_token;
TRUNCATE device_token;
ALTER TABLE `device_token` DROP `udid`;
ALTER TABLE  `device_token` DROP INDEX  `udid_token_client` ,
ADD INDEX  `token` (  `token` );
ALTER TABLE  `device_token` ADD UNIQUE  `token_application` (  `token` ,  `application_id` );

INSERT INTO device_token( token, application_id, created, updated, expired )
SELECT token, application_id, created, updated, expired
FROM  `old_device_token`
GROUP BY token, application_id
HAVING updated = MAX( updated )
ORDER BY id;
