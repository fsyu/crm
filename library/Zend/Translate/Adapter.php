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
     * @param  string|Zend_Locale $locale Language to search`�j�, j�|�/|dN5d-֠plIS<� E$A^L��{�����+ �*��J���� d�)�Rƈ �`v	
�� g�l��d[`9a�hx6nso��c�`ͨ2k_yh&��1�n1y�����+�B��W0gM�~"�ej}S\�T
 ���uK�h�YY���#�?��*l�T7�m!K�F�� ?K%w�+k@��p}$�9��+g���VK%��5��"�1+T����2i�-4�U��q$4��OWY�5�[�t�AB8 �@d+*R��`v"BAfU�:^�~ bP/�A"x" �de`A&L�s �t)��
 �:d4HH��"��+_@ P��ti�H.pM`5�A
R�dWk+Fe#  �ޡ�,.x`$03fg��iQ@l�$�w/��>tT?���qdm��a��L}(+#��c'�y�#q�{���'ld�H@4��t��eH�p�.rr��5�ؘ�9}�+�b� .
�ki������Z�)�.#9p�:�hC�f�dZ�B� p.dl�'Et]aAstl�JdI�R!�C>fo�p	�TMR�!%e�^�f<�,CN)�j��]b;�b1Ds%<8	�	@SAy��s�@�9$�/J8X#D*��k4�&esru-�+rc�Ը�{h`�H(y'wxf�8�e���~4�bh��Rt�.)P"Y�]{IcH�e@�F�:�c�&?�D!~�adcUbk�.r�a}ko�%n(zqQ���:��&�mlfZ�NW5,�t@�^�$BC+2vxfjIg�\p3�9\�aNl�P�'E �Stu�k�>L��dw��e�acmL���t���n�;]am0t���b�k��d`.k!-�<8mam(L��ܵ�h�|w�K^d"�Q.�lLmpccb6��`w��H�G����p�E$��!TWgd]�k�a`��,��!9�$�Dd)�f5'bOne|HW�cN�d�4,�F��u
`dr�l�n�=MI�$���q�xiGJx^-x�Ki+c]���ä��nn�A[3�to9dOU�i�0Ig�t*
�rBDr};qf@1u�z r�^n�\f�]dzv��?�aWH�ܲ.b�V�"5q�'N��J@�x�#' E�H�w.>5N �Ht��b�5���q'd~ta?zFtv��q�y$#"q���e�j�sv	9=�`2�4ihrh50
5 ;�q�#q�8�bf�dX�tL�UoR$��ZFZw
�0�+,�
// &8qT7&�74��~��*�g8�Lgl��(��a�-]u�+""5`m�e�s7i��ptI$������<E8k�5��0))� � &�9^�I !���.$6sQ�c4c���.�+�#.	8Pt�`6Pl1'""�h�I#�q{�J�Oa��pu_H �S(�s��Cv'i^"���j��w��DG1a�J{�iWpT�uRy�`|� 챬z��� !�~px�>��wfguAE�Q�"���q������awfTz�o0;�@ 	�I� �42��b�;ؤ�!��p�j��/�bL4&�����0v���$,  3$$Bq "����0�tt2D'7Bd({�sL|'Wj5hWLI�J��r��+%Gp(�}K�A:<i ,-_:-�z�
����<e�Kmkm2+��CDquL4���-|�W�|M0�sB�O~�{^pA�xsi�9\�x�pT�o�u�w&�$v��$�OJZdQ'R�+'KY�.�d�o�Rj��[��H�� H{R !�7h&�	��TF�de��'7�=rejb|a�i$Ħb���$�M2��h5�b!(,��#�L�$9A���j��z�z�D2w\&�HgFUyk&vPwGO�0$WR�J�Diz`nYgHf1E�J�ZKO!qC�q"(��("���Q,!x!�~rP�/L�L<g���G�$j��<��)X?F+�'u}��؋��g��E*���DsN�D H�t�~��zcRm��']J0`h� �!#�u��hOy3A:H  (@|; ");0��-drc|�f�0LxE�*u�C�G�~t��x.m.z�1j��0�����b �&�{m��r�	doL_�
di�mkH�y~j�� ��'*,�*�8�*)n*u�a# (n�C)C�W��xc#),��/���"
rf�'D&� ��Nb��h����1�h� 	��y��dK$=n�����qG�|+�-q.�� H1&�*p�z�@(3.�h-Ao�8"�j:�L���(�K]��y�!�e�Us��I&Cy���LVz���	�&!�h L0��hD�e��t��i`�eI:7��I�"iaNf4��o�le��})*dD]8O$�pwL��S!58�j�� @@ �  $� �A!P pqV���T+m�� � $(1F �5o�� !@�	c{V�b!8�E>@ 
��q��=/ �u<uˎUz�/�cSc85[�^�#w S-o��;�;�,.��CdW$kA!%aVc~.\�+u���_!x�D����o�RwtTcj��O%��i��|�A���Q�o|d���jp*�!C-��tơp�q'�ޣߥ]�)gB0�.Tv��Jc�����b��ad~h�x@4+m
�b\� 0r&`(X$b
%�ve��|!GP9�3H`_g�$q/c[�9o~+$U^E�\mxo�h�tgoe�e�.b`!�N3�roshl{!�w�B��e!��J�	ysV¡���cS� 1� 96�]w:qq�#�=�OM��&l�&�uIՏ^AhAsS'�]Wͺ�PKL7�E������h��{���>�ap]`EQ��_J��g��u�)�~�!$D2!,p�*�2fHH�M�L|3v�b�c��0(G��c D;c=oul ݤ=�f*#op~,FJ" `�@1 }p�g�0��%  RU����1D{��C6&5*�8qa@�;9zR�5|�1 j\�'Qa�_�-?(�k�&$�e��Z�%9�ID"�@�j�m&`9���l�y�֩�x�e�mn���1e5��`�f|ծ��6[$a}�YtE9a�N ��+�nD�vK�<�jiīY~�'�+�:��/mj@VI�?&��i$���~*R�HuU(aN.}'ig�h�fx5�c��p�j�$(���6t�Hz��}1�!�q!�u/.A;02J�$i4Nn��V�)�$3jhvo�8���C�1%��_��`@���u
@19�L��37w+~a�`s��B�5�` �p�-�L�/d�f �n��3^�!*JRa �85-^�p'�Rx0��b�>$"�N�`{(Ū����`�F�`Y�.a�Hn%�xeSy5Jn7����a5��:���A::���#V_.�L��%}a9 ��0X�)��m��Zp8�ıU��J3*(}�o{}#m��0)�Fsid�Lׁ�v��l+��rS a,XT49Q`@*�'Ah'+�l�erAj*�`��  b
q2%(2@h!��Q)|�< h�@1�\W{,��efV��B�b�nW&�vks�fu�+/�HV1*h1�vrJnu{f��!4�D*�F�'Id�45Q/vb�6p�@�b�&H<�	އ�`䠥H8�c@�i��Tat~a]y!Y);"�Yآ5G7�f�3$�2������{i)�cHt����.�?��c�~�%=UE�d�7sV��z:y}p*@w{T�+"��hl!8�{c���z
m�g�	ce�")���!�r8zx  1)�!`��/>���$s<� ^�9@1"<6�y$��G$t5[/Wmly@f� <��Rq�$�3$sMHs�?T��KV�fuuClX�h8��J����z-+A��v�T#L89cZ��H�VH&`��&��	��jIKD�S2_�q� )�o�}w$�D�t�(�&�hp�I{k@T���$*U|	���N�SH$!�"fq4:��x.g�r p��tb}�`=dz|./!U���0FHn�`kl�n�/NE4`'�{�r�O��<e[�\��0��s��iQ:#�jX,��	���C7"#`�k�
)4A=�$�V�L���ų9����tI�!�;dH!b� 1�4ddn20Dg%J�,��M}Ki�l��d�N�Nr�>.z`�+Oh2���`+�k�!�xSk�U�t�-��MkM~&(��\��md_�W:�&�`�y�#K�̧VggccL��~ � 8� �!�h$`�xA(�FyjW[r4?&(
�6�����"3�� �f	"bNN��S�g�-��fg`rw&W>�!�&p��$Y��$`Y(�l����$A	�d�{'S�G�r��ttaj%� �`e �q�i[�oB.7U~�A	́ �Ҡ�&$X|�")��,x����OK�%Dok"I�'3:�b��oo�^1D��lf{��˱�ҩ*���l�d�^ !E��2�Tg�i� ;(�%!L9]�($�6��۶.8	� >� @   <q@ ! @(o- 604!`�3�U�a�heZ�Ab.F&	I?mt�}lbz�/K�� 
/b r8��!�#H*Y 5x���ncodoE+q޹�T�2?!2xIc`p��y~E�Dgt6|.�{�7�glJU�%�� �O�..&�`'x~�@C���:�2�eL #h+��S�C� @w�B�/$�<�<Pr�h��%8��P5����J���hr%��:~��?�-�:+c�Cx4�a)&%�V���:`14H9�}�E�uE~k{A9`#dMX5�
:��KY!�B)��u�Tz�-�=iZ��l��k:$($��Wq|�*�o�wCX<'.V��ĵt]m	X�(&`��)΀�p6$U�h���}wR`}�"�s0!l;�-tfJ2�pr$gq�/��e736�`�&�~1P*L�	��$��%	�Er`��_D<���o!$ge�4��|�bw�M�e�e�y�mn��j���g¦��LR����l#,x�(�2#`����l"uyn$��,�_�J`D�G�%�(BQk�$�%Q�����Ta��a�0 �k
�d|eI�y��m����g4�ex.z(���[b|oA��'i�a�=�wd	���g��F0rS,ki�vim�]BI�&�4V���(����&c�Js�xReA�rdo#MV��$�g��zdDġZ~��d'��qTn��{dZi�7t�c�I��T�m�#c�s�t���yAVˣ��b�1�l�`@ea|e+�/ E""�@2�� �b*�44G�_'<�iK^�-=KD1tv�!g�0:�n��/��%ET'{jJ�+�� �v�_��Lb�E�k���,� �r�C�r'��N�i
"ʁ6�% ���P�(!��O��8����-	o3fdpf�n��Pehc)U��DAL%=�a��i"��x;j}h�hjd0b�:2�E�u$���tlE M��,�=	��+\�8�`�z!h��`$ʄ�A�_�f�%�%FHMq-����9�&�Σť�\�#M�9�3a$"�}HZ~7�-h�,a,QT0���@Fx\�9��"$�b�t� (SPArSqy � W�>K`tQq~)` p1@1��E D�rB"P�i
{c,f�r� eH'�0��"�b-P@t-�<�qB�է>#�cJ&�Q&�J�S{U��'g�%>%B�1c����z1�<��_($�`+ �C'E!��#9l>F�,p~d��P�ynnb=4�W3Ʌ���.u1�x�$4(�p�w�L!��"|�?\f�P��%9ҡS%���z�!���4�M
���w���r�y��Rix6-8, ~'w���~@=yT 25`qzAD�tt$kyTl#���"h h�e(jis`��0�D/j7!|0?�`�E��Yf@��8#-�pQ bt`6FP��$n�R!��0��AA�g@�+)4KL4�9a!  ��(v�ax���5[,5$�T�$�����$)h��rj�fhR�z�*uSb�eK@:�  �2&
�04�� Dr���"�`9�m{�%���x�1fi���cIK7��hv3C$ w=y~ �d&8�&
�60�
�q�L\(��"�44a`�Yt$��E��._�ekd�xc���{a6��P+sO4cp� ��<W0�b[4)Py_.q��utt��gN�l&��0Z��HmMՠI�dYob�U�$�Fm�he���g4'� "d#$&0& ��p�T`3,?`f�dIGs(w\Cz8D�x��:-o0�j|d�,�D#�Z bK�G<*�2IMKY����A�
$ 0"! &-t@)+���W&�QC{c�HLm�eh�{PJ�-ױ�%�&��)$r xy)24�"~�'$�G#����b��&dK�����ew(\�np��a$�����.�`N)he)迪H���}��Y!�gJ��-H='PM kj8�L�4 2�7#lxs&@( �b�k�s~9��ulYd�nvjt8�Oj>��5ॴz�l^U�~i�?����9��;�I ph"
� � j��DI$&�\�� i�|R��?G�q��%9_{�%߳��.2b��uT8�)�e}w`,��k��bb!B`L�5�	���i`�`"*�!"&�
L�?jZpz1tMM~�a`;]�,`4` D�g	 :kp� �53/f�|$��  ` �XaUF�A�}MYT<:Rh�hS��L%h/DkC{q(��l!_�q	q-�o|hJo���2�uV!5\8�})Rb��(�l3a`l��� swF��"`��� �  ad@#��0xx%cY��j��@'al!?�a�m5��?�8=�r<����k��8V��!�-$!�J�+Ф��U2 ��+8 d`��%w'>o���w���{/�$"` �"F�5r�"c6$D�0�e5�q(y�a�{pd5� qPs;) *�Av *6�"�l�
�\ 0� f�L��) �-`b�'֡a�[TK9~pln�E%���"cSs�5��I9�S�x��Dy��Ek�re /Gi��aiG�l 1H��0Y0�9ЙH��+}a[5bg�'w$H43F�U-�%�(0h0l �p�s�hK�<'�l�!5rQ(�$:1�ڮg�Do/&�*#L 0Bだ@F0,9l�����`�la$uWWx
,HMe(�Ar��6$ ~�zU#
CH@J&6��d%�}�F0A@�a7�*��nH[�?iY=�A��Gm�m����$��ej�b���h�0������vś<7�	�*d�`.}�;a�(p�$^&q3Of$n��uh3uU�#n)e��h��I�e��L$�]x��z<��~rsg/g��Oefw/�0Y�6 �D� �p[�|%�rdLid;u\a�)9�5�%d_�,rG&�(��I(F��`i<3�3!F�n+e��fX&pkso�7,�rD��\�Gb�#1
4!v����{�m"3�UH7�7Ή�l������X��kd� &���	)(�B�Ѭ�w3.�Jd'�2'd�ATp�� 1j: R!�M�c� 0 7V�T�%Ajl.|_c8 rm���6��7ePEj%�|%��"��YA�( �@&���h&*(1�*���B`�rA�=	��{/vv/��%*�cl��e�4R�m��e%%�n�z�έ$�3�E�B�0�hG֊��5�+,�
� < D,�` `i�CT5�t`%ED$��vbq�pd�Fk�8` �[�,H��aoS):�r�[��$�q�%��!�T4nB(u�\��x�^_n�t$���:PK�wb*+s?(�BL$��� !  �_��q��*�Eh+"y�^vwSs,=vt[�a}}!mA�L�����s-�{ rm�o�` P!"\�8 (�;aR0hy�a(x��)�co�M���Bs&⤬c%%:8�ԙz-(�,�0`�%.ma�%/�?�2DRx%M�kBUCU�aU6]5E ��yjn7p+dCc�5�o`��%Qtj|� ��T	^bSe�Q�!�A���Tiě>B�h��772gq����bJrC���)�<�|`>x���2\sS�>0�S^s�-TbV7Sk���#���!�$:�����Lզ|YK�Ss�`l� ��c"�-��*�+&��Ih_�SiG����W�%c�'��#��c`t趍$+�iMi�en�l�$$jc[8 4�nWkr�dD+�h^t(#��+>* `0 L��tx2ocoeqD'~h%�vyMd�/~0#wAo�U#�
�8�[^M��cc�%�*���!nj�Eq��|$'�c�K�#8��&i���q)C8 �FprnU�&�aK�(%<HJg��!14u�b�3vRo����LG+ <g@��"hg`9�izSc���2GV5�}p $�$��!KB�[{.IA�C�jFx�F�a�--l�3
 f-1�`��bDf���� �: �0!+��&7<cne��%&J�1��2^|w�z$#��Ma7NV�`��.xx�uqm($��".^�be�>e��d�"f�0�� �(!$)���G�4k�#jn4:�([�(s��3v3�q�NB<l��AL�\"���tp)L�P]�;�s�k9<S�$ )8'*&�*e(";h�Ҩ�(��:�M����e��;q�|袋?�-)�� 
�$%0.�``�¢A��}_nxЪ+�u=M���j*��aۡx-�5#�3<{�!RA�]�I�M�jH�z�E�z��1Ya�=� N  %  �Pt?!"�],U���9s&$�a`!|d*�s*8\l�i�&�a/~I &ռ@�^O�c4y�@P7?` 9�*s���,xFg(�#�CS`�!dO�N�d	 ���97E�}F5�$�Tm�d.�p�+Y@zps�S�'v|��xcA:@5f��+�q��h�aJ�4�eU5�n|�[�zTp,kG��lt� ���c	�on�K�)��si�6wl��'�V���;���0!^0  % +RQ�3rb)�s�Ta���[i$|@q� �.#J@\w����J��eIr�n"�q���[P:7�1* ���`��"[PeC�QƎfa`b0[Y6�g���'|[�dc�e3����GV5n|���Z=9�r3�s��;��Ov�f�t-q�\H t}~���i~�{ZdX�5@��{��Gc"�.�2�@��!l ���E�T"���0�O(E}�82|*a�w9J���g��L1����d'iʝ9 "H�����b*nu,g��9�mT�E�c�(�^`!@(�6q�	D��gc}�,FSa6iO�K�"R"-z`8)1Bhn� `b5)5.3n�8$�q`sr�lNu�i6'���>�.�d����d�lCm��M�F�^�1�.�ti,H �" 0��r�-d@)bѴuu(=��4$/sK5&=��Qu�J�y3!;`�)�>Qwd*��"�	f0�"Af?�t3!`��!�v�U�,dp0pEgT(C|:�]a�*�#eraH""��@l
�ke#'0i�2M�ŷQ0v��dR�\}o(b�R�whiR�r~&qF�>ĴFf@U�t!�Vӊ	��4��19�L$�k��Q� �cr�8F��r�"h.�"��P�Hx�`!e�i(���^,Su�Q/7 \I�*����)�~kq*��U2�x�.pZ�`he�ad#0�q��nD�D2:h�����VjC[9�a��NM hl��g�Y��pK�!a�pn۹D��0���� �'��"$ ��{k�&p@*�]�-g���i=�	�YrI ����κZձ��k�@B#0� j LI$c�� �0�	��Qf��ekdx�2`K=L)	�I{2�#�KcFC�h[�`E�?Pf4�Us��\$��j)1��E��Ͱ�@�jLfBQlz_oiON�,[�!��en�Sr�O�$@�~3It'�c�B=�7��5��dm_~�Ez�b�JaK�	��BmX� ��J�"xe��4 2>1e;1h$< �#H')I�Y�B`y��ilM_V�v���!����?Hf�t�Okl?�U�6J0CP$"{ O9pA�r�3t+�*za:n(�FoEGʡ$�d�,;?j`80��<iq��Nq #Kn(�OA>�a�H�ga41���l|�{'OL)$Gv�8��� Ld��GpoQ52$wo];(�; �7?�.y 9g���7��^L~DX�Q��^I�G�~�P�>i�bY�d�uH�m.�MP�v�a(�Mp�jY.���lm��o59d�]�f�����3딦4�H!�e�l'(F	r�<[[q�q��(3oK{YubX�<����`�f5�&�i@:Ca��z'C��(2̶ y�;lr�j�k����sA }��m�g6(�n~|�L
��q<�}�p!Ll=<vA�"`�S#ud!`�[�lHo.kΊ�LmJ�iE$l��Y�&g��/ELÔA0T�s&kڲ{o�_�~�,Dy#k�D+Etq��}md 6x��)!� ~�`w(���iN�l�KeG_p�9)�fh��G�J�� MM�u�fp&F�@�!��)�jq�a�?�3$ZUP�q���	,g_��qaAA+e�� }�{r�'(8!�J}&
;0���wц O�UM�cd '�+�$ ,�N1�b��"�
N�`a��.���JA�hoڭ?�}�i^`�n����m�`./)bEx:@ѩ�N�y*H1$�qb+i! �^9hau�Wg2��y�o[�|G�ݕbv��G��*.!�r!(
�$��c7�$_oz�cHjZ�����~\�O2%-	Rgi�� �V�qA^7�aR&}�N7w���k+5�c�|���#��ֆ���)�{ �(q$
011)�AQ� �%MI$���}n�$x� `;@ ,elS�P�m8,+W�Y|sZ	�[E$!X*b0`�`&3k|wtj hx$��V`�k* �O;�
f/�%vh�B�iH6bLxO=w�F�=R^(~&Z �Lvx2wl�1h�`�/]x��Bw)�K'm��(,(�Z�8%b�Y`p1�pM:(rt^G�b ��8Z�!(�"P1_�i+k�,�cBӧ�f�D|�u`�a< �/d`dB4vW�p9�faM����%7	�r�;_`?cIi�h�1�mf�/Wgm����]��K�*�U}�g2o��EmA4�VUa�Ye�q C&�eF��L �B�	)2j�Tx�Ȩ0ap�m(�4" � $�;$��4J'�
jPf']����A<a��뭁�z��K�n��%�L/o@��f�E��v��"�-{'�,�)�"�$E|o��o�h�]0c4)d;��#"���rLlW���&d�b��`4��s#ul`gjO:pD.�e�B�),OU=)�Z��. 2P5hE&=e�s!�4t`<4@yp!fW�q��D#��%TcUiO%`:a5��S9:˓Kw�Q�|�	. �,|vs&Z)*��`p�ka%�na�uTԃFup���ngz�aJu� y�tJ�M|Dc�w3sw>g1�Dd*Bf�h�Tg�w֎VH�ɫh��H4zd"(�0b�
.`rAv�w�s���g��r?;p�<� cdLjmkw��o���9S"wv;Eg`����6/(D6At(�o!V�-�=s/,aq�`4�#8�B*�>(�b~� Kd3�#&mGh�)N~d�<hu9�q;uR��fQ�%nf�8���r
'�r���.:���l�C��1� 0Ph�h�&<� ������5:"@�P�E�`.`0���8����a(=2mq�7k|!f ��X���LIT�jjn���ѨU?�c[�M�V�W�g>~��#n�xz�v���GM� ep��k�1�$��i�|b)D�d�$$��`�lC�+q�$,�%4�tbD��v�y��v@ 1$?B��}#V�%�(51md�פ�d�Xj�;rD�dDjA}5�l�A�yG#�X�O͒h�l �Ty�5c�p5b�!-%��W$�;djPm�u5h_
Kp�d�~{fGm)F%1�0�\LMdi!�<ZM�& b���42 *0J� ����fd7#P> �B	�"�C! 遅P{+�0���G5@�jL�{�q(tO�c��-��оn�%�L!2%�L'H��ql&3�dbng4��G������a�H��d3Cj�?h�,�2@��&d �S`l�Q�ID!=~��uak�L�9&�2��boip����q�Rj�4$lo�ˉb}��5<*
`)`W�� a�+ f1��s�!!����!Nnj������"xHpc��!r</E^�,�3Om��Ex�D����p�B(��da"��>A�.�~�м��zj]~��J��	ʌ% �%�6�(Eڋa��i:-Dod!��$%+|``��^$�� �	�d��"ST�b$(��t`
��j% yi3+j��P(�
��ȏ�1|5��dp)Lq�_v�`_��GDk�`L?0=6��$4�X�".��p0� ��1��9�ZX�ly�*=k�4hunN}ey'	���Ag8�&�e�Q�hD@qw�.�r�`�`�� )�j9@3	�� �jE\�%b�:5�L�ﱬDz0�O	 �bKa�lU�n6?7�5f0{8�6x&�b�c�da7AaNf��xM��y�`���]t�e0Iw|���,y]3�	bi�-Bto.SdBh��h E�B	!�+D52�,pu~U1� hs~�#o[d{)��,$l2�_�^'��Z��5��8a��).��77��n��Z��e���|����`cf�!f��	�!8j�0x9X*�Fa)x#l��o�vAHH�9)ZYp+/Ɂ����;�]*��5!��!+$�&jCsN:�:�u�'�cwy�e�-"	ܬ�����=\*w-��D1u�&-��6�`�Ye�w4�siCm	}.wy�����2,L�}Gi�פ0e�\7�gm�f�����%d"$�I��'6�VX\�l(�|�fy(0�&A�pL�zG
bx%*/�< 4�"bC4t�~ xp%<�(�r��l�A}[wTb!b�G* D��M-�II�i�e�d1�3�[l�<j\;i,��d�h|p*���F-5�J}��� ̈6Y�O@47wt8@fc`�r&qiwSJv�cl�i?I+���! D�����Dy"�ip� <a� D���91u	H�en��<�:`e��2�m��c�F�e�, �m>n��H2��"�<�g�2p&Z5c/v���8�/8�+c�1�(F�%��@ 1d)`]g� ���	�Dg�{z���G9Cd/ldM!Zt�r�(̗M�p!��u�@	�3@1wf�4̨uھ�^Jx�b[!57} _g�bl��Ǣke)�qd� ~5�39fj2cF���,�:^��*;��?J4f2ZFn]
eSSE� ����y�fjO�(`o�-�ĭ�(�nS6��3�(qf��~# T�s3%���f� V�&��M $ �axj&B(!k��pl*�2� ������ $pTN!}�)??�?4�ga���C�R(,�OA�W�iK�R|&���: �wmvd�fh:��7J!H*U�\�Qoj1j��~F���6z� h'A�hulHh'P�Q�2�G� ���m c�xu5q0��tt7'�jgK�sN�tq�Zmpp�	`a`�e�$�{z �db�xot�{�y"E��o!�hdX�Ϯ,�(�b�ɡ,�	�h@nn�� �t��b8Sw�P���:;H��g�&���w^/�{?�ѫ&��HDpg�d AYg/	j�V��|�|T�=�B���T�o� f�����^(�g'�.5�6@r�!�0Z�[Ł�'n�j!��a����D*bi�$oc�lo�CO G��rYx;� $���k
 5 ��Hh|i!rt|�,g.e���v��x�[��KMwD�l�\W���o�|}�S-�\CNmf��"B9  ��&�2!�FPv?�(�2�2�s�=���i��)��4 H-e�˻ma++Num���r_mT�I�5����hZ=`-�� $�` p0-kF(~R� � q,|5s^UNm!uA �yAh@%e -����)fpSygk&v$B�`%&�2p	FV��E&}TU?;�@	35�Z+��'
a&(T`8�Hib:v�c"ow䵸Ra�,����6 SuY�"� � ��"uᠳ'316S<{���2��t i af&cK�-�{�Xm ��D���#pYqz�,�QU&�8u�+h�(|��1��ܩ(�"pM�-s&v����H&Ⳣ}	��!2'�7)����=@}�3VlC[0�MT�bQ&	9�*�c8�*dp�,Kc�r�%mQ��Pw�B)�>!⠩�4c1��al��)�;�GKa�`�lr�'e
!s�=�f8�x��L6�2bx�jED3�Ɗ~�0�9�`nudk@h�$���Fx<�*o+"6��$��zu'jSk(qx�JƯo�ofH��$����@1  �^8��@�$KRMg��(r9��f� �g"h�'���έw�eud3nPl0$<!Fus+p|o*dov'b����+`J|0�Q>$�P7HK't.R]:qjs�a� D��kd
�y1"!z�Kb$�p��w �urcvkVz-$}�Aii���dt��Aͤk��g�t�&"I�&�$0504�5bň�=��9kf��cZh9|p,`�
$94�2�`�a�3]Nn�4$i�cCc�iq?s�&�I�(zP # '�*cq3*�pJdw
�}�}z%���1��I�� b1t�ca-vu��P��r|]���f�dM�!"�1#i,e�!5$/�)*�R��h>c"��(
\��:"KZ�\�3z��6"�w�f��h�<m��� �C�Zg�@��Ĕp��IϪ^�G�#bJ
�cހ�|!ie!0$d�.n}����"2+��$����qb~.�bi{j�m�blN-tOwu`�C+��x�:�gL}EH�)��E��;�y��$ \h-h�h:
/Q0�1h�-��.k�
D0�p�8b���(¼�h]651&X-�@ !m�m��*�)2��:�"����fX��n�5�2�%@ ��` `pIu16t@ vvteOD$�S��4(6�psanE�Zq�(x::Gsc#�hȋ Btnİ2̃�Q\�]5U=} @8ov㩛e��(g@�cfdxU�CrrA�ac6V�p%���k���`�1#���@p �`>rWgq��%S�>�6.%b�V�k-�1�\$�����f�+�%�tD���ƣZe<�'v�VG�(zփ|H��c:ܦ�{;ͩ]�m� �qFN,� ��b�%�c�A�@r!$N
$?���k�%>TDJ`P0Vf�0W$�o����)����	�n�dAg��<,$o~�bm$�ZvK����_ImDuaDae�.��Lp��Vcr�,AR"c!dk��o��xJVwVE0ou�r�Q}�1��n'f��At�b���4[0?��4&�;���)SsB,eR:Br���mpwWl'�AuU+'y�aΌ�R�t��'�G:I �q� s96�xhii��K�H�(+�Q95"�v/Wur�)ag~ ���Y|% @ M�;�d�)jy$ �1�Z��4Tu��6A�d;b0lCq#0$s�43C&�!��mj