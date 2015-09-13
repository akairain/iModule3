-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- 생성 시간: 15-09-13 15:33
-- 서버 버전: 5.5.44-0ubuntu0.14.04.1
-- PHP 버전: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- 데이터베이스: `arzzcom`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `im_addon_table`
--

CREATE TABLE IF NOT EXISTS `im_addon_table` (
  `addon` varchar(20) NOT NULL,
  `hash` char(32) NOT NULL,
  `target` longtext NOT NULL,
  `active` enum('TRUE','FALSE') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_apidocument_post_table`
--

CREATE TABLE IF NOT EXISTS `im_apidocument_post_table` (
  `idx` int(11) NOT NULL,
  `aid` varchar(50) NOT NULL,
  `type` enum('CONFIG','PROPERTY','GLOBAL','METHOD','EVENT','ERROR') NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_required` enum('TRUE','FALSE') NOT NULL,
  `defined` varchar(10) NOT NULL,
  `deprecated` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_apidocument_post_version_table`
--

CREATE TABLE IF NOT EXISTS `im_apidocument_post_version_table` (
  `idx` int(11) NOT NULL,
  `aid` varchar(50) NOT NULL,
  `parent` int(11) NOT NULL,
  `property` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `version` varchar(10) NOT NULL,
  `stability` enum('DEPRECATED','EXPERIMENTAL','UNSTABLE','STABLE','FROZEN','LOCKED') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_apidocument_table`
--

CREATE TABLE IF NOT EXISTS `im_apidocument_table` (
  `aid` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `templet` varchar(20) NOT NULL,
  `class` varchar(100) NOT NULL,
  `defined` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_article_table`
--

CREATE TABLE IF NOT EXISTS `im_article_table` (
  `module` varchar(20) NOT NULL,
  `context` varchar(20) NOT NULL,
  `type` varchar(10) NOT NULL,
  `idx` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `update_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_attachment_table`
--

CREATE TABLE IF NOT EXISTS `im_attachment_table` (
  `idx` int(11) NOT NULL,
  `module` varchar(20) NOT NULL,
  `target` varchar(50) NOT NULL,
  `path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `mime` varchar(100) NOT NULL,
  `size` bigint(15) NOT NULL,
  `width` int(5) NOT NULL,
  `height` int(5) NOT NULL,
  `wysiwyg` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `reg_date` int(11) NOT NULL,
  `status` enum('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
  `download` int(11) NOT NULL,
  `extra` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_board_attachment_table`
--

CREATE TABLE IF NOT EXISTS `im_board_attachment_table` (
  `idx` int(11) NOT NULL,
  `bid` varchar(20) NOT NULL,
  `type` enum('POST','MENT') NOT NULL,
  `parent` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_board_category_table`
--

CREATE TABLE IF NOT EXISTS `im_board_category_table` (
  `idx` int(11) NOT NULL,
  `bid` varchar(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `postnum` int(11) NOT NULL,
  `last_post` int(11) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_board_history_table`
--

CREATE TABLE IF NOT EXISTS `im_board_history_table` (
  `idx` int(11) NOT NULL,
  `type` enum('POST','MENT') NOT NULL,
  `parent` int(11) NOT NULL,
  `action` enum('VOTE','MODIFY') NOT NULL,
  `midx` int(11) NOT NULL,
  `result` varchar(10) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_board_ment_depth_table`
--

CREATE TABLE IF NOT EXISTS `im_board_ment_depth_table` (
  `idx` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `head` int(11) NOT NULL,
  `arrange` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  `source` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_board_ment_table`
--

CREATE TABLE IF NOT EXISTS `im_board_ment_table` (
  `idx` int(11) NOT NULL,
  `bid` varchar(20) NOT NULL,
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(65) NOT NULL,
  `content` longtext NOT NULL,
  `search` longtext NOT NULL,
  `reg_date` int(11) NOT NULL,
  `modify_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `good` int(11) NOT NULL,
  `bad` int(11) NOT NULL,
  `is_delete` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `is_secret` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `is_hidename` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_board_post_table`
--

CREATE TABLE IF NOT EXISTS `im_board_post_table` (
  `idx` int(11) NOT NULL,
  `bid` varchar(20) NOT NULL,
  `category` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` char(65) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `search` text NOT NULL,
  `field1` varchar(255) NOT NULL,
  `field2` varchar(255) NOT NULL,
  `field3` varchar(255) NOT NULL,
  `image` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `hit` int(11) NOT NULL,
  `ment` int(11) NOT NULL,
  `good` int(11) NOT NULL,
  `bad` int(11) NOT NULL,
  `last_ment` int(11) NOT NULL,
  `is_notice` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `is_html_title` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `is_secret` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `is_hidename` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_board_table`
--

CREATE TABLE IF NOT EXISTS `im_board_table` (
  `bid` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `templet` varchar(30) NOT NULL,
  `use_category` enum('NONE','USED','USEDALL','FORCE') NOT NULL DEFAULT 'NONE',
  `postlimit` int(2) NOT NULL,
  `pagelimit` int(2) NOT NULL,
  `mentlimit` int(3) NOT NULL,
  `view_notice_page` enum('ALL','FIRST') NOT NULL DEFAULT 'ALL',
  `view_notice_count` enum('INCLUDE','EXCLUDE') NOT NULL DEFAULT 'INCLUDE',
  `view_notice_list` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `post_point` int(11) NOT NULL DEFAULT '30',
  `ment_point` int(11) NOT NULL DEFAULT '10',
  `vote_point` int(11) NOT NULL DEFAULT '3',
  `post_exp` int(11) NOT NULL DEFAULT '10',
  `ment_exp` int(11) NOT NULL DEFAULT '5',
  `vote_exp` int(11) NOT NULL DEFAULT '1',
  `postnum` int(11) NOT NULL,
  `last_post` int(11) NOT NULL,
  `permission` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_answer_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_answer_table` (
  `idx` int(11) NOT NULL,
  `did` varchar(20) NOT NULL,
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `reg_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_attachment_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_attachment_table` (
  `idx` int(11) NOT NULL,
  `did` varchar(20) NOT NULL,
  `type` enum('POST','MENT','QUESTION','ANSWER') NOT NULL,
  `parent` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_category_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_category_table` (
  `idx` int(11) NOT NULL,
  `did` varchar(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `postnum` int(11) NOT NULL,
  `last_post` int(11) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_history_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_history_table` (
  `parent` int(11) NOT NULL,
  `action` enum('VOTE') NOT NULL,
  `midx` int(11) NOT NULL,
  `result` varchar(10) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_ment_depth_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_ment_depth_table` (
  `idx` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `head` int(11) NOT NULL,
  `arrange` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  `source` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_ment_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_ment_table` (
  `idx` int(11) NOT NULL,
  `did` varchar(20) NOT NULL,
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `search` longtext NOT NULL,
  `reg_date` int(11) NOT NULL,
  `modify_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `is_delete` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_post_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_post_table` (
  `idx` int(11) NOT NULL,
  `did` varchar(20) NOT NULL,
  `category` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `search` text NOT NULL,
  `homepage` varchar(255) NOT NULL,
  `license` varchar(20) NOT NULL,
  `price` int(11) NOT NULL,
  `field1` varchar(255) NOT NULL,
  `field2` varchar(255) NOT NULL,
  `field3` varchar(255) NOT NULL,
  `logo` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `hit` int(11) NOT NULL,
  `download` int(11) NOT NULL,
  `qna` int(11) NOT NULL,
  `ment` int(11) NOT NULL,
  `good` int(11) NOT NULL,
  `bad` int(11) NOT NULL,
  `last_version` varchar(20) NOT NULL,
  `last_update` int(11) NOT NULL,
  `is_delete` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_post_version_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_post_version_table` (
  `idx` int(11) NOT NULL,
  `did` varchar(20) NOT NULL,
  `parent` int(11) NOT NULL,
  `version` varchar(20) NOT NULL,
  `history` longtext NOT NULL,
  `file` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_purchase_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_purchase_table` (
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_question_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_question_table` (
  `idx` int(11) NOT NULL,
  `did` varchar(20) NOT NULL,
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `search` longtext NOT NULL,
  `reg_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `has_answer` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_dataroom_table`
--

CREATE TABLE IF NOT EXISTS `im_dataroom_table` (
  `did` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `templet` varchar(30) NOT NULL,
  `use_category` enum('NONE','USED') NOT NULL DEFAULT 'NONE',
  `postlimit` int(2) NOT NULL,
  `pagelimit` int(2) NOT NULL,
  `qnalimit` int(2) NOT NULL,
  `mentlimit` int(3) NOT NULL,
  `post_point` int(11) NOT NULL DEFAULT '100',
  `ment_point` int(11) NOT NULL DEFAULT '30',
  `vote_point` int(11) NOT NULL DEFAULT '3',
  `post_exp` int(11) NOT NULL DEFAULT '50',
  `ment_exp` int(11) NOT NULL DEFAULT '10',
  `vote_exp` int(11) NOT NULL DEFAULT '2',
  `postnum` int(11) NOT NULL,
  `last_post` int(11) NOT NULL,
  `permission` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_donation_list_table`
--

CREATE TABLE IF NOT EXISTS `im_donation_list_table` (
  `idx` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `midx` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `intype` varchar(5) NOT NULL,
  `is_secret` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `reg_date` date NOT NULL,
  `status` enum('TRUE','FALSE','WAIT') NOT NULL DEFAULT 'WAIT',
  `gift_point` int(11) NOT NULL,
  `gift_exp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_email_receiver_table`
--

CREATE TABLE IF NOT EXISTS `im_email_receiver_table` (
  `idx` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `to` varchar(100) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `status` enum('SUCCESS','SENDING','FAIL') NOT NULL DEFAULT 'SENDING',
  `result` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_email_send_table`
--

CREATE TABLE IF NOT EXISTS `im_email_send_table` (
  `idx` int(11) NOT NULL,
  `from` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `search` text NOT NULL,
  `receiver` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_forum_attachment_table`
--

CREATE TABLE IF NOT EXISTS `im_forum_attachment_table` (
  `idx` int(11) NOT NULL,
  `fid` varchar(20) NOT NULL,
  `type` enum('POST','MENT') NOT NULL,
  `parent` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_forum_history_table`
--

CREATE TABLE IF NOT EXISTS `im_forum_history_table` (
  `type` enum('POST','MENT') NOT NULL,
  `parent` int(11) NOT NULL,
  `action` enum('VOTE','MODIFY') NOT NULL,
  `midx` int(11) NOT NULL,
  `result` varchar(10) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_forum_label_table`
--

CREATE TABLE IF NOT EXISTS `im_forum_label_table` (
  `idx` int(11) NOT NULL,
  `fid` varchar(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `postnum` int(11) NOT NULL,
  `last_post` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_forum_ment_depth_table`
--

CREATE TABLE IF NOT EXISTS `im_forum_ment_depth_table` (
  `idx` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `head` int(11) NOT NULL,
  `arrange` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  `source` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_forum_ment_table`
--

CREATE TABLE IF NOT EXISTS `im_forum_ment_table` (
  `idx` int(11) NOT NULL,
  `fid` varchar(20) NOT NULL,
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `search` longtext NOT NULL,
  `reg_date` int(11) NOT NULL,
  `modify_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `vote` int(11) NOT NULL,
  `is_delete` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_forum_post_label_table`
--

CREATE TABLE IF NOT EXISTS `im_forum_post_label_table` (
  `idx` int(11) NOT NULL,
  `label` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_forum_post_table`
--

CREATE TABLE IF NOT EXISTS `im_forum_post_table` (
  `idx` int(11) NOT NULL,
  `fid` varchar(20) NOT NULL,
  `midx` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `search` text NOT NULL,
  `field1` varchar(255) NOT NULL,
  `field2` varchar(255) NOT NULL,
  `field3` varchar(255) NOT NULL,
  `image` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `hit` int(11) NOT NULL,
  `ment` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `last_ment` int(11) NOT NULL,
  `last_ment_midx` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_forum_table`
--

CREATE TABLE IF NOT EXISTS `im_forum_table` (
  `fid` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `templet` varchar(30) NOT NULL,
  `use_label` enum('NONE','USED','FORCE') NOT NULL DEFAULT 'NONE',
  `postlimit` int(2) NOT NULL DEFAULT '20',
  `pagelimit` int(2) NOT NULL DEFAULT '10',
  `mentlimit` int(3) NOT NULL DEFAULT '20',
  `post_point` int(11) NOT NULL DEFAULT '30',
  `ment_point` int(11) NOT NULL DEFAULT '10',
  `vote_point` int(11) NOT NULL DEFAULT '3',
  `post_exp` int(11) NOT NULL DEFAULT '10',
  `ment_exp` int(11) NOT NULL DEFAULT '5',
  `vote_exp` int(11) NOT NULL DEFAULT '1',
  `postnum` int(11) NOT NULL,
  `last_post` int(11) NOT NULL,
  `permission` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_keyword_table`
--

CREATE TABLE IF NOT EXISTS `im_keyword_table` (
  `keyword` varchar(20) NOT NULL,
  `keycode` varchar(50) NOT NULL,
  `engcode` varchar(50) NOT NULL,
  `hit` int(11) NOT NULL,
  `last_search` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_attachment_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_attachment_table` (
  `idx` int(11) NOT NULL,
  `fid` varchar(20) NOT NULL,
  `type` enum('POST','MENT') NOT NULL,
  `parent` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_attend_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_attend_table` (
  `midx` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `status` enum('ACTIVE','DEACTIVE') NOT NULL DEFAULT 'ACTIVE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_class_label_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_class_label_table` (
  `idx` int(11) NOT NULL,
  `label` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_class_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_class_table` (
  `idx` int(11) NOT NULL,
  `lid` varchar(20) NOT NULL,
  `midx` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('public','private') NOT NULL DEFAULT 'public',
  `attend` enum('open','close') NOT NULL DEFAULT 'open',
  `cover` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `subject` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `ment` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `last_subject` int(11) NOT NULL,
  `last_ment_midx` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_label_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_label_table` (
  `idx` int(11) NOT NULL,
  `lid` varchar(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `classnum` int(11) NOT NULL,
  `last_class` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_ment_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_ment_table` (
  `idx` int(11) NOT NULL,
  `lid` varchar(20) NOT NULL,
  `parent` int(11) NOT NULL,
  `fromidx` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `content` longtext NOT NULL,
  `reg_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `position` double NOT NULL,
  `is_delete` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_post_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_post_table` (
  `idx` int(11) NOT NULL,
  `lid` varchar(20) NOT NULL,
  `class` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `search` text NOT NULL,
  `context` text NOT NULL,
  `progress_check` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `ment` int(11) NOT NULL,
  `last_ment` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_subject_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_subject_table` (
  `idx` int(11) NOT NULL,
  `lid` varchar(30) NOT NULL,
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_table` (
  `lid` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `templet` varchar(30) NOT NULL,
  `use_label` enum('NONE','USED','FORCE') NOT NULL DEFAULT 'NONE',
  `classlimit` int(2) NOT NULL,
  `pagelimit` int(2) NOT NULL,
  `mentlimit` int(2) NOT NULL,
  `classnum` int(11) NOT NULL,
  `last_class` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_lms_tracking_table`
--

CREATE TABLE IF NOT EXISTS `im_lms_tracking_table` (
  `midx` int(11) NOT NULL,
  `pidx` int(11) NOT NULL,
  `tracking` longtext NOT NULL,
  `percent` int(3) NOT NULL,
  `last_position` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `update_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_member_activity_table`
--

CREATE TABLE IF NOT EXISTS `im_member_activity_table` (
  `idx` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `module` varchar(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `content` longtext NOT NULL,
  `exp` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_member_email_table`
--

CREATE TABLE IF NOT EXISTS `im_member_email_table` (
  `midx` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `code` char(6) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `status` enum('SENDING','VERIFIED','CANCELED') NOT NULL DEFAULT 'SENDING'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_member_level_table`
--

CREATE TABLE IF NOT EXISTS `im_member_level_table` (
  `level` int(11) NOT NULL DEFAULT '0',
  `exp` int(11) NOT NULL DEFAULT '0',
  `next` int(11) NOT NULL DEFAULT '0',
  `limit_exp` int(11) NOT NULL DEFAULT '0',
  `limit_point` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_member_point_table`
--

CREATE TABLE IF NOT EXISTS `im_member_point_table` (
  `idx` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  `module` varchar(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `content` longtext NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_member_signup_table`
--

CREATE TABLE IF NOT EXISTS `im_member_signup_table` (
  `gidx` varchar(20) NOT NULL DEFAULT '' COMMENT '그룹아이디',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '필드명',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '필드종류',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '필드제목',
  `msg` varchar(255) NOT NULL DEFAULT '' COMMENT '필드설명',
  `value` longtext NOT NULL COMMENT '필드설정값',
  `allowblank` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE' COMMENT '필수입력유무',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '순서'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_member_social_table`
--

CREATE TABLE IF NOT EXISTS `im_member_social_table` (
  `midx` int(11) NOT NULL,
  `code` enum('facebook','github','google','youtube') NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `refresh_token` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_member_table`
--

CREATE TABLE IF NOT EXISTS `im_member_table` (
  `idx` int(11) NOT NULL,
  `type` enum('ADMINISTRATOR','MODERATOR','MEMBER') NOT NULL DEFAULT 'MEMBER',
  `gidx` varchar(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` char(65) NOT NULL,
  `password_question` int(11) NOT NULL,
  `password_answer` char(65) NOT NULL,
  `name` varchar(20) NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `homepage` varchar(255) NOT NULL,
  `cellphone` varchar(12) NOT NULL,
  `gender` enum('','MALE','FEMALE') NOT NULL,
  `birthday` varchar(10) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `address1` varchar(150) NOT NULL,
  `address2` varchar(255) NOT NULL,
  `exp` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `leave_date` int(11) NOT NULL,
  `last_login` int(11) NOT NULL,
  `status` enum('ACTIVE','VERIFYING','WAITING','LEAVE','DEACTIVE') NOT NULL DEFAULT 'VERIFYING'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_minitalk_price_table`
--

CREATE TABLE IF NOT EXISTS `im_minitalk_price_table` (
  `usernum` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_minitalk_server_table`
--

CREATE TABLE IF NOT EXISTS `im_minitalk_server_table` (
  `idx` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `port` int(5) NOT NULL,
  `is_ssl` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `version` varchar(8) NOT NULL,
  `status` enum('ONLINE','OFFLINE') NOT NULL,
  `user` int(11) NOT NULL,
  `channel` int(11) NOT NULL,
  `memory` int(11) NOT NULL,
  `uptime` int(11) NOT NULL,
  `is_select` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE',
  `check_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_minitalk_service_hosting_table`
--

CREATE TABLE IF NOT EXISTS `im_minitalk_service_hosting_table` (
  `idx` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `service` enum('BETA','FREE','PAID') NOT NULL DEFAULT 'FREE',
  `title` varchar(100) NOT NULL,
  `client_id` char(32) NOT NULL,
  `server_id` char(32) NOT NULL,
  `maxuser` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `expire_date` int(11) NOT NULL,
  `server` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `channel` int(11) NOT NULL,
  `callback` varchar(255) NOT NULL,
  `check_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_module_table`
--

CREATE TABLE IF NOT EXISTS `im_module_table` (
  `module` varchar(20) NOT NULL,
  `hash` char(32) NOT NULL,
  `database` varchar(20) NOT NULL,
  `is_global` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `is_article` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `configs` longtext NOT NULL,
  `target` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_page_table`
--

CREATE TABLE IF NOT EXISTS `im_page_table` (
  `domain` varchar(100) NOT NULL,
  `language` char(2) NOT NULL DEFAULT 'ko',
  `menu` varchar(20) NOT NULL,
  `page` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('page','external','widget','module') NOT NULL,
  `layout` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `image` int(11) NOT NULL,
  `context` longtext NOT NULL,
  `sort` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_push_table`
--

CREATE TABLE IF NOT EXISTS `im_push_table` (
  `midx` int(11) NOT NULL,
  `module` varchar(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `fromcode` varchar(20) NOT NULL,
  `content` longtext NOT NULL,
  `reg_date` int(11) NOT NULL,
  `is_check` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `is_read` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_qna_attachment_table`
--

CREATE TABLE IF NOT EXISTS `im_qna_attachment_table` (
  `idx` int(11) NOT NULL,
  `qid` varchar(20) NOT NULL,
  `type` enum('POST','MENT') NOT NULL,
  `parent` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_qna_history_table`
--

CREATE TABLE IF NOT EXISTS `im_qna_history_table` (
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `result` varchar(10) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_qna_label_table`
--

CREATE TABLE IF NOT EXISTS `im_qna_label_table` (
  `idx` int(11) NOT NULL,
  `qid` varchar(20) NOT NULL,
  `title` varchar(20) NOT NULL,
  `postnum` int(11) NOT NULL,
  `last_post` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_qna_ment_table`
--

CREATE TABLE IF NOT EXISTS `im_qna_ment_table` (
  `idx` int(11) NOT NULL,
  `qid` varchar(20) NOT NULL,
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `reg_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `is_secret` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_qna_post_label_table`
--

CREATE TABLE IF NOT EXISTS `im_qna_post_label_table` (
  `idx` int(11) NOT NULL,
  `label` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_qna_post_table`
--

CREATE TABLE IF NOT EXISTS `im_qna_post_table` (
  `idx` int(11) NOT NULL,
  `qid` varchar(20) NOT NULL,
  `type` enum('QUESTION','ANSWER') NOT NULL,
  `parent` int(11) NOT NULL,
  `midx` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `point` int(11) NOT NULL,
  `search` text NOT NULL,
  `image` int(11) NOT NULL,
  `reg_date` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `hit` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `answer` int(11) NOT NULL,
  `last_answer` int(11) NOT NULL,
  `is_select` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_qna_table`
--

CREATE TABLE IF NOT EXISTS `im_qna_table` (
  `qid` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `templet` varchar(30) NOT NULL,
  `use_label` enum('NONE','USED','FORCE') NOT NULL DEFAULT 'NONE',
  `postlimit` int(2) NOT NULL,
  `pagelimit` int(2) NOT NULL,
  `post_point` int(11) NOT NULL DEFAULT '30',
  `answer_point` int(11) NOT NULL DEFAULT '50',
  `select_point` int(11) NOT NULL DEFAULT '100',
  `vote_point` int(11) NOT NULL DEFAULT '5',
  `post_exp` int(11) NOT NULL DEFAULT '10',
  `answer_exp` int(11) NOT NULL DEFAULT '30',
  `select_exp` int(11) NOT NULL DEFAULT '50',
  `vote_exp` int(11) NOT NULL DEFAULT '3',
  `postnum` int(11) NOT NULL,
  `last_post` int(11) NOT NULL,
  `permission` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `im_site_table`
--

CREATE TABLE IF NOT EXISTS `im_site_table` (
  `domain` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `alias` text NOT NULL,
  `language` char(2) NOT NULL DEFAULT 'ko',
  `description` text NOT NULL,
  `image` int(11) NOT NULL,
  `templet` varchar(20) NOT NULL DEFAULT 'default',
  `logo` text NOT NULL,
  `emblem` int(11) NOT NULL,
  `favicon` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `is_ssl` enum('TRUE','FALSE') NOT NULL DEFAULT 'FALSE',
  `is_default` enum('TRUE','FALSE') NOT NULL DEFAULT 'TRUE'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `im_addon_table`
--
ALTER TABLE `im_addon_table`
  ADD PRIMARY KEY (`addon`);

--
-- 테이블의 인덱스 `im_apidocument_post_table`
--
ALTER TABLE `im_apidocument_post_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `aid` (`aid`,`type`);

--
-- 테이블의 인덱스 `im_apidocument_post_version_table`
--
ALTER TABLE `im_apidocument_post_version_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `parent` (`parent`);

--
-- 테이블의 인덱스 `im_apidocument_table`
--
ALTER TABLE `im_apidocument_table`
  ADD PRIMARY KEY (`aid`);

--
-- 테이블의 인덱스 `im_article_table`
--
ALTER TABLE `im_article_table`
  ADD PRIMARY KEY (`module`,`type`,`idx`);

--
-- 테이블의 인덱스 `im_attachment_table`
--
ALTER TABLE `im_attachment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `module` (`module`);

--
-- 테이블의 인덱스 `im_board_attachment_table`
--
ALTER TABLE `im_board_attachment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `type` (`type`,`parent`);

--
-- 테이블의 인덱스 `im_board_category_table`
--
ALTER TABLE `im_board_category_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `bid` (`bid`);

--
-- 테이블의 인덱스 `im_board_history_table`
--
ALTER TABLE `im_board_history_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `type` (`type`,`parent`,`action`);

--
-- 테이블의 인덱스 `im_board_ment_depth_table`
--
ALTER TABLE `im_board_ment_depth_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `parent` (`parent`),
  ADD KEY `head` (`head`),
  ADD KEY `source` (`source`);

--
-- 테이블의 인덱스 `im_board_ment_table`
--
ALTER TABLE `im_board_ment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `bid` (`bid`),
  ADD KEY `parent` (`parent`),
  ADD FULLTEXT KEY `search` (`search`);

--
-- 테이블의 인덱스 `im_board_post_table`
--
ALTER TABLE `im_board_post_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `bid` (`bid`),
  ADD KEY `category` (`category`),
  ADD KEY `midx` (`midx`),
  ADD KEY `name` (`name`),
  ADD FULLTEXT KEY `title` (`title`,`search`);

--
-- 테이블의 인덱스 `im_board_table`
--
ALTER TABLE `im_board_table`
  ADD PRIMARY KEY (`bid`);

--
-- 테이블의 인덱스 `im_dataroom_answer_table`
--
ALTER TABLE `im_dataroom_answer_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `parent` (`parent`);

--
-- 테이블의 인덱스 `im_dataroom_attachment_table`
--
ALTER TABLE `im_dataroom_attachment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `type` (`type`,`parent`);

--
-- 테이블의 인덱스 `im_dataroom_category_table`
--
ALTER TABLE `im_dataroom_category_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `did` (`did`);

--
-- 테이블의 인덱스 `im_dataroom_history_table`
--
ALTER TABLE `im_dataroom_history_table`
  ADD PRIMARY KEY (`parent`,`action`,`midx`);

--
-- 테이블의 인덱스 `im_dataroom_ment_depth_table`
--
ALTER TABLE `im_dataroom_ment_depth_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `parent` (`parent`),
  ADD KEY `head` (`head`),
  ADD KEY `source` (`source`);

--
-- 테이블의 인덱스 `im_dataroom_ment_table`
--
ALTER TABLE `im_dataroom_ment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `parent` (`parent`),
  ADD FULLTEXT KEY `search` (`search`);

--
-- 테이블의 인덱스 `im_dataroom_post_table`
--
ALTER TABLE `im_dataroom_post_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `did` (`did`),
  ADD KEY `category` (`category`),
  ADD KEY `midx` (`midx`),
  ADD FULLTEXT KEY `title` (`title`,`search`);

--
-- 테이블의 인덱스 `im_dataroom_post_version_table`
--
ALTER TABLE `im_dataroom_post_version_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `parent` (`parent`);

--
-- 테이블의 인덱스 `im_dataroom_purchase_table`
--
ALTER TABLE `im_dataroom_purchase_table`
  ADD PRIMARY KEY (`parent`,`midx`);

--
-- 테이블의 인덱스 `im_dataroom_question_table`
--
ALTER TABLE `im_dataroom_question_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `parent` (`parent`),
  ADD FULLTEXT KEY `title` (`title`,`search`);

--
-- 테이블의 인덱스 `im_dataroom_table`
--
ALTER TABLE `im_dataroom_table`
  ADD PRIMARY KEY (`did`);

--
-- 테이블의 인덱스 `im_donation_list_table`
--
ALTER TABLE `im_donation_list_table`
  ADD PRIMARY KEY (`idx`);

--
-- 테이블의 인덱스 `im_email_receiver_table`
--
ALTER TABLE `im_email_receiver_table`
  ADD PRIMARY KEY (`idx`);

--
-- 테이블의 인덱스 `im_email_send_table`
--
ALTER TABLE `im_email_send_table`
  ADD PRIMARY KEY (`idx`);

--
-- 테이블의 인덱스 `im_forum_attachment_table`
--
ALTER TABLE `im_forum_attachment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `type` (`type`,`parent`);

--
-- 테이블의 인덱스 `im_forum_history_table`
--
ALTER TABLE `im_forum_history_table`
  ADD PRIMARY KEY (`type`,`parent`,`action`,`midx`);

--
-- 테이블의 인덱스 `im_forum_label_table`
--
ALTER TABLE `im_forum_label_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `fid` (`fid`);

--
-- 테이블의 인덱스 `im_forum_ment_depth_table`
--
ALTER TABLE `im_forum_ment_depth_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `parent` (`parent`),
  ADD KEY `head` (`head`),
  ADD KEY `source` (`source`);

--
-- 테이블의 인덱스 `im_forum_ment_table`
--
ALTER TABLE `im_forum_ment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `fid` (`fid`),
  ADD KEY `parent` (`parent`),
  ADD FULLTEXT KEY `search` (`search`);

--
-- 테이블의 인덱스 `im_forum_post_label_table`
--
ALTER TABLE `im_forum_post_label_table`
  ADD PRIMARY KEY (`idx`,`label`),
  ADD KEY `idx` (`idx`),
  ADD KEY `label` (`label`);

--
-- 테이블의 인덱스 `im_forum_post_table`
--
ALTER TABLE `im_forum_post_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `fid` (`fid`),
  ADD KEY `midx` (`midx`),
  ADD KEY `last_ment` (`last_ment`),
  ADD FULLTEXT KEY `title` (`title`,`search`);

--
-- 테이블의 인덱스 `im_forum_table`
--
ALTER TABLE `im_forum_table`
  ADD PRIMARY KEY (`fid`);

--
-- 테이블의 인덱스 `im_keyword_table`
--
ALTER TABLE `im_keyword_table`
  ADD PRIMARY KEY (`keyword`),
  ADD KEY `keycode` (`keycode`,`engcode`);

--
-- 테이블의 인덱스 `im_lms_attachment_table`
--
ALTER TABLE `im_lms_attachment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `type` (`type`,`parent`);

--
-- 테이블의 인덱스 `im_lms_attend_table`
--
ALTER TABLE `im_lms_attend_table`
  ADD PRIMARY KEY (`midx`,`class`);

--
-- 테이블의 인덱스 `im_lms_class_label_table`
--
ALTER TABLE `im_lms_class_label_table`
  ADD PRIMARY KEY (`idx`,`label`),
  ADD KEY `idx` (`idx`),
  ADD KEY `label` (`label`);

--
-- 테이블의 인덱스 `im_lms_class_table`
--
ALTER TABLE `im_lms_class_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `midx` (`midx`),
  ADD KEY `last_ment` (`last_subject`),
  ADD KEY `lid` (`lid`),
  ADD FULLTEXT KEY `title` (`title`);

--
-- 테이블의 인덱스 `im_lms_label_table`
--
ALTER TABLE `im_lms_label_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `lid` (`lid`);

--
-- 테이블의 인덱스 `im_lms_ment_table`
--
ALTER TABLE `im_lms_ment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `parent` (`parent`),
  ADD KEY `lid` (`lid`);

--
-- 테이블의 인덱스 `im_lms_post_table`
--
ALTER TABLE `im_lms_post_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `midx` (`midx`),
  ADD KEY `lid` (`lid`),
  ADD FULLTEXT KEY `title` (`title`,`search`);

--
-- 테이블의 인덱스 `im_lms_subject_table`
--
ALTER TABLE `im_lms_subject_table`
  ADD PRIMARY KEY (`idx`);

--
-- 테이블의 인덱스 `im_lms_table`
--
ALTER TABLE `im_lms_table`
  ADD PRIMARY KEY (`lid`);

--
-- 테이블의 인덱스 `im_lms_tracking_table`
--
ALTER TABLE `im_lms_tracking_table`
  ADD PRIMARY KEY (`midx`,`pidx`);

--
-- 테이블의 인덱스 `im_member_activity_table`
--
ALTER TABLE `im_member_activity_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `midx` (`midx`);

--
-- 테이블의 인덱스 `im_member_email_table`
--
ALTER TABLE `im_member_email_table`
  ADD PRIMARY KEY (`midx`,`email`);

--
-- 테이블의 인덱스 `im_member_level_table`
--
ALTER TABLE `im_member_level_table`
  ADD PRIMARY KEY (`level`),
  ADD KEY `exp` (`exp`);

--
-- 테이블의 인덱스 `im_member_point_table`
--
ALTER TABLE `im_member_point_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `midx` (`midx`);

--
-- 테이블의 인덱스 `im_member_signup_table`
--
ALTER TABLE `im_member_signup_table`
  ADD PRIMARY KEY (`gidx`,`name`),
  ADD KEY `group` (`gidx`);

--
-- 테이블의 인덱스 `im_member_social_table`
--
ALTER TABLE `im_member_social_table`
  ADD PRIMARY KEY (`midx`,`code`),
  ADD KEY `code` (`code`,`user_id`);

--
-- 테이블의 인덱스 `im_member_table`
--
ALTER TABLE `im_member_table`
  ADD PRIMARY KEY (`idx`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 테이블의 인덱스 `im_minitalk_price_table`
--
ALTER TABLE `im_minitalk_price_table`
  ADD PRIMARY KEY (`usernum`);

--
-- 테이블의 인덱스 `im_minitalk_server_table`
--
ALTER TABLE `im_minitalk_server_table`
  ADD PRIMARY KEY (`idx`);

--
-- 테이블의 인덱스 `im_minitalk_service_hosting_table`
--
ALTER TABLE `im_minitalk_service_hosting_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `mno` (`midx`);

--
-- 테이블의 인덱스 `im_module_table`
--
ALTER TABLE `im_module_table`
  ADD PRIMARY KEY (`module`);

--
-- 테이블의 인덱스 `im_page_table`
--
ALTER TABLE `im_page_table`
  ADD PRIMARY KEY (`domain`,`language`,`menu`,`page`);

--
-- 테이블의 인덱스 `im_push_table`
--
ALTER TABLE `im_push_table`
  ADD PRIMARY KEY (`midx`,`module`,`code`,`fromcode`);

--
-- 테이블의 인덱스 `im_qna_attachment_table`
--
ALTER TABLE `im_qna_attachment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `type` (`type`,`parent`);

--
-- 테이블의 인덱스 `im_qna_history_table`
--
ALTER TABLE `im_qna_history_table`
  ADD PRIMARY KEY (`parent`,`midx`);

--
-- 테이블의 인덱스 `im_qna_label_table`
--
ALTER TABLE `im_qna_label_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `qid` (`qid`);

--
-- 테이블의 인덱스 `im_qna_ment_table`
--
ALTER TABLE `im_qna_ment_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `bid` (`qid`),
  ADD KEY `parent` (`parent`);

--
-- 테이블의 인덱스 `im_qna_post_label_table`
--
ALTER TABLE `im_qna_post_label_table`
  ADD PRIMARY KEY (`idx`,`label`),
  ADD KEY `idx` (`idx`),
  ADD KEY `label` (`label`);

--
-- 테이블의 인덱스 `im_qna_post_table`
--
ALTER TABLE `im_qna_post_table`
  ADD PRIMARY KEY (`idx`),
  ADD KEY `qid` (`qid`,`type`),
  ADD KEY `parent` (`parent`),
  ADD KEY `midx` (`midx`),
  ADD FULLTEXT KEY `title` (`title`,`search`);

--
-- 테이블의 인덱스 `im_qna_table`
--
ALTER TABLE `im_qna_table`
  ADD PRIMARY KEY (`qid`);

--
-- 테이블의 인덱스 `im_site_table`
--
ALTER TABLE `im_site_table`
  ADD PRIMARY KEY (`domain`,`language`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `im_apidocument_post_table`
--
ALTER TABLE `im_apidocument_post_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_apidocument_post_version_table`
--
ALTER TABLE `im_apidocument_post_version_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_attachment_table`
--
ALTER TABLE `im_attachment_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_board_category_table`
--
ALTER TABLE `im_board_category_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_board_history_table`
--
ALTER TABLE `im_board_history_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_board_ment_table`
--
ALTER TABLE `im_board_ment_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_board_post_table`
--
ALTER TABLE `im_board_post_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_dataroom_answer_table`
--
ALTER TABLE `im_dataroom_answer_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_dataroom_category_table`
--
ALTER TABLE `im_dataroom_category_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_dataroom_ment_table`
--
ALTER TABLE `im_dataroom_ment_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_dataroom_post_table`
--
ALTER TABLE `im_dataroom_post_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_dataroom_post_version_table`
--
ALTER TABLE `im_dataroom_post_version_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_dataroom_question_table`
--
ALTER TABLE `im_dataroom_question_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_donation_list_table`
--
ALTER TABLE `im_donation_list_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_email_receiver_table`
--
ALTER TABLE `im_email_receiver_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_email_send_table`
--
ALTER TABLE `im_email_send_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_forum_label_table`
--
ALTER TABLE `im_forum_label_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_forum_ment_table`
--
ALTER TABLE `im_forum_ment_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_forum_post_table`
--
ALTER TABLE `im_forum_post_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_lms_class_table`
--
ALTER TABLE `im_lms_class_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_lms_label_table`
--
ALTER TABLE `im_lms_label_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_lms_ment_table`
--
ALTER TABLE `im_lms_ment_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_lms_post_table`
--
ALTER TABLE `im_lms_post_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_lms_subject_table`
--
ALTER TABLE `im_lms_subject_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_member_activity_table`
--
ALTER TABLE `im_member_activity_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_member_point_table`
--
ALTER TABLE `im_member_point_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_member_table`
--
ALTER TABLE `im_member_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_minitalk_server_table`
--
ALTER TABLE `im_minitalk_server_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_minitalk_service_hosting_table`
--
ALTER TABLE `im_minitalk_service_hosting_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_qna_label_table`
--
ALTER TABLE `im_qna_label_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_qna_ment_table`
--
ALTER TABLE `im_qna_ment_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `im_qna_post_table`
--
ALTER TABLE `im_qna_post_table`
  MODIFY `idx` int(11) NOT NULL AUTO_INCREMENT;