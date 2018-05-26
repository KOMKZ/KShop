<?php
return [
    'y2log_alias' => [

    ],
    'apifiles' => [
        'all' => [
            '@swg/root.php',
            '@api/GoodsController.php',
            '@api/FileController.php'
        ],
        'all-test' => [
            '@swg/root-test.php',
            '@api/GoodsController.php',
            '@api/FileController.php'
        ]
    ],
    'apides' => [
        'all' => 'lshop api swagger',
        'all-test' => 'lshop api swagger',
    ],
    'apialias' => [
        '@api' => ROOT_PATH . '/kshopapi/controllers',
        '@swg' => ROOT_PATH . '/swagger',
    ],
    'docdef' => [
        'out_dir' => ''
    ],
    'enumcmd' => 'kzcmd file/enums-str /home/master/doc/trs-doc/global/enums.yaml /home/master/doc/trs-doc/global/enums-str.php',
];
