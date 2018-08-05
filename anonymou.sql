-- phpMyAdmin SQL Dump
-- version 4.4.15.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2017-10-09 00:00:27
-- 服务器版本： 5.7.11-log
-- PHP Version: 5.6.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anonymou`
--

-- --------------------------------------------------------

--
-- 表的结构 `anony_comment`
--

CREATE TABLE IF NOT EXISTS `anony_comment` (
  `cid` bigint(20) NOT NULL,
  `openid` varchar(35) NOT NULL,
  `tid` int(15) NOT NULL,
  `cretime` int(11) NOT NULL COMMENT '创建时间',
  `content` text NOT NULL COMMENT '评论内容',
  `display` enum('1','2') NOT NULL DEFAULT '1' COMMENT '该评论是否显示，1：显示，2：不显示'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表';

-- --------------------------------------------------------

--
-- 表的结构 `anony_conf`
--

CREATE TABLE IF NOT EXISTS `anony_conf` (
  `mediaid` varchar(35) NOT NULL,
  `name` varchar(20) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `anony_media`
--

CREATE TABLE IF NOT EXISTS `anony_media` (
  `mediaid` varchar(32) NOT NULL,
  `apikey` varchar(127) NOT NULL,
  `switch` int(2) NOT NULL DEFAULT '4' COMMENT '默认为4，表示应用匿名评价',
  `schoolname` varchar(35) NOT NULL COMMENT '学校名称',
  `keywords` text COMMENT '公众号对应应用的关键字集合，为空则表示对应应用无须记录关键字'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='media表';

-- --------------------------------------------------------

--
-- 表的结构 `anony_score`
--

CREATE TABLE IF NOT EXISTS `anony_score` (
  `id` bigint(20) NOT NULL,
  `tid` int(15) NOT NULL COMMENT '该用户打分的教师id',
  `openid` varchar(35) NOT NULL,
  `addtime` int(10) NOT NULL COMMENT '添加时间',
  `quality` int(3) NOT NULL COMMENT '讲课质量',
  `responsible` int(3) NOT NULL COMMENT '负责人程度',
  `pass` int(3) NOT NULL COMMENT '课程通过率'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `anony_teacher`
--

CREATE TABLE IF NOT EXISTS `anony_teacher` (
  `id` int(15) NOT NULL,
  `mediaid` char(35) NOT NULL COMMENT '该教师添加的公众号',
  `name` char(32) NOT NULL COMMENT '教师姓名',
  `detail` varchar(256) NOT NULL COMMENT '教师详细信息',
  `lessons` text NOT NULL COMMENT '教师课程',
  `quality` float NOT NULL DEFAULT '80' COMMENT '该教师教学质量分',
  `responsible` float NOT NULL DEFAULT '80' COMMENT '该教师负责人程度分',
  `pass` float NOT NULL DEFAULT '80' COMMENT '该教师课程通过率分数',
  `subtime` int(11) NOT NULL COMMENT '提交时间',
  `headimg` varchar(64) NOT NULL DEFAULT '1' COMMENT '教师头像，可能为用户上传时存储的七牛云hashkey',
  `show` int(2) NOT NULL DEFAULT '2' COMMENT '是否显示，1：表示显示；2：表示新增审核；3：表示修改审核',
  `comcount` int(11) NOT NULL DEFAULT '0' COMMENT '该教师评论数'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='教师信息表';

-- --------------------------------------------------------

--
-- 表的结构 `anony_user`
--

CREATE TABLE IF NOT EXISTS `anony_user` (
  `openid` varchar(35) NOT NULL,
  `unionid` varchar(35) DEFAULT NULL,
  `headimgurl` varchar(256) NOT NULL,
  `nickname` varchar(32) NOT NULL,
  `accesstoken` varchar(60) DEFAULT NULL COMMENT '用户身份标记，使用openid加密得到',
  `overtime` int(11) NOT NULL DEFAULT '0' COMMENT '该accesstoken超时时间',
  `addtime` int(11) NOT NULL COMMENT '新建用户时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='匿名评教用户表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anony_comment`
--
ALTER TABLE `anony_comment`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `detail_page_comment` (`cretime`,`openid`,`tid`,`display`);

--
-- Indexes for table `anony_conf`
--
ALTER TABLE `anony_conf`
  ADD PRIMARY KEY (`mediaid`,`name`);

--
-- Indexes for table `anony_media`
--
ALTER TABLE `anony_media`
  ADD PRIMARY KEY (`mediaid`,`apikey`);

--
-- Indexes for table `anony_score`
--
ALTER TABLE `anony_score`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `anony_teacher`
--
ALTER TABLE `anony_teacher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `main_page_show` (`comcount`,`mediaid`,`show`),
  ADD KEY `admin_page_teacher` (`subtime`,`mediaid`,`show`);

--
-- Indexes for table `anony_user`
--
ALTER TABLE `anony_user`
  ADD PRIMARY KEY (`openid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anony_comment`
--
ALTER TABLE `anony_comment`
  MODIFY `cid` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `anony_score`
--
ALTER TABLE `anony_score`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `anony_teacher`
--
ALTER TABLE `anony_teacher`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
