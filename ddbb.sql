-- phpMyAdmin SQL Dump
-- version 2.6.4-pl3
-- http://www.phpmyadmin.net
-- 
-- Servidor: db392308074.db.1and1.com
-- Tiempo de generación: 10-10-2012 a las 12:05:56
-- Versión del servidor: 5.0.95
-- Versión de PHP: 5.3.3-7+squeeze14
-- 
-- Base de datos: `db392308074`
-- 

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `photos`
-- 

CREATE TABLE `photos` (
  `id` bigint(20) NOT NULL auto_increment,
  `photo_id` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `low` varchar(255) NOT NULL,
  `caption` mediumtext,
  `filter` varchar(255) default NULL,
  `link` varchar(255) NOT NULL,
  `latitude` varchar(50) default NULL,
  `longitude` varchar(50) default NULL,
  `user_id` bigint(20) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Estructura de tabla para la tabla `users`
-- 

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL auto_increment,
  `email` varchar(255) NOT NULL,
  `instagram_id` bigint(20) NOT NULL,
  `instagram_user` varchar(255) NOT NULL,
  `profile_picture` varchar(255) default NULL,
  `bio` mediumtext,
  `website` varchar(255) default NULL,
  `public` tinyint(1) NOT NULL default '0',
  `date` datetime NOT NULL,
  `access_token` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;
