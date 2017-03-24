/*
Navicat MySQL Data Transfer

Source Server         : una
Source Server Version : 50527
Source Host           : localhost:3306
Source Database       : cca

Target Server Type    : MYSQL
Target Server Version : 50527
File Encoding         : 65001

Date: 2017-01-11 22:04:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `computer_room`
-- ----------------------------
DROP TABLE IF EXISTS `computer_room`;
CREATE TABLE `computer_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_year` int(11) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `room_num` varchar(255) DEFAULT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `pc_num` int(11) DEFAULT NULL,
  `sever_num` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of computer_room
-- ----------------------------
INSERT INTO `computer_room` VALUES ('1', '105', '1', '資訊傳播系', 'I3501', '數位媒體傳播實驗室', '31', '1');
INSERT INTO `computer_room` VALUES ('2', '105', '1', '資訊傳播系', 'I3502', '多媒體設計實驗室', '31', '1');
INSERT INTO `computer_room` VALUES ('3', '105', '1', '資訊傳播系', 'I4502', '互動式數位學習系統', '61', '1');
INSERT INTO `computer_room` VALUES ('4', '105', '1', '資訊傳播系', 'I5504', '專業證照輔導實習室', '54', '1');
INSERT INTO `computer_room` VALUES ('5', '105', '1', '機械工程系', 'E1502', '機電與儀控整合實驗室', '60', '0');
INSERT INTO `computer_room` VALUES ('6', '105', '1', '機械工程系', 'E3405', '電腦輔助設計室', '50', '0');
INSERT INTO `computer_room` VALUES ('7', '105', '1', '資訊管理系', 'I4202', '電腦教室(一)', '60', '0');
INSERT INTO `computer_room` VALUES ('8', '105', '1', '資訊管理系', 'I5202', '電腦教室(二)', '60', '0');
INSERT INTO `computer_room` VALUES ('9', '105', '1', '資訊管理系', 'I3202', '電子商務實務實驗室', '40', '1');
INSERT INTO `computer_room` VALUES ('10', '105', '1', '資訊管理系', 'I2205', '專題實驗室', '10', '0');
INSERT INTO `computer_room` VALUES ('11', '105', '1', '資訊管理系', 'I2211', 'ERP系統專業教室', '47', '1');
INSERT INTO `computer_room` VALUES ('14', '105', '1', '電腦與通訊系', 'I2603', '電子電路實習室', '31', '0');
INSERT INTO `computer_room` VALUES ('15', '105', '1', '電腦與通訊系', 'I3401', '電腦實習室', '60', '0');
INSERT INTO `computer_room` VALUES ('16', '105', '1', '電腦與通訊系', 'I4402', '網路模擬實習室', '60', '0');
INSERT INTO `computer_room` VALUES ('17', '105', '1', '電腦與通訊系', 'I5402', '通訊模擬實習室', '31', '0');
INSERT INTO `computer_room` VALUES ('18', '105', '1', '電腦與通訊系', 'I3402', '多媒體通訊實習室', '30', '1');
INSERT INTO `computer_room` VALUES ('19', '105', '1', '電機工程系', 'E1608', '電腦教室', '60', '0');
INSERT INTO `computer_room` VALUES ('20', '105', '1', '電機工程系', 'E1602', '自動控制實習室', '26', '0');
INSERT INTO `computer_room` VALUES ('21', '105', '1', '電機工程系', 'E1604', '基礎電學實習室', '26', '0');
INSERT INTO `computer_room` VALUES ('22', '105', '1', '電機工程系', 'E1605', '電動機控制實習室', '12', '0');
INSERT INTO `computer_room` VALUES ('23', '105', '1', '電機工程系', 'E1606', '電力電子實習室', '26', '0');
INSERT INTO `computer_room` VALUES ('24', '105', '1', '電機工程系', 'E1607', '可程式控制實習室', '28', '0');
INSERT INTO `computer_room` VALUES ('25', '105', '1', '電機工程系', 'E1609', '電機機械實習室', '32', '0');
INSERT INTO `computer_room` VALUES ('26', '105', '1', '電機工程系', 'E1611', '影像與伺服器控制實習室', '25', '0');
INSERT INTO `computer_room` VALUES ('27', '105', '1', '財務金融系', 'B3603', '電腦教室(二)', '60', '0');
INSERT INTO `computer_room` VALUES ('28', '105', '1', '企業管理系', 'B3602', '', '50', '0');
INSERT INTO `computer_room` VALUES ('29', '105', '1', '企業管理系', 'B3502', '企業經營體驗室', '28', '0');

-- ----------------------------
-- Table structure for `computer_room_os`
-- ----------------------------
DROP TABLE IF EXISTS `computer_room_os`;
CREATE TABLE `computer_room_os` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `computer_room_id` int(11) DEFAULT NULL,
  `os_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of computer_room_os
-- ----------------------------
INSERT INTO `computer_room_os` VALUES ('1', '1', '1');
INSERT INTO `computer_room_os` VALUES ('2', '1', '2');
INSERT INTO `computer_room_os` VALUES ('3', '2', '1');
INSERT INTO `computer_room_os` VALUES ('4', '2', '2');
INSERT INTO `computer_room_os` VALUES ('5', '3', '1');
INSERT INTO `computer_room_os` VALUES ('6', '3', '2');
INSERT INTO `computer_room_os` VALUES ('7', '4', '1');
INSERT INTO `computer_room_os` VALUES ('8', '4', '2');
INSERT INTO `computer_room_os` VALUES ('9', '5', '2');
INSERT INTO `computer_room_os` VALUES ('10', '6', '3');
INSERT INTO `computer_room_os` VALUES ('11', '7', '3');
INSERT INTO `computer_room_os` VALUES ('12', '8', '3');
INSERT INTO `computer_room_os` VALUES ('13', '9', '3');
INSERT INTO `computer_room_os` VALUES ('14', '10', '3');
INSERT INTO `computer_room_os` VALUES ('15', '11', '3');
INSERT INTO `computer_room_os` VALUES ('16', '14', '3');
INSERT INTO `computer_room_os` VALUES ('17', '15', '1');
INSERT INTO `computer_room_os` VALUES ('18', '15', '2');
INSERT INTO `computer_room_os` VALUES ('19', '16', '3');
INSERT INTO `computer_room_os` VALUES ('20', '17', '3');
INSERT INTO `computer_room_os` VALUES ('21', '18', '4');
INSERT INTO `computer_room_os` VALUES ('22', '18', '3');
INSERT INTO `computer_room_os` VALUES ('23', '19', '5');
INSERT INTO `computer_room_os` VALUES ('24', '20', '3');
INSERT INTO `computer_room_os` VALUES ('25', '21', '2');
INSERT INTO `computer_room_os` VALUES ('26', '22', '3');
INSERT INTO `computer_room_os` VALUES ('27', '23', '3');
INSERT INTO `computer_room_os` VALUES ('28', '24', '3');
INSERT INTO `computer_room_os` VALUES ('29', '25', '3');
INSERT INTO `computer_room_os` VALUES ('30', '26', '2');
INSERT INTO `computer_room_os` VALUES ('31', '27', '2');
INSERT INTO `computer_room_os` VALUES ('32', '28', '3');
INSERT INTO `computer_room_os` VALUES ('33', '29', '2');

-- ----------------------------
-- Table structure for `computer_room_software`
-- ----------------------------
DROP TABLE IF EXISTS `computer_room_software`;
CREATE TABLE `computer_room_software` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `computer_room_id` int(11) DEFAULT NULL,
  `software_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of computer_room_software
-- ----------------------------
INSERT INTO `computer_room_software` VALUES ('1', '1', '1');
INSERT INTO `computer_room_software` VALUES ('2', '1', '2');
INSERT INTO `computer_room_software` VALUES ('3', '1', '3');
INSERT INTO `computer_room_software` VALUES ('4', '1', '4');
INSERT INTO `computer_room_software` VALUES ('5', '1', '5');
INSERT INTO `computer_room_software` VALUES ('6', '2', '1');
INSERT INTO `computer_room_software` VALUES ('7', '2', '2');
INSERT INTO `computer_room_software` VALUES ('8', '2', '3');
INSERT INTO `computer_room_software` VALUES ('9', '2', '4');
INSERT INTO `computer_room_software` VALUES ('10', '2', '5');
INSERT INTO `computer_room_software` VALUES ('11', '3', '1');
INSERT INTO `computer_room_software` VALUES ('12', '3', '2');
INSERT INTO `computer_room_software` VALUES ('13', '3', '3');
INSERT INTO `computer_room_software` VALUES ('14', '3', '4');
INSERT INTO `computer_room_software` VALUES ('15', '3', '5');
INSERT INTO `computer_room_software` VALUES ('16', '4', '1');
INSERT INTO `computer_room_software` VALUES ('17', '4', '2');
INSERT INTO `computer_room_software` VALUES ('18', '4', '3');
INSERT INTO `computer_room_software` VALUES ('19', '4', '4');
INSERT INTO `computer_room_software` VALUES ('20', '4', '5');
INSERT INTO `computer_room_software` VALUES ('21', '7', '1');
INSERT INTO `computer_room_software` VALUES ('22', '7', '2');
INSERT INTO `computer_room_software` VALUES ('23', '7', '4');
INSERT INTO `computer_room_software` VALUES ('24', '7', '5');
INSERT INTO `computer_room_software` VALUES ('25', '8', '1');
INSERT INTO `computer_room_software` VALUES ('26', '8', '2');
INSERT INTO `computer_room_software` VALUES ('27', '8', '4');
INSERT INTO `computer_room_software` VALUES ('28', '9', '1');
INSERT INTO `computer_room_software` VALUES ('29', '10', '1');
INSERT INTO `computer_room_software` VALUES ('30', '11', '1');
INSERT INTO `computer_room_software` VALUES ('31', '11', '2');
INSERT INTO `computer_room_software` VALUES ('32', '14', '1');
INSERT INTO `computer_room_software` VALUES ('33', '14', '8');
INSERT INTO `computer_room_software` VALUES ('34', '14', '9');
INSERT INTO `computer_room_software` VALUES ('35', '15', '1');
INSERT INTO `computer_room_software` VALUES ('36', '15', '9');
INSERT INTO `computer_room_software` VALUES ('37', '15', '10');
INSERT INTO `computer_room_software` VALUES ('38', '16', '1');
INSERT INTO `computer_room_software` VALUES ('39', '16', '9');
INSERT INTO `computer_room_software` VALUES ('40', '17', '1');
INSERT INTO `computer_room_software` VALUES ('41', '17', '9');
INSERT INTO `computer_room_software` VALUES ('42', '18', '1');
INSERT INTO `computer_room_software` VALUES ('43', '18', '9');
INSERT INTO `computer_room_software` VALUES ('44', '18', '11');
INSERT INTO `computer_room_software` VALUES ('45', '18', '12');
INSERT INTO `computer_room_software` VALUES ('46', '19', '13');
INSERT INTO `computer_room_software` VALUES ('47', '20', '13');
INSERT INTO `computer_room_software` VALUES ('48', '20', '14');
INSERT INTO `computer_room_software` VALUES ('49', '22', '14');
INSERT INTO `computer_room_software` VALUES ('50', '23', '15');
INSERT INTO `computer_room_software` VALUES ('51', '24', '11');
INSERT INTO `computer_room_software` VALUES ('52', '24', '16');
INSERT INTO `computer_room_software` VALUES ('53', '26', '11');
INSERT INTO `computer_room_software` VALUES ('54', '26', '13');
INSERT INTO `computer_room_software` VALUES ('55', '26', '18');
INSERT INTO `computer_room_software` VALUES ('56', '27', '1');
INSERT INTO `computer_room_software` VALUES ('57', '27', '2');
INSERT INTO `computer_room_software` VALUES ('58', '27', '20');
INSERT INTO `computer_room_software` VALUES ('59', '27', '21');
INSERT INTO `computer_room_software` VALUES ('60', '27', '22');
INSERT INTO `computer_room_software` VALUES ('61', '28', '1');
INSERT INTO `computer_room_software` VALUES ('62', '28', '22');
INSERT INTO `computer_room_software` VALUES ('63', '29', '22');
INSERT INTO `computer_room_software` VALUES ('64', '29', '23');
INSERT INTO `computer_room_software` VALUES ('65', '29', '24');
INSERT INTO `computer_room_software` VALUES ('66', '29', '25');
INSERT INTO `computer_room_software` VALUES ('67', '29', '26');

-- ----------------------------
-- Table structure for `lecture`
-- ----------------------------
DROP TABLE IF EXISTS `lecture`;
CREATE TABLE `lecture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `computer_room_id` int(11) DEFAULT NULL,
  `lesson_name` varchar(255) DEFAULT NULL,
  `student_num` int(11) DEFAULT NULL,
  `hour` int(11) DEFAULT NULL,
  `teacher_name` varchar(255) DEFAULT NULL,
  `lecture_name` varchar(255) DEFAULT NULL,
  `school_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lecture
-- ----------------------------
INSERT INTO `lecture` VALUES ('1', '1', '四資傳一A', '50', '3', '柯玲琴', '資訊傳播導論', '4');
INSERT INTO `lecture` VALUES ('2', '1', '四資傳一A', '50', '3', '王美金', '文創產業與數位內容應用實務', '4');
INSERT INTO `lecture` VALUES ('3', '1', '四資傳二A', '45', '3', '蔡哲民', '互動多媒體程式設計', '4');
INSERT INTO `lecture` VALUES ('4', '1', '四資傳二A', '45', '1', '蔡哲民', '班會', '4');
INSERT INTO `lecture` VALUES ('5', '1', '四資傳二A', '45', '3', '熊亮原', '數位影像圖文編輯排版', '4');
INSERT INTO `lecture` VALUES ('6', '1', '四資傳二A', '45', '3', '陳儒毅', '創意設計與思考技巧', '4');
INSERT INTO `lecture` VALUES ('7', '1', '四資傳三A', '44', '3', '張世熙', '3D電腦動畫', '4');
INSERT INTO `lecture` VALUES ('8', '1', '四資傳三A', '44', '3', '許季秦', '多媒體企劃專案撰寫', '4');
INSERT INTO `lecture` VALUES ('9', '1', '夜四資傳二A', '29', '3', '楊智彰', '繪圖實務', 'A');
INSERT INTO `lecture` VALUES ('10', '1', '夜四資傳二A', '29', '3', '陳淵琮', '動態網頁設計', 'A');
INSERT INTO `lecture` VALUES ('11', '1', '夜四資傳二A', '29', '3', '董德明', '基礎攝影與美學賞析', 'A');
INSERT INTO `lecture` VALUES ('12', '1', '夜四資傳二A', '29', '3', '張世熙', '創意引導與設計', 'A');
INSERT INTO `lecture` VALUES ('13', '1', '夜四資傳三A', '10', '4', '王美金', '多媒體企劃與製作', 'A');
INSERT INTO `lecture` VALUES ('14', '2', '四資傳四A', '43', '3', '蔡德明', '專題製作(二)', '4');
INSERT INTO `lecture` VALUES ('15', '2', '四資傳四A', '43', '1', '蔡德明', '班會', '4');
INSERT INTO `lecture` VALUES ('16', '2', '四資傳四A', '43', '3', '陳彥碩', '進階影像處理與製作', '4');
INSERT INTO `lecture` VALUES ('17', '2', '夜四資傳三A', '10', '3', '王譔博', '虛擬實境原理與應用', 'A');
INSERT INTO `lecture` VALUES ('18', '2', '夜四資傳三A', '10', '3', '王譔博', '人機介面', 'A');
INSERT INTO `lecture` VALUES ('19', '2', '夜四資傳三A', '10', '3', '蔡德明', '伺服器建置實務', 'A');
INSERT INTO `lecture` VALUES ('20', '2', '夜四資傳三A', '10', '3', '熊效儀', '編排設計', 'A');
INSERT INTO `lecture` VALUES ('21', '2', '夜四資傳四A', '21', '3', '柯玲琴', '數位學習概論', 'A');
INSERT INTO `lecture` VALUES ('22', '3', '四資傳一A', '50', '3', '王美金', '多媒體設計概論', '4');
INSERT INTO `lecture` VALUES ('23', '3', '四資傳一A', '50', '1', '王美金', '班會', '4');
INSERT INTO `lecture` VALUES ('24', '3', '四資傳一A', '50', '3', '曾惠青', '素描與設計技法', '4');
INSERT INTO `lecture` VALUES ('25', '3', '四資傳一A', '50', '4', '蔡哲民', '程式設計(一)', '4');
INSERT INTO `lecture` VALUES ('26', '3', '四資傳二A', '45', '3', '陳淵琮', '數位影音製作實務', '4');
INSERT INTO `lecture` VALUES ('27', '3', '四資傳二A', '45', '3', '蔡德明', '網站規劃與設計', '4');
INSERT INTO `lecture` VALUES ('28', '3', '四資傳四A', '43', '3', '陳淵琮', '網站程式設計', '4');
INSERT INTO `lecture` VALUES ('29', '3', '四資傳四A', '43', '3', '柯玲琴', '行動商務與行銷', '4');
INSERT INTO `lecture` VALUES ('30', '3', '夜四資傳一A', '15', '3', '許建平', '電腦與網路概論', 'A');
INSERT INTO `lecture` VALUES ('31', '3', '夜四資傳四A', '21', '3', '陳士達', '數位印刷與資料科技整合', 'A');
INSERT INTO `lecture` VALUES ('32', '4', '四資傳三A', '44', '3', '陳淵琮', '虛擬實境原理應用', '4');
INSERT INTO `lecture` VALUES ('33', '4', '四資傳三A', '44', '1', '陳淵琮', '班會', '4');
INSERT INTO `lecture` VALUES ('34', '4', '四資傳三A', '44', '3', '蔡德明', '雲端運算運用實務', '4');
INSERT INTO `lecture` VALUES ('35', '4', '四資傳三A', '44', '3', '蔡哲民', '網路資料庫系統', '4');
INSERT INTO `lecture` VALUES ('36', '4', '四資傳三A', '44', '3', '陳士達', '印刷生產力與資訊科技整合', '4');
INSERT INTO `lecture` VALUES ('37', '4', '夜四資傳一A', '15', '3', '柯玲琴', '資訊傳播科技導論', 'A');
INSERT INTO `lecture` VALUES ('38', '4', '夜四資傳一A', '15', '3', '楊智彰', '基礎統計應用', 'A');
INSERT INTO `lecture` VALUES ('39', '4', '夜四資傳一A', '15', '3', '王美金', '多媒體設計概論', 'A');
INSERT INTO `lecture` VALUES ('40', '4', '夜四資傳二A', '29', '4', '許季秦', '數位影音製作實務', 'A');
INSERT INTO `lecture` VALUES ('41', '5', '四機械一C', '50', '4', '朱紹舒', '計算機程式', '4');
INSERT INTO `lecture` VALUES ('42', '5', '四機械一E(外籍生)', '17', '4', '朱紹舒', '計算機程式', '4');
INSERT INTO `lecture` VALUES ('43', '5', '四機汽三A', '50', '3', '鄒忠全', '機電儀控整合及實習', '4');
INSERT INTO `lecture` VALUES ('44', '5', '四機械三F(雙軌班)', '12', '3', '王文榮', 'ANSYS有限元分析及實習', '4');
INSERT INTO `lecture` VALUES ('45', '5', '四機汽四A', '20', '4', '吳向宸', '微處理機應用與實習', '4');
INSERT INTO `lecture` VALUES ('46', '5', '四機械四B', '39', '3', '李浩榕', '機電儀控整合及實習', '4');
INSERT INTO `lecture` VALUES ('47', '5', '四機械四C', '44', '3', '李浩榕', '機電儀控整合及實習', '4');
INSERT INTO `lecture` VALUES ('48', '5', '四機械四D', '39', '3', '王文榮', '機電儀控整合及實習', '4');
INSERT INTO `lecture` VALUES ('49', '5', '四機械四E', '4', '3', '黃有弟', '工業產品設計', '4');
INSERT INTO `lecture` VALUES ('50', '5', '四機械二A', '55', '3', '廖培凱', '計算機程式', '4');
INSERT INTO `lecture` VALUES ('51', '5', '四機械四C', '33', '3', '鄒忠全', '機電儀控整合及實習', '4');
INSERT INTO `lecture` VALUES ('52', '5', '四機械四C', '33', '3', '吳向宸', '微處理機應用與實習', '4');
INSERT INTO `lecture` VALUES ('53', '6', '四機汽一A', '63', '4', '鄒忠全', '計算機程式', '4');
INSERT INTO `lecture` VALUES ('54', '6', '四機械一B', '50', '4', '廖培凱', '計算機程式', '4');
INSERT INTO `lecture` VALUES ('55', '6', '四機械一D', '50', '4', '陳維仁', '計算機程式', '4');
INSERT INTO `lecture` VALUES ('56', '6', '四機械二B', '51', '3', '陳維仁', '3D電腦繪圖實習', '4');
INSERT INTO `lecture` VALUES ('57', '6', '四機械二C', '50', '3', '李浩榕', '電腦輔助繪圖', '4');
INSERT INTO `lecture` VALUES ('58', '6', '四機械三B', '52', '3', '王松浩', '進階數控加工與實習(二)', '4');
INSERT INTO `lecture` VALUES ('59', '6', '四機械三C', '51', '3', '徐榮昌', '數值分析及實習', '4');
INSERT INTO `lecture` VALUES ('60', '6', '四機械三D', '40', '4', '孫書煌', '電腦輔助設計及實習', '4');
INSERT INTO `lecture` VALUES ('61', '6', '四機械三E', '5', '4', '王松浩', '電腦輔助設計與製造', '4');
INSERT INTO `lecture` VALUES ('62', '6', '四材料三A', '38', '3', '陳維仁', '電腦圖學', '4');
INSERT INTO `lecture` VALUES ('63', '6', '四機械二C', '40', '3', '洪興林', '計算機程式', '4');
INSERT INTO `lecture` VALUES ('64', '7', '四資管一A', '40', '3', '林文輝', '電子書製作', '4');
INSERT INTO `lecture` VALUES ('65', '7', '四資管一B', '40', '3', '沈英謀', '基礎程式設計', '4');
INSERT INTO `lecture` VALUES ('66', '7', '四資管一B', '40', '3', '林文輝', '電子書製作', '4');
INSERT INTO `lecture` VALUES ('67', '7', '四資管三A', '45', '3', '林孝忠', '雲端伺服器建置與管理', '4');
INSERT INTO `lecture` VALUES ('68', '7', '四資管三A', '45', '3', '曾生元', '3D互動多媒體', '4');
INSERT INTO `lecture` VALUES ('69', '7', '四資管四B', '43', '3', '林孝忠', '智慧物流與產銷數位化', '4');
INSERT INTO `lecture` VALUES ('70', '7', '夜四資管二A', '36', '3', '林文輝', '電子商務', 'A');
INSERT INTO `lecture` VALUES ('71', '7', '夜四資管二A', '36', '3', '張玲華', '2D多媒體動畫', 'A');
INSERT INTO `lecture` VALUES ('72', '8', '四資管一A', '40', '3', '羅仕堂', '企劃書製作與簡報', '4');
INSERT INTO `lecture` VALUES ('73', '8', '四資管一B', '40', '3', '賴正文', '企劃書製作與簡報', '4');
INSERT INTO `lecture` VALUES ('74', '8', '四資管三A', '45', '3', '沈英謀', '伺服器網頁程式設計', '4');
INSERT INTO `lecture` VALUES ('75', '8', '四資管三A', '45', '3', '游峰碩', '系統分析與設計', '4');
INSERT INTO `lecture` VALUES ('76', '8', '四資管三B', '43', '3', '游峰碩', '系統分析與設計', '4');
INSERT INTO `lecture` VALUES ('77', '8', '四資管四A', '47', '3', '林孝忠', '系統安全實務', '4');
INSERT INTO `lecture` VALUES ('78', '8', '在職碩專二', '12', '3', '洪俊銘', '網路技術專論', 'K');
INSERT INTO `lecture` VALUES ('79', '8', '在職碩專二', '12', '3', '許蕙纓', '網路行銷', 'K');
INSERT INTO `lecture` VALUES ('80', '8', '夜四資管一A', '38', '3', '羅仕堂', '企劃書製作與簡報', 'A');
INSERT INTO `lecture` VALUES ('81', '8', '夜四資管三A', '32', '3', '徐國鈞', '系統分析與設計', 'A');
INSERT INTO `lecture` VALUES ('82', '9', '四資管一A', '40', '3', '曾生元', '基礎程式設計', '4');
INSERT INTO `lecture` VALUES ('83', '9', '四資管二A', '49', '3', '曾生元', '動態網頁設計', '4');
INSERT INTO `lecture` VALUES ('84', '9', '夜四資管一A', '38', '3', '曾生元', '基礎程式設計', 'A');
INSERT INTO `lecture` VALUES ('85', '9', '夜四資管三A', '32', '3', '游峰碩', '手機應用程式', 'A');
INSERT INTO `lecture` VALUES ('86', '7', '專四資管一甲', '11', '3', '林孝忠', '網頁設計(併班)', 'E');
INSERT INTO `lecture` VALUES ('87', '7', '專四資管二甲', '8', '0', '林孝忠', '網頁設計(併班)', 'E');
INSERT INTO `lecture` VALUES ('88', '7', '專四資管三甲', '7', '3', '張玲華', '2D多媒體動畫(併班)', 'E');
INSERT INTO `lecture` VALUES ('89', '7', '專四資管四甲', '5', '0', '張玲華', '2D多媒體動畫(併班)', 'E');
INSERT INTO `lecture` VALUES ('90', '7', '專校資管一甲', '13', '0', '張玲華', '2D多媒體動畫(併班)', 'P');
INSERT INTO `lecture` VALUES ('91', '7', '專校資管二甲', '5', '0', '張玲華', '2D多媒體動畫(併班)', 'P');
INSERT INTO `lecture` VALUES ('92', '8', '二技資管三B', '15', '3', '沈英謀', '基礎程式設計(併班)', 'U');
INSERT INTO `lecture` VALUES ('93', '8', '二技資管四B', '11', '0', '沈英謀', '基礎程式設計(併班)', 'P');
INSERT INTO `lecture` VALUES ('94', '8', '專校四資管二甲', '8', '0', '沈英謀', '基礎程式設計(併班)', 'E');
INSERT INTO `lecture` VALUES ('95', '9', '專四資管一甲', '11', '3', '王意順', '企劃書製作與簡報(併班)', 'E');
INSERT INTO `lecture` VALUES ('96', '9', '專四資管四甲', '5', '0', '王意順', '企劃書製作與簡報(併班)', 'E');
INSERT INTO `lecture` VALUES ('97', '9', '專校資管二甲', '5', '0', '王意順', '企劃書製作與簡報(併班)', 'E');
INSERT INTO `lecture` VALUES ('98', '9', '資管碩一', '11', '3', '高淑珍', '研究方法', 'G');
INSERT INTO `lecture` VALUES ('99', '9', '資管碩一', '11', '3', '游峰碩', '程式樣式設計(併班)', 'G');
INSERT INTO `lecture` VALUES ('100', '9', '資管碩二', '11', '0', '游峰碩', '程式樣式設計(併班)', 'G');
INSERT INTO `lecture` VALUES ('101', '10', '在職碩專一', '11', '3', '徐國鈞', '物流與產銷', 'K');
INSERT INTO `lecture` VALUES ('102', '10', '在職碩專一', '11', '3', '柯玲琴', '商務經營與雲端整合', 'K');
INSERT INTO `lecture` VALUES ('103', '10', '在職碩專一', '11', '1', '', '學術倫理', 'K');
INSERT INTO `lecture` VALUES ('104', '10', '夜四資管四A', '20', '4', '游峰碩', '專題製作(二)', 'A');
INSERT INTO `lecture` VALUES ('105', '10', '資管碩一', '11', '2', '王平', '論文研討(一)', 'G');
INSERT INTO `lecture` VALUES ('106', '10', '資管碩一', '11', '3', '林文暉', '模式辨識', 'G');
INSERT INTO `lecture` VALUES ('107', '10', '資管碩一', '11', '3', '陳熙玫', '決策支援系統', 'G');
INSERT INTO `lecture` VALUES ('108', '10', '資管碩二', '11', '2', '王平', '論文研討(三)', 'G');
INSERT INTO `lecture` VALUES ('109', '10', '資管碩二', '11', '3', '吳國龍', '大數據分析', 'G');
INSERT INTO `lecture` VALUES ('110', '11', '夜四資管三A', '32', '3', '郭政翰', '網路行銷與企劃', 'A');
INSERT INTO `lecture` VALUES ('111', '11', '夜四資管三A', '32', '3', '賴正文', 'ERP配銷模組', 'A');
INSERT INTO `lecture` VALUES ('112', '11', '夜四資管三A', '32', '3', '陳智維', '簡報製作與表達', 'A');
INSERT INTO `lecture` VALUES ('113', '11', '四資管四B', '43', '3', '高淑珍', '資料探勘', '4');
INSERT INTO `lecture` VALUES ('114', '11', '四資管三B', '43', '3', '葉俊吾', 'ERP配銷模組', '4');
INSERT INTO `lecture` VALUES ('115', '11', '二技資管四B', '19', '3', '郭文真', 'ERP配銷模組', 'U');
INSERT INTO `lecture` VALUES ('116', '11', '四資管二A', '49', '3', '吳國龍', '統計學', '4');
INSERT INTO `lecture` VALUES ('117', '11', '四資管二B', '47', '3', '吳國龍', '統計學', '4');
INSERT INTO `lecture` VALUES ('118', '11', '二技資管三B', '15', '3', '許蕙纓', '網路行銷與企劃(併班)', 'U');
INSERT INTO `lecture` VALUES ('119', '11', '二技資管四B', '11', '0', '許蕙纓', '網路行銷與企劃(併班)', 'U');
INSERT INTO `lecture` VALUES ('120', '11', '專四資管一甲', '11', '0', '許蕙纓', '網路行銷與企劃(併班)', 'U');
INSERT INTO `lecture` VALUES ('121', '11', '專四資管二甲', '8', '0', '許蕙纓', '網路行銷與企劃(併班)', 'E');
INSERT INTO `lecture` VALUES ('122', '11', '專四資管一甲', '11', '3', '徐國鈞', '數位影片編輯(併班)', 'E');
INSERT INTO `lecture` VALUES ('123', '11', '專四資管二甲', '8', '0', '徐國鈞', '數位影片編輯(併班)', 'E');
INSERT INTO `lecture` VALUES ('124', '11', '專校資管一甲', '13', '0', '徐國鈞', '數位影片編輯(併班)', 'P');
INSERT INTO `lecture` VALUES ('125', '11', '專校資管二甲', '6', '0', '徐國鈞', '數位影片編輯(併班)', 'P');
INSERT INTO `lecture` VALUES ('126', '14', '四電通二A', '47', '4', '陳奉殷', '電子學暨實習(一)', '4');
INSERT INTO `lecture` VALUES ('127', '14', '電通碩一', '28', '3', '蔡崇洲', '天線設計實務', 'G');
INSERT INTO `lecture` VALUES ('128', '14', '四電通一B', '46', '4', '陳耀煌', '基本電學暨實習(一)', '4');
INSERT INTO `lecture` VALUES ('129', '14', '四電通一A', '36', '4', '吳宏偉', '基本電學暨實習(一)', '4');
INSERT INTO `lecture` VALUES ('130', '14', '四電通四B', '20', '3', '曾昭雄', '硬體描述語言', '4');
INSERT INTO `lecture` VALUES ('131', '14', '四電通二B', '54', '4', '陳添智', '電子學暨實習(一)', '4');
INSERT INTO `lecture` VALUES ('132', '15', '四電通四A', '52', '3', '程榮祥', '資料結構', '4');
INSERT INTO `lecture` VALUES ('133', '15', '四電通三B', '43', '4', '郭崇仁', '數位信號處理暨實習', '4');
INSERT INTO `lecture` VALUES ('134', '15', '四電通一B', '56', '4', '陳國泰', '程式設計(一)', '4');
INSERT INTO `lecture` VALUES ('135', '15', '四電通一A', '42', '4', '劉崇汎', '程式設計(一)', '4');
INSERT INTO `lecture` VALUES ('136', '15', '四電通一B', '35', '4', '郭晉魁', '網頁設計暨實習', '4');
INSERT INTO `lecture` VALUES ('137', '15', '四電通三A', '69', '3', '桂台麟', '網路資料庫設計', '4');
INSERT INTO `lecture` VALUES ('138', '15', '四電通三B', '63', '3', '郭晉魁', 'Linux作業系統', '4');
INSERT INTO `lecture` VALUES ('139', '18', '四電通二B', '53', '4', '吳崇民', '自動控制實務', '4');
INSERT INTO `lecture` VALUES ('140', '18', '四電通二A', '47', '4', '曾紹雄', '微處理機應用暨實習(一)', '4');
INSERT INTO `lecture` VALUES ('141', '18', '四電通一A', '34', '4', '陳奉殷', '邏輯設計暨實習', '4');
INSERT INTO `lecture` VALUES ('142', '18', '四電通四B', '34', '4', '吳崇民', '通訊微處理暨實習', '4');
INSERT INTO `lecture` VALUES ('143', '18', '四電通二B', '57', '4', '郭崇仁', '微處理機應用暨實習(一)', '4');
INSERT INTO `lecture` VALUES ('144', '18', '四電通一B', '33', '4', '林明權', '邏輯設計暨實習', '4');
INSERT INTO `lecture` VALUES ('145', '18', '四電通二A', '41', '4', '吳崇民', '自動控制實務', '4');
INSERT INTO `lecture` VALUES ('146', '18', '四電通三C', '11', '8', '吳崇民', '微處理機應用暨實習(一)', '4');
INSERT INTO `lecture` VALUES ('147', '16', '四電通三C', '11', '2', '王建仁', '創新與創意思考', '4');
INSERT INTO `lecture` VALUES ('148', '16', '四電通三A', '38', '4', '王建仁', '網路路由技術', '4');
INSERT INTO `lecture` VALUES ('149', '16', '四電通二A', '47', '4', '王建仁', '訊號與系統暨實習', '4');
INSERT INTO `lecture` VALUES ('150', '16', '四電通一A', '35', '4', '郭崇仁', '網頁設計暨實習', '4');
INSERT INTO `lecture` VALUES ('151', '16', '四電通四A', '60', '3', '許智威', '物聯網概論', '4');
INSERT INTO `lecture` VALUES ('152', '16', '四電通二A', '59', '4', '劉崇汎', '手機程式設計', '4');
INSERT INTO `lecture` VALUES ('153', '17', '四電通三A', '27', '4', '林志鴻', '數位通訊暨實習', '4');
INSERT INTO `lecture` VALUES ('154', '17', '四電通三B', '44', '3', '許智威', '收發機實務', '4');
INSERT INTO `lecture` VALUES ('155', '17', '電通碩一', '10', '3', '程榮祥', '網路模擬與效能計算', 'G');
INSERT INTO `lecture` VALUES ('156', '17', '四電通二B', '58', '4', '林志鴻', '訊號與系統暨實習', '4');
INSERT INTO `lecture` VALUES ('157', '17', '四電通三B', '42', '4', '林志鴻', '數位通訊暨實習', '4');
INSERT INTO `lecture` VALUES ('158', '17', '四電通三A', '60', '3', '曾昭雄', '數位影像處理', '4');
INSERT INTO `lecture` VALUES ('159', '27', '夜四財金一A', '30', '3', '陳哲斐', '商用套裝軟體', 'A');
INSERT INTO `lecture` VALUES ('160', '27', '四會資四A', '25', '2', '黃共惠', '電子商務', '4');

-- ----------------------------
-- Table structure for `os`
-- ----------------------------
DROP TABLE IF EXISTS `os`;
CREATE TABLE `os` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `valid` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of os
-- ----------------------------
INSERT INTO `os` VALUES ('1', 'Linux', null);
INSERT INTO `os` VALUES ('2', 'Windows 7', null);
INSERT INTO `os` VALUES ('3', 'Windows XP', null);
INSERT INTO `os` VALUES ('4', 'Windows Server 2003', null);
INSERT INTO `os` VALUES ('5', 'Windows 8.1', null);

-- ----------------------------
-- Table structure for `software`
-- ----------------------------
DROP TABLE IF EXISTS `software`;
CREATE TABLE `software` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of software
-- ----------------------------
INSERT INTO `software` VALUES ('1', 'Office 系列軟體');
INSERT INTO `software` VALUES ('2', 'Adobe 系列軟體');
INSERT INTO `software` VALUES ('3', 'Linux 自由軟體');
INSERT INTO `software` VALUES ('4', '程式編寫自由軟體');
INSERT INTO `software` VALUES ('5', '影音剪輯處理軟體');
INSERT INTO `software` VALUES ('6', 'Iclone');
INSERT INTO `software` VALUES ('7', 'Tomcat 7');
INSERT INTO `software` VALUES ('8', '電子電路模擬補局軟體');
INSERT INTO `software` VALUES ('9', 'Visual Studio');
INSERT INTO `software` VALUES ('10', '數位訊號系統應用軟體');
INSERT INTO `software` VALUES ('11', '圖控式軟體');
INSERT INTO `software` VALUES ('12', 'Microsoft SQL 2005');
INSERT INTO `software` VALUES ('13', 'C++ Visual Studuo');
INSERT INTO `software` VALUES ('14', 'MATLAB');
INSERT INTO `software` VALUES ('15', 'Ispice電路模擬軟體');
INSERT INTO `software` VALUES ('16', '可程式控制編輯軟體');
INSERT INTO `software` VALUES ('18', 'C8051編譯軟體');
INSERT INTO `software` VALUES ('20', 'TQC技能認證');
INSERT INTO `software` VALUES ('21', 'Work Flow ERP 系統');
INSERT INTO `software` VALUES ('22', 'SPSS 統計軟體');
INSERT INTO `software` VALUES ('23', '供應鏈實習軟體');
INSERT INTO `software` VALUES ('24', '企業模擬系統');
INSERT INTO `software` VALUES ('25', '零售專家教學與競賽軟體');
INSERT INTO `software` VALUES ('26', '流通大師模擬競賽軟體');
