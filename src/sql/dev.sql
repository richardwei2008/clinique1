SELECT s.openid, i.headimgurl, TRIM(LEADING '<br>' FROM t.tag), count( 1)AS numberOfSupport
FROM clnq_user_support s
LEFT JOIN clnq_user_tag t ON ( t.openid = s.openid )
LEFT JOIN clnq_user_info i ON ( i.openid = s.openid )
GROUP BY s.openid, i.headimgurl, t.tag
ORDER BY numberOfSupport DESC
LIMIT 10

truncate table clnq_user_info;
truncate table clnq_user_success;
truncate table clnq_user_support;
truncate table clnq_user_tag;

INSERT INTO `app_beyondwechattest`.`clnq_user_support` (
 `id` ,
`openid` ,
`support_openid` ,
`createtime`
)
VALUES (
 '4', 'oxqECj13-L8dz--q6dd9Z34ouTfc', 'oxqECj13-d8dz--q6dd9r34ouTfc',
CURRENT_TIMESTAMP
), (
 '5', 'oxqECj13-L8dz--q6dd9Z34ouTfc', 'oxqEed13-L8dz--q6dd9Z34ouTfc',
CURRENT_TIMESTAMP
), (
 '6', 'oxqECj13-L8dz--q6dd9Z34ouTfc', 'oxqECd13-L8dz--q6df9Z34ouTfc',
CURRENT_TIMESTAMP
);

oxqECj13-L8dz--q6dd9Z34ouTfc
2
北京
010
北京
12
北京百盛
17171888818
CURRENT_TIMESTAMP


INSERT INTO`app_beyondwechattest`.`clnq_user_success` (
 `id` ,
 `openid` ,
 `province_code` ,
 `province_name` ,
 `city_code` ,
 `city_name` ,
 `site_code` ,
 `site_name` ,
 `cellphone` ,
 `createtime`
)
VALUES (
 '7','oxqECj13-L8dz--q6dd9Z34ouTf3','2','北京','010','北京','12','北京百盛','17171888810',
CURRENT_TIMESTAMP
), (
 '8','oxqECj13-L8dz--q6dd9Z34ouTf4','2','北京','010','北京','12','北京百盛','17171888819',
CURRENT_TIMESTAMP
), (
 '9','oxqECj13-L8dz--q6dd9Z34ouTf5','2','北京','010','北京','12','北京百盛','17171888818',
CURRENT_TIMESTAMP
), (
 '10','oxqECj13-L8dz--q6dd9Z34ouTf6','2','北京','010','北京','12','北京百盛','17171888817',
CURRENT_TIMESTAMP
), (
 '11','oxqECj13-L8dz--q6dd9Z34ouTf7','2','北京','010','北京','12','北京百盛','17171888816',
CURRENT_TIMESTAMP
), (
 '12','oxqECj13-L8dz--q6dd9Z34ouTf8','2','北京','010','北京','12','北京百盛','17171888815',
CURRENT_TIMESTAMP
), (
 '13','oxqECj13-L8dz--q6dd9Z34ouTf9','2','北京','010','北京','12','北京百盛','17171888814',
CURRENT_TIMESTAMP
);


--statistics
select * from cl_cp.clnq_user_info u where u.createtime between '2014-10-10' and '2014-10-11';
--今日参加活动独立用户总人数
select count(1) from cl_cp.clnq_user_info u where u.createtime between '2014-10-10' and '2014-10-11';

--今日真男人宣言选择总人数
SELECT count(1) FROM `clnq_user_tag` where createtime between '2014-10-10' and '2014-10-11'

--今日真男人宣言投票总人数
SELECT count(1) FROM `clnq_user_support` where createtime between '2014-10-10' and '2014-10-11'

--今日Top10用户信息
SELECT s.openid, i.nickname, u.cellphone, count(1) as numberOfSupport FROM cl_cp.clnq_user_support s
LEFT JOIN cl_cp.clnq_user_tag t on (t.openid = s.openid)
LEFT JOIN cl_cp.clnq_user_info i on (i.openid = s.openid)
LEFT JOIN cl_cp.clnq_user_success u on (u.openid = s.openid) WHERE s.createtime
BETWEEN '2014-10-10' AND '2014-10-11' GROUP BY s.openid, i.headimgurl, t.tag ORDER BY numberOfSupport DESC LIMIT 10;

SELECT site_code, site_name, count(1) FROM `clnq_user_success` group by site_code, site_name

ALTER TABLE `clnq_user_support` ADD `IP_MAC` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL AFTER `support_openid`

ALTER TABLE `clnq_user_success` ADD `IP_MAC` VARCHAR( 256 ) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL AFTER `cellphone`

ALTER TABLE `clnq_user_success` ADD `servertime` DATETIME NULL DEFAULT NULL AFTER `status`

update clnq_user_success set servertime = DATE_ADD(createtime, INTERVAL -12 MINUTE)