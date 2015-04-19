CalderaWP Magic Tags
=======================
Magic Tags renderer

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/CalderaWP/magic-tags/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/CalderaWP/magic-tags/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/CalderaWP/magic-tags/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/CalderaWP/magic-tags/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/CalderaWP/magic-tags/badges/build.png?b=master)](https://scrutinizer-ci.com/g/CalderaWP/magic-tags/build-status/master)

Using it (Making The Magic Happen)
---------
```PHP
$magic = new \calderawp\filter\magictag();
echo $magic->do_magic_tag('{post:custom_field}');

```

Built in tags
-------------
- `{user:[ field | meta field ]}`<br>
retrieves field or meta field of the current logged in user. e.g. `{user:first_name} {user:last_name}`
- `{_GET:[name]}`<br>
A GET var name. `$_GET['page']` is `{_GET:page}` 
- `{_POST:[name]}`<br>
A POST var name. `$_POST['page']` is `{_POST:page}`
-`{_REQUEST:[name]}`<br>
A REQUEST var name. `$_REQUEST['page']` is `{_REQUEST:page}`
- `{date:[format]}`<br>
A PHP date format string. `{date:Y-m-d}` `{date:F j, Y, g:i a}`<br>
- `{post:[ [field | meta field] | [ post_id : [ field | meta field ] ] ] }`<br>
Post field or meta field. Array fields withh be imploded into a comma-separated list. Optional Post ID and field.
`{post:post_title}` or `{post:223:post_title}` to get the tiel of post ID 223
- `{ip}`<br>
IP address of the visitor

Extending - Filters
---------
```PHP
apply_filters( 'caldera_magic_tag', $filterd_tag, $original_tag );
apply_filters( 'caldera_magic_tag-my_tag', $filterd_tag, $args_array );
echo $magic->do_magic_tag('An example of {my_tag:argument}');
```

