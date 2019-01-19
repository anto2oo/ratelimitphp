## RateLimitPhp - Efficient PHP Rate Limiter

### About
RateLimitPhp is a PHP class that uses Memcache to enable you to implement rate limiting in your PHP projects (aka a certain number of requests per period of time), which can be pretty useful if you happen to develop an API in PHP. This is pretty self-explanatory. 

**Note** : This class works by calculating quotas on a rolling-period basis. For instance, if you make a first call at 6:03, and you set the rate limiting to reset every hour, it will only come down to zero at 7:03.

### Usage
When you instanciate the class, you can use an optionnal parameter that will be used as the Memcache server IP. It is set to default at 127.0.0.1 : 
```php
$ratelimiter = new RateLimiter("127.0.0.1");
```
You can then start to register calls to your API (or anything else you're using it for) by calling the "hit" function :
```php
$ratelimiter->hit(IDENTIFIER, QUOTA, TIME_PERIOD);
```
`IDENTIFIER` is a way to uniquely identify users. It can be an API key, the users IP address or anything else you're thinking about
`QUOTA` is the limit of requests made for each time period, after which user will be blocked.
`TIME_PERIOD` is the time period, **in seconds**, after which the calls counter will reset.

This function returns an array structured like the following :
```php
array(3) { ["granted"]=> bool(true) ["limit"]=> int(60) ["limit_left"]=> int(59) }
```
If `granted` is True, access to the user should be granted and in case it's False, the user has reached its quota and you'll be able to deal with it the way you want.
`limit` is the integer equal the number of requests you defined that this user can make every period of time.
`limit_left` is the number of request can still make during this period of time.

### Some examples
Here's a simple implementation example :

```php
<?php
require_once('RateLimiter.php');
$ratelimiter = new RateLimiter();
// Here we grant 60 requests per hour (3600 seconds) to each IP address ($_SERVER['REMOTE_ADDR'])
$req = $ratelimiter->hit("guest-" . $_SERVER['REMOTE_ADDR'], 60, 3600);
if($req['granted']){
    echo "You can still make " . $req['limit_left'] . " requests";
} else {
	die("You exhausted your quota !");
}
```