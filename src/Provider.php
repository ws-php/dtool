<?php

namespace Yeosz\Dtool;

/**
 * Class Provider
 *
 * @property string name
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property string qq
 * @property string mobile
 * @property string phone
 * @property string postcode
 * @property string image_url
 * @property string bitmap_url
 * @property string company_name
 * @property string id_card
 * @property string address
 * @property string datetime
 * @property string timestamp
 * @property string year
 * @property string date
 * @property string time
 * @property int tinyint
 * @property int smallint
 * @property int mediumint
 * @property int bigint
 * @property int int
 * @property int integer
 * @property string uuid
 * @property string ip
 */
class Provider
{
    /**
     * 资源对应
     */
    const RESOURCES = [
        'chinese_characters' => 'chinese.characters.csv',
        'company' => 'company.csv',
        'first_name' => 'first.name.csv',
        'last_name' => 'last.name.csv',
        'area' => 'area.json',
    ];

    /**
     * 属性对应
     */
    const PROPERTY_MATCH = [
        'name' => 'getName',
        'first_name' => 'getFirstName',
        'last_name' => 'getLastName',
        'email' => 'getEmail',
        'qq' => 'getQq',
        'mobile' => 'getMobile',
        'phone' => 'getPhone',
        'postcode' => 'getPostCode',
        'image_url' => 'getImageUrl',
        'bitmap_url' => 'getBitmapUrl',
        'company_name' => 'getCompanyName',
        'id_card' => 'getIdCard',
        'address' => 'getAddress',
        'uuid' => 'getUuid',
        'ip' => 'getIp',
    ];

    /**
     * 资源
     * @var array
     */
    private $resources = [];

    /**
     * 增长类属性
     * @var \stdClass
     */
    private $increments;

    /**
     * @var \stdClass
     */
    private $providers;

    /**
     * @var Number
     */
    public $numberProvider;

    /**
     * @var Datetime
     */
    public $datetimeProvider;

    /**
     * Provider constructor.
     */
    public function __construct()
    {
        $this->increments = new \stdClass();
        $this->providers = new \stdClass();
        $this->numberProvider = new Number();
        $this->datetimeProvider = new Datetime();
    }

    /**
     * 获取资源
     *
     * @param $key
     * @return array
     */
    public function getResource($key)
    {
        if (isset($this->resources[$key])) {
            return $this->resources[$key];
        }

        $path = __DIR__ . '/resources/' . self::RESOURCES[$key];
        if (pathinfo($path, PATHINFO_EXTENSION) == 'json') {
            $result = json_decode(file_get_contents($path), true);
        } else {
            $content = file($path);
            $split = function ($value) {
                return explode(',', $value);
            };
            if (count($content) == 1) {
                $result = $split($content[0]);
            } else {
                $result = array_map($split, $content);
            }
        }

        return $this->resources[$key] = $result;
    }

    /**
     * 随机字符串
     *
     * @param int $length 长度
     * @param int $type 0大小写,1小写,2大写
     * @return string
     */
    public function getString($length = 8, $type = 0)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $chars .= strtoupper($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars{rand(0, 51)};
        }
        return $type == 0 ? $string : ($type == 1 ? strtolower($string) : strtoupper($string));
    }

    /**
     * 随机中文字符串
     *
     * @param int $length 长度
     * @return string
     */
    public function getMbString($length = 8)
    {
        $chars = $this->getResource('chinese_characters');
        $count = count($chars) - 1;
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[rand(0, $count)];
        }
        return $string;
    }

    /**
     * 随机邮箱
     *
     * @return string
     */
    public function getEmail()
    {
        $suffix = ['@qq.com', '@126.com', '@163.com', '@sina.com', '@yahoo.com', '@gmail.com', '@hotmail.com'];
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $length = rand(6, 10);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars{rand(0, 35)};
        }
        return $string . $suffix[rand(0, 6)];
    }

    /**
     * 获取qq
     *
     * @return string
     */
    public function getQq()
    {
        $qq = strval(mt_rand(1, 9));
        $length = rand(4, 8);
        for ($i = 0; $i < $length; $i++) {
            $qq .= rand(0, 9);
        }
        return $qq;
    }

    /**
     * 随机手机号码
     *
     * @return string
     */
    public function getMobile()
    {
        $suffix = ['13', '15', '17', '18'];
        return $suffix[array_rand($suffix)] . rand(100000000, 999999999);
    }

    /**
     * 随机电话号码
     *
     * @return string
     */
    public function getPhone()
    {
        return '0' . rand(20, 999) . '-' . rand(10000000, 99999999);
    }

    /**
     * 随机身份证号码
     *
     * @return string
     */
    public function getIdCard()
    {
        // 区域code
        $area = $this->getResource('area');
        $code = [];
        foreach ($area as $region) {
            if (substr($region['id'], -2) != '00') {
                $code[] = $region['id'];
            }
        }

        // 生日
        $time = time() - 86400 * rand(1, 20800);
        $date = date('Ymd', $time);

        $idCard = $this->randomValue($code) . $date . rand(1, 9) . rand(1, 9) . rand(1, 9);

        // 检验位
        $idCard = str_split($idCard);
        $weight = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $sum = [];
        foreach ($idCard as $key => $value) {
            $sum[] = $value * $weight[$key];
        }
        $lastIndex = array_sum($sum) % 11;
        $last = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];

        return implode('', $idCard) . $last[$lastIndex];
    }

    /**
     * 随机邮政编码
     *
     * @return string
     */
    public static function getPostcode()
    {
        return rand(1, 9) . rand(1000, 9999) . '0';
    }

    /**
     * 数据随机值
     *
     * @param $arr
     * @return mixed
     */
    public static function randomValue($arr)
    {
        return $arr[array_rand($arr)];
    }

    /**
     * 获取随机图片
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    public function getImageUrl($width = 200, $height = 200)
    {
        return "http://lorempixel.com/{$width}/{$height}/";
    }

    /**
     * 获取占位图
     *
     * @param int $width
     * @param int $height
     * @param string $txt
     * @return string
     */
    public function getBitmapUrl($width = 200, $height = 200, $txt = 'image')
    {
        $txtSize = $txt ? floor($width / strlen($txt)) : 25;
        return "https://placeholdit.imgix.net/~text?txtsize={$txtSize}&txt={$txt}&w={$width}&h={$height}";
    }

    /**
     * 获取公司名称
     *
     * @return string
     */
    public function getCompanyName()
    {
        $resource = $this->getResource('company');
        return $this->randomValue($resource[0]) . $this->randomValue($resource[1]);
    }

    /**
     * 获取姓
     *
     * @return string
     */
    public function getLastName()
    {
        $resource = $this->getResource('last_name');
        return $this->randomValue($resource);
    }

    /**
     * 获取名
     *
     * @param int $gender 0随机,1男,2女
     * @return string
     */
    public function getFirstName($gender = 0)
    {
        $resource = $this->getResource('first_name');
        if ($gender == 1) {
            $resource = array_merge($resource[0], $resource[1]);
        } elseif ($gender == 2) {
            $resource = array_merge($resource[2], $resource[3]);
        } else {
            $resource = array_merge($resource[0], $resource[1], $resource[2], $resource[3]);
        }
        return $this->randomValue($resource);
    }

    /**
     * 获取姓名
     *
     * @param int $gender 0随机,1男,2女
     * @return string
     */
    public function getName($gender = 0)
    {
        return $this->getLastName() . $this->getFirstName($gender);
    }

    /**
     * 地址
     *
     * @return string
     */
    public function getAddress()
    {
        // 区域code
        $areas = $this->getResource('area');
        $regions = [];
        $ids = [];
        foreach ($areas as $area) {
            $regions[$area['id']] = $area['name'];
            if (substr($area['id'], -2) != '00') {
                $ids[$area['id']] = $area['id'];
            }
        }
        $id = $this->randomValue($ids);
        $code = [
            substr($id, 0, 2) . '0000',
            substr($id, 0, 4) . '00',
            $id,
        ];

        $getName = function ($id) use ($regions) {
            return in_array($regions[$id], ['县', '市辖区']) ? '' : $regions[$id];
        };
        $address = $getName($code[0]) . $getName($code[1]) . $getName($code[2]);
        return $address;
    }

    /**
     * 地址
     *
     * @return string
     */
    public function getCity()
    {
        // 区域code
        $areas = $this->getResource('area');
        $regions = [];
        $ids = [];
        foreach ($areas as $area) {
            $regions[$area['id']] = $area['name'];
            if (substr($area['id'], -2) == '00' && substr($area['id'], -4) != '0000') {
                $ids[$area['id']] = $area['id'];
            } elseif (in_array($area['id'], [110000, 120000, 310000, 500000,])) {
                $ids[$area['id']] = $area['id'];
            }
        }
        $id = $this->randomValue($ids);
        return $regions[$id];
    }

    /**
     * 增加供给器
     *
     * @param $key
     * @param $callback
     */
    public function addProvider($key, $callback)
    {
        $this->providers->$key = $callback;
    }

    /**
     * 初始化自增的供给器
     *
     * @param $key
     * @param int $start
     */
    public function addIncrement($key, $start = 0)
    {
        $this->increments->$key = $start;
    }

    /**
     * uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return md5(uniqid() . '-' . getmypid() . '-' . rand(111111111, 999999999));
    }

    /**
     * ip
     *
     * @return string
     */
    public function getIp()
    {
        $ipLong = array(
            array('607649792', '608174079'), //36.56.0.0-36.63.255.255
            array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
            array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
            array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
            array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
            array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
            array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
            array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
            array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
            array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
        );
        $key = mt_rand(0, 9);
        $ip= long2ip(mt_rand($ipLong[$key][0], $ipLong[$key][1]));
        return $ip;
    }

    /**
     * 下划线转驼峰
     *
     * @param string $string
     * @param bool $first
     * @return mixed
     */
    public static function toHump($string, $first = false)
    {
        $string = preg_replace_callback(
            '/([-_]+([a-z]{1}))/i',
            function ($matches) {
                return strtoupper($matches[2]);
            },
            $string
        );
        return $first ? ucfirst($string) : $string;
    }

    /**
     * 驼峰转下划线
     *
     * @param string $string
     * @return mixed
     */
    public static function toUnderline($string){
        $string = preg_replace_callback(
            '/([A-Z]{1})/',
            function ($matches) {
                return '_' . strtolower($matches[0]);
            },
            $string);
        return $string;
    }

    /**
     * 魔术方法
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        $match = self::PROPERTY_MATCH;
        if (isset($match[$name])) {
            $method = $match[$name];
            return $this->$method();
        } else if (isset($this->increments->$name)) {
            return $this->increments->$name++;
        } else if (isset($this->providers->$name)) {
            return call_user_func_array($this->providers->$name, []);
        } else if (in_array($name, ['tinyint', 'smallint', 'mediumint', 'bigint', 'int', 'integer'])) {
            if ($name == 'integer') $name = 'int';
            $method = 'random' . ucfirst($name);
            return call_user_func_array([$this->numberProvider, $method], []);
        } else if (in_array($name, ['datetime', 'timestamp', 'year', 'date', 'time'])) {
            if ($name == 'timestamp') $name = 'datetime';
            return call_user_func_array([$this->datetimeProvider, $name], []);
        }
        return null;
    }
}