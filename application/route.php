<?php
return ['__pattern__' => ['name' => '\w+',], 'links/:linkid' => ['links/index', ['linkid' => '\w+']], 'lists/:linkid' => ['lists/index', ['linkid' => '\w+']], 'detail/:linkid' => ['detail/index', ['linkid' => '\w+']], 'notify/:directory' => ['notify/index', ['directory' => '\w+']], '__alias__' => [],];