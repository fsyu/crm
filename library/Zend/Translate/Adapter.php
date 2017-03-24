<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage Zend_Translate_Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Adapter.php 24593 2012-01-05 20:35:02Z matthew $
 */

/**
 * @see Zend_Locale
 */
require_once 'Zend/Locale.php';

/**
 * @see Zend_Translate_Plural
 */
require_once 'Zend/Translate/Plural.php';

/**
 * Basic adapter class for each translation source adapter
 *
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage Zend_Translate_Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Translate_Adapter {
    /**
     * Shows if locale detection is in automatic level
     * @var boolean
     */
    private $_automatic = true;

    /**
     * Internal value to see already routed languages
     * @var array()
     */
    private $_routed = array();

    /**
     * Internal cache for all adapters
     * @var Zend_Cache_Core
     */
    protected static $_cache     = null;

    /**
     * Internal value to remember if cache supports tags
     *
     * @var boolean
     */
    private static $_cacheTags = false;

    /**
     * Scans for the locale within the name of the directory
     * @constant integer
     */
    const LOCALE_DIRECTORY = 'directory';

    /**
     * Scans for the locale within the name of the file
     * @constant integer
     */
    const LOCALE_FILENAME  = 'filename';

    /**
     * Array with all options, each adapter can have own additional options
     *   'clear'           => when true, clears already loaded translations when adding new files
     *   'content'         => content to translate or file or directory with content
     *   'disableNotices'  => when true, omits notices from being displayed
     *   'ignore'          => a prefix for files and directories which are not being added
     *   'locale'          => the actual set locale to use
     *   'log'             => a instance of Zend_Log where logs are written to
     *   'logMessage'      => message to be logged
     *   'logPriority'     => priority which is used to write the log message
     *   'logUntranslated' => when true, untranslated messages are not logged
     *   'reload'          => reloads the cache by reading the content again
     *   'scan'            => searches for translation files using the LOCALE constants
     *   'tag'             => tag to use for the cache
     *
     * @var array
     */
    protected $_options = array(
        'clear'           => false,
        'content'         => null,
        'disableNotices'  => false,
        'ignore'          => '.',
        'locale'          => 'auto',
        'log'             => null,
        'logMessage'      => "Untranslated message within '%locale%': %message%",
        'logPriority'     => 5,
        'logUntranslated' => false,
        'reload'          => false,
        'route'           => null,
        'scan'            => null,
        'tag'             => 'Zend_Translate'
    );

    /**
     * Translation table
     * @var array
     */
    protected $_translate = array();

    /**
     * Generates the adapter
     *
     * @param  array|Zend_Config $options Translation options for this adapter
     * @throws Zend_Translate_Exception
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args               = func_get_args();
            $options            = array();
            $options['content'] = array_shift($args);

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt     = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } else if (!is_array($options)) {
            $options = array('content' => $options);
        }

        if (array_key_exists('cache', $options)) {
            self::setCache($options['cache']);
            unset($options['cache']);
        }

        if (isset(self::$_cache)) {
            $id = 'Zend_Translate_' . $this->toString() . '_Options';
            $result = self::$_cache->load($id);
            if ($result) {
                $this->_options = $result;
            }
        }

        if (empty($options['locale']) || ($options['locale'] === "auto")) {
            $this->_automatic = true;
        } else {
            $this->_automatic = false;
        }

        $locale = null;
        if (!empty($options['locale'])) {
            $locale = $options['locale'];
            unset($options['locale']);
        }

        $this->setOptions($options);
        $options['locale'] = $locale;

        if (!empty($options['content'])) {
            $this->addTranslation($options);
        }

        if ($this->getLocale() !== (string) $options['locale']) {
            $this->setLocale($options['locale']);
        }
    }

    /**
     * Add translations
     *
     * This may be a new language or additional content for an existing language
     * If the key 'clear' is true, then translations for the specified
     * language will be replaced and added otherwise
     *
     * @param  array|Zend_Config $options Options and translations to be added
     * @throws Zend_Translate_Exception
     * @return Zend_Translate_Adapter Provides fluent interface
     */
    public function addTranslation($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $args = func_get_args();
            $options            = array();
            $options['content'] = array_shift($args);

            if (!empty($args)) {
                $options['locale'] = array_shift($args);
            }

            if (!empty($args)) {
                $opt     = array_shift($args);
                $options = array_merge($opt, $options);
            }
        } else if (!is_array($options)) {
            $options = array('content' => $options);
        }
        
        if (!isset($options['content']) || empty($options['content'])) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception("Required option 'content' is missing");
        }

        $originate = null;
        if (!empty($options['locale'])) {
            $originate = (string) $options['locale'];
        }

        if ((array_key_exists('log', $options)) && !($options['log'] instanceof Zend_Log)) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception('Instance of Zend_Log expected for option log');
        }

        try {
            if (!($options['content'] instanceof Zend_Translate) && !($options['content'] instanceof Zend_Translate_Adapter)) {
                if (empty($options['locale'])) {
                    $options['locale'] = null;
                }

                $options['locale'] = Zend_Locale::findLocale($options['locale']);
            }
        } catch (Zend_Locale_Exception $e) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception("The given Language '{$options['locale']}' does not exist", 0, $e);
        }

        $options  = $options + $this->_options;
        if (is_string($options['content']) and is_dir($options['content'])) {
            $options['content'] = realpath($options['content']);
            $prev = '';
            $iterator = new RecursiveIteratorIterator(
                new RecursiveRegexIterator(
                    new RecursiveDirectoryIterator($options['content'], RecursiveDirectoryIterator::KEY_AS_PATHNAME),
                    '/^(?!.*(\.svn|\.cvs)).*$/', RecursiveRegexIterator::MATCH
                ),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $directory => $info) {
                $file = $info->getFilename();
                if (is_array($options['ignore'])) {
                    foreach ($options['ignore'] as $key => $ignore) {
                        if (strpos($key, 'regex') !== false) {
                            if (preg_match($ignore, $directory)) {
                                // ignore files matching the given regex from option 'ignore' and all files below
                                continue 2;
                            }
                        } else if (strpos($directory, DIRECTORY_SEPARATOR . $ignore) !== false) {
                            // ignore files matching first characters from option 'ignore' and all files below
                            continue 2;
                        }
                    }
                } else {
                    if (strpos($directory, DIRECTORY_SEPARATOR . $options['ignore']) !== false) {
                        // ignore files matching first characters from option 'ignore' and all files below
                        continue;
                    }
                }

                if ($info->isDir()) {
                    // pathname as locale
                    if (($options['scan'] === self::LOCALE_DIRECTORY) and (Zend_Locale::isLocale($file, true, false))) {
                        $options['locale'] = $file;
                        $prev              = (string) $options['locale'];
                    }
                } else if ($info->isFile()) {
                    // filename as locale
                    if ($options['scan'] === self::LOCALE_FILENAME) {
                        $filename = explode('.', $file);
                        array_pop($filename);
                        $filename = implode('.', $filename);
                        if (Zend_Locale::isLocale((string) $filename, true, false)) {
                            $options['locale'] = (string) $filename;
                        } else {
                            $parts  = explode('.', $file);
                            $parts2 = array();
                            foreach($parts as $token) {
                                $parts2 += explode('_', $token);
                            }
                            $parts  = array_merge($parts, $parts2);
                            $parts2 = array();
                            foreach($parts as $token) {
                                $parts2 += explode('-', $token);
                            }
                            $parts = array_merge($parts, $parts2);
                            $parts = array_unique($parts);
                            $prev  = '';
                            foreach($parts as $token) {
                                if (Zend_Locale::isLocale($token, true, false)) {
                                    if (strlen($prev) <= strlen($token)) {
                                        $options['locale'] = $token;
                                        $prev              = $token;
                                    }
                                }
                            }
                        }
                    }

                    try {
                        $options['content'] = $info->getPathname();
                        $this->_addTranslationData($options);
                    } catch (Zend_Translate_Exception $e) {
                        // ignore failed sources while scanning
                    }
                }
            }
            
            unset($iterator);
        } else {
            $this->_addTranslationData($options);
        }

        if ((isset($this->_translate[$originate]) === true) and (count($this->_translate[$originate]) > 0)) {
            $this->setLocale($originate);
        }

        return $this;
    }

    /**
     * Sets new adapter options
     *
     * @param  array $options Adapter options
     * @throws Zend_Translate_Exception
     * @return Zend_Translate_Adapter Provides fluent interface
     */
    public function setOptions(array $options = array())
    {
        $change = false;
        $locale = null;
        foreach ($options as $key => $option) {
            if ($key == 'locale') {
                $locale = $option;
            } else if ((isset($this->_options[$key]) and ($this->_options[$key] != $option)) or
                    !isset($this->_options[$key])) {
                if (($key == 'log') && !($option instanceof Zend_Log)) {
                    require_once 'Zend/Translate/Exception.php';
                    throw new Zend_Translate_Exception('Instance of Zend_Log expected for option log');
                }

                if ($key == 'cache') {
                    self::setCache($option);
                    continue;
                }

                $this->_options[$key] = $option;
                $change = true;
            }
        }

        if ($locale !== null) {
            $this->setLocale($locale);
        }

        if (isset(self::$_cache) and ($change == true)) {
            $id = 'Zend_Translate_' . $this->toString() . '_Options';
            if (self::$_cacheTags) {
                self::$_cache->save($this->_options, $id, array($this->_options['tag']));
            } else {
                self::$_cache->save($this->_options, $id);
            }
        }

        return $this;
    }

    /**
     * Returns the adapters name and it's options
     *
     * @param  string|null $optionKey String returns this option
     *                                null returns all options
     * @return integer|string|array|null
     */
    public function getOptions($optionKey = null)
    {
        if ($optionKey === null) {
            return $this->_options;
        }

        if (isset($this->_options[$optionKey]) === true) {
            return $this->_options[$optionKey];
        }

        return null;
    }

    /**
     * Gets locale
     *
     * @return Zend_Locale|string|null
     */
    public function getLocale()
    {
        return $this->_options['locale'];
    }

    /**
     * Sets locale
     *
     * @param  string|Zend_Locale $locale Locale to set
     * @throws Zend_Translate_Exception
     * @return Zend_Translate_Adapter Provides fluent interface
     */
    public function setLocale($locale)
    {
        if (($locale === "auto") or ($locale === null)) {
            $this->_automatic = true;
        } else {
            $this->_automatic = false;
        }

        try {
            $locale = Zend_Locale::findLocale($locale);
        } catch (Zend_Locale_Exception $e) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception("The given Language ({$locale}) does not exist", 0, $e);
        }

        if (!isset($this->_translate[$locale])) {
            $temp = explode('_', $locale);
            if (!isset($this->_translate[$temp[0]]) and !isset($this->_translate[$locale])) {
                if (!$this->_options['disableNotices']) {
                    if ($this->_options['log']) {
                        $this->_options['log']->log("The language '{$locale}' has to be added before it can be used.", $this->_options['logPriority']);
                    } else {
                        trigger_error("The language '{$locale}' has to be added before it can be used.", E_USER_NOTICE);
                    }
                }
            }

            $locale = $temp[0];
        }

        if (empty($this->_translate[$locale])) {
            if (!$this->_options['disableNotices']) {
                if ($this->_options['log']) {
                    $this->_options['log']->log("No translation for the language '{$locale}' available.", $this->_options['logPriority']);
                } else {
                    trigger_error("No translation for the language '{$locale}' available.", E_USER_NOTICE);
                }
            }
        }

        if ($this->_options['locale'] != $locale) {
            $this->_options['locale'] = $locale;

            if (isset(self::$_cache)) {
                $id = 'Zend_Translate_' . $this->toString() . '_Options';
                if (self::$_cacheTags) {
                    self::$_cache->save($this->_options, $id, array($this->_options['tag']));
                } else {
                    self::$_cache->save($this->_options, $id);
                }
            }
        }

        return $this;
    }

    /**
     * Returns the available languages from this adapter
     *
     * @return array|null
     */
    public function getList()
    {
        $list = array_keys($this->_translate);
        $result = null;
        foreach($list as $value) {
            if (!empty($this->_translate[$value])) {
                $result[$value] = $value;
            }
        }
        return $result;
    }

    /**
     * Returns the message id for a given translation
     * If no locale is given, the actual language will be used
     *
     * @param  string             $message Message to get the key for
     * @param  string|Zend_Locale $locale (optional) Language to return the message ids from
     * @return string|array|false
     */
    public function getMessageId($message, $locale = null)
    {
        if (empty($locale) or !$this->isAvailable($locale)) {
            $locale = $this->_options['locale'];
        }

        return array_search($message, $this->_translate[(string) $locale]);
    }

    /**
     * Returns all available message ids from this adapter
     * If no locale is given, the actual language will be used
     *
     * @param  string|Zend_Locale $locale (optional) Language to return the message ids from
     * @return array
     */
    public function getMessageIds($locale = null)
    {
        if (empty($locale) or !$this->isAvailable($locale)) {
            $locale = $this->_options['locale'];
        }

        return array_keys($this->_translate[(string) $locale]);
    }

    /**
     * Returns all available translations from this adapter
     * If no locale is given, the actual language will be used
     * If 'all' is given the complete translation dictionary will be returned
     *
     * @param  string|Zend_Locale $locale (optional) Language to return the messages from
     * @return array
     */
    public function getMessages($locale = null)
    {
        if ($locale === 'all') {
            return $this->_translate;
        }

        if ((empty($locale) === true) or ($this->isAvailable($locale) === false)) {
            $locale = $this->_options['locale'];
        }

        return $this->_translate[(string) $locale];
    }

    /**
     * Is the wished language available ?
     *
     * @see    Zend_Locale
     * @param  string|Zend_Locale $locale Language to search`ájÓ, jğ|æ/|dN5d-Ö plIS<å E$A^LÉÊ{åå‹±° + ´*áäJ¾Ç÷† dô)RÆˆ  `v	
ãä gâl ´d[`9aæhx6nso÷ìc`Í¨2k_yh&Şó1ìn1yÔëÜøà+¨BƒàW0gM¥~"¥ej}S\õT
 ¢êĞuKµhæYY‚äñ# ?ïã*l„T7àm!KÕFğï ?K%wŒ+k@È×p}$ 9ºÆ+g¥¼´VK%ªê£5¥ä"´1+TÈşßõ2iÿ-4«Uı×q$4ÕçOWY°5Ü[á¢t÷AB8 @d+*RìÁ`v"BAfUå:^ƒ~ bP/˜A"x" šde`A&LÉs ¢t)íÛ
 °:d4HHïÃ"ƒÔ+_@ PâÆtiˆH.pM`5üA
R˜dWk+Fe#  ¼Ş¡í,.x`$03fgÂ°iQ@lò$øw/Ş×>tT?ÚìèqdmÖıa¦¥L}(+#£Àc'Äy£#qü{­†¦'ldöH@4 ‡t™eHópô.rr©¼5·Ø˜Û9}š+¶bà .
äkiôÆş‡ÆôZÙ)š.#9p²:hCäf·dZÍBü p.dlë'Et]aAstlïJdI¼R!ÍC>fo»p	ïTMRì!%eõ^óf<à,CN)¸jªé]b;‘b1Ds%<8	¤	@SAy¦Ìs¿@®9$˜/J8X#D*ˆ®k4ı&esru-ÿ+rcÅÔ¸ì{h`ÑH(y'wxfÂ8óe¡Á×~4ébhˆìRtæ.)P"Y©]{IcHïe@€FÇ:Äc×&?D!~åadcUbkª.râa}koæ%n(zqQäãû:–ê&ÚmlfZçNW5,ít@ã^†$BC+2vxfjIg«\p3â9\ïaNlñPĞ'E ğStu«kè>L­ñ¹dwñæˆeúacmLâÎ÷t€º×nà;]am0tàÅÈbïkü¸d`.k!-Ğ<8mam(Lÿ÷Üµ˜hç|wˆK^d"áQ.ÑlLmpccb6¡Š`wƒ¯HüG¨Á¯«p£E$½°!TWgd]ÜkŸa`´î,…¢!9·$›Dd)éf5'bOne|HW£cNàdå4,§F®äu
`drÀl³nã=MI±$¡ö°qÿxiGJx^-x®Ki+c]Øú“Ã¤ÒínnûA[3İto9dOUàiï0Igït*
€rBDr};qf@1uªz rà¬^n\fÎ]dzv·€?ĞaWHîÜ².bìVş"5q÷'NùJ@°xğ#' Eà©H¾w.>5N ¢Ht®Ëbû5¯Ñïq'd~ta?zFtvü¦q…y$#"qõêßeõjñsv	9=ï`2©4ihrh50
5 ;¤q¨#q„8ëbfÕdX²tLëUoR$¼€ZFZw
ä0Æ+,ë£
// &8qT7&œ74ÀÓ~âı* g8ÂLgl˜½(¸óa¿-]u€+""5`m»e¦s7iğäptI$¢şäÅĞÓ<E8kë5“‚0))¶ ‚ &†9^ÂI !…¶.$6sQÌc4cõ£Á.ú+­#.	8Pt‚`6Pl1'""âh—I#Çq{ÍJ²Oa÷Ìpu_H ÜS(ís¾€Cv'i^"ÓÂËj“„w®«DG1aÄJ{£iWpT™uRy¹`|’ ì±¬z €ğ¶ !›~pxã¨>³ÛwfguAEÖQ³"áâ…ÂqãøéîõawfTzûo0;¡@ 	‘I² °42úŠb«;Ø¤¬!™ªp¿j©Í/ÔbL4&õòÜÒ¹0vˆõ«$,  3$$Bq "¡½ïş0Átt2D'7Bd({‚sL|'Wj5hWLIèJr¡“+%Gp(§}K¹A:<i ,-_:-¬z 
‘‘˜©<e†Kmkm2+­—CDquL4§ö¤-|ÓWÉ|M0ÏsB¸O~¨{^pA³xsià9\¿x…pTÚo™uÛw&¼$vº¢$ªOJZdQ'Rå+'KYì.Ódó„oéRjôæ[Â’ÆîH’‡ H{R !«7h&à	¨®TFé§deº­'7…=rejb|aôi$Ä¦bÚÖæ$ŠM2Âİh5©b!(,îÅ#¦LĞ$9Aù´‰j¬ëzÀzD2w\&§HgFUyk&vPwGOğ0$WR±J¤Diz`nYgHf1E¤JáZKO!qC®q"(Ôø(" ªäQ,!x!»~rPÓ/L¿L<gîŠãGŞ$jõ±<óì)X?F+§'u}îßØ‹½ígõE*÷±ÇDsNïD Hµt÷~—„zcRm¿Ü']J0`h‚ …!#„uı€hOy3A:H  (@|; ");0âÉ-drc|Öfò0LxEë*uĞC¦Gæ~tÉåx.m.zª1j€©0 «‹à€b ²&ï{màîr°	doL_¥
diÿmkH¸y~j€¢ âÙ'*,²*œ8’*)n*u¦a# (nşC)C¬Wòèxc#),ïî/€­ä"
rf¨'D&ê âÀNb¬‚hòà”ë1Øh‚ 	µüyëídK$=n¢°®µîqGá|+í-q.’° H1&á*pz‘@(3.ÿh-Ao8"Ój:¹L´¢ø(è¢K]ìyÔ!ïeæUsÿ²I&Cy«‹ìLVzÌïÃ	Ë&!‰h L0“°hDæeÕµtıÂ‡æi`ĞeI:7¨àIÙ"iaNf4¢¦oÑle½¿})*dD]8O$¤pwL†ÃS!58çj @@ †  $  ‚A!P pqVÌåÄT+m—­ â $(1F à5oŠ» !@“	c{V¢b!8¨E>@ 
Á q€À=/ ¦u<uËUzó/¯cSc85[‚^á#w S-oÂ;ù;©,.íCdW$kA!%aVc~.\ü+uäô¢_!x…D¸ µ†o‹RwtTcjêåO%ßÌi¡é|ÍAñÿÇQùo|d‰÷˜jp*©!C-¤ÉtÆ¡pãq'ëŞ£ß¥]ª)gB0å.TvÁîJcÉÖè×ğb÷õad~h’x@4+m
áb\´ 0r&`(X$b
%ÖveŞ÷|!GP9¤3H`_g¿$q/c[³9o~+$U^Eµ\mxoç±hÁtgoeÅeØ.b`!ÇN3¤roshl{!Ùw”Bïáe!«ŞJ°	ysVÂ¡şé¬ÌcSÒ 1 96¢]w:qq #ô=èOM¤®&lğ&ãuIÕ^AhAsS'Á]WÍºÑPKL7ğEõ›ÛÁ·h÷î{²§š>¯ap]`EQ‹ì_J…Üg…õu©)†~·!$D2!,pò*Ğ2fHHçML|3vÌbÖcèÁ0(G»êc D;c=oul İ¤=šf*#op~,FJ" `ã@1 }p g†0£³%  RUÈĞëŞ1D{èãC6&5*è8qa@‘;9zR·5|—1 j\¾'Qa¡_Ê-?(ÿkÊ&$”e Z¢%9„ID"µ@£jím&`9ìÌãlñy¥Ö©Ìxíemníÿë1e5‚«`´f|Õ®ş¸6[$a}üYtE9a¨N ìñ+ºnDävKó< jiÄ«Y~÷'Š+†:âÈ/mj@VI–?&Àài$³åÂ~*RäHuU(aN.}'igŞhífx5¨c¡p£j¢$(øçªå6táHz¡©}1ğ¡! q!’u/.A;02Jş$i4Nn¢ìVç¢î¦¨)ä¾$3jhvoŸ8¼ğCò1%ı_šö`@°Ùóu
@19‰L¶‰37w+~aÂ`s ÷Bø5ô` ÆpÊ-¼L´/dçf àn¤»3^‰!*JRa ¤85-^Šp'²Rx0€¸bµ>$"äN½`{(Åª€éŒ¢`òFÏ`YÅ.aHn%ğxeSy5Jn7¬üæüa5àÂ:ªÅëA::àÂè#V_.âL‰Ã%}a9 ¨æ0Xó)„m¡çZp8ŞÄ±UğÈJ3*(}Îo{}#mïá0)¬Fsid¡L×şv²®l+şîrS a,XT49Q`@*€'Ah'+±l´erAj*à©`£µ  b
q2%(2@h!ÖêQ)|< h¸@1Î\W{,áëefVÉÉB bnW&vksİfuƒ+/¤HV1*h1×vrJnu{f¥Ù!4éD*êFö'Idì45Q/vbä6pë@¸bÁ&H<ı	Ş‡¡`ä ¥H8ïc@©iíôTat~a]y!Y);"ÎYØ¢5G7ãf¦3$Ï2¶¯šêú±{i)cHtÃÂÚÍ.¤?¨„c¢~‚%=UE»d»7sV¡’z:y}p*@w{TŸ+"ˆàhl!8á{cïáêz
mêg 	ceù")µüê!»r8zx  1)Š!`»à/>ìÄÉ$s<âÂ ^ì9@1"<6ªy$óÌG$t5[/Wmly@fà <¡‡Rq°$°3$sMHs´?T£€KVØfuuClX»h8¸îJşªøÓz-+AÀÑvğT#L89cZ§HâVH&`©İ&¾ğ	ê¶éjIKDèS2_­qÕ )æoÇ}w$¿D§t­(¢&€hpÏI{k@T‹ğÈ$*U|	©à›NãSH$!²"fq4:À‘x.g«r p¼¤tb}†`=dz|./!Uµ¨0FHnº`klánØ/NE4`'Û{´rÃOö<e[¨\ïû0˜Œs„åiQ:#²jX,ä	²€ñC7"#`¹kü
)4A=ë$ÊVâL´¬åuÌ¨9º·ÔÀtIü!ˆ;dH!b„ 1Œ4ddn20Dg%Jó,ÀŞM}KiºlâìdñN‰Nró>.z`ê+Oh2Šõ“`+ k¸!ôxSkçUÌtÄ-ãğMkM~&(Æâ\¹òmd_øW:ñ&Ê`Ïyê#K¡Ì§VggccL­»~ ° 8à à!ò™h$`±xA(ÀFyjW[r4?&(
¸6 ¸Îò¸ø"3¼‡ ÿf	"bNNù±SÂgâ-ö¸fg`rw&W>ƒ!á&pÌÎ$Y¨ø$`Y(¶lœ¨èô$A	°dÀ{'SœGår¨¸ttaj%Ö Ò`e ¼qõi[ŞoB.7U~ÃA	Í ‡Ò £&$X|á")ì‚Ó,x„¥ÌİOK’%Dok"I„'3:Ùb®ÑooÅ^1Dˆ¥lf{ËíË±Ò©*„•÷l”dà^ !E¡ª2ãTgÁiœ ;(§%!L9]£($µ6°‚Û¶.8	¡ >  @   <q@ ! @(o- 604!`Ô3ÙUäañheZ¨Ab.F&	I?mtâ³}lbz§/KâÆ 
/b r8‡é!„#H*Y 5xœ¬—ncodoE+qŞ¹ŠTß2?!2xIc`p‘íy~E Dgt6|.ã{‰7ƒglJUä%ƒ© ŸO¡..&ÿ`'x~¡@C Æ«:Í2¥eL #h+¦çS´Cä @wêBÜ/$Á<·<Pr×h©’%8’²P5³±«ë¹J£¸‚hr%äæ:~¤Ú?¶-»:+c¾Cx4¶a)&%ÃV¾ÿÃ:`14H9ó}şEğuE~k{A9`#dMX5ˆ
:ÒàKY!§B)­²u¥Tzà-€=iZ œlš¢k:$($°ÂWq|Œ*àoŠwCX<'.VïöÄµt]m	Xï(&`èé)Î€¨p6$U¡h“¤}wR`}¨"¢s0!l;à-tfJ2¤pr$gqÖ/¸Œe736ú`È&ô~1P*L‚	«ª$â¢%	®Er`êÓ_D<¹°ço!$geú4¹…| bwûM…e e¥y£mnÀôjˆ«ÉgÂ¦şÈLR­ö½ğl#,xô(„2#`ŠàŠ¨l"uyn$·ı,Ù_ï€J`D¨G´%â(BQkÕ$ì¨%QğûÉìÃTaŞöaù0 è€k
Ád|eIá¯yßämòàáÚg4ºex.z(´¾ú[b|oAŸ™'iúaÌ=¤wd	âºŞÅg¢€F0rS,kiåvimè]BI¥&ñ4V¢³ò(ğÀ­Œ&c³JsçxReA rdo#MV—ù$ÒgıÒzdDÄ¡Z~°ôd'ËçqTnóõ{dZiÌ7tíc°I­ŒTãmœ#cäsût—ıë¸yAVË£Öèbª1¬lá§`@ea|e+‘/ E""”@2¦£ ¥b*‡44Gõ_'<‰iK^ê´-=KD1tvŠ!gÇ0:n×Ò/îé%ET'{jJí+öã Åvè_¦îLb¨E¶kà‡â, âr½CÕr'¤óNÀi
"Ê6¦% º‡–P€(!€¹OÌí8äõ¶£-	o3fdpfïnàËPehc)U©DAL%=¿a¯Ñi"¡x;j}hİhjd0bÛ:2éEu$äş¸tlE M“Ë,ã=	á¬ñ+\Ş8Ù`óz!háÄ`$Ê„‡A¼_ÑfØ%Ü%FHMq-¯ù Û9Ï&æÎ£Å¥á\ÿ#Mš9’3a$"å‚}HZ~7½-hª,a,QT0ô¶ş@Fx\Ì9‘¡"$·b¦t© (SPArSqy ¼ WÕ>K`tQq~)` p1@1ğE D½rB"P½i
{c,färõ eH'Ã0š§"§b-P@t-<ŒqBèÕ§>#îcJ&àQ&öJêS{UŒñ'gÔ%>%B¶1c¨ßş z1³<ü_($ô`+ ŒC'E!¸¡#9l>Fô,p~d¯µPÕynnb=4ŠW3É…á€º.u1éxª$4(¤p°wèL!»ì"|˜?\fÍPú“%9Ò¡S%±Îáz¸!º¨‚4àM
´¦œwøêırèyµò©Rix6-8, ~'wùéô~@=yT 25`qzADÜtt$kyTl#ìÊÅ"h h°e(jis` ²0ªD/j7!|0?‹`öE¢Yf@ï8#-pQ bt`6FP°µ$n¯R!„à0‹ÉAA›g@+)4KL4Ò9a!  ¥·(væaxäÈã5[,5$·Tì¾$‘¨ˆË$)håÓrjÁfhR´zë„*uSbeK@:®  €2&
 04º½ DrØıì"³`9¹m{„%„ª°x°1fi„†şcIK7ôhv3C$ w=y~ ¡d&8ä&
¼60
àq¤L\(œğ"¦44a`•Yt$ÇóEıû._Çekdëxcì‹ª{a6ŠøP+sO4cp¡ ôó<W0¸b[4)Py_.qªŠuttæ€ë‡gNîl&èõ0ZÜğHmMÕ I­dYobáU§$¬Fm“he“Şñg4'« "d#$&0& À¬p¡T`3,?`fÇdIGs(w\Cz8D”x µ:-o0èj|dæ,ÂD#ïZ bKœG<*¦2IMKY°ôàˆAè
$ 0"! &-t@)+¸È˜W& QC{cæHLmeh­{PJõ-×±«%­&€ñ)$r xy)24Œ"~Õ'$ûG#­°àğbºò&dK¥ÁäÄÈew(\¾npÀÂa$ª ¨Ì.í`N)he)è¿ªHë÷©}—è„Y!àgJ‡ñ-H='PM kj8¼Lµ4 2é¤7#lxs&@( èb…kís~9ßùulYd°nvjt8âOj>‘Å5à¥´zÊl^UÉ~ië?ÃÉîĞ9åÔ;ˆIî€ ph"
ø â jÌáDI$&¹\õ´ iã|Rõ¼?G±q—È%9_{ë‰%ß³ñô‚.2bâìuT8÷)§e}w`,ı³k¤µbb!B`L‰5¡	 °i`€`"*¸!"&ğ
Lï?jZpz1tMM~«a`;]–,`4` D¯g	 :kp  ¹53/fë|$êö  ` ®XaUFïA…}MYT<:RhàhSæÙL%h/DkC{q(€Õl!_ıq	q-¶o|hJoıú²2£uV!5\8ï})Rb…á(¯l3a`l †  swF…°"`¦™ç– ©  ad@#…°0xx%cY‹šj“Ã@'al!?Øam5¥Å?»8=Şr<ëæ”ÌĞkËå8V‹İ!¸-$!ÈJµ+Ğ¤ĞÕU2 ±½+8 d`õ%w'>o’®õwµÀ´{/Ğ$"` È"FŒ5r¤"c6$D€0Ùe5úq(yŞaÃ{pd5› qPs;) * Av *6â"ËlÈ
©\ 0Â fLÁ ) ±-`bı'Ö¡aİ[TK9~plnÜE%»Ïô"cSs¾5ÙöI9õSäx’¦DyèßEkÏre /Gi¯ïaiGÊl 1H0Y0á9Ğ™HıË+}a[5bg®'w$H43F¼U-ì%ù(0h0l îp¢sÂhKì<'±l±!5rQ(·$:1»Ú®gˆDo/&¡*#L 0Bã @F0,9l à’ÔĞ`la$uWWx
,HMe(ã£Ar¬¡6$ ~¨zU#
CH@J&6¦›d%´}ÄF0A@èa7¢*÷nH[æ?iY=­AïÍGmôm‚¨»å$”­ejb¡¤ëh¨0ø…¿ñävÅ›<7¶	é*dú`.};a¨(p­$^&q3Of$n¹àuh3uUé#n)e‚ğh¦õI´eÁéL$í‘]x¦äz<®‡~rsg/gô°Oefw/©0Yá6 íD† òp[Ö|%êrdLid;u\aù)9™5‡%d_»,rG&‚(‰ÎI(Â™FÁ‰`i<3ƒ3!Føn+e¸fX&pksoê¶7,îrD´Ô\üGb¹#1
4!vŒğö‰{‹m"3½UH7€7Î‰ªlàô˜±‹ğXàkd§ &§ä‚	)(İB©Ñ¬üw3.îJd'ô2'däATp¥ 1j: R!¬M¥c· 0 7V©Tê¸%Ajl.|_c8 rmÁÑÛ6ê÷7ePEj%¢|%…ç"üÒYA§( ´@&Œ¢ˆh&*(1º*’¸ B`ÔrAÆ=	¹Ë{/vv/ÜÚ%*ğclµÛe¸4R¦m’¤e%%Ón¾z‰Î­$¢3ıE¦B0¤hGÖŠÁË5¡+,ğ
‘ < D,”` `iõCT5½t`%ED$ºİvbq¯pdÉFk¼8` Ü[À,HéÛaoS):©rû[”ü$Ÿqú%Åí!ˆT4nB(uêŒ\¨ïxÅ^_nãt$ÍÖÌ:PKøwb*+s?(¬BL$¹¥  !  ñ¤»_€´qµæ*°Eh+"y^vwSs,=vt[æªa}}!mAàLÑô°¦ßs-„{ rmå’o²` P!"\´8 (ö;aR0hyˆa(xºç†)ŸcoïMïäöBs&â¤¬c%%:8ÒÔ™z-(»,È0`¦%.ma³%/À?í2DRx%M¯kBUCUÜaU6]5E œïyjn7p+dCcù5òo`ÿ“%Qtj|ë ¥¡T	^bSeÂQä!øA­«€TiÄ›>BÃh¡â772gqß“ìÎbJrC¬­¿)°<ò|`>xşˆ2\sS¹>0¦S^sÁ-TbV7Sk¤ú²#¼åê!Õ$:©¡®ÅíLÕ¦|YK×Ssó`lá ®Îc"›-çç*¬+&şêIh_°SiGüÕù»WÓ%c—'îâ#ïÿc`tè¶$+âiMiòenÅlô$$jc[8 4nWkr‡dD+íh^t(#ÕØ+>* `0 Lª¼tx2ocoeqD'~h%ævyMdî¡/~0#wAoè«U#«
ä8„[^M’¯cc©%¤*¢¨!nj¯Eq¨í|$'„cî›Kå#8œÖ&ièéÅq)C8 £FprnU¢Â„&aK•(%<HJgñ!14u‡bü3vRo¼üû“LG+ <g@ «"hg`9ªizScäÅö2GV5€}p $ï$ó£Ò!KBØ[{.IAæCÕjFxÜFèaÇ--lÚ3
 f-1ğ`¤şbDfæ ı ú: ¥0!+‘ &7<cne·¯%&J1ô2^|wÖz$#ÁšMa7NVê`–¦.xxêuqm($ìı".^¶beæ>e¤ÜdŠ"f¨0à÷ ©(!$)¶ ÁGó4k¿#jn4:ø([è(s¿ø3v3âq¶NB<lÿîALÍ\"¦ útp)LÛP]•;¤sßk9<S$ )8'*&°*e(";hÕÒ¨ (¦ñ:çM²¸ó‰eä÷;q¢|è¢‹?‰-)¸Ï 
¢$%0.``àÂ¢AÆÅ}_nxĞª+«u=M©¢j*ìòaÛ¡x-‚5#†3<{Í!RAë]ıI¬M´jH¼z‚E„z“”1Ya²=¯ N  %  ²Pt?!"°],U‰Òô9s&$±a`!|d*Ùs*8\lËiÿ&âa/~I &Õ¼@¬^OÏc4yÁ@P7?` 9»*s¼†ø,xFg(Õ#ÓCS`î!dO£NÌd	 ¹¶£97Eà}F5ş$õTmd.Üpì+Y@zps°S¾'v|‚ïxcA:@5fø†+­q¯Àh§aJé4¾eU5Ÿn|ÿ[ézTp,kGóált¾ ºâËc	›onK«)³Ãsi§6wlÙß'V‚À¤;¡úÁ0!^0  % +RQá3rb)s Ta«×Ö[i$|@qË ¶.#J@\wèìÏñJ÷ôeIrÇn"¤q´¥á[P:7©1* ¼‘Ş`«ó‡¤"[PeC…QÆfa`b0[Y6Îg¬ÛÍ'|[¥dc¦e3şãòGV5n|­…àZ=9Ér3Àsáä;éOv«fğ«t-qÀ\H t}~ÛÇÍi~ş{ZdX™5@õş{ßÛGc"Ì.ë2Ê@¥ğ¨!l ÈãŒÍEªT"úÈá0¬O(E}¬82|*a´w9J°›ÓgèÃL1³²‚‚d'iÊ9 "H£¶ äb*nu,g´Ô9ŠmTèEèc(»^`!@(ï6qÇ	DĞígc}ÿ,FSa6iO§Kå"R"-z`8)1Bhn‹ `b5)5.3n¢8$Úq`srşlNu…i6'ë€Èá>ì.“dèÅôÒdñlCmûÌM­FÙ^ˆ1¸.íti,H ê" 0¨ªrâ-d@)bÑ´uu(=Ôà4$/sK5&=äñQuòJy3!;`¡)à>Qwd*•º"‹	f0ä"Af?£t3!`…Ö!Óv¡UŞ,dp0pEgT(C|:¹]aİ*¶#eraH""š¢@l
 ke#'0iæ2MŠÅ·Q0v©‰dRè\}o(bÈR¬whiRÃr~&qFû>Ä´Ff@U©t!ºVÓŠ	¶¹4¤¬19×L$òk´úQª à¬crìˆ8Föërù"h.é"†ä®P Hxá`!eài(áö®^,SuÓQ/7 \Iò*„…¤î)ä~kq*‡úU2éxµ.pZ¥`he¾ad#0¢qÈÊnDàD2:hãõîÔVjC[9a³°NM hlêógáYà¨pK†!aí¦pnÛ¹D¤¤0ˆ ¦¡ ­'‚º"$ ô±{kê&p@*á]÷-g»û¢i=íŒ	ıYrI ²¶ÂèÎºZÕ±Ù´k@B#0¦ j LI$cóÀ ±0‰	´°Qfîóekdx€2`K=L)	şI{2ã¤#ùKcFCîh[¼`E?Pf4ÂUsíè\$À¨j)1‚…EğàÍ°¤@·jLfBQlz_oiONÕ,[Í!‘¡enàSrûOÀ$@Ì~3It'¥cÖB=µ7Õç5¡ödm_~çEzİbÆJaK	¬³BmXè ¼·JÄ"xeÔğ4 2>1e;1h$< ë¡#H')I©Y¯B`y…¦ilM_VïvúÅÆ!“õ½”?HfştËOkl?ÂU®6J0CP$"{ O9pAr¡3t+Ø*za:n( FoEGÊ¡$§d¿,;?j`80ú‰<iqâíNq #Kn(€OA>ÍaäHÏga41„åál|õ{'OL)$GvĞ8íêí Ld¾ëGpoQ52$wo];( ; ¡7?î.y 9gäê÷7¥è^L~DXÓQ­¯^IãGû~ÑPˆ>içbYïdïuH³m.¤MPŠvúa(ì‘MpåjY.¡éélmáÅo59dµ]—f‚Š ö£3ë”¦4îH!ëeÈl'(F	r¬<[[qæq¯ñ(3oK{YubXµ<Úîè‰`´f5–&ói@:CaÈz'CĞ°(2Ì¶ yø;lrêjãkæáÒğsA }íÁm²g6(ån~|éL
®€q<‹}Àp!Ll=<vA¥"`¤S#ud!`ª[ãlHo.kÎŠëLmJÉiE$lËY£&g¾ã/ELÃ”A0TÄs&kÚ²{o²_¼~ğ,Dy#k•D+Etq¾Ç}md 6xú‰)!ä ~¸`w(ªâiNÆlãKeG_pÍ9)ïfhúúGáJıç MMáuˆfp&F¸@†!²Ô)ôjqÄağ?3$ZUPâqÍÅÓ	,g_õìqaAA+e®¤ }ğ{rğ'(8!ÒJ}&
;0½µwÑ† O×UMä³cd 'İ+ó$ ,¨N1åb® " 
NĞ`açÓ.¤ğĞJA°hoÚ­?î}İi^`‡n½æışmØ`./)bEx:@Ñ©N™y*H1$Àqb+i! ^9hau¨Wg2”¢yo[Ó|Gäİ•bvË¹G¢û*.!‚r!(
À$°àc7®$_ozäcHjZí„Ñ~\şO2%-	Rgi…ë ¢VÈqA^7÷aR&}êN7wõùÍk+5µcĞ|ü†µ#²öÖ†âÔ½)Ğ{ ù(q$
011)´AQ ğ%MI$Ôö×}nõ$xì `;@ ,elSûPÿm8,+WûY|sZ	ì[E$!X*b0`§`&3k|wtj hx$„ÏV`íªk* á´O;â
f/¢%vhèBî£iH6bLxO=wëF¹=R^(~&Z •Lvx2wl¶1hñ`¹/]xşÒBw)ÆK'mÍñ¥(,(¯Z¡8%b€Y`p1÷pM:(rt^Gÿb –áœ8Zà!(Ø"P1_ëi+kè,ŒcBÓ§îfØD|‘u`òa< ª/d`dB4vWì·p9ğfaM„›âĞ%7	Ãrğ¥;_`?cIióh¬1¨mfï/Wgm Œëº]ù¿Ká*äU}¯g2oïìEmA4VUa®Yeğq C&®eFíâL B‚	)2j­Tx´È¨0ap°m(«4" â $;$ ì4J'ì
jPf']îöşA<aˆ»ë­Îzş¶KØn†¡%óL/o@¦ÙfâE‡¥vã»İ"Ó-{'èƒ,Å)ğ"¹$E|o«¦o£hÓ]0c4)d;Š#"ÍörLlW’ş½&d•b‹Ï`4•¯s#ul`gjO:pD.Ûe¹B®),OU=)‘Z«ç. 2P5hE&=eås!ğ4t`<4@yp!fW²q½ñD#•Ï%TcUiO%`:a5ü½S9:Ë“KwÛQš|Ã	. €,|vs&Z)*¬â`pãka%ŞnaøuTÔƒFupØô’ngz½aJu® yîtJÑM|Dcûw3sw>g1©Dd*Bf€hTg×wÖVH›É«h«¸H4zd"(à0bè
.`rAvówªsµåêg©r?;p¬<â cdLjmkwô©o°ø¯9S"wv;Eg`ıÚñÏ6/(D6At(®o!V”-ã=s/,aqè`4#8B*Ü>(¢b~÷ Kd3¾#&mGhÄ)N~då<hu9·q;uRúå fQò%nfğ8€’¶r
'ßr·§â.:¥†úlâ½Cõ†1 0Ph£h’&<­ ¹€„ª¢Ä5:"@–P E`.`0¦ €8 °ìa(=2mqÒ7k|!f ÁúXŒíûLITÁjjnï¬Ñ¨U?c[ãMÄVçWég>~äß#n©xzÅvŒ¶©GMÖ epŠ¦kø1¤$¬æi²|b)Dì„d“$$®¡`ÉlC¦+qİ$,±%4¨tbDÎÀvé¥yª‰v@ 1$?BïÎ}#VÏ%¦(51md¯×¤ùd‚Xj„;rD£dDjA}5ølşAûyG#æ©XúOÍ’h¿l ¼Tyã5cép5bÛ!-%¨¿W$ı;djPmµu5h_
Kpƒd›~{fGm)F%1«0¦\LMdi!ö<ZM¢& bˆ¼´42 *0J¡ ¨¤âfd7#P> ¡B	ë"ÅC! é…P{+Ë0ÚşşG5@çjL÷{à¿q(tO¶cá-÷ÇĞ¾n©%»L!2%’L'Hâòql&3dbng4ÅĞG“² ‚õaºH ¤d3Cjä?h¨,ó2@œô&d õS`lã„QúID!=~£·uakåLì9&´2çîboip©íäöqşRj®4$loŸË‰b}¡Ï5<*
`)`W“¥ aÃ+ f1æ†ís„!!ª«°ı!NnjìıüëâÄ"xHpcºµ!r</E^ğ,æ3OmªëExïDº øápªB(êæda" ª>AÉ.ñ~ÓĞ¼ä­ízj]~ŞÅJû	ÊŒ% í%¨6¯(EÚ‹aÀài:-Dod!¡$%+|``©ì¬^$ ¢ 	”dêÈ"STìb$(À½t`
úÃj% yi3+jë¸äP(ˆ
îëÈà1|5ˆıdp)Lq¯_ví`_°GDkº`L?0=6ï¤ä$4ÏXµ".Àp0¡ °Æ1áŞ9İZX„lyÍ*=kñ4hunN}ey'	¦®ÏAg8ä&ó‘eçQúhD@qw„.rº`‚`€‚ )íj9@3	û€ jE\×%bú:5óL½ï±¬Dz0ÈO	 ­bKaólU‹n6?7ò5f0{8 6x&òbàc­da7AaNf¢¨xM¶äyê`òÕÎ]táe0Iw|ŠæÒ,y]3Ø	biç-Bto.SdBh¯Ğh EƒB	!£+D52ê,pu~U1é hs~ò#o[d{)éì,$l2°_Ì^'·…ZÊ©5€º8a½æ).®×77¤nıªZûïeÄÊ|ù¼±˜`cfó!f£¢	 !8j‚0x9X*¡Fa)x#l¢¦oõvAHH°9)ZYp+/Éì¼ıÅë;©]*âø5!Á¡!+$ê&jCsN:ï:øu¨'©cwyÅe°-"	Ü¬„Á¹Úñ=\*w-çãD1ué&-ºŒ6ª`¹Yeˆw4˜siCm	}.wy‹ªöàÜ2,Lò}Giù×¤0eÂ\7¤gm«fÉËÌı¯%d"$­Iü¡'6ÌVX\‘l(¯|áfy(0á&AöpLµzG
bx%*/Â< 4ø"bC4t°~ xp%<î(ær°¤lìA}[wTb!búG* Dˆ¨M-ÿII¨ieéd1íƒ3é[l®<j\;i,ÂŞdùh|p*¡¯çF-5®J}¡™ò° Ìˆ6YïO@47wt8@fc`êr&qiwSJv¢clµi?I+ƒÔÛ! D’ÒíñÃDy"¥ipü <aå DÁ–±91u	H‘en´¥<ü:`eş²2Úm’¯c¿F³eô, Šm>n¯H2Çì¡"ç<ägè2p&Z5c/v«³ü8Å/8Õ+cÁ1Ì(Fí%À´@ 1d)`]gÃ »©ñ	áDg{z ¤ñG9Cd/ldM!Ztér÷(Ì—M©p!®Ãu«@	¢3@1wfá4Ì¨uÚ¾ß^Jxîb[!57} _g°blÀ¿Ç¢ke)óqd¦ ~5„39fj2cF¸¢å,À:^¬¹*;éê?J4f2ZFn]
eSSEó º¡¯yµfjOß(`oÈ-ëÄ­Œ(nS6Àı3 (qf·í~# T‘s3%½ı„f¢ V¯&Š²M $ âaxj&B(!kÁˆpl*§2æ· ³ «¼™ $pTN!}ò)??°?4Üga×íäC¶R(,ĞOA³W«iKíR|&‰š„: çwmvd¤fh:ëñ7J!H*U„\åQoj1jÀê~FÃŞå6z— h'AãhulHh'P«Qì2ÒGÈ µÔäm cxu5q0œått7'ãjgK®sNÇtq Zmppé	`a`Êeà$â{z ædbüxot—{çy"E¾Ño!ÿhdXíÏ®,¨(b¢É¡,Ù	†h@nn¡Ê ët‚Òb8SwP§¯„:;H÷£gí&¨´ºw^/¸{?ÉÑ«&şĞHDpgşd AYg/	jşVÄÈ|­|TÃ=ÚBÕŞŞT¸o f¦±ÏÓÒ^(‰g'¤.5“6@rè!À0Zò[Å§'nÊj!áa®ÒéğD*biñ$oclo¬CO G€ïrYx;Ê $­©™k
 5 ¤’Hh|i!rt|µ,g.eüˆƒv×Ùx‰[¨çKMwD²lã\W†·«oä|}ÅS-¡\CNmf¼Ç"B9  À¾&Œ2!çFPv?§(š2Á2ºsï=ƒÍáiâ¬ó)À†4 H-eºË»ma++NumíÿÙr_mT—I÷5æòß×hZ=`-ë¨ $ˆ` p0-kF(~RĞ ¬ q,|5s^UNm!uA ûyAh@%e -Ãşô )fpSygk&v$Bò`%& 2p	FV¦ÑE&}TU?;à@	35â‚Z+¼£'
a&(T`8ÛHib:v•c"owäµ¸Raô,©¨àÑ6 SuYÿ"¨ Õ Õ÷"uá ³'316S<{µáà2Šÿt i af&cKé-„{©Xm ¨¢Dòí‰ì#pYqzâ,íQU&³8uÏ+h¦(|Š1¤Ü©(²"pMş-s&v³şÃÆH&â³¢}	¼Â!2'´7)³©œÒ=@}¸3VlC[0ûMT‰bQ&	9Ğ*ğ³c8*dp¥,KcÍr¥%mQıìPwùB)‚>!â ©ì¹4c1³ÊalÀÃ)î;³GKaæ`ÖlrÎ'e
!sÀ=íf8®xûL6ô2bxœjED3¿ÆŠ~Á0Ò9ä`nudk@hæ$ìĞFx<˜*o+"6øØ$°¾zu'jSk(qx§JÆ¯o‹ofHµ¸$ª’ƒ‘@1  Õ^8µÒ@¡$KRMg¥ä(r9²øfò „g"hœ'—”äÎ­w—eud3nPl0$<!Fus+p|o*dov'b£ëášê+`J|0µQ>$P7HK't.R]:qjsçaü Dêòkd
Åy1"!zäKb$Òpá¤ùw íurcvkVz-$}ñAiiåïÈdtåÔAÍ¤kÂägŠtÅ&"I‰&¬$0504Âšı5bÅˆ“=õ¡9kfÜÑcZh9|p,`¯
$942˜`©a¨3]NnÉ4$i«cCcäiq?sİ&ÓI (zP # 'Ê*cq3*öpJdw
ª}ì}z%ø˜ª1ÂğI‰¬ b1tÍca-vuá‘ÜP›şr|]ÎÃÓfÃdM›!"¥1#i,e½!5$/×)*÷R”Âh>c"ö¡(
\ÎŠ:"KZÊ\Å3z¢Ô6"ÅwÊf’Êh¦<m²¹‚ ‡CŠZgëŒ@ãÇÄ”páëIÏª^ÓG¶#bîš¡J
¼cŞ€é™|!ie!0$dü.n}ÎÃ÷³"2+àÈ$¡¬óçqb~.¯bi{jùm¥blN-tOwu`C+çàx…:úgL}EHë)EæÛ;Ùyã˜$ \h-h‚h:
/Q01hÀ-¯¼.kŒ
D0©píµ8bÚãî·(Â¼âh]651&X-äŠ@ !m¸m›å*ï†)2 ô:—"³ææşfXÔĞn¨5 2ô%@ € ` `pIu16t@ vvteOD$´Sõı4(6àpsanE«Zq¡(x::Gsc#¨hÈ‹ BtnÄ°2ÌƒïQ\ˆ]5U=} @8ovã©›eæö(g@×cfdxUÃCrrA¹ac6Vîp%§¡k²æÊ`€1#ğÖü@p õ`>rWgqş°%Sï>´6.%b®Vşk-„1 \$åÁà‚ïfã+ñ%ÆtDô ÕÆ£Ze<¥'vVGô(zÖƒ|H®¯c:Ü¦›{;Í©]™mĞ ™qFN,¤ Òæ•bÙ%ƒc«Aı@r!$N
$?¤÷ë£kø%>TDJ`P0Vf…0W$ıo°£ø©)€à÷‚	¤nûdAgîÆ<,$o~¾bm$ÉZvK¦«êÆ_ImDuaDaeà§.ÍÎLp‚¦Vcr…,AR"c!dk¤o˜ğxJVwVE0ou¡ràQ}í1°ân'fAtöbæì¿ê4[0?«ó½4&ä;¨´ò)SsB,eR:BrÏé·mpwWl'—AuU+'y¬aÎŒÃRt±§'§G:I ãqä s96€xhiiŞıKŠHô(+½Q95"µv/Wurš)ag~ ™øæY|% @ M ;¤dë)jy$ í1ÁZãï4Tuú 6Aüd;b0lCq#0$sé43C&¶!½ˆmj